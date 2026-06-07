<?php

namespace App\Console\Commands;

use App\Models\MeetingData;
use App\Models\MeetingLongData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RebuildSearchIndex extends Command
{
    protected $signature = 'bmlt:RebuildSearchIndex';

    protected $description = 'Rebuild meeting search full-text indexes to repair drift that hides meetings.';

    public function handle(): int
    {
        $prefix = DB::connection()->getTablePrefix();
        $targets = [
            [(new MeetingData())->getTable(), 'data_string'],
            [(new MeetingLongData())->getTable(), 'data_blob'],
        ];

        foreach ($targets as [$table, $column]) {
            $fullTable = $prefix . $table;
            $canonical = $fullTable . '_' . $column . '_fulltext';

            $existing = DB::select(
                "SELECT DISTINCT INDEX_NAME FROM information_schema.STATISTICS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_TYPE = 'FULLTEXT' AND COLUMN_NAME = ?",
                [$fullTable, $column]
            );

            // Drop every existing full-text index on the column and re-add a single canonical one,
            // all in one ALTER so the table is never left without a search index if the rebuild fails.
            $this->info("Rebuilding full-text index on {$fullTable}({$column})...");
            $drops = array_map(fn ($row) => "DROP INDEX `{$row->INDEX_NAME}`", $existing);
            $drops = $drops ? implode(', ', $drops) . ', ' : '';
            DB::statement("ALTER TABLE `{$fullTable}` {$drops}ADD FULLTEXT `{$canonical}` (`{$column}`)");
        }

        $this->info('Search indexes rebuilt.');
        return 0;
    }
}
