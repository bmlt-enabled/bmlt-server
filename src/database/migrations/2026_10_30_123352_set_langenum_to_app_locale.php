<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('comdef_meetings_data')->update(['lang_enum' => config('app.locale')]);
        DB::table('comdef_meetings_longdata')->update(['lang_enum' => config('app.locale')]);
        Schema::table('comdef_users', function ($table) {
            $table->string('target_language', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
