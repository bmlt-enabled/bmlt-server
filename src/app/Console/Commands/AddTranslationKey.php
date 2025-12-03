<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
* # Add a new key
* php artisan translation:add meetingName "Meeting Name"
*
* # Update an existing key (with --force)
* php artisan translation:add cancel "Cancelled" --force
*
**/

class AddTranslationKey extends Command
{
    protected $signature = 'translation:add {key} {value} {--force : Overwrite existing keys}';

    protected $description = 'Add a translation key/value to all language files in alphabetical order';

    public function handle()
    {
        $key = $this->argument('key');
        $value = $this->argument('value');

        if (!preg_match('/^[a-z][a-zA-Z0-9]*$/', $key)) {
            $this->error("Invalid key format. Key must be in camelCase (start with lowercase letter, no underscores or spaces).");
            return 1;
        }

        $langPath = resource_path('js/lang');
        $files = File::glob($langPath . '/*.ts');

        $files = array_filter($files, fn($file) => basename($file) !== 'index.ts');

        if (empty($files)) {
            $this->error("No translation files found in {$langPath}");
            return 1;
        }

        $this->info("Adding translation key '{$key}' to all language files...");

        foreach ($files as $file) {
            $this->processFile($file, $key, $value);
        }

        $this->info("\n✓ Translation key '{$key}' added to all language files.");
        if (!$this->option('force')) {
            $this->info("Note: Non-English files have been marked with '// TODO: translate' comments.");
        }

        return 0;
    }

    private function processFile(string $filePath, string $key, string $value): void
    {
        $fileName = basename($filePath);
        $content = File::get($filePath);

        $isEnglish = str_starts_with($fileName, 'en.');

        $translationValue = $isEnglish ? $value : $value;
        $comment = $isEnglish ? '' : ', // TODO: translate';

        // Find the translations export (e.g., export const enTranslations = {)
        if (!preg_match('/export const \w+Translations = \{/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $this->error("Could not find translations export in {$fileName}");
            return;
        }

        $exportStart = $matches[0][1] + strlen($matches[0][0]);

        $closingBracePos = $this->findClosingBrace($content, $exportStart);

        if ($closingBracePos === false) {
            $this->error("Could not find closing brace for translations in {$fileName}");
            return;
        }

        // Extract the translations section
        $translationsContent = substr($content, $exportStart, $closingBracePos - $exportStart);

        // Parse existing keys
        preg_match_all('/^\s*(\w+):/m', $translationsContent, $keyMatches);
        $existingKeys = $keyMatches[1];

        // Check if key already exists
        if (in_array($key, $existingKeys)) {
            if (!$this->option('force')) {
                $this->warn("  {$fileName}: Key '{$key}' already exists, skipping. Use --force to overwrite.");
                return;
            }
            // Remove the existing key so we can replace it
            $this->removeExistingKey($translationsContent, $key, $exportStart, $closingBracePos, $content, $filePath, $value, $fileName, $isEnglish);
            return;
        }

        // Find insertion point (alphabetically, case-insensitive)
        $insertAfterKey = null;
        $insertBeforeKey = null;

        foreach ($existingKeys as $existingKey) {
            if (strcasecmp($existingKey, $key) < 0) {
                $insertAfterKey = $existingKey;
            } else {
                $insertBeforeKey = $existingKey;
                break;
            }
        }

        // Create the new line
        // escape single quotes and backslashes
        $escapedValue = str_replace(['\\', "'"], ['\\\\', "\\'"], $translationValue);
        
        // Determine if we're inserting at the end (after last key)
        // If so, don't add comma since it will be the new last item
        $isLastItem = ($insertAfterKey && !$insertBeforeKey);
        $commentForNewLine = $isLastItem && !$isEnglish ? ' // TODO: translate' : $comment;
        
        $newLine = "  {$key}: '{$escapedValue}'{$commentForNewLine}";

        // Find the position to insert
        if ($insertBeforeKey) {
            // Insert before the next key (add comma after if English, otherwise comment includes comma)
            if (preg_match('/^\s*' . preg_quote($insertBeforeKey, '/') . ':/m', $translationsContent, $match, PREG_OFFSET_CAPTURE)) {
                $insertPos = $match[0][1];
                $comma = $isEnglish ? ',' : '';
                $before = substr($translationsContent, 0, $insertPos);
                $after = substr($translationsContent, $insertPos);

                if (trim($before) === '') {
                    // At the start - keep structure but ensure newline before first key
                    if (substr($after, 0, 1) === "\n") {
                        $after = substr($after, 1);
                    }
                    $updatedContent = "\n" . $newLine . $comma . "\n" . $after;
                } else {
                    $updatedContent = $before . $newLine . $comma . "\n" . $after;
                }
            } else {
                return;
            }
        } elseif ($insertAfterKey) {
            // Insert after the previous key (at the end)
            if (preg_match('/^\s*' . preg_quote($insertAfterKey, '/') . ':.*$/m', $translationsContent, $match, PREG_OFFSET_CAPTURE)) {
                $lineStart = $match[0][1];
                $lineLen = strlen($match[0][0]);
                $before = substr($translationsContent, 0, $lineStart);
                $lastLine = substr($translationsContent, $lineStart, $lineLen);
                $after = substr($translationsContent, $lineStart + $lineLen);

                // Ensure previous last line has trailing comma before any comment
                if (!preg_match('/,\s*(?:\/\/.*)?$/', $lastLine)) {
                    if (preg_match('/(.*?)(\s*\/\/.*)$/', $lastLine, $m2)) {
                        $beforeComment = rtrim($m2[1]);
                        $commentPart = ltrim($m2[2]);
                        $lastLine = $beforeComment . ', ' . $commentPart;
                    } else {
                        $lastLine = rtrim($lastLine) . ',';
                    }
                }

                $updatedContent = $before . $lastLine . "\n" . $newLine . $after;
            } else {
                return;
            }
        } else {
            // No existing keys, add as first key
            $updatedContent = "\n" . $newLine . "\n";
        }

        $newContent = substr($content, 0, $exportStart)
            . $updatedContent
            . substr($content, $closingBracePos);

        File::put($filePath, $newContent);
        $this->info("  ✓ {$fileName}");
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

    private function removeExistingKey(
        string $translationsContent,
        string $key,
        int $exportStart,
        int $closingBracePos,
        string $content,
        string $filePath,
        string $value,
        string $fileName,
        bool $isEnglish
    ): void {
        // Find the line with the existing key and extract its value
        $pattern = '/^(\s*)' . preg_quote($key, '/') . ':\s*([\'"])(.*?)\2(,?)(.*?)$/m';
        if (preg_match($pattern, $translationsContent, $match, PREG_OFFSET_CAPTURE)) {
            $lineStart = $match[0][1];
            $indent = $match[1][0];
            $trailingComma = $match[4][0];

            // Escape the new value
            $escapedValue = str_replace(['\\', "'"], ['\\\\', "\\'"], $value);

            // Add TODO comment for non-English files
            $comment = $isEnglish ? '' : ', // TODO: translate';

            // If there's no comma, we need to add one (or use the comment's comma)
            if (!$trailingComma && !$comment) {
                $trailingComma = ',';
            } elseif ($trailingComma && $comment) {
                // Comment already has comma, don't duplicate
                $trailingComma = '';
            }

            $newLine = "{$indent}{$key}: '{$escapedValue}'{$trailingComma}{$comment}";

            // Replace the entire line
            $beforeLine = substr($translationsContent, 0, $lineStart);
            $lineEnd = $lineStart + strlen($match[0][0]);
            $afterLine = substr($translationsContent, $lineEnd);

            $updatedContent = $beforeLine . $newLine . $afterLine;

            // Rebuild the full content
            $newContent = substr($content, 0, $exportStart)
                . $updatedContent
                . substr($content, $closingBracePos);

            File::put($filePath, $newContent);
            $this->info("  ✓ {$fileName} (updated)");
        }
    }
}
