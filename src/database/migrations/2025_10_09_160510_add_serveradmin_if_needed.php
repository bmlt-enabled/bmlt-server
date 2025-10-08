<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * New for release 4.0.0: we are no longer including an installer wizard.  Instead, the migrations will add the needed tables to the initial
     * empty database for a brand new server.  But we still need to create a serveradmin user, which is done here.
     */
    public function up(): void
    {
        $n = DB::table('comdef_users')->where('user_level_tinyint', 1)->count();
        if ($n == 0) {
            DB::table('comdef_users')->insert([
                'user_level_tinyint' => 1,
                'name_string' => 'Server Administrator',
                'description_string' => 'Main Server Administrator',
                'email_address_string' => '',
                'login_string' => 'serveradmin',
                'password_string' => Hash::make('change-this-password-first-thing')
            ]);
        };
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
