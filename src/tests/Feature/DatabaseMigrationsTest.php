<?php

namespace Tests\Feature;

use App\Http\Middleware\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseMigrationsTest extends TestCase
{
    use RefreshDatabase;

    public function testMigrationsShouldRun()
    {
        $middleware = app(DatabaseMigrations::class);
        $this->assertFalse($middleware->migrationsShouldRun());
    }
}
