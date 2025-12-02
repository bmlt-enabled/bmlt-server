<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class UpdateTranslationsFromSpreadsheetTest extends TestCase
{
    use RefreshDatabase;

    private string $testSpreadsheetPath;
    private string $testTranslationFilePath;
    private string $originalTranslationContent;

    protected function setUp(): void
    {
        parent::setUp();

        $testDir = storage_path('framework/testing');
        if (!File::exists($testDir)) {
            File::makeDirectory($testDir, 0755, true);
        }

        $this->testSpreadsheetPath = $testDir . '/test-translations.xlsx';
        $this->testTranslationFilePath = resource_path('js/lang/test.ts');

        if (File::exists($this->testTranslationFilePath)) {
            $this->originalTranslationContent = File::get($this->testTranslationFilePath);
        }
    }

    protected function tearDown(): void
    {
        if (File::exists($this->testSpreadsheetPath)) {
            File::delete($this->testSpreadsheetPath);
        }

        if (isset($this->originalTranslationContent)) {
            File::put($this->testTranslationFilePath, $this->originalTranslationContent);
        } elseif (File::exists($this->testTranslationFilePath)) {
            File::delete($this->testTranslationFilePath);
        }

        $backups = File::glob(resource_path('js/lang/*.backup.*'));
        foreach ($backups as $backup) {
            File::delete($backup);
        }

        parent::tearDown();
    }

    private function createTestSpreadsheet(array $translations): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Key');
        $sheet->setCellValue('B1', 'English');
        $sheet->setCellValue('C1', 'Test Language');

        $row = 2;
        foreach ($translations as $key => $value) {
            $sheet->setCellValue("A{$row}", $key);
            $sheet->setCellValue("B{$row}", "English: {$key}");
            $sheet->setCellValue("C{$row}", $value);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($this->testSpreadsheetPath);
    }

    private function createTestTranslationFile(string $content): void
    {
        File::put($this->testTranslationFilePath, $content);
    }

    public function testUpdatesTranslationsWithSingleQuotes()
    {
        $translationContent = <<<'TS'
import type { LocaleObject } from 'yup';

export const testYupLocale: LocaleObject = {};

/*eslint sort-keys: ["error", "asc", {caseSensitive: false}]*/
export const testTranslations = {
  accountTitle: 'Account',
  addMeeting: 'Add Meeting',
  cancel: 'Cancel'
};
TS;

        $this->createTestTranslationFile($translationContent);
        $this->createTestSpreadsheet([
            'accountTitle' => 'Conto',
            'addMeeting' => 'Aggiungi Riunione',
            'cancel' => 'Annulla'
        ]);

        $this->artisan('translation:update-from-spreadsheet', [
            'spreadsheet' => $this->testSpreadsheetPath,
            'language' => 'test'
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        $this->assertStringContainsString("accountTitle: 'Conto'", $updatedContent);
        $this->assertStringContainsString("addMeeting: 'Aggiungi Riunione'", $updatedContent);
        $this->assertStringContainsString("cancel: 'Annulla'", $updatedContent);
    }

    public function testUpdatesTranslationsWithDoubleQuotes()
    {
        $translationContent = <<<'TS'
import type { LocaleObject } from 'yup';

export const testYupLocale: LocaleObject = {};

/*eslint sort-keys: ["error", "asc", {caseSensitive: false}]*/
export const testTranslations = {
  accountTitle: 'Account',
  confirmYesImSure: "Yes, I'm sure.",
  meetingNote: "It's a test"
};
TS;

        $this->createTestTranslationFile($translationContent);
        $this->createTestSpreadsheet([
            'accountTitle' => 'Conto',
            'confirmYesImSure' => "Sì, sono sicuro.",
            'meetingNote' => "È un test"
        ]);

        $this->artisan('translation:update-from-spreadsheet', [
            'spreadsheet' => $this->testSpreadsheetPath,
            'language' => 'test'
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        // Should convert double quotes to single quotes with escaped apostrophes
        $this->assertStringContainsString("accountTitle: 'Conto'", $updatedContent);
        $this->assertStringContainsString("confirmYesImSure: 'Sì, sono sicuro.'", $updatedContent);
        $this->assertStringContainsString("meetingNote: 'È un test'", $updatedContent);
    }

    public function testHandlesApostrophesInTranslations()
    {
        $translationContent = <<<'TS'
import type { LocaleObject } from 'yup';

export const testYupLocale: LocaleObject = {};

/*eslint sort-keys: ["error", "asc", {caseSensitive: false}]*/
export const testTranslations = {
  chooseStartTime: 'Choose start time',
  userNote: 'User note'
};
TS;

        $this->createTestTranslationFile($translationContent);
        $this->createTestSpreadsheet([
            'chooseStartTime' => "Scegli l'ora di inizio",
            'userNote' => "L'utente è attivo"
        ]);

        $this->artisan('translation:update-from-spreadsheet', [
            'spreadsheet' => $this->testSpreadsheetPath,
            'language' => 'test'
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        // Should escape apostrophes
        $this->assertStringContainsString("chooseStartTime: 'Scegli l\\'ora di inizio'", $updatedContent);
        $this->assertStringContainsString("userNote: 'L\\'utente è attivo'", $updatedContent);
    }

    public function testPreservesCommentsAndTrailingCommas()
    {
        $translationContent = <<<'TS'
import type { LocaleObject } from 'yup';

export const testYupLocale: LocaleObject = {};

/*eslint sort-keys: ["error", "asc", {caseSensitive: false}]*/
export const testTranslations = {
  accountTitle: 'Account', // TODO: translate
  addMeeting: 'Add Meeting',
  cancel: 'Cancel'
};
TS;

        $this->createTestTranslationFile($translationContent);
        $this->createTestSpreadsheet([
            'accountTitle' => 'Conto',
            'addMeeting' => 'Aggiungi Riunione'
        ]);

        $this->artisan('translation:update-from-spreadsheet', [
            'spreadsheet' => $this->testSpreadsheetPath,
            'language' => 'test'
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        // Should preserve the TODO comment
        $this->assertStringContainsString("accountTitle: 'Conto', // TODO: translate", $updatedContent);
        $this->assertStringContainsString("addMeeting: 'Aggiungi Riunione',", $updatedContent);
    }

    public function testReportsNotFoundKeys()
    {
        $translationContent = <<<'TS'
import type { LocaleObject } from 'yup';

export const testYupLocale: LocaleObject = {};

/*eslint sort-keys: ["error", "asc", {caseSensitive: false}]*/
export const testTranslations = {
  accountTitle: 'Account',
  addMeeting: 'Add Meeting'
};
TS;

        $this->createTestTranslationFile($translationContent);
        $this->createTestSpreadsheet([
            'accountTitle' => 'Conto',
            'addMeeting' => 'Aggiungi Riunione',
            'nonExistentKey' => 'Some Value',
            'anotherMissingKey' => 'Another Value'
        ]);

        $this->artisan('translation:update-from-spreadsheet', [
            'spreadsheet' => $this->testSpreadsheetPath,
            'language' => 'test'
        ])
            ->expectsOutputToContain('Keys not found in test.ts:')
            ->expectsOutputToContain('nonExistentKey')
            ->expectsOutputToContain('anotherMissingKey')
            ->assertExitCode(0);
    }

    public function testCreatesBackupFile()
    {
        $translationContent = <<<'TS'
import type { LocaleObject } from 'yup';

export const testYupLocale: LocaleObject = {};

/*eslint sort-keys: ["error", "asc", {caseSensitive: false}]*/
export const testTranslations = {
  accountTitle: 'Account'
};
TS;

        $this->createTestTranslationFile($translationContent);
        $this->createTestSpreadsheet([
            'accountTitle' => 'Conto'
        ]);

        $this->artisan('translation:update-from-spreadsheet', [
            'spreadsheet' => $this->testSpreadsheetPath,
            'language' => 'test'
        ])->assertExitCode(0);

        // Check backup was created
        $backups = File::glob(resource_path('js/lang/test.ts.backup.*'));
        $this->assertCount(1, $backups);

        // Backup should contain original content
        $backupContent = File::get($backups[0]);
        $this->assertEquals($translationContent, $backupContent);
    }

    public function testFailsWhenSpreadsheetDoesNotExist()
    {
        $this->artisan('translation:update-from-spreadsheet', [
            'spreadsheet' => '/nonexistent/file.xlsx',
            'language' => 'test'
        ])
            ->expectsOutput('Spreadsheet file not found: /nonexistent/file.xlsx')
            ->assertExitCode(1);
    }

    public function testFailsWhenLanguageFileDoesNotExist()
    {
        $this->createTestSpreadsheet([
            'accountTitle' => 'Conto'
        ]);

        $this->artisan('translation:update-from-spreadsheet', [
            'spreadsheet' => $this->testSpreadsheetPath,
            'language' => 'nonexistent'
        ])
            ->expectsOutputToContain('Translation file not found')
            ->assertExitCode(1);
    }

    public function testShowsCorrectSummary()
    {
        $translationContent = <<<'TS'
import type { LocaleObject } from 'yup';

export const testYupLocale: LocaleObject = {};

/*eslint sort-keys: ["error", "asc", {caseSensitive: false}]*/
export const testTranslations = {
  accountTitle: 'Account',
  addMeeting: 'Add Meeting',
  cancel: 'Cancel'
};
TS;

        $this->createTestTranslationFile($translationContent);
        $this->createTestSpreadsheet([
            'accountTitle' => 'Conto',
            'addMeeting' => 'Aggiungi Riunione',
            'cancel' => 'Annulla',
            'missingKey' => 'Missing'
        ]);

        $this->artisan('translation:update-from-spreadsheet', [
            'spreadsheet' => $this->testSpreadsheetPath,
            'language' => 'test'
        ])
            ->expectsOutput('   • Keys found in spreadsheet: 4')
            ->expectsOutput('   • Updated existing keys: 3')
            ->expectsOutput('   • Keys not found in file: 1')
            ->assertExitCode(0);
    }

    public function testPreventsUpdatingEnglishTranslations()
    {
        $this->createTestSpreadsheet([
            'accountTitle' => 'Account'
        ]);

        $this->artisan('translation:update-from-spreadsheet', [
            'spreadsheet' => $this->testSpreadsheetPath,
            'language' => 'en'
        ])
            ->expectsOutputToContain('Cannot update English translations from spreadsheet')
            ->expectsOutputToContain("translation:add")
            ->assertExitCode(1);
    }
}
