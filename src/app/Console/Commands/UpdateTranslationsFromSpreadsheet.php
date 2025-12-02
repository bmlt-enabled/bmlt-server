<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * php artisan translation:update-from-spreadsheet /path/to/italian-translations.xlsx it
 *
 * Spreadsheet format:
 * Column A: Key
 * Column B: English
 * Column C: Target Language (e.g., Italiano)
 */
class UpdateTranslationsFromSpreadsheet extends Command
{
    protected $signature = 'translation:update-from-spreadsheet
                            {spreadsheet : Path to the XLSX file}
                            {language : Language code (e.g., it, fr, es)}';

    protected $description = 'Update translation file from XLSX spreadsheet';

    public function handle()
    {
        $spreadsheetPath = $this->argument('spreadsheet');
        $languageCode = $this->argument('language');

        if ($languageCode === 'en') {
            $this->error("Cannot update English translations from spreadsheet.");
            $this->info("English translations should be updated directly in the source file or using 'translation:add' command.");
            return 1;
        }

        if (!file_exists($spreadsheetPath)) {
            $this->error("Spreadsheet file not found: {$spreadsheetPath}");
            return 1;
        }

        $langPath = resource_path("js/lang/{$languageCode}.ts");
        if (!File::exists($langPath)) {
            $this->error("Translation file not found: {$langPath}");
            $this->info("Available languages: " . implode(', ', $this->getAvailableLanguages()));
            return 1;
        }

        try {
            $this->info("Reading spreadsheet: {$spreadsheetPath}");
            $translations = $this->parseSpreadsheet($spreadsheetPath);

            if (empty($translations)) {
                $this->error("No translations found in spreadsheet");
                return 1;
            }

            $this->info("Found " . count($translations) . " translations in spreadsheet");

            $backupFile = "{$langPath}.backup." . time();
            File::copy($langPath, $backupFile);
            $this->info("Created backup: {$backupFile}");

            $this->info("Updating {$languageCode}.ts...");
            $result = $this->updateTranslationFile($langPath, $translations);

            $this->info("\n📊 Translation summary:");
            $this->info("   • Keys found in spreadsheet: " . count($translations));
            $this->info("   • Updated existing keys: {$result['updated']}");
            $this->info("   • Keys not found in file: {$result['notFound']}");

            if ($result['notFound'] > 0) {
                $this->warn("\nKeys not found in {$languageCode}.ts:");
                foreach ($result['notFoundKeys'] as $key) {
                    $this->warn("  • {$key}");
                }
                $this->warn("\nUse 'translation:add' command to add new keys first");
            }

            $this->info("\n✅ Translation file updated successfully!");
            $this->info("📁 Backup created at: {$backupFile}");

            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }

    private function parseSpreadsheet(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $translations = [];

        // Start from row 2 (skip header: Key, English, Target Language)
        for ($row = 2; $row <= $highestRow; $row++) {
            $key = trim((string) $sheet->getCell("A{$row}")->getValue());
            // Column C contains the target language translation
            $value = trim((string) $sheet->getCell("C{$row}")->getValue());

            if ($key && $value) {
                $translations[$key] = $value;
            }
        }

        return $translations;
    }

    private function updateTranslationFile(string $filePath, array $newTranslations): array
    {
        $content = File::get($filePath);
        $updated = 0;
        $notFound = 0;
        $notFoundKeys = [];

        foreach ($newTranslations as $key => $newValue) {
            // Escape the new value for single quotes
            $escapedValue = str_replace(['\\', "'"], ['\\\\', "\\'"], $newValue);

            // Match the key with its current value (both single and double quotes)
            $pattern = '/^(\s*)' . preg_quote($key, '/') . ':\s*([\'"])(.*)\\2(,?)(.*?)$/m';

            if (preg_match($pattern, $content, $match)) {
                $indent = $match[1];
                $comma = $match[4];
                $restOfLine = $match[5]; // This captures comments like // TODO: translate

                // Build the replacement line with single quotes
                $replacement = "{$indent}{$key}: '{$escapedValue}'{$comma}{$restOfLine}";

                $content = preg_replace($pattern, $replacement, $content, 1);
                $updated++;
            } else {
                $notFound++;
                $notFoundKeys[] = $key;
            }
        }

        File::put($filePath, $content);

        return [
            'updated' => $updated,
            'notFound' => $notFound,
            'notFoundKeys' => $notFoundKeys,
        ];
    }

    private function getAvailableLanguages(): array
    {
        $langPath = resource_path('js/lang');
        $files = File::glob($langPath . '/*.ts');

        return array_map(function ($file) {
            $basename = basename($file, '.ts');
            return $basename !== 'index' ? $basename : null;
        }, array_filter($files, fn($file) => basename($file) !== 'index.ts'));
    }
}
