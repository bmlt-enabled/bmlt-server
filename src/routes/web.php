<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserInterfaceController;
use App\Http\Controllers\Legacy\LegacyAuthController;
use App\Http\Controllers\Query\ServerInfoXmlController;
use App\Http\Controllers\Query\SwitcherController;
use App\Http\Controllers\SemanticWorkshopController;

Route::get('semantic', [SemanticWorkshopController::class, 'get']);

// reimplemented serverInfo.xml
Route::get('/{moreSlashes}client_interface/serverInfo.xml', [ServerInfoXmlController::class, 'get'])
    ->where('moreSlashes', '/*'); // some old clients have repeating slashes at beginning of path

// reimplemented query apis
Route::get('/{moreSlashes}client_interface/{dataFormat}', [SwitcherController::class, 'get'])
    ->where('moreSlashes', '/*') // some old clients have repeating slashes at beginning of path
    ->middleware('json');

// reimplemented auth
Route::any('/{moreSlashes}local_server/server_admin/{dataFormat}.php', [LegacyAuthController::class, 'handle'])
    ->where('moreSlashes', '/*') // some old clients have repeating slashes at beginning of path
    ->where('dataFormat', 'json|xml');

// yap still has a code path that could try to auth at / or /index.php
Route::any('/', [LegacyAuthController::class, 'handle'])
    ->name('login');
Route::any('/{moreSlashes}index.php', [LegacyAuthController::class, 'handle'])
    ->where('moreSlashes', '/*'); // some old clients have repeating slashes at beginning of path

Route::get('{any}', [UserInterfaceController::class, 'all'])
    ->where('any', '.*');
