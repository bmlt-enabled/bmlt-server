<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleTranslateService
{
    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Translate an array of texts from source language to target language.
     *
     * @param array $texts Array of strings to translate
     * @param string $source Source language code (e.g., 'en')
     * @param string $target Target language code (e.g., 'es')
     * @return array Array of translated strings in same order
     */
    public function translate(array $texts, string $source, string $target): array
    {
        $nonEmptyTexts = array_filter($texts, fn ($text) => !is_null($text) && $text !== '');
        if (empty($nonEmptyTexts)) {
            return $texts;
        }

        $response = Http::post('https://translation.googleapis.com/language/translate/v2?key=' . urlencode($this->apiKey), [
            'q' => array_values($nonEmptyTexts),
            'source' => $source,
            'target' => $target,
            'format' => 'text',
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Google Translate API error: ' . $response->body());
        }

        $translatedTexts = collect($response->json('data.translations'))
            ->pluck('translatedText')
            ->values()
            ->toArray();

        // Map translated texts back to original positions
        $result = [];
        $translatedIndex = 0;
        foreach ($texts as $key => $text) {
            if (!is_null($text) && $text !== '') {
                $result[$key] = $translatedTexts[$translatedIndex] ?? $text;
                $translatedIndex++;
            } else {
                $result[$key] = $text;
            }
        }

        return $result;
    }
}
