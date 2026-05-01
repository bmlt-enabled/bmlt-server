<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Replace the comma-separated comdef_meetings_main.formats column with a
     * proper m:n pivot table, and at the same time normalize comdef_formats by
     * extracting the columns that are conceptually shared across language
     * translations (root_server_id, source_id, worldid_mixed, icon_blob,
     * format_type_enum) into a new comdef_format_shared table keyed by
     * shared_id_bigint. Both the pivot and the translation table get real
     * foreign keys to comdef_format_shared with cascade on delete.
     *
     * Each step is idempotent so a partially-applied run can be retried.
     * Foreign keys are looked up by the column they're attached to (rather
     * than by Laravel's auto-generated name), since legacy installs may have
     * named the constraints differently.
     */
    public function up(): void
    {
        $prefix = DB::connection()->getTablePrefix();

        if (!Schema::hasTable('comdef_format_shared')) {
            Schema::create('comdef_format_shared', function (Blueprint $table) {
                $table->unsignedBigInteger('shared_id_bigint');
                $table->foreignId('root_server_id')->nullable()->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('source_id')->nullable();
                $table->string('worldid_mixed', 255)->nullable();
                $table->binary('icon_blob')->nullable();
                $table->string('format_type_enum', 7)->default('FC1')->nullable();
                $table->primary('shared_id_bigint');
                $table->index(['root_server_id', 'source_id'], 'root_server_id_source_id');
                $table->index('worldid_mixed', 'worldid_mixed');
                $table->index('format_type_enum', 'format_type_enum');
            });

            // Backfill one shared row per distinct shared_id_bigint, picking the
            // row with the lowest id as the representative (the shared columns
            // are identical across the per-language translations of the same
            // format).
            DB::statement("
                INSERT INTO {$prefix}comdef_format_shared
                    (shared_id_bigint, root_server_id, source_id, worldid_mixed, icon_blob, format_type_enum)
                SELECT f.shared_id_bigint, f.root_server_id, f.source_id, f.worldid_mixed, f.icon_blob, f.format_type_enum
                FROM {$prefix}comdef_formats f
                INNER JOIN (
                    SELECT MIN(id) AS min_id FROM {$prefix}comdef_formats GROUP BY shared_id_bigint
                ) m ON f.id = m.min_id
            ");
        }

        $this->dropForeignKeyOnColumn('comdef_formats', 'root_server_id');

        foreach (['root_server_id_source_id', 'worldid_mixed', 'format_type_enum'] as $indexName) {
            if ($this->indexExists('comdef_formats', $indexName)) {
                Schema::table('comdef_formats', fn (Blueprint $t) => $t->dropIndex($indexName));
            }
        }

        $columnsToDrop = array_filter(
            ['root_server_id', 'source_id', 'worldid_mixed', 'icon_blob', 'format_type_enum'],
            fn ($c) => Schema::hasColumn('comdef_formats', $c),
        );
        if (!empty($columnsToDrop)) {
            Schema::table('comdef_formats', fn (Blueprint $t) => $t->dropColumn(array_values($columnsToDrop)));
        }

        if (!$this->foreignKeyExists('comdef_formats', 'comdef_formats_shared_fk')) {
            Schema::table('comdef_formats', function (Blueprint $table) {
                $table->foreign('shared_id_bigint', 'comdef_formats_shared_fk')
                    ->references('shared_id_bigint')
                    ->on('comdef_format_shared')
                    ->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('comdef_meetings_formats')) {
            Schema::create('comdef_meetings_formats', function (Blueprint $table) {
                $table->unsignedBigInteger('meeting_id_bigint');
                $table->unsignedBigInteger('format_shared_id_bigint');
                $table->primary(['meeting_id_bigint', 'format_shared_id_bigint']);
                $table->foreign('meeting_id_bigint')
                    ->references('id_bigint')
                    ->on('comdef_meetings_main')
                    ->cascadeOnDelete();
                $table->foreign('format_shared_id_bigint', 'comdef_meetings_formats_shared_fk')
                    ->references('shared_id_bigint')
                    ->on('comdef_format_shared')
                    ->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('comdef_meetings_main', 'formats')) {
            $validFormatIds = DB::table('comdef_format_shared')
                ->pluck('shared_id_bigint')
                ->flip();

            $minId = DB::table('comdef_meetings_main')->min('id_bigint');
            $maxId = DB::table('comdef_meetings_main')->max('id_bigint');

            if (!is_null($minId) && !is_null($maxId)) {
                $chunkSize = 500;
                $startId = $minId;
                while ($startId <= $maxId) {
                    $endId = min($startId + $chunkSize - 1, $maxId);

                    $meetings = DB::table('comdef_meetings_main')
                        ->whereBetween('id_bigint', [$startId, $endId])
                        ->whereNotNull('formats')
                        ->whereNot('formats', '')
                        ->select('id_bigint', 'formats')
                        ->get();

                    $rows = [];
                    foreach ($meetings as $meeting) {
                        $seen = [];
                        foreach (explode(',', $meeting->formats) as $rawId) {
                            $id = trim($rawId);
                            if ($id === '' || !ctype_digit($id)) {
                                continue;
                            }
                            $id = (int)$id;
                            if (isset($seen[$id]) || !$validFormatIds->has($id)) {
                                continue;
                            }
                            $seen[$id] = true;
                            $rows[] = [
                                'meeting_id_bigint' => $meeting->id_bigint,
                                'format_shared_id_bigint' => $id,
                            ];
                        }
                    }

                    if ($rows) {
                        DB::table('comdef_meetings_formats')->insertOrIgnore($rows);
                    }

                    $startId = $endId + 1;
                }
            }

            Schema::table('comdef_meetings_main', function (Blueprint $table) {
                if ($this->indexExists('comdef_meetings_main', 'formats')) {
                    $table->dropIndex('formats');
                }
                $table->dropColumn('formats');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('comdef_meetings_main', 'formats')) {
            Schema::table('comdef_meetings_main', function (Blueprint $table) {
                $table->string('formats', 255)->nullable()->after('time_zone');
                $table->index('formats', 'formats');
            });
        }

        if (Schema::hasTable('comdef_meetings_formats')) {
            $byMeeting = [];
            DB::table('comdef_meetings_formats')
                ->orderBy('meeting_id_bigint')
                ->orderBy('format_shared_id_bigint')
                ->get()
                ->each(function ($row) use (&$byMeeting) {
                    $byMeeting[$row->meeting_id_bigint][] = $row->format_shared_id_bigint;
                });

            foreach ($byMeeting as $meetingId => $ids) {
                DB::table('comdef_meetings_main')
                    ->where('id_bigint', $meetingId)
                    ->update(['formats' => implode(',', $ids)]);
            }

            Schema::drop('comdef_meetings_formats');
        }

        if ($this->foreignKeyExists('comdef_formats', 'comdef_formats_shared_fk')) {
            Schema::table('comdef_formats', fn (Blueprint $t) => $t->dropForeign('comdef_formats_shared_fk'));
        }

        $columnsToAdd = [
            'root_server_id' => fn (Blueprint $t) => $t->foreignId('root_server_id')->nullable()->after('shared_id_bigint')->constrained()->cascadeOnDelete(),
            'source_id' => fn (Blueprint $t) => $t->unsignedBigInteger('source_id')->nullable()->after('root_server_id'),
            'worldid_mixed' => fn (Blueprint $t) => $t->string('worldid_mixed', 255)->nullable()->after('key_string'),
            'icon_blob' => fn (Blueprint $t) => $t->binary('icon_blob')->nullable()->after('worldid_mixed'),
            'format_type_enum' => fn (Blueprint $t) => $t->string('format_type_enum', 7)->default('FC1')->nullable()->after('description_string'),
        ];

        Schema::table('comdef_formats', function (Blueprint $table) use ($columnsToAdd) {
            foreach ($columnsToAdd as $column => $add) {
                if (!Schema::hasColumn('comdef_formats', $column)) {
                    $add($table);
                }
            }
            if (!$this->indexExists('comdef_formats', 'root_server_id_source_id')) {
                $table->index(['root_server_id', 'source_id'], 'root_server_id_source_id');
            }
            if (!$this->indexExists('comdef_formats', 'worldid_mixed')) {
                $table->index('worldid_mixed', 'worldid_mixed');
            }
            if (!$this->indexExists('comdef_formats', 'format_type_enum')) {
                $table->index('format_type_enum', 'format_type_enum');
            }
        });

        if (Schema::hasTable('comdef_format_shared')) {
            $prefix = DB::connection()->getTablePrefix();
            DB::statement("
                UPDATE {$prefix}comdef_formats f
                INNER JOIN {$prefix}comdef_format_shared s ON f.shared_id_bigint = s.shared_id_bigint
                SET f.root_server_id = s.root_server_id,
                    f.source_id = s.source_id,
                    f.worldid_mixed = s.worldid_mixed,
                    f.icon_blob = s.icon_blob,
                    f.format_type_enum = s.format_type_enum
            ");

            Schema::drop('comdef_format_shared');
        }
    }

    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $prefix = DB::connection()->getTablePrefix();
        $row = DB::selectOne(
            "
            SELECT 1
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND CONSTRAINT_TYPE = 'FOREIGN KEY'
              AND CONSTRAINT_NAME = ?
            LIMIT 1
            ",
            [$prefix . $table, $constraintName],
        );
        return !is_null($row);
    }

    private function dropForeignKeyOnColumn(string $table, string $column): void
    {
        $prefix = DB::connection()->getTablePrefix();
        $row = DB::selectOne(
            "
            SELECT CONSTRAINT_NAME AS name
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
              AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
            ",
            [$prefix . $table, $column],
        );
        if ($row) {
            Schema::table($table, fn (Blueprint $t) => $t->dropForeign($row->name));
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $prefix = DB::connection()->getTablePrefix();
        $row = DB::selectOne(
            "
            SELECT 1
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND INDEX_NAME = ?
            LIMIT 1
            ",
            [$prefix . $table, $indexName],
        );
        return !is_null($row);
    }
};
