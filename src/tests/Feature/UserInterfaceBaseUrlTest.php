<?php

namespace Tests\Feature;

use App\Http\Controllers\UserInterfaceController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class UserInterfaceBaseUrlTest extends TestCase
{
    use RefreshDatabase;

    private function renderApiBaseUrl(string $requestUri, string $scriptName): string
    {
        $this->withoutVite(); // no Vite manifest is built during PHP tests

        $request = Request::create($requestUri, 'GET', server: [
            'SCRIPT_NAME' => $scriptName,
            'SCRIPT_FILENAME' => $scriptName,
            'PHP_SELF' => $scriptName,
        ]);

        return UserInterfaceController::handle($request)->getContent();
    }

    public function testApiBaseUrlDropsIndexPhpWhenLoadedThroughFrontController()
    {
        $content = $this->renderApiBaseUrl('/main_server/index.php', '/main_server/index.php');
        $this->assertStringContainsString("apiBaseUrl: '/main_server',", $content);
        $this->assertStringNotContainsString("apiBaseUrl: '/main_server/index.php'", $content);
    }

    public function testApiBaseUrlIsUnchangedWhenLoadedThroughCleanUrl()
    {
        $content = $this->renderApiBaseUrl('/main_server/', '/main_server/index.php');
        $this->assertStringContainsString("apiBaseUrl: '/main_server',", $content);
    }
}
