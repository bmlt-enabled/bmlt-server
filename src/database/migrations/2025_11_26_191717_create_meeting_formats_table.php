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
           'select shared_id_bigint, worldid_mixed, format_type_enum from '.$prefix.'comdef_formats a '.
                'inner join ( select min(id) min_id from '.$prefix.'comdef_formats group by shared_id_bigint ) b '.
                'on a.id = b.min_id ;');
        DB::statement('ALTER TABLE '.$prefix.'comdef_formats_main ADD PRIMARY KEY (shared_id_bigint)');
        $next_auto = (DB::table('comdef_formats_main')->max('shared_id_bigint') ?? 0) + 1;
        DB::statement('ALTER TABLE '.$prefix.'comdef_formats_main AUTO_INCREMENT =  '.$next_auto.';');
        DB::statement('ALTER TABLE '.$prefix.'comdef_formats dROP COLUMN format_type_enum;');
        DB::statement('ALTER TABLE '.$prefix.'comdef_formats dROP COLUMN worldid_mixed;');
        DB::statement('ALTER TABLE '.$prefix.'comdef_formats ADD CONSTRAINT fk_format_main FOREIGN KEY (shared_id_bigint) REFERENCES '.$prefix.'comdef_formats_main(shared_id_bigint) ON DELETE CASCADE;');
        Schema::create('comdef_meeting_formats', function (Blueprint $table) {
            $table->bigInteger('meeting_id')->unsigned();
            $table->bigInteger('format_id')->unsigned();
            $table->foreign('meeting_id')->references('id_bigint')->on('comdef_meetings_main')->onDelete('cascade');
            $table->foreign('format_id')->references('shared_id_bigint')->on('comdef_formats_main')->onDelete('cascade');
            $table->primary(['meeting_id', 'format_id']);
            $table->index('format_id');
            $table->timestamps();
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
        DB::statement('ALTER TABLE '.$prefix.'comdef_meetings_main dROP COLUMN formats;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_formats');
    }
};
