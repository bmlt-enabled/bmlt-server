<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * This migration fixes service bodies that have assigned users (editors_string)
     * referencing user IDs that no longer exist in the database. This issue occurs
     * in older servers that didn't properly clean up service body assignments when
     * users were deleted.
     */
    public function up(): void
    {
        if (legacy_config('aggregator_mode_enabled')) {
            return;
        }

        $allUserIds = DB::table('comdef_users')->pluck('id_bigint');
        $serviceBodies = DB::table('comdef_service_bodies')
            ->whereNotNull('editors_string')
            ->whereNot('editors_string', '')
            ->get(['id_bigint', 'editors_string']);

        foreach ($serviceBodies as $serviceBody) {
            $oldUserIds = collect(explode(',', trim($serviceBody->editors_string)))
                ->map(fn($userId) => trim($userId))
                ->filter(fn($userId) => !empty($userId) && is_numeric($userId))
                ->map(fn($userId) => intval($userId));

            $newUserIds = $oldUserIds->filter(fn($userId) => $allUserIds->contains($userId));
            if ($oldUserIds->count() == $newUserIds->count()) {
                continue;
            }

            DB::table('comdef_service_bodies')
                ->where('id_bigint', $serviceBody->id_bigint)
                ->update(['editors_string' => $newUserIds->isEmpty() ? null : $newUserIds->implode(',')]);

            Log::info('Removed non-existent assigned users from service body', [
                'service_body_id' => $serviceBody->id_bigint,
                'old_editors_string' => $serviceBody->editors_string,
                'new_editors_string' => $newUserIds->isEmpty() ? null : $newUserIds->implode(','),
                'removed_count' => $oldUserIds->count() - $newUserIds->count()
            ]);
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
