<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Migrates settings from auto-config.inc.php to the database.
     * Uses defaults if the legacy config file doesn't exist or doesn't contain the setting.
     *
     * @return void
     */
    public function up()
    {
        Setting::migrateLegacyConfig();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::truncate();
    }
};
