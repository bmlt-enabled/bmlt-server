<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $tables = [
        'comdef_meetings_main',
        'comdef_formats',
        'comdef_service_bodies',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $prefix = DB::getTablePrefix();

        Schema::table('root_server_statistics', function (Blueprint $table) {
            $table->dropForeign(['root_server_id']);
        });

        Schema::rename('root_servers', 'servers');
        Schema::rename('root_server_statistics', 'server_statistics');

        Schema::table('server_statistics', function (Blueprint $table) {
            $table->renameColumn('root_server_id', 'server_id');
            $table->foreign('server_id')
                ->references('id')
                ->on('servers')
                ->cascadeOnDelete();
        });

        foreach ($this->tables as $tableName) {
            $tables = [$tableName, $prefix . $tableName];
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    Schema::table($table, function (Blueprint $table) {
                        if (Schema::hasColumn($table->getTable(), 'root_server_id')) {
                            $table->renameColumn('root_server_id', 'server_id');
                        }
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = DB::getTablePrefix();

        Schema::table('server_statistics', function (Blueprint $table) {
            $table->dropForeign(['server_id']);
        });

        foreach ($this->tables as $tableName) {
            $tables = [$tableName, $prefix . $tableName];
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    Schema::table($table, function (Blueprint $table) {
                        if (Schema::hasColumn($table->getTable(), 'server_id')) {
                            $table->renameColumn('server_id', 'root_server_id');
                        }
                    });
                }
            }
        }

        Schema::rename('servers', 'root_servers');
        Schema::rename('server_statistics', 'root_server_statistics');

        Schema::table('root_server_statistics', function (Blueprint $table) {
            $table->renameColumn('server_id', 'root_server_id');
            $table->foreign('root_server_id')
                ->references('id')
                ->on('root_servers')
                ->cascadeOnDelete();
        });
    }
};
