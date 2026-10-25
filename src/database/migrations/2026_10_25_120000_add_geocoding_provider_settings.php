<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        // Default the provider to preserve current behavior: installs with a Google
        // key keep using Google, everyone else keeps falling back to Nominatim.
        $googleApiKey = DB::table('settings')->where('name', 'googleApiKey')->value('value');
        $googleApiKey = is_null($googleApiKey) ? '' : json_decode($googleApiKey, true);
        $defaultProvider = !empty($googleApiKey) ? 'google' : 'nominatim';

        $settings = [
            'geocodingProvider' => $defaultProvider,
            'geoApiKey' => '',
        ];

        foreach ($settings as $name => $value) {
            if (!DB::table('settings')->where('name', $name)->exists()) {
                DB::table('settings')->insert([
                    'name' => $name,
                    'type' => Setting::TYPE_STRING,
                    'value' => json_encode($value),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('name', ['geocodingProvider', 'geoApiKey'])->delete();
    }
};
