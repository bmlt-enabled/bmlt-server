<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Re-run of the orphaned format ID cleanup migration.
     *
     * The original migration (2025_12_31_233709_clean_orphaned_format_ids.php) shipped
     * in 4.1.0 with a bug: it compared meeting format IDs against comdef_formats.id
     * instead of comdef_formats.shared_id_bigint. On servers where those two columns
     * had drifted, the migration silently wiped valid format assignments from meetings.
     *
     * The original migration was patched in 4.2.1, but Laravel only runs each migration
     * file once per database. Servers whose wiped data was restored out-of-band need
     * the corrected cleanup to run against their restored data; this duplicate provides
     * that re-run. For servers that were unaffected (or never ran the buggy version),
     * this is a no-op because there are no orphaned format IDs to remove.
     */
    public function up(): void
    {
        $validFormatIds = DB::table('comdef_formats')
            ->pluck('shared_id_bigint')
            ->map(fn($id) => (string)$id)
            ->flip();

        $minId = DB::table('comdef_meetings_main')->min('id_bigint');
        $maxId = DB::table('comdef_meetings_main')->max('id_bigint');

        if (is_null($minId) || is_null($maxId)) {
            return;
        }

        $chunkSize = 500;
        $startId = $minId;

        while ($startId <= $maxId) {
            $endId = min($startId + $chunkSize, $maxId);

            $meetings = DB::table('comdef_meetings_main')
                ->whereBetween('id_bigint', [$startId, $endId])
                ->whereNotNull('formats')
                ->whereNot('formats', '')
                ->select('id_bigint', 'formats')
                ->get();

            foreach ($meetings as $meeting) {
                $formatIds = collect(explode(',', $meeting->formats))
                    ->map(fn($id) => trim($id))
                    ->filter(fn($id) => $id !== '');

                $validIds = $formatIds->filter(fn($id) => $validFormatIds->has($id));
                $cleanedFormats = $validIds->implode(',');

                if ($cleanedFormats !== $meeting->formats) {
                    DB::table('comdef_meetings_main')
                        ->where('id_bigint', $meeting->id_bigint)
                        ->update(['formats' => $cleanedFormats]);
                }
            }

            $startId = $endId + 1;
        }
    }

    /**
     * Reverse the migrations.
     *
     * Note: This migration cannot be reliably reversed because we don't know
     * which format IDs were removed. The down() method is intentionally left empty.
     */
    public function down(): void
    {
        // Cannot reverse this migration - we don't know which format IDs were orphaned
    }
};
