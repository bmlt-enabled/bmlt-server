<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // This has to be set in middleware because laravel settings can be bootstrapped
        // before the database migrations have run.
        app()->setLocale(legacy_config('language', 'en'));
        return $next($request);
    }
}
