<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditFormatInventory extends Command
{
    protected $signature = 'bmlt:audit-format-inventory 
                            {--fix : Automatically fix orphaned format references}
                            {--json= : Output results to JSON file}';

    protected $description = 'Audit meetings for orphaned format references';

    public function handle()
    {
        $this->info('BMLT Format Inventory Audit');
        $this->line(str_repeat('=', 80));

        // Get all valid format IDs from comdef_formats table
        $validFormatIds = DB::table('comdef_formats')
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->flip();

        if ($validFormatIds->isEmpty()) {
            $this->error('No formats found in database.');
            return 1;
        }

        $this->info(sprintf('Found %d valid formats', $validFormatIds->count()));

        // Get min and max meeting IDs to determine range
        $minId = DB::table('comdef_meetings_main')->min('id_bigint');
        $maxId = DB::table('comdef_meetings_main')->max('id_bigint');

        if (is_null($minId) || is_null($maxId)) {
            $this->info('No meetings found in database.');
            return 0;
        }

        $totalMeetings = DB::table('comdef_meetings_main')->count();
        $this->info(sprintf('Scanning %d meetings (ID range: %d-%d)', $totalMeetings, $minId, $maxId));
        $this->newLine();

        $orphanedMeetings = collect();
        $updatedCount = 0;
        $totalOrphanedIds = 0;
        $chunkSize = 500;
        $processedCount = 0;
        $startId = $minId;

        $progressBar = $this->output->createProgressBar($maxId - $minId + 1);
        $progressBar->setFormat('verbose');

        // Process meetings in chunks using BETWEEN to avoid race conditions
        while ($startId <= $maxId) {
            $endId = min($startId + $chunkSize, $maxId);

            // Only select id_bigint and formats columns to minimize memory usage
            $chunkResults = DB::table('comdef_meetings_main')
                ->whereBetween('id_bigint', [$startId, $endId])
                ->whereNotNull('formats')
                ->whereNot('formats', '')
                ->select('id_bigint', 'formats')
                ->get()
                ->map(function ($meeting) use ($validFormatIds) {
                    $formatIds = collect(explode(',', $meeting->formats))
                        ->map(fn($id) => trim($id))
                        ->filter(fn($id) => $id !== '');

                    $validIds = $formatIds->filter(fn($id) => $validFormatIds->has($id));
                    $orphanedIds = $formatIds->diff($validIds);

                    if ($orphanedIds->isEmpty()) {
                        return null;
                    }

                    return [
                        'meeting_id' => $meeting->id_bigint,
                        'original_formats' => $meeting->formats,
                        'orphaned_ids' => $orphanedIds->values()->all(),
                        'valid_ids' => $validIds->values()->all(),
                        'cleaned_formats' => $validIds->implode(','),
                    ];
                })
                ->filter();

            $totalOrphanedIds += $chunkResults->sum(fn($row) => count($row['orphaned_ids']));
            $processedCount += DB::table('comdef_meetings_main')
                ->whereBetween('id_bigint', [$startId, $endId])
                ->whereNotNull('formats')
                ->whereNot('formats', '')
                ->count();

            // Fix if --fix option is provided
            if ($this->option('fix')) {
                $chunkResults->each(function ($row) use (&$updatedCount) {
                    DB::table('comdef_meetings_main')
                        ->where('id_bigint', $row['meeting_id'])
                        ->update(['formats' => $row['cleaned_formats']]);
                    $updatedCount++;
                });
            }

            $orphanedMeetings = $orphanedMeetings->concat($chunkResults);
            $progressBar->advance(min($chunkSize, $maxId - $startId + 1));
            $startId = $endId + 1;
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display summary
        $this->line(str_repeat('=', 80));
        $this->info('AUDIT SUMMARY');
        $this->line(str_repeat('=', 80));
        $this->line(sprintf('Total meetings processed: %d', $processedCount));
        $this->line(sprintf('Meetings with orphaned formats: %d', $orphanedMeetings->count()));
        $this->line(sprintf('Total orphaned format IDs: %d', $totalOrphanedIds));

        if ($this->option('fix')) {
            $this->newLine();
            $this->info(sprintf('✓ Fixed %d meetings', $updatedCount));
        }

        // Display detailed results
        if ($orphanedMeetings->isNotEmpty()) {
            $this->newLine();
            $this->line(str_repeat('=', 80));
            $this->warn('MEETINGS WITH ORPHANED FORMATS');
            $this->line(str_repeat('=', 80));

            $displayLimit = 20;
            $displayCount = min($orphanedMeetings->count(), $displayLimit);

            foreach ($orphanedMeetings->take($displayLimit) as $meeting) {
                $this->newLine();
                $this->line(sprintf('Meeting ID: <comment>%s</comment>', $meeting['meeting_id']));
                $this->line(sprintf('  Original formats: %s', $meeting['original_formats']));
                $this->line(sprintf('  Orphaned IDs: <error>%s</error>', implode(', ', $meeting['orphaned_ids'])));
                $this->line(sprintf('  Valid IDs: %s', implode(', ', $meeting['valid_ids'])));
                if (!$this->option('fix')) {
                    $this->line(sprintf('  Cleaned would be: <info>%s</info>', $meeting['cleaned_formats']));
                }
            }

            if ($orphanedMeetings->count() > $displayLimit) {
                $this->newLine();
                $this->line(sprintf('... and %d more (see JSON output for full results)', $orphanedMeetings->count() - $displayLimit));
            }
        }

        // Save to JSON file if requested
        $jsonFile = $this->option('json');
        if ($jsonFile) {
            $report = [
                'timestamp' => now()->toIso8601String(),
                'summary' => [
                    'total_meetings_processed' => $processedCount,
                    'meetings_with_orphaned_formats' => $orphanedMeetings->count(),
                    'total_orphaned_format_ids' => $totalOrphanedIds,
                    'fixed' => $this->option('fix'),
                    'meetings_updated' => $updatedCount,
                ],
                'orphaned_meetings' => $orphanedMeetings->all(),
            ];

            file_put_contents($jsonFile, json_encode($report, JSON_PRETTY_PRINT));
            $this->newLine();
            $this->info(sprintf('✓ Report saved to: %s', $jsonFile));
        }

        // Exit with appropriate code
        if ($orphanedMeetings->isNotEmpty() && !$this->option('fix')) {
            $this->newLine();
            $this->warn('Run with --fix option to automatically clean orphaned format references');
            return 1;
        }

        return 0;
    }
}
