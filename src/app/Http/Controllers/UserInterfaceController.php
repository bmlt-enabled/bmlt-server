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
        if (file_config('aggregator_mode_enabled')) {
            return response('"the aggregator formerly known as tomato"');
        }

        return response()->view('frontend', [
            'autoGeocodingEnabled' => bmlt_config('autoGeocodingEnabled'),
            'baseUrl' => $request->getBaseurl(),
            'bmltTitle' => bmlt_config('bmltTitle'),
            'bmltNotice' => bmlt_config('bmltNotice'),
            'centerLongitude' => bmlt_config('searchSpecMapCenterLongitude'),
            'centerLatitude' => bmlt_config('searchSpecMapCenterLatitude'),
            'centerZoom' => bmlt_config('searchSpecMapCenterZoom'),
            'countyAutoGeocodingEnabled' => bmlt_config('countyAutoGeocodingEnabled'),
            'customFields' => self::getCustomFields(),
            'defaultClosedStatus' => bmlt_config('defaultClosedStatus'),
            'defaultDuration' => bmlt_config('defaultDurationTime'),
            'defaultLanguage' => App::currentLocale(),
            'distanceUnits' => bmlt_config('distanceUnits'),
            'googleApiKey' => bmlt_config('googleApiKey', ''),
            'isLanguageSelectorEnabled' => bmlt_config('enableLanguageSelector'),
            'languageMapping' => self::getLanguageMapping(),
            'formatLangNames' => bmlt_config('formatLangNames', []),
            'meetingStatesAndProvinces' => bmlt_config('meetingStatesAndProvinces', []),
            'meetingCountiesAndSubProvinces' => bmlt_config('meetingCountiesAndSubProvinces', []),
            'regionBias' => bmlt_config('regionBias'),
            'version' => config('app.version'),
            'zipAutoGeocodingEnabled' => bmlt_config('zipAutoGeocodingEnabled'),
        ]);
    }

    private static function getLanguageMapping(): array
    {
        return collect(scandir(base_path('lang')))
            ->reject(fn($dir) => str_starts_with($dir, '.'))
            ->sort()
            ->mapWithKeys(fn($langCode) => [$langCode => __('language_name.name', [], $langCode)])
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
