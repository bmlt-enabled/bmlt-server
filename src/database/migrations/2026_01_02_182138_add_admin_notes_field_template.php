<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // visibility=1 means admin-only (not visible to public)
        DB::table('comdef_meetings_data')->insert([
            'meetingid_bigint' => 0,
            'key' => 'admin_notes',
            'field_prompt' => 'Admin Notes',
            'lang_enum' => 'en',
            'visibility' => 1,
            'data_string' => 'Admin Notes',
            'data_bigint' => null,
            'data_double' => null,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('comdef_meetings_data')
            ->where('meetingid_bigint', 0)
            ->where('key', 'adminNotes')
            ->delete();
    }
};
