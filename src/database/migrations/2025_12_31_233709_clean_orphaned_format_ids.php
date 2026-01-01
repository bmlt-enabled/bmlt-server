<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * This migration cleans up orphaned format IDs from the meetings table.
     *
     * The `formats` column in comdef_meetings_main stores a comma-separated list of format IDs.
     * Over time, if formats are deleted from comdef_formats, meetings may still reference
     * those deleted format IDs. This migration identifies and removes those orphaned references.
     *
     * The API already filters out orphaned format IDs when returning meeting data (via the
     * calculateFormatsFields method), but the raw database may still contain them, which can
     * cause data integrity issues. The new UI protects against this from happening but legacy did not.
     */
    public function up(): void
    {
        $validFormatIds = DB::table('comdef_formats')
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();

        $minId = DB::table('comdef_meetings_main')->min('id_bigint');
        $maxId = DB::table('comdef_meetings_main')->max('id_bigint');

        if (is_null($minId) || is_null($maxId)) {
            return;
        }

        $chunkSize = 500;

        for ($startId = $minId; $startId <= $maxId; $startId += $chunkSize) {
            $endId = $startId + $chunkSize - 1;
            $meetings = DB::table('comdef_meetings_main')
                ->whereBetween('id_bigint', [$startId, $endId])
                ->whereNotNull('formats')
                ->where('formats', '!=', '')
                ->select('id_bigint', 'formats')
                ->get();

            foreach ($meetings as $meeting) {
                $formatIds = array_map('trim', explode(',', $meeting->formats));
                $formatIds = array_filter($formatIds, fn($id) => $id !== '');
                $orphanedIds = array_diff($formatIds, $validFormatIds);

                if (!empty($orphanedIds)) {
                    $validIds = array_intersect($formatIds, $validFormatIds);
                    $cleanedFormats = implode(',', $validIds);

                    DB::table('comdef_meetings_main')
                        ->where('id_bigint', $meeting->id_bigint)
                        ->update(['formats' => $cleanedFormats]);
                }
            }
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
