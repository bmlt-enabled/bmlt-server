<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * php artisan translation:export-spreadsheet it /path/to/output.xlsx
 *
 * Exports translations to XLSX format:
 * Column A: Key
 * Column B: English
 * Column C: Target Language
 *
 * Rows with // TODO: Translate comments are highlighted in green
 */
class ExportTranslationsToSpreadsheet extends Command
{
    protected $signature = 'translation:export-spreadsheet
                            {language : Language code (e.g., it, fr, es)}
                            {output : Path for the output XLSX file}';

    protected $description = 'Export translation file to XLSX spreadsheet with green highlighting for TODO items';

    public function handle()
    {
        $languageCode = $this->argument('language');
        $outputPath = $this->argument('output');

        if ($languageCode === 'en') {
            $this->error("Cannot export English translations.");
            $this->info("English is the source language. Export a target language instead (e.g., it, fr, es).");
            return 1;
        }

        $enPath = resource_path("js/lang/en.ts");
        $langPath = resource_path("js/lang/{$languageCode}.ts");

        if (!File::exists($enPath)) {
            $this->error("English translation file not found: {$enPath}");
            return 1;
        }

        if (!File::exists($langPath)) {
            $this->error("Translation file not found: {$langPath}");
            $this->info("Available languages: " . implode(', ', $this->getAvailableLanguages()));
            return 1;
        }

        try {
            $this->info("Reading translation files...");
            $enTranslations = $this->parseTranslationFile($enPath);
            $targetTranslations = $this->parseTranslationFile($langPath);

            $this->info("Creating spreadsheet...");
            $spreadsheet = $this->createSpreadsheet($enTranslations, $targetTranslations, $languageCode);

            $this->info("Writing to file: {$outputPath}");
            $writer = new Xlsx($spreadsheet);
            $writer->save($outputPath);

            $todoCount = $this->countTodoTranslations($targetTranslations);

            $this->info("\n✅ Spreadsheet created successfully!");
            $this->info("📁 Output file: {$outputPath}");
            $this->info("📊 Total translations: " . count($enTranslations));
            if ($todoCount > 0) {
                $this->line("<fg=green>🟢 Rows needing translation (highlighted in green): {$todoCount}</>");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }

    private function parseTranslationFile(string $filePath): array
    {
        $content = File::get($filePath);
        $translations = [];

        // Find the translations export (e.g., export const enTranslations = {)
        // This skips the YupLocale object
        if (!preg_match('/export const \w+Translations = \{/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            return $translations;
        }

        $exportStart = $matches[0][1] + strlen($matches[0][0]);
        $closingBracePos = $this->findClosingBrace($content, $exportStart);

        if ($closingBracePos === false) {
            return $translations;
        }

        // Extract only the translations section
        $translationsContent = substr($content, $exportStart, $closingBracePos - $exportStart);

        // Match translation keys with their values and any trailing comments
        // Pattern: key: 'value' or key: "value", with optional comma and comments
        preg_match_all('/^\s*([a-zA-Z0-9_]+):\s*([\'"])(.*)\\2(,?)(.*?)$/m', $translationsContent, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $key = $match[1];
            $value = $match[3];
            $comment = trim($match[5]);

            $translations[$key] = [
                'value' => $value,
                'needsTranslation' => stripos($comment, '// TODO: Translate') !== false
            ];
        }

        return $translations;
    }

    private function findClosingBrace(string $content, int $startPos): int|false
    {
        $depth = 1;
        $length = strlen($content);

        for ($i = $startPos; $i < $length; $i++) {
            if ($content[$i] === '{') {
                $depth++;
            } elseif ($content[$i] === '}') {
                $depth--;
                if ($depth === 0) {
                    return $i;
                }
            }
        }

        return false;
    }

    private function createSpreadsheet(array $enTranslations, array $targetTranslations, string $languageCode): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set column headers
        $sheet->setCellValue('A1', 'Key');
        $sheet->setCellValue('B1', 'English');
        $sheet->setCellValue('C1', ucfirst($languageCode));

        // Style the header row
        $headerStyle = $sheet->getStyle('A1:C1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Add translations
        $row = 2;
        foreach ($enTranslations as $key => $enData) {
            $sheet->setCellValue("A{$row}", $key);
            $sheet->setCellValue("B{$row}", $enData['value']);

            $targetValue = $targetTranslations[$key]['value'] ?? '';
            $needsTranslation = $targetTranslations[$key]['needsTranslation'] ?? false;

            $sheet->setCellValue("C{$row}", $targetValue);

            // Highlight row in green if it needs translation
            if ($needsTranslation) {
                $rowStyle = $sheet->getStyle("A{$row}:C{$row}");
                $rowStyle->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF90EE90'); // Light green
            }

            $row++;
        }

        // Auto-size columns
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        // Freeze the header row
        $sheet->freezePane('A2');

        return $spreadsheet;
    }

    private function countTodoTranslations(array $translations): int
    {
        return count(array_filter($translations, fn($item) => $item['needsTranslation']));
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
