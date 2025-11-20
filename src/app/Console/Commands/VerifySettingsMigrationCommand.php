<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class VerifySettingsMigrationCommand extends Command
{
    protected $signature = 'settings:verify';
    protected $description = 'Verify settings migration from auto-config.inc.php';

    public function handle()
    {
        $this->info('Verifying settings migration...');
        $this->newLine();

        $legacyConfigFile = base_path() . '/../auto-config.inc.php';
        
        if (!file_exists($legacyConfigFile)) {
            $this->warn('No auto-config.inc.php file found. This appears to be a fresh installation.');
            $this->verifyDefaults();
            return Command::SUCCESS;
        }

        // Load legacy config
        defined('BMLT_EXEC') or define('BMLT_EXEC', 1);
        require($legacyConfigFile);

        $this->info('Comparing legacy config with database settings:');
        $this->newLine();

        $allMatch = true;
        $legacyMap = [
            'googleApiKey' => $gkey ?? null,
            'changeDepthForMeetings' => $change_depth_for_meetings ?? null,
            'defaultSortKey' => $default_sort_key ?? null,
            'language' => $comdef_global_language ?? null,
            'defaultDurationTime' => $default_duration_time ?? null,
            'regionBias' => $region_bias ?? null,
            'distanceUnits' => $comdef_distance_units ?? null,
            'meetingStatesAndProvinces' => $meeting_states_and_provinces ?? null,
            'meetingCountiesAndSubProvinces' => $meeting_counties_and_sub_provinces ?? null,
            'searchSpecMapCenterLongitude' => $search_spec_map_center['longitude'] ?? null,
            'searchSpecMapCenterLatitude' => $search_spec_map_center['latitude'] ?? null,
            'searchSpecMapCenterZoom' => $search_spec_map_center['zoom'] ?? null,
            'numberOfMeetingsForAuto' => $number_of_meetings_for_auto ?? null,
            'autoGeocodingEnabled' => $auto_geocoding_enabled ?? null,
            'countyAutoGeocodingEnabled' => $county_auto_geocoding_enabled ?? null,
            'zipAutoGeocodingEnabled' => $zip_auto_geocoding_enabled ?? null,
            'defaultClosedStatus' => $g_defaultClosedStatus ?? null,
            'enableLanguageSelector' => $g_enable_language_selector ?? null,
            'aggregatorModeEnabled' => $aggregator_mode_enabled ?? null,
            'aggregatorMaxGeoWidthKm' => $aggregator_max_geo_width_km ?? null,
            'includeServiceBodyEmailInSemantic' => $g_include_service_body_email_in_semantic ?? null,
            'bmltTitle' => $bmlt_title ?? null,
            'bmltNotice' => $bmlt_notice ?? null,
            'formatLangNames' => $format_lang_names ?? null,
        ];

        foreach ($legacyMap as $key => $legacyValue) {
            $dbSetting = Setting::where('name', $key)->first();
            
            if (!$dbSetting) {
                $this->error("✗ {$key}: Missing from database");
                $allMatch = false;
                continue;
            }

            $dbValue = $dbSetting->value;
            $legacyValueToCompare = $legacyValue ?? Setting::SETTING_DEFAULTS[$key];

            if ($this->valuesMatch($dbValue, $legacyValueToCompare)) {
                $displayValue = $this->formatValueForDisplay($dbValue);
                $this->info("✓ {$key}: {$displayValue}");
            } else {
                $this->error("✗ {$key}: Mismatch!");
                $this->line("  Legacy:   " . $this->formatValueForDisplay($legacyValueToCompare));
                $this->line("  Database: " . $this->formatValueForDisplay($dbValue));
                $allMatch = false;
            }
        }

        $this->newLine();
        
        if ($allMatch) {
            $this->info('✓ All settings migrated successfully!');
            return Command::SUCCESS;
        } else {
            $this->error('✗ Some settings did not migrate correctly. Please review the output above.');
            return Command::FAILURE;
        }
    }

    private function verifyDefaults(): void
    {
        $this->info('Verifying all settings exist with default values:');
        $this->newLine();

        $allExist = true;
        foreach (Setting::SETTING_DEFAULTS as $key => $defaultValue) {
            $setting = Setting::where('name', $key)->first();
            
            if (!$setting) {
                $this->error("✗ {$key}: Missing from database");
                $allExist = false;
                continue;
            }

            if ($this->valuesMatch($setting->value, $defaultValue)) {
                $displayValue = $this->formatValueForDisplay($setting->value);
                $this->info("✓ {$key}: {$displayValue}");
            } else {
                $this->error("✗ {$key}: Value doesn't match default");
                $allExist = false;
            }
        }

        $this->newLine();
        
        if ($allExist) {
            $this->info('✓ All settings initialized with defaults!');
        } else {
            $this->error('✗ Some settings are missing or incorrect.');
        }
    }

    private function valuesMatch($value1, $value2): bool
    {
        // Handle null comparison
        if ($value1 === null && $value2 === null) {
            return true;
        }

        // Handle array comparison
        if (is_array($value1) && is_array($value2)) {
            return $value1 === $value2;
        }

        // Handle boolean comparison
        if (is_bool($value1) && is_bool($value2)) {
            return $value1 === $value2;
        }

        // Handle numeric comparison (float/int)
        if (is_numeric($value1) && is_numeric($value2)) {
            return abs($value1 - $value2) < 0.0001;
        }

        // String comparison
        return $value1 === $value2;
    }

    private function formatValueForDisplay($value): string
    {
        if (is_null($value)) {
            return 'null';
        }
        
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        
        if (is_array($value)) {
            if (empty($value)) {
                return '[]';
            }
            // Check if associative array
            if (array_keys($value) !== range(0, count($value) - 1)) {
                return json_encode($value);
            }
            return '[' . implode(', ', $value) . ']';
        }
        
        if (is_string($value) && strlen($value) > 50) {
            return substr($value, 0, 47) . '...';
        }
        
        return (string)$value;
    }
}
