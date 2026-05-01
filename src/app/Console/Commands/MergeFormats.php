<?php

namespace App\Console\Commands;

use App\Interfaces\FormatRepositoryInterface;
use App\Interfaces\MeetingRepositoryInterface;
use App\Models\FormatShared;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MergeFormats extends Command
{
    protected $signature = 'bmlt:MergeFormats {formatIds} {targetFormatId}';

    protected $description = 'Merge formats';

    public function handle(
        FormatRepositoryInterface $formatRepository,
        MeetingRepositoryInterface $meetingRepository
    ) {
        $formatIds = $this->argument('formatIds');
        $formatIds = array_map(fn ($id) => trim($id), explode(',', $formatIds));
        $formatIds = ensure_integer_array($formatIds);
        $formats = $formatRepository->search(formatsInclude: $formatIds, showAll: true);
        if (count($formatIds) != $formats->unique(fn ($f) => $f->shared_id_bigint)->count()) {
            $this->error("Some of the specified formatIds do not exist.");
            return 1;
        }

        $targetFormatId = $this->argument('targetFormatId');
        $targetFormatId = intval($targetFormatId);
        $targetFormats = $formatRepository->search(formatsInclude: [$targetFormatId], showAll: true);
        if ($targetFormats->isEmpty()) {
            $this->error("The target format {$targetFormatId} does not exist.");
            return 1;
        }

        $meetings = $meetingRepository->getSearchResults(formatsInclude: $formatIds, formatsComparisonOperator: 'OR');

        DB::transaction(function () use ($meetings, $formatIds, $targetFormatId) {
            foreach ($meetings as $meeting) {
                $oldIds = $meeting->getFormatSharedIds()->all();
                $newIds = collect($oldIds)
                    ->reject(fn ($id) => in_array($id, $formatIds))
                    ->concat([$targetFormatId])
                    ->unique()
                    ->sort()
                    ->values()
                    ->all();
                $this->info("$meeting->id_bigint: " . implode(',', $oldIds) . " -> " . implode(',', $newIds));
                $meeting->formats()->sync($newIds);
            }

            FormatShared::query()->whereIn('shared_id_bigint', $formatIds)->delete();
            foreach ($formatIds as $formatId) {
                $this->info("deleted format $formatId");
            }
        });
    }
}
