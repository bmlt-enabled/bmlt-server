<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('settings')->insert([
            'name' => 'multiLingualEnabled',
            'type' => 'bool',
            'value' => json_encode(false),
        ]);
    }

    public function down()
    {
        DB::table('settings')->where('name', 'multiLingualEnabled')->delete();
    }
};
