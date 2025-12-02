<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DeleteTranslationKeyTest extends TestCase
{
    use RefreshDatabase;

    private string $testTranslationFilePath;
    private string $testTranslationFilePath2;
    private array $savedFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->testTranslationFilePath = resource_path('js/lang/test.ts');
        $this->testTranslationFilePath2 = resource_path('js/lang/test2.ts');

        $langFiles = File::glob(resource_path('js/lang/*.ts'));
        foreach ($langFiles as $file) {
            if (File::exists($file)) {
                $this->savedFiles[$file] = File::get($file);
            }
        }
    }

    protected function tearDown(): void
    {
        // Restore all saved files
        foreach ($this->savedFiles as $file => $content) {
            File::put($file, $content);
        }

        // Delete any test files that didn't exist before
        if (!isset($this->savedFiles[$this->testTranslationFilePath]) && File::exists($this->testTranslationFilePath)) {
            File::delete($this->testTranslationFilePath);
        }
        if (!isset($this->savedFiles[$this->testTranslationFilePath2]) && File::exists($this->testTranslationFilePath2)) {
            File::delete($this->testTranslationFilePath2);
        }

        parent::tearDown();
    }

    private function createTestTranslationFile(string $content, ?string $filePath = null): void
    {
        File::put($filePath ?? $this->testTranslationFilePath, $content);
    }

    public function testDeletesKeyFromMiddle()
    {
        $content = <<<'TS'
export const testTranslations = {
  accountTitle: 'Account',
  addMeeting: 'Add Meeting',
  cancel: 'Cancel'
};
TS;

        $this->createTestTranslationFile($content);

        $this->artisan('translation:delete', [
            'key' => 'addMeeting'
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        $this->assertStringNotContainsString('addMeeting', $updatedContent);
        $this->assertStringContainsString('accountTitle', $updatedContent);
        $this->assertStringContainsString('cancel', $updatedContent);
    }

    public function testDeletesKeyFromBeginning()
    {
        $content = <<<'TS'
export const testTranslations = {
  accountTitle: 'Account',
  addMeeting: 'Add Meeting',
  cancel: 'Cancel'
};
TS;

        $this->createTestTranslationFile($content);

        $this->artisan('translation:delete', [
            'key' => 'accountTitle'
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        $this->assertStringNotContainsString('accountTitle', $updatedContent);
        $this->assertStringContainsString('addMeeting', $updatedContent);
        $this->assertStringContainsString('cancel', $updatedContent);
    }

    public function testDeletesKeyFromEnd()
    {
        $content = <<<'TS'
export const testTranslations = {
  accountTitle: 'Account',
  addMeeting: 'Add Meeting',
  cancel: 'Cancel'
};
TS;

        $this->createTestTranslationFile($content);

        $this->artisan('translation:delete', [
            'key' => 'cancel'
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        $this->assertStringNotContainsString('cancel', $updatedContent);
        $this->assertStringContainsString('accountTitle', $updatedContent);
        $this->assertStringContainsString('addMeeting', $updatedContent);
    }

    public function testDeletesKeyWithTrailingComment()
    {
        $content = <<<'TS'
export const testTranslations = {
  accountTitle: 'Account',
  addMeeting: 'Add Meeting', // TODO: translate
  cancel: 'Cancel'
};
TS;

        $this->createTestTranslationFile($content);

        $this->artisan('translation:delete', [
            'key' => 'addMeeting'
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        $this->assertStringNotContainsString('addMeeting', $updatedContent);
        $this->assertStringNotContainsString('TODO: translate', $updatedContent);
    }

    public function testDeletesFromAllLanguageFiles()
    {
        $content1 = <<<'TS'
export const testTranslations = {
  accountTitle: 'Account',
  cancel: 'Cancel'
};
TS;

        $content2 = <<<'TS'
export const test2Translations = {
  accountTitle: 'Compte',
  cancel: 'Annuler'
};
TS;

        $this->createTestTranslationFile($content1);
        $this->createTestTranslationFile($content2, $this->testTranslationFilePath2);

        $this->artisan('translation:delete', [
            'key' => 'accountTitle'
        ])->assertExitCode(0);

        $updatedContent1 = File::get($this->testTranslationFilePath);
        $updatedContent2 = File::get($this->testTranslationFilePath2);

        $this->assertStringNotContainsString('accountTitle', $updatedContent1);
        $this->assertStringNotContainsString('accountTitle', $updatedContent2);
    }

    public function testHandlesNonExistentKey()
    {
        $content = <<<'TS'
export const testTranslations = {
  accountTitle: 'Account',
  cancel: 'Cancel'
};
TS;

        $this->createTestTranslationFile($content);

        $this->artisan('translation:delete', [
            'key' => 'nonExistentKey'
        ])
            ->expectsOutputToContain("Key 'nonExistentKey' not found")
            ->assertExitCode(0);

        // Original content should be unchanged
        $updatedContent = File::get($this->testTranslationFilePath);
        $this->assertStringContainsString('accountTitle', $updatedContent);
        $this->assertStringContainsString('cancel', $updatedContent);
    }

    public function testPreservesCommasCorrectly()
    {
        $content = <<<'TS'
export const testTranslations = {
  accountTitle: 'Account',
  addMeeting: 'Add Meeting',
  cancel: 'Cancel',
  delete: 'Delete'
};
TS;

        $this->createTestTranslationFile($content);

        $this->artisan('translation:delete', [
            'key' => 'addMeeting'
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        // Verify proper comma structure remains
        $this->assertStringContainsString("accountTitle: 'Account',", $updatedContent);
        $this->assertStringContainsString("cancel: 'Cancel',", $updatedContent);
        $this->assertStringContainsString("delete: 'Delete'", $updatedContent);
    }

    public function testDeletesOnlyKey()
    {
        $content = <<<'TS'
export const testTranslations = {
  accountTitle: 'Account'
};
TS;

        $this->createTestTranslationFile($content);

        $this->artisan('translation:delete', [
            'key' => 'accountTitle'
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        // Should have empty object (but not necessarily with newline)
        $this->assertMatchesRegularExpression('/\{\s*\}/', $updatedContent);
        $this->assertStringNotContainsString('accountTitle', $updatedContent);
    }

    public function testPreservesFileStructure()
    {
        $content = <<<'TS'
import type { LocaleObject } from 'yup';

export const testYupLocale: LocaleObject = {};

/*eslint sort-keys: ["error", "asc", {caseSensitive: false}]*/
export const testTranslations = {
  accountTitle: 'Account',
  addMeeting: 'Add Meeting',
  cancel: 'Cancel'
};
TS;

        $this->createTestTranslationFile($content);

        $this->artisan('translation:delete', [
            'key' => 'addMeeting'
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        // Should preserve imports and other exports
        $this->assertStringContainsString("import type { LocaleObject } from 'yup';", $updatedContent);
        $this->assertStringContainsString('export const testYupLocale', $updatedContent);
        $this->assertStringContainsString('/*eslint sort-keys:', $updatedContent);

        // But not the deleted key
        $this->assertStringNotContainsString('addMeeting', $updatedContent);
    }

    public function testDeletesFromMultipleFiles()
    {
        $content = <<<'TS'
export const testTranslations = {
  accountTitle: 'Account',
  cancel: 'Cancel'
};
TS;

        $this->createTestTranslationFile($content);
        $this->createTestTranslationFile($content, $this->testTranslationFilePath2);

        $this->artisan('translation:delete', [
            'key' => 'accountTitle'
        ])->assertExitCode(0);

        // Verify the key was deleted by checking file content immediately
        $updatedContent1 = File::get($this->testTranslationFilePath);
        $updatedContent2 = File::get($this->testTranslationFilePath2);

        $this->assertStringNotContainsString('accountTitle', $updatedContent1);
        $this->assertStringNotContainsString('accountTitle', $updatedContent2);
        $this->assertStringContainsString('cancel', $updatedContent1);
        $this->assertStringContainsString('cancel', $updatedContent2);
    }

    public function testDeletesKeyWithApostrophe()
    {
        $content = <<<'TS'
export const testTranslations = {
  accountTitle: 'Account',
  confirmMessage: 'Yes, I\'m sure',
  cancel: 'Cancel'
};
TS;

        $this->createTestTranslationFile($content);

        $this->artisan('translation:delete', [
            'key' => 'confirmMessage'
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        $this->assertStringNotContainsString('confirmMessage', $updatedContent);
        $this->assertStringContainsString('accountTitle', $updatedContent);
        $this->assertStringContainsString('cancel', $updatedContent);
    }
}
