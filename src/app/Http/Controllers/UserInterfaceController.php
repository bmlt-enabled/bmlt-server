<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
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
            'autoGeocodingEnabled' => legacy_config('autoGeocodingEnabled'),
            'baseUrl' => $request->getBaseurl(),
            'bmltTitle' => legacy_config('bmltTitle'),
            'bmltNotice' => legacy_config('bmltNotice'),
            'centerLongitude' => legacy_config('searchSpecMapCenterLongitude'),
            'centerLatitude' => legacy_config('searchSpecMapCenterLatitude'),
            'centerZoom' => legacy_config('searchSpecMapCenterZoom'),
            'countyAutoGeocodingEnabled' => legacy_config('countyAutoGeocodingEnabled'),
            'customFields' => self::getCustomFields(),
            'defaultClosedStatus' => legacy_config('default_closed_status'),
            'defaultDuration' => legacy_config('default_duration_time'),
            'defaultLanguage' => App::currentLocale(),
            'distanceUnits' => legacy_config('distance_units'),
            'googleApiKey' => legacy_config('google_api_key', ''),
            'isLanguageSelectorEnabled' => legacy_config('enable_language_selector'),
            'languageMapping' => self::getLanguageMapping(),
            'formatLangNames' => legacy_config('formatLangNames', []),
            'meetingStatesAndProvinces' => legacy_config('meetingStatesAndProvinces', []),
            'meetingCountiesAndSubProvinces' => legacy_config('meetingCountiesAndSubProvinces', []),
            'regionBias' => legacy_config('regionBias'),
            'version' => config('app.version'),
            'zipAutoGeocodingEnabled' => legacy_config('zipAutoGeocodingEnabled'),
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
