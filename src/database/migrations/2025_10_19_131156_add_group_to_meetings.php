<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('comdef_meetings_main', function (Blueprint $table) {
            $table->boolean('is_group')->default(0);
            $table->integer('group_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comdef_meetings_main', function (Blueprint $table) {
            $table->dropColumn('is_group');
            $table->dropColumn('group_id');
        });
    }
};
