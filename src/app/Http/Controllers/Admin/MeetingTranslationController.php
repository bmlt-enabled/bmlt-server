<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Repositories\MeetingRepository;
use App\Services\GoogleTranslateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeetingTranslationController extends Controller
{
    public function translate(Request $request, Meeting $meeting): JsonResponse
    {
        $this->authorize('update', $meeting);

        $validated = $request->validate([
            'targetLanguages' => 'required|array|min:1',
            'targetLanguages.*' => 'required|string|max:10',
            'sourceLanguage' => 'required|string|max:10',
            'sourceTexts' => 'sometimes|array',
            'sourceTexts.*' => 'nullable|string|max:512',
        ]);

        $googleApiKey = bmlt_config('googleApiKey', '');
        if (empty($googleApiKey)) {
            return response()->json(['message' => 'Google API key is not configured.'], 422);
        }

        $targetLanguages = $validated['targetLanguages'];
        $sourceLanguage = $validated['sourceLanguage'];

        // Get source texts either from the request or from the meeting's data
        if (!empty($validated['sourceTexts'])) {
            $sourceTexts = $validated['sourceTexts'];
        } else {
            $meeting->loadMissing('data');
            $primaryLangEnum = $meeting->lang_enum ?: app()->getLocale();
            $meetingData = $meeting->data
                ->filter(fn ($data) => $data->lang_enum === $primaryLangEnum)
                ->mapWithKeys(fn ($data) => [$data->key => $data->data_string]);

            $sourceTexts = [];
            foreach (MeetingRepository::TRANSLATABLE_LOCATION_FIELDS as $field) {
                $sourceTexts[$field] = $meetingData->get($field) ?? '';
            }
        }

        $translateService = new GoogleTranslateService($googleApiKey);
        $result = [];

        foreach ($targetLanguages as $targetLanguage) {
            $translated = $translateService->translate($sourceTexts, $sourceLanguage, $targetLanguage);
            $result[$targetLanguage] = $translated;
        }

        return response()->json($result);
    }
}
