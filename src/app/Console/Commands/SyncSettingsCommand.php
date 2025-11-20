<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class SyncSettingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:sync 
                            {--force : Overwrite existing settings}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync settings from environment variables to database';

    /**
     * Map camelCase setting names to SCREAMING_SNAKE_CASE env var names.
     */
    private const ENV_VAR_MAP = [
        'googleApiKey' => 'GKEY',
        'changeDepthForMeetings' => 'CHANGE_DEPTH_FOR_MEETINGS',
        'defaultSortKey' => 'DEFAULT_SORT_KEY',
        'language' => 'LANGUAGE',
        'defaultDurationTime' => 'DEFAULT_DURATION_TIME',
        'regionBias' => 'REGION_BIAS',
        'distanceUnits' => 'DISTANCE_UNITS',
        'meetingStatesAndProvinces' => 'MEETING_STATES_AND_PROVINCES',
        'meetingCountiesAndSubProvinces' => 'MEETING_COUNTIES_AND_SUB_PROVINCES',
        'searchSpecMapCenterLongitude' => 'SEARCH_SPEC_MAP_CENTER_LONGITUDE',
        'searchSpecMapCenterLatitude' => 'SEARCH_SPEC_MAP_CENTER_LATITUDE',
        'searchSpecMapCenterZoom' => 'SEARCH_SPEC_MAP_CENTER_ZOOM',
        'numberOfMeetingsForAuto' => 'NUMBER_OF_MEETINGS_FOR_AUTO',
        'autoGeocodingEnabled' => 'AUTO_GEOCODING_ENABLED',
        'countyAutoGeocodingEnabled' => 'COUNTY_AUTO_GEOCODING_ENABLED',
        'zipAutoGeocodingEnabled' => 'ZIP_AUTO_GEOCODING_ENABLED',
        'defaultClosedStatus' => 'DEFAULT_CLOSED_STATUS',
        'enableLanguageSelector' => 'ENABLE_LANGUAGE_SELECTOR',
        'aggregatorModeEnabled' => 'AGGREGATOR_MODE_ENABLED',
        'aggregatorMaxGeoWidthKm' => 'AGGREGATOR_MAX_GEO_WIDTH_KM',
        'includeServiceBodyEmailInSemantic' => 'INCLUDE_SERVICE_BODY_EMAIL_IN_SEMANTIC',
        'bmltTitle' => 'BMLT_TITLE',
        'bmltNotice' => 'BMLT_NOTICE',
        'formatLangNames' => 'FORMAT_LANG_NAMES',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $force = $this->option('force');
        $settingsToSync = array_keys(Setting::SETTING_TYPES);
        $synced = 0;
        $skipped = 0;

        foreach ($settingsToSync as $key) {
            $envVar = self::ENV_VAR_MAP[$key];
            $envValue = env($envVar);
            
            // If env var not set, use default
            if (is_null($envValue)) {
                $value = Setting::SETTING_DEFAULTS[$key];
            } else {
                $value = $this->parseEnvValue($envValue, Setting::SETTING_TYPES[$key]);
            }

            $type = Setting::SETTING_TYPES[$key];

            // Check if setting already exists
            $existing = Setting::where('name', $key)->first();
            
            if ($existing && !$force) {
                $this->info("Skipped {$key} (already exists, use --force to overwrite)");
                $skipped++;
                continue;
            }

            Setting::updateOrCreate(
                ['name' => $key],
                ['type' => $type, 'value' => $value]
            );

            $displayValue = is_array($value) ? json_encode($value) : (is_bool($value) ? ($value ? 'true' : 'false') : $value);
            $action = $existing ? 'Updated' : 'Created';
            $this->info("{$action} {$key} = {$displayValue}");
            $synced++;
        }

        $this->newLine();
        $this->info("Synced {$synced} setting(s), skipped {$skipped}");
        
        return Command::SUCCESS;
    }

    /**
     * Parse environment variable value to appropriate type.
     */
    private function parseEnvValue(string $value, string $type)
    {
        return match ($type) {
            Setting::TYPE_INT => (int)$value,
            Setting::TYPE_FLOAT => (float)$value,
            Setting::TYPE_BOOL => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            Setting::TYPE_ARRAY => $this->parseArrayValue($value),
            default => $value,
        };
    }

    /**
     * Parse array value from comma-separated string or JSON.
     */
    private function parseArrayValue(string $value): array
    {
        // Try JSON first
        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Fall back to comma-separated
        return array_map('trim', explode(',', $value));
    }
}
