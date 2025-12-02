<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * # Delete a key from all language files
 * php artisan translation:delete oldKey
 *
 **/

class DeleteTranslationKey extends Command
{
    protected $signature = 'translation:delete {key}';

    protected $description = 'Delete a translation key from all language files';

    public function handle()
    {
        $key = $this->argument('key');

        $langPath = resource_path('js/lang');
        $files = File::glob($langPath . '/*.ts');

        $files = array_filter($files, fn($file) => basename($file) !== 'index.ts');

        if (empty($files)) {
            $this->error("No translation files found in {$langPath}");
            return 1;
        }

        $this->info("Deleting translation key '{$key}' from all language files...");

        $deletedCount = 0;
        $notFoundCount = 0;

        foreach ($files as $file) {
            $result = $this->deleteKeyFromFile($file, $key);
            if ($result) {
                $deletedCount++;
            } else {
                $notFoundCount++;
            }
        }

        if ($deletedCount > 0) {
            $this->info("\n✓ Translation key '{$key}' deleted from {$deletedCount} file(s).");
        }

        if ($notFoundCount > 0) {
            $this->warn("Key not found in {$notFoundCount} file(s).");
        }

        return 0;
    }

    private function deleteKeyFromFile(string $filePath, string $key): bool
    {
        $fileName = basename($filePath);
        $content = File::get($filePath);

        // Find the translations export (e.g., export const enTranslations = {)
        if (!preg_match('/export const \w+Translations = \{/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $this->error("Could not find translations export in {$fileName}");
            return false;
        }

        $exportStart = $matches[0][1] + strlen($matches[0][0]);

        $closingBracePos = $this->findClosingBrace($content, $exportStart);

        if ($closingBracePos === false) {
            $this->error("Could not find closing brace for translations in {$fileName}");
            return false;
        }

        // Extract the translations section
        $translationsContent = substr($content, $exportStart, $closingBracePos - $exportStart);

        // Find the line with the key
        $pattern = '/^(\s*)' . preg_quote($key, '/') . ':.*?(?:,\s*(?:\/\/.*)?)?$/m';
        if (!preg_match($pattern, $translationsContent, $match, PREG_OFFSET_CAPTURE)) {
            $this->warn("  {$fileName}: Key '{$key}' not found");
            return false;
        }

        $lineStart = $match[0][1];
        $lineLength = strlen($match[0][0]);

        // Remove the line including the newline
        $beforeLine = substr($translationsContent, 0, $lineStart);
        $afterLine = substr($translationsContent, $lineStart + $lineLength);

        // Remove the newline after the key
        if (substr($afterLine, 0, 1) === "\n") {
            $afterLine = substr($afterLine, 1);
        }

        // Check if we need to fix comma on the previous line
        // If the deleted line had no comma and the previous line exists,
        // and the next content is not the closing brace, we may need to add a comma
        $hasComma = preg_match('/,\s*(?:\/\/.*)?$/', $match[0][0]);

        if (!$hasComma && trim($afterLine) !== '' && !preg_match('/^\s*}/', $afterLine)) {
            // The deleted line didn't have a comma, but there's more content after
            // We need to ensure the previous line has a comma
            if (preg_match('/^(.*?)(\s*(?:\/\/.*)?)\s*$/s', rtrim($beforeLine, "\n"), $prevMatch)) {
                $beforeContent = $prevMatch[1];
                $trailingComment = $prevMatch[2] ?? '';

                // Check if the last line already has a comma
                if (!preg_match('/,\s*$/', $beforeContent)) {
                    // Add comma before any trailing comment
                    $beforeLine = rtrim($beforeContent, " \t") . ',' . $trailingComment . "\n";
                }
            }
        }

        $updatedContent = $beforeLine . $afterLine;

        // Rebuild the full content
        $newContent = substr($content, 0, $exportStart)
            . $updatedContent
            . substr($content, $closingBracePos);

        File::put($filePath, $newContent);
        $this->info("  ✓ {$fileName}");

        return true;
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
}
