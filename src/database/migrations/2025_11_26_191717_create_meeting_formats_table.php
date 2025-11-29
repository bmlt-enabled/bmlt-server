<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $prefix = DB::connection()->getTablePrefix();
        DB::statement('create table '.$prefix.'comdef_formats_main '.
           'select shared_id_bigint, root_server_id, source_id, worldid_mixed, format_type_enum from '.$prefix.'comdef_formats a '.
                'inner join ( select min(id) min_id from '.$prefix.'comdef_formats group by shared_id_bigint ) b '.
                'on a.id = b.min_id ;');
        DB::statement('ALTER TABLE '.$prefix.'comdef_formats_main ADD PRIMARY KEY (shared_id_bigint);');
        DB::statement('ALTER TABLE '.$prefix.'comdef_formats_main MODIFY shared_id_bigint bigint(20) unsigned NOT NULL AUTO_INCREMENT;');
        $next_auto = (DB::table('comdef_formats_main')->max('shared_id_bigint') ?? 0) + 1;
        DB::statement('ALTER TABLE '.$prefix.'comdef_formats_main AUTO_INCREMENT =  '.$next_auto.';');
        DB::statement('ALTER TABLE '.$prefix.'comdef_formats DROP COLUMN format_type_enum;');
        DB::statement('ALTER TABLE '.$prefix.'comdef_formats DROP COLUMN worldid_mixed;');
        Schema::rename('comdef_formats', 'comdef_formats_translations');
        DB::statement('ALTER TABLE '.$prefix.'comdef_formats_translations ADD CONSTRAINT fk_format_main FOREIGN KEY (shared_id_bigint) REFERENCES '.$prefix.'comdef_formats_main(shared_id_bigint) ON DELETE CASCADE;');
        Schema::create('comdef_meeting_formats', function (Blueprint $table) {
            $table->bigInteger('meeting_id')->unsigned();
            $table->bigInteger('format_id')->unsigned();
            $table->foreign('meeting_id')->references('id_bigint')->on('comdef_meetings_main')->onDelete('cascade');
            $table->foreign('format_id')->references('shared_id_bigint')->on('comdef_formats_main')->onDelete('cascade');
            $table->primary(['meeting_id', 'format_id']);
            $table->index('format_id');
        });
        $meetings = DB::table('comdef_meetings_main')->select('id_bigint', 'formats')->get();
        $meetings->each(function ($meeting) {
            if (empty($meeting->formats)) {
                return;
            }
            $formatIds = array_unique(explode(',', $meeting->formats));
            foreach ($formatIds as $formatId) {
                DB::table('comdef_meeting_formats')->insert([
                    'meeting_id' => $meeting->id_bigint,
                    'format_id' => (int)$formatId,
                ]);
            }
        });
        DB::statement('ALTER TABLE '.$prefix.'comdef_meetings_main DROP COLUMN formats;');

        DB::statement(
            'CREATE OR REPLACE VIEW '.$prefix.'comdef_formats AS '.
                'SELECT main.shared_id_bigint, main.root_server_id, main.source_id, worldid_mixed, id, key_string, icon_blob, lang_enum, name_string, description_string '.
                    'FROM '.$prefix.'comdef_formats_main main, '.$prefix.'comdef_formats_translations ft '.
                    'WHERE main.shared_id_bigint = ft.shared_id_bigint;'
        );

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_formats');
    }
};
