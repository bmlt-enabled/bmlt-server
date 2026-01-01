<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditOrphanedMeetings extends Command
{
    protected $signature = 'bmlt:audit-orphaned-meetings 
                            {--delete : Delete orphaned meetings}
                            {--json= : Output results to JSON file}';

    protected $description = 'Audit meetings with invalid service body references';

    public function handle()
    {
        $this->info('BMLT Orphaned Meetings Audit');
        $this->line(str_repeat('=', 80));

        // Get all valid service body IDs
        $validServiceBodyIds = DB::table('comdef_service_bodies')
            ->pluck('id_bigint')
            ->flip();

        if ($validServiceBodyIds->isEmpty()) {
            $this->error('No service bodies found in database.');
            return 1;
        }

        $this->info(sprintf('Found %d valid service bodies', $validServiceBodyIds->count()));

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
        $deletedCount = 0;
        $chunkSize = 500;
        $processedCount = 0;
        $startId = $minId;

        $progressBar = $this->output->createProgressBar($maxId - $minId + 1);
        $progressBar->setFormat('verbose');

        // Process meetings in chunks using BETWEEN to avoid race conditions
        while ($startId <= $maxId) {
            $endId = min($startId + $chunkSize, $maxId);

            $chunkResults = DB::table('comdef_meetings_main')
                ->whereBetween('id_bigint', [$startId, $endId])
                ->select('id_bigint', 'service_body_bigint')
                ->get()
                ->filter(fn($meeting) => !$validServiceBodyIds->has($meeting->service_body_bigint))
                ->map(fn($meeting) => [
                    'meeting_id' => $meeting->id_bigint,
                    'service_body_id' => $meeting->service_body_bigint,
                ]);

            $processedCount += DB::table('comdef_meetings_main')
                ->whereBetween('id_bigint', [$startId, $endId])
                ->count();

            // Delete if --delete option is provided
            if ($this->option('delete') && $chunkResults->isNotEmpty()) {
                $meetingIds = $chunkResults->pluck('meeting_id');
                DB::table('comdef_meetings_main')
                    ->whereIn('id_bigint', $meetingIds)
                    ->delete();
                $deletedCount += $meetingIds->count();
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
        $this->line(sprintf('Orphaned meetings found: %d', $orphanedMeetings->count()));

        if ($this->option('delete')) {
            $this->newLine();
            $this->info(sprintf('✓ Deleted %d orphaned meetings', $deletedCount));
        }

        // Display detailed results
        if ($orphanedMeetings->isNotEmpty()) {
            $this->newLine();
            $this->line(str_repeat('=', 80));
            $this->warn('ORPHANED MEETINGS');
            $this->line(str_repeat('=', 80));

            $displayLimit = 20;

            foreach ($orphanedMeetings->take($displayLimit) as $meeting) {
                $this->line(sprintf(
                    'Meeting ID: <comment>%s</comment> → Invalid Service Body ID: <error>%s</error>',
                    $meeting['meeting_id'],
                    $meeting['service_body_id']
                ));
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
                    'orphaned_meetings_found' => $orphanedMeetings->count(),
                    'deleted' => $this->option('delete'),
                    'meetings_deleted' => $deletedCount,
                ],
                'orphaned_meetings' => $orphanedMeetings->all(),
            ];

            file_put_contents($jsonFile, json_encode($report, JSON_PRETTY_PRINT));
            $this->newLine();
            $this->info(sprintf('✓ Report saved to: %s', $jsonFile));
        }

        // Exit with appropriate code
        if ($orphanedMeetings->isNotEmpty() && !$this->option('delete')) {
            $this->newLine();
            $this->warn('Run with --delete option to remove orphaned meetings');
            return 1;
        }

        return 0;
    }
}
