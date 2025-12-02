<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AddTranslationKeyTest extends TestCase
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

    public function testAddsKeyInAlphabeticalOrder()
    {
        $content = <<<'TS'
export const testTranslations = {
  accountTitle: 'Account',
  cancel: 'Cancel',
  delete: 'Delete'
};
TS;

        $this->createTestTranslationFile($content);

        $this->artisan('translation:add', [
            'key' => 'addMeeting',
            'value' => 'Add Meeting'
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        // Should be inserted between accountTitle and cancel (with TODO comment for non-English files)
        $this->assertStringContainsString("addMeeting: 'Add Meeting', // TODO: translate", $updatedContent);
        $this->assertStringContainsString("accountTitle: 'Account'", $updatedContent);
        $this->assertStringContainsString("cancel: 'Cancel'", $updatedContent);
    }

    public function testAddsKeyAtBeginning()
    {
        $content = <<<'TS'
export const testTranslations = {
  cancel: 'Cancel',
  delete: 'Delete'
};
TS;

        $this->createTestTranslationFile($content);

        $this->artisan('translation:add', [
            'key' => 'accountTitle',
            'value' => 'Account'
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        // With TODO comment for non-English files
        $this->assertStringContainsString("accountTitle: 'Account', // TODO: translate", $updatedContent);
        $this->assertStringContainsString("cancel: 'Cancel'", $updatedContent);
        
        // Verify proper formatting with newline after opening brace
        $this->assertStringContainsString("{\n  accountTitle:", $updatedContent);
        $this->assertStringNotContainsString("{ \n  accountTitle:", $updatedContent);
    }

    public function testAddsKeyAtEnd()
    {
        $content = <<<'TS'
export const testTranslations = {
  accountTitle: 'Account',
  cancel: 'Cancel'
};
TS;

        $this->createTestTranslationFile($content);

        $this->artisan('translation:add', [
            'key' => 'zipCode',
            'value' => 'Zip Code'
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        // With TODO comment for non-English files
        $this->assertStringContainsString("zipCode: 'Zip Code', // TODO: translate", $updatedContent);
    }

    public function testAddsKeyToEmptyTranslations()
    {
        $content = <<<'TS'
export const testTranslations = {
};
TS;

        $this->createTestTranslationFile($content);

        $this->artisan('translation:add', [
            'key' => 'accountTitle',
            'value' => 'Account'
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        // With TODO comment for non-English files
        $this->assertStringContainsString("accountTitle: 'Account', // TODO: translate", $updatedContent);
    }

    public function testEscapesApostrophesInValue()
    {
        $content = <<<'TS'
export const testTranslations = {
  cancel: 'Cancel'
};
TS;

        $this->createTestTranslationFile($content);

        $this->artisan('translation:add', [
            'key' => 'confirmMessage',
            'value' => "Yes, I'm sure"
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        $this->assertStringContainsString("confirmMessage: 'Yes, I\\'m sure'", $updatedContent);
    }

    public function testRejectsInvalidKeyFormat()
    {
        $content = <<<'TS'
export const testTranslations = {
  cancel: 'Cancel'
};
TS;

        $this->createTestTranslationFile($content);

        // Key with underscore
        $this->artisan('translation:add', [
            'key' => 'invalid_key',
            'value' => 'Invalid'
        ])->assertExitCode(1);

        // Key starting with uppercase
        $this->artisan('translation:add', [
            'key' => 'InvalidKey',
            'value' => 'Invalid'
        ])->assertExitCode(1);

        // Key with spaces
        $this->artisan('translation:add', [
            'key' => 'invalid key',
            'value' => 'Invalid'
        ])->assertExitCode(1);
    }

    public function testAddsToAllLanguageFiles()
    {
        $content = <<<'TS'
export const testTranslations = {
  cancel: 'Cancel'
};
TS;

        $this->createTestTranslationFile($content);
        $this->createTestTranslationFile($content, $this->testTranslationFilePath2);

        $this->artisan('translation:add', [
            'key' => 'accountTitle',
            'value' => 'Account'
        ])->assertExitCode(0);

        $updatedContent1 = File::get($this->testTranslationFilePath);
        $updatedContent2 = File::get($this->testTranslationFilePath2);

        $this->assertStringContainsString("accountTitle: 'Account'", $updatedContent1);
        $this->assertStringContainsString("accountTitle: 'Account'", $updatedContent2);
    }

    public function testAddsToNonEnglishWithTodoComment()
    {
        $enContent = <<<'TS'
export const enTranslations = {
  cancel: 'Cancel'
};
TS;

        $itContent = <<<'TS'
export const itTranslations = {
  cancel: 'Annulla'
};
TS;

        File::put(resource_path('js/lang/en.ts'), $enContent);
        File::put(resource_path('js/lang/it.ts'), $itContent);

        $this->artisan('translation:add', [
            'key' => 'accountTitle',
            'value' => 'Account'
        ])->assertExitCode(0);

        $enUpdated = File::get(resource_path('js/lang/en.ts'));
        $itUpdated = File::get(resource_path('js/lang/it.ts'));

        // English should not have TODO comment
        $this->assertStringContainsString("accountTitle: 'Account',\n", $enUpdated);
        $this->assertStringNotContainsString("// TODO: translate", $enUpdated);

        // Italian should have TODO comment
        $this->assertStringContainsString("accountTitle: 'Account', // TODO: translate", $itUpdated);
    }

    public function testSkipsExistingKeyWithoutForce()
    {
        $content = <<<'TS'
export const testTranslations = {
  accountTitle: 'Account',
  cancel: 'Cancel'
};
TS;

        $this->createTestTranslationFile($content);

        $this->artisan('translation:add', [
            'key' => 'accountTitle',
            'value' => 'New Account'
        ])
            ->expectsOutputToContain("Key 'accountTitle' already exists")
            ->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        // Should still have original value
        $this->assertStringContainsString("accountTitle: 'Account'", $updatedContent);
        $this->assertStringNotContainsString("New Account", $updatedContent);
    }

    public function testUpdatesExistingKeyWithForce()
    {
        $content = <<<'TS'
export const testTranslations = {
  accountTitle: 'Account',
  cancel: 'Cancel'
};
TS;

        $this->createTestTranslationFile($content);

        $this->artisan('translation:add', [
            'key' => 'accountTitle',
            'value' => 'New Account',
            '--force' => true
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        $this->assertStringContainsString("accountTitle: 'New Account'", $updatedContent);
    }

    public function testCaseInsensitiveAlphabeticalOrdering()
    {
        $content = <<<'TS'
export const testTranslations = {
  accountTitle: 'Account',
  Cancel: 'Cancel',
  zipCode: 'Zip Code'
};
TS;

        $this->createTestTranslationFile($content);

        $this->artisan('translation:add', [
            'key' => 'addMeeting',
            'value' => 'Add Meeting'
        ])->assertExitCode(0);

        $updatedContent = File::get($this->testTranslationFilePath);

        // Should be inserted between accountTitle and Cancel (case-insensitive, with TODO comment)
        $this->assertStringContainsString("addMeeting: 'Add Meeting', // TODO: translate", $updatedContent);
        $this->assertStringContainsString("accountTitle: 'Account'", $updatedContent);
        $this->assertStringContainsString("Cancel: 'Cancel'", $updatedContent);
    }
}
