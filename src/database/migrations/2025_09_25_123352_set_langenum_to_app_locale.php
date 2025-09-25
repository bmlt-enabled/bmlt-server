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
        DB::table('comdef_meetings_data')->update(['lang_enum' => config('app.locale')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
