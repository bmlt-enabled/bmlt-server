<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration fixes service bodies that have assigned users (editors_string)
     * referencing user IDs that no longer exist in the database.
     * This issue occurs in older servers that didn't properly clean up
     * service body assignments when users were deleted.
     */
    public function up(): void
    {
        if (!legacy_config('aggregator_mode_enabled')) {
            $validUserIds = DB::table('comdef_users')
                ->pluck('id_bigint')
                ->map(fn($id) => (string)$id)
                ->toArray();

            $serviceBodies = DB::table('comdef_service_bodies')
                ->whereNotNull('editors_string')
                ->where('editors_string', '!=', '')
                ->get(['id_bigint', 'editors_string']);

            foreach ($serviceBodies as $serviceBody) {
                $assignedUserIds = array_filter(
                    array_map('trim', explode(',', $serviceBody->editors_string)),
                    fn($id) => !empty($id)
                );

                $validAssignedUserIds = array_filter(
                    $assignedUserIds,
                    fn($id) => in_array($id, $validUserIds)
                );

                if (count($assignedUserIds) !== count($validAssignedUserIds)) {
                    $newEditorsString = implode(',', $validAssignedUserIds);

                    DB::table('comdef_service_bodies')
                        ->where('id_bigint', $serviceBody->id_bigint)
                        ->update(['editors_string' => $newEditorsString ?: null]);

                    Log::info('Removed non-existent assigned users from service body', [
                        'service_body_id' => $serviceBody->id_bigint,
                        'old_editors_string' => $serviceBody->editors_string,
                        'new_editors_string' => $newEditorsString,
                        'removed_count' => count($assignedUserIds) - count($validAssignedUserIds)
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
