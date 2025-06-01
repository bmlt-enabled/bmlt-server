<?php

namespace App\Console\Commands;

use App\Interfaces\FormatRepositoryInterface;
use App\Interfaces\MeetingRepositoryInterface;
use App\Interfaces\ServerRepositoryInterface;
use App\Interfaces\ServiceBodyRepositoryInterface;
use App\Models\Change;
use App\Models\Format;
use App\Models\Meeting;
use App\Models\MeetingData;
use App\Models\MeetingLongData;
use App\Models\Server;
use App\Models\ServerStatistics;
use App\Models\ServiceBody;
use App\Repositories\External\ExternalFormat;
use App\Repositories\External\ExternalMeeting;
use App\Repositories\External\ExternalServer;
use App\Repositories\External\ExternalServiceBody;
use App\Repositories\External\InvalidObjectException;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportServers extends Command
{
    protected $signature = 'aggregator:ImportServers {--list-url=https://raw.githubusercontent.com/bmlt-enabled/aggregator/main/serverList.json}';

    protected $description = 'Import servers';

    private const DEFAULT_REQUEST_DELAY_SECONDS = 0;
    private const DEFAULT_RETRY_DELAY_SECONDS = 3;
    private static int $requestDelaySeconds = self::DEFAULT_REQUEST_DELAY_SECONDS;
    private static int $retryDelaySeconds = self::DEFAULT_RETRY_DELAY_SECONDS;

    public function handle(
        ServerRepositoryInterface $serverRepository,
        FormatRepositoryInterface $formatRepository,
        MeetingRepositoryInterface $meetingRepository,
        ServiceBodyRepositoryInterface $serviceBodyRepository
    ) {
        if (!legacy_config('aggregator_mode_enabled')) {
            $this->error('aggregator mode is not enabled');
            return;
        }

        $rateLimits = collect(config('aggregator.rate_limit_servers'));
        DB::transaction(fn () => $this->deleteNonAggregatorData());
        DB::transaction(fn () => $this->importServersList($serverRepository));
        foreach ($serverRepository->search() as $server) {
            try {
                $delaySettings = collect($rateLimits->get($server->source_id));
                self::$requestDelaySeconds = $delaySettings->get('request_delay') ?? self::DEFAULT_REQUEST_DELAY_SECONDS;
                self::$retryDelaySeconds = $delaySettings->get('retry_delay') ?? self::DEFAULT_RETRY_DELAY_SECONDS;
                DB::transaction(fn() => $this->importServer(
                    $server,
                    $serverRepository,
                    $formatRepository,
                    $meetingRepository,
                    $serviceBodyRepository
                ));
            } catch (\Throwable $e) {
                $this->error($e->getMessage());
            }
            $this->analyzeTables();
        }
    }

    private function deleteNonAggregatorData(): void
    {
        $meetingIds = Meeting::query()->whereNull('_server_id')->pluck('id_bigint');
        Meeting::query()->whereIn('id_bigint', $meetingIds)->delete();
        MeetingData::query()->whereIn('meetingid_bigint', $meetingIds)->whereNot('meetingid_bigint', 0)->delete();
        MeetingLongData::query()->whereIn('meetingid_bigint', $meetingIds)->whereNot('meetingid_bigint', 0)->delete();
        ServiceBody::query()->whereNull('_server_id')->delete();
        Format::query()->whereNull('_server_id')->delete();
        Change::query()->delete();
    }

    private function importServersList(ServerRepositoryInterface $serverRepository)
    {
        try {
            $url = $this->option('list-url');
            $response = $this->httpGet($url);
            $externalServers = collect($response)
                ->map(function ($server) {
                    try {
                        return new ExternalServer($server);
                    } catch (InvalidObjectException) {
                        return null;
                    }
                })
                ->reject(fn($e) => is_null($e));
            $serverRepository->import($externalServers);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            throw $e;
        }
    }

    private function importServer(
        Server $server,
        ServerRepositoryInterface $serverRepository,
        FormatRepositoryInterface $formatRepository,
        MeetingRepositoryInterface $meetingRepository,
        ServiceBodyRepositoryInterface $serviceBodyRepository,
    ): void {
        $this->info("importing server $server->id:$server->url");
        $this->importServerInfo($server, $serverRepository);
        $this->importServiceBodies($server, $serviceBodyRepository);
        $this->importFormats($server, $formatRepository);
        $this->importMeetings($server, $meetingRepository);
        $this->updateStatistics($server);
        $serverRepository->update($server->id, ['last_successful_import' => Carbon::now()]);
    }

    private function importServerInfo(Server $server, ServerRepositoryInterface $serverRepository)
    {
        $this->info('importing server info');
        $url = rtrim($server->url, '/') . '/client_interface/json/?switcher=GetServerInfo';
        $response = $this->httpGet($url);
        $serverRepository->update($server->id, ['server_info' => json_encode($response[0])]);
        $server->refresh();
    }

    private function importServiceBodies(Server $server, ServiceBodyRepositoryInterface $serviceBodyRepository)
    {
        $this->info('importing service bodies');
        $url = rtrim($server->url, '/') . '/client_interface/json/?switcher=GetServiceBodies';
        $response = $this->httpGet($url);
        $externalServiceBodies = collect($response)
            ->map(function ($serviceBody) {
                try {
                    return new ExternalServiceBody($serviceBody);
                } catch (InvalidObjectException) {
                    // TODO: Log something and save an error to the database
                    return null;
                }
            })
            ->reject(fn($e) => is_null($e));
        $serviceBodyRepository->import($server->id, $externalServiceBodies);
    }

    private function importFormats(Server $server, FormatRepositoryInterface $formatRepository)
    {
        $this->info('importing formats');
        $serverInfo = json_decode($server->server_info);
        $languages = explode(',', $serverInfo->langs);

        $url = rtrim($server->url, '/') . '/client_interface/json/?switcher=GetFormats';
        $externalFormats = collect([]);
        foreach ($languages as $language) {
            $this->info("importing formats:$language");
            $response = $this->httpGet($url . "&lang_enum=$language");
            $externalFormats = $externalFormats->concat(
                collect($response)
                    ->map(function ($format) {
                        try {
                            return new ExternalFormat($format);
                        } catch (InvalidObjectException) {
                            // TODO: Log something and save an error to the database
                            return null;
                        }
                    })
                    ->reject(fn($e) => is_null($e))
                    ->toArray()
            );
        }

        $formatRepository->import($server->id, $externalFormats);
    }

    private function importMeetings(Server $server, MeetingRepositoryInterface $meetingRepository)
    {
        $this->info('importing meetings');
        $url = rtrim($server->url, '/') . '/client_interface/json/?switcher=GetSearchResults';
        $response = $this->httpGet($url);
        $externalMeetings = collect($response)
            ->map(function ($meeting) {
                try {
                    return new ExternalMeeting($meeting);
                } catch (InvalidObjectException) {
                    // TODO: Log something and save an error to the database
                    return null;
                }
            })
            ->reject(fn($e) => is_null($e));
        $meetingRepository->import($server->id, $externalMeetings);
    }

    private function updateStatistics(Server $server)
    {
        ServerStatistics::query()->where('_server_id', $server->id)->update(['is_latest' => false]);
        ServerStatistics::create([
            '_server_id' => $server->id,
            'num_zones' => ServiceBody::query()->where('_server_id', $server->id)->where('sb_type', ServiceBody::SB_TYPE_ZONE)->count(),
            'num_regions' => ServiceBody::query()->where('_server_id', $server->id)->where('sb_type', ServiceBody::SB_TYPE_REGION)->count(),
            'num_areas' => ServiceBody::query()->where('_server_id', $server->id)->where('sb_type', ServiceBody::SB_TYPE_AREA)->count(),
            'num_groups' => $this->getNumGroups($server),
            'num_total_meetings' => Meeting::query()->where('_server_id', $server->id)->count(),
            'num_in_person_meetings' => Meeting::query()->where('_server_id', $server->id)->where('venue_type', Meeting::VENUE_TYPE_IN_PERSON)->count(),
            'num_virtual_meetings' => Meeting::query()->where('_server_id', $server->id)->where('venue_type', Meeting::VENUE_TYPE_VIRTUAL)->count(),
            'num_hybrid_meetings' => Meeting::query()->where('_server_id', $server->id)->where('venue_type', Meeting::VENUE_TYPE_HYBRID)->count(),
            'num_unknown_meetings' => Meeting::query()->where('_server_id', $server->id)->whereNull('venue_type')->count(),
            'is_latest' => true,
        ]);
    }

    private function getNumGroups(Server $server): int
    {
        // with world ids
        $numGroups = Meeting::query()
            ->where('_server_id', $server->id)
            ->whereNotNull('worldid_mixed')
            ->whereNot('worldid_mixed', '')
            ->distinct()
            ->count('worldid_mixed');

        // without world ids, unique meeting names per service body
        $serviceBodies = ServiceBody::query()->where('_server_id', $server->id)->get();
        foreach ($serviceBodies as $serviceBody) {
            $numGroups += MeetingData::query()
                ->where('key', 'meeting_name')
                ->whereIn('meetingid_bigint', function ($query) use ($serviceBody) {
                    $query
                        ->select('id_bigint')
                        ->from((new Meeting)->getTable())
                        ->where('service_body_bigint', $serviceBody->id_bigint)
                        ->where(function (Builder $query) {
                            $query
                                ->whereNull('worldid_mixed')
                                ->orWhere('worldid_mixed', '');
                        });
                })
                ->distinct()
                ->count('data_string');
        }

        return $numGroups;
    }

    private function analyzeTables(): void
    {
        $this->info('analyzing tables');
        $prefix = DB::connection()->getTablePrefix();
        $tableNames = [
            $prefix . (new Meeting)->getTable(),
            $prefix . (new MeetingData)->getTable(),
            $prefix . (new MeetingLongData)->getTable(),
            $prefix . (new ServiceBody)->getTable(),
            $prefix . (new Format)->getTable(),
            $prefix . (new Server)->getTable(),
            $prefix . (new ServerStatistics)->getTable(),
        ];
        foreach ($tableNames as $tableName) {
            $sql = DB::raw("ANALYZE TABLE $tableName;")->getValue(DB::connection()->getQueryGrammar());
            DB::statement($sql);
        }
    }

    private function httpGet(string $url): array
    {
        sleep(self::$requestDelaySeconds);

        $headers = ['User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:52.0) Gecko/20100101 Firefox/52.0 +aggregator'];
        $response = Http::withHeaders($headers)->retry(3, self::$retryDelaySeconds * 1000)->get($url);

        if (!$response->ok()) {
            throw new \Exception("Got bad status code {$response->status()} from $url");
        }

        $data = $response->json();
        if (!is_array($data)) {
            throw new \Exception("Response from $url is not json");
        }

        return $data;
    }
}
