<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Converts empty strings to NULL in email_contact field.
     * This fixes migration issues from 2.x servers where empty strings
     * were used instead of NULL, causing spurious change records like
     * "email was changed from '' to ''".
     */
    public function up(): void
    {
        DB::table('comdef_meetings_main')
            ->where('email_contact', '')
            ->update(['email_contact' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
