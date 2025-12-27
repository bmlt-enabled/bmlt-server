<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The Danish language code should be 'da' (ISO 639-1) not 'dk' (country code).
     */
    public function up(): void
    {
        if (Schema::hasTable('comdef_formats') && Schema::hasColumn('comdef_formats', 'lang_enum')) {
            DB::table('comdef_formats')
                ->where('lang_enum', 'dk')
                ->update(['lang_enum' => 'da']);
        }

        if (Schema::hasTable('comdef_meetings_data') && Schema::hasColumn('comdef_meetings_data', 'lang_enum')) {
            DB::table('comdef_meetings_data')
                ->where('lang_enum', 'dk')
                ->update(['lang_enum' => 'da']);
        }

        if (Schema::hasTable('comdef_meetings_longdata') && Schema::hasColumn('comdef_meetings_longdata', 'lang_enum')) {
            DB::table('comdef_meetings_longdata')
                ->where('lang_enum', 'dk')
                ->update(['lang_enum' => 'da']);
        }

        if (Schema::hasTable('comdef_meetings_main') && Schema::hasColumn('comdef_meetings_main', 'lang_enum')) {
            DB::table('comdef_meetings_main')
                ->where('lang_enum', 'dk')
                ->update(['lang_enum' => 'da']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('comdef_formats') && Schema::hasColumn('comdef_formats', 'lang_enum')) {
            DB::table('comdef_formats')
                ->where('lang_enum', 'da')
                ->update(['lang_enum' => 'dk']);
        }

        if (Schema::hasTable('comdef_meetings_data') && Schema::hasColumn('comdef_meetings_data', 'lang_enum')) {
            DB::table('comdef_meetings_data')
                ->where('lang_enum', 'da')
                ->update(['lang_enum' => 'dk']);
        }

        if (Schema::hasTable('comdef_meetings_longdata') && Schema::hasColumn('comdef_meetings_longdata', 'lang_enum')) {
            DB::table('comdef_meetings_longdata')
                ->where('lang_enum', 'da')
                ->update(['lang_enum' => 'dk']);
        }

        if (Schema::hasTable('comdef_meetings_main') && Schema::hasColumn('comdef_meetings_main', 'lang_enum')) {
            DB::table('comdef_meetings_main')
                ->where('lang_enum', 'da')
                ->update(['lang_enum' => 'dk']);
        }
    }
};
