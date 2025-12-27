<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('comdef_users')
            ->whereNotNull('name_string')
            ->update(['name_string' => DB::raw('TRIM(name_string)')]);

        DB::table('comdef_users')
            ->whereNotNull('description_string')
            ->update(['description_string' => DB::raw('TRIM(description_string)')]);

        DB::table('comdef_users')
            ->whereNotNull('login_string')
            ->update(['login_string' => DB::raw('TRIM(login_string)')]);

        DB::table('comdef_users')
            ->whereNotNull('email_address_string')
            ->update(['email_address_string' => DB::raw('TRIM(email_address_string)')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
