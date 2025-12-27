<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The Danish language code should be 'da' (ISO 639-1) not 'dk' (country code).
     */
    public function up(): void
    {
        DB::table('comdef_formats')
            ->where('lang_enum', 'dk')
            ->update(['lang_enum' => 'da']);

        DB::table('comdef_service_bodies')
            ->where('lang_enum', 'dk')
            ->update(['lang_enum' => 'da']);

        DB::table('comdef_users')
            ->where('lang_enum', 'dk')
            ->update(['lang_enum' => 'da']);

        DB::table('comdef_meetings_data')
            ->where('lang_enum', 'dk')
            ->update(['lang_enum' => 'da']);

        DB::table('comdef_meetings_longdata')
            ->where('lang_enum', 'dk')
            ->update(['lang_enum' => 'da']);

        DB::table('comdef_meetings_main')
            ->where('lang_enum', 'dk')
            ->update(['lang_enum' => 'da']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('comdef_formats')
            ->where('lang_enum', 'da')
            ->update(['lang_enum' => 'dk']);

        DB::table('comdef_service_bodies')
            ->where('lang_enum', 'da')
            ->update(['lang_enum' => 'dk']);

        DB::table('comdef_users')
            ->where('lang_enum', 'da')
            ->update(['lang_enum' => 'dk']);

        DB::table('comdef_meetings_data')
            ->where('lang_enum', 'da')
            ->update(['lang_enum' => 'dk']);

        DB::table('comdef_meetings_longdata')
            ->where('lang_enum', 'da')
            ->update(['lang_enum' => 'dk']);

        DB::table('comdef_meetings_main')
            ->where('lang_enum', 'da')
            ->update(['lang_enum' => 'dk']);
    }
};
