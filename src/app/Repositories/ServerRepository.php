<?php

namespace App\Repositories;

use App\Interfaces\ServerRepositoryInterface;
use App\Models\Meeting;
use App\Models\MeetingData;
use App\Models\MeetingLongData;
use App\Models\Server;
use App\Repositories\External\ExternalServer;
use Illuminate\Support\Collection;

class ServerRepository implements ServerRepositoryInterface
{
    public function search(bool $eagerStatistics = false): Collection
    {
        $servers = Server::query();
        if ($eagerStatistics) {
            $servers = $servers->with(['statistics' => function ($query) {
                $query->where('is_latest', true);
            }]);
        }

        return $servers->get();
    }

    public function create(array $values): Server
    {
        return Server::create($values);
    }

    public function update(int $id, array $values): bool
    {
        $server = Server::find($id);
        if (!is_null($server)) {
            Server::query()->where('id', $id)->update($values);
            return true;
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $server = Server::find($id);
        if (!is_null($server)) {
            $server->delete();
            return true;
        }
        return false;
    }

    public function import(Collection $externalObjects): void
    {
        $ignoreServerUrls = config('aggregator.ignore_servers');
        $externalObjects = $externalObjects->reject(fn (ExternalServer $ex) => in_array($ex->url, $ignoreServerUrls));

        $sourceIds = $externalObjects->map(fn (ExternalServer $ex) => $ex->id);
        Server::query()->whereNotIn('source_id', $sourceIds)->delete();

        // TODO test these
        MeetingData::query()
            ->whereNot('meetingid_bigint', 0)
            ->whereNotIn('meetingid_bigint', function ($query) {
                $query->select('id_bigint')->from((new Meeting)->getTable());
            })->delete();
        MeetingLongData::query()
            ->whereNot('meetingid_bigint', 0)
            ->whereNotIn('meetingid_bigint', function ($query) {
                $query->select('id_bigint')->from((new Meeting)->getTable());
            })->delete();

        foreach ($externalObjects as $externalServer) {
            $externalServer = $this->castExternalServer($externalServer);
            $dbServer = Server::query()->firstWhere('source_id', $externalServer->id);
            $values = ['source_id' => $externalServer->id, 'name' => $externalServer->name, 'url' => $externalServer->url];
            if (is_null($dbServer)) {
                $this->create($values);
            } else if (!$externalServer->isEqual($dbServer)) {
                $this->update($dbServer->id, $values);
            }
        }
    }

    private function castExternalServer($obj): ExternalServer
    {
        return $obj;
    }
}
