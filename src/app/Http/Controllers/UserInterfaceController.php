<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use App\Interfaces\MeetingRepositoryInterface;

class UserInterfaceController extends Controller
{
    public function all(Request $request): Response
    {
        return self::handle($request);
    }

    public static function handle(Request $request): Response
    {
        if (config_file_setting('aggregator_mode_enabled')) {
            return response('"the aggregator formerly known as tomato"');
        }

        return response()->view('frontend', [
            'autoGeocodingEnabled' => legacy_config('auto_geocoding_enabled'),
            'baseUrl' => $request->getBaseurl(),
            'bmltTitle' => legacy_config('bmlt_title'),
            'bmltNotice' => legacy_config('bmlt_notice'),
            'centerLongitude' => legacy_config('search_spec_map_center_longitude'),
            'centerLatitude' => legacy_config('search_spec_map_center_latitude'),
            'centerZoom' => legacy_config('search_spec_map_center_zoom'),
            'countyAutoGeocodingEnabled' => legacy_config('county_auto_geocoding_enabled'),
            'customFields' => self::getCustomFields(),
            'defaultClosedStatus' => legacy_config('default_closed_status'),
            'defaultDuration' => legacy_config('default_duration_time'),
            'defaultLanguage' => App::currentLocale(),
            'distanceUnits' => legacy_config('distance_units'),
            'googleApiKey' => legacy_config('google_api_key', ''),
            'isLanguageSelectorEnabled' => legacy_config('enable_language_selector'),
            'languageMapping' => self::getLanguageMapping(),
            'formatLangNames' => legacy_config('format_lang_names', []),
            'meetingStatesAndProvinces' => legacy_config('meeting_states_and_provinces', []),
            'meetingCountiesAndSubProvinces' => legacy_config('meeting_counties_and_sub_provinces', []),
            'regionBias' => legacy_config('region_bias'),
            'version' => config('app.version'),
            'zipAutoGeocodingEnabled' => legacy_config('zip_auto_geocoding_enabled'),
        ]);
    }

    private static function getLanguageMapping(): array
    {
        return collect(scandir(base_path('lang')))
            ->reject(fn ($dir) => str_starts_with($dir, '.'))
            ->sort()
            ->mapWithKeys(function ($langAbbreviation, $_) {
                $langName = $langAbbreviation == 'dk' ? 'da' : $langAbbreviation;
                $langName = \Locale::getDisplayLanguage($langName, $langName);
                $langName = mb_str_split($langName);
                $langName = mb_strtoupper($langName[0]) . implode('', array_slice($langName, 1));
                return [$langAbbreviation => $langName];
            })
            ->toArray();
    }

    private static function getCustomFields(): Collection
    {
        $meetingRepository = resolve(MeetingRepositoryInterface::class);
        $customFields = $meetingRepository->getCustomFields();
        return $meetingRepository->getDataTemplates()
            ->reject(fn ($t) => !$customFields->contains($t->key))
            ->map(fn ($t) => [
                'name' => $t->key,
                'displayName' => $t->field_prompt,
                'language' => $t->lang_enum
            ])
            ->values();
    }
}
