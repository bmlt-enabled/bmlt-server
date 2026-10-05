<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('comdef_service_bodies', 'meeting_update_url')) {
            Schema::table('comdef_service_bodies', function (Blueprint $table) {
                $table->string('meeting_update_url', 255)->nullable()->after('uri_string');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('comdef_service_bodies', 'meeting_update_url')) {
            Schema::table('comdef_service_bodies', function (Blueprint $table) {
                $table->dropColumn('meeting_update_url');
            });
        }
    }
};
