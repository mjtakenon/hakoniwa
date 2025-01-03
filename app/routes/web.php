<?php

use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$baseMiddleware = [
    \App\Http\Middleware\MaintenanceFilter::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Http\Middleware\FrameGuard::class,
    'throttle:web',
];

Route::prefix('/')->middleware($baseMiddleware)->group( function() {
    Route::get('', [\App\Http\Controllers\Web\IndexController::class, 'get'])->name(RouteServiceProvider::ROUTE_HOME);
});

Route::prefix('/islands')->middleware($baseMiddleware)->group( function() {
    Route::get('{island_id}', [\App\Http\Controllers\Web\Islands\DetailController::class, 'get'])
        ->whereNumber('island_id');
});

Route::prefix('/islands')->middleware(array_merge($baseMiddleware, ['auth:sanctum']))->group( function() {
    Route::get('{island_id}/plans', [\App\Http\Controllers\Web\Islands\PlansController::class, 'get'])
        ->whereNumber('island_id');
});

Route::prefix('/register')->middleware(array_merge($baseMiddleware, ['auth:sanctum']))->group( function() {
    Route::get('', [\App\Http\Controllers\Web\Register\IndexController::class, 'get'])->name(RouteServiceProvider::ROUTE_REGISTER);
    Route::post('', [\App\Http\Controllers\Web\Register\IndexController::class, 'post'])->middleware(['throttle:register_islands']);
});

Route::prefix('/settings')->middleware(array_merge($baseMiddleware, ['auth:sanctum']))->group( function() {
    Route::get('', [\App\Http\Controllers\Web\Settings\IndexController::class, 'get']);
});

Route::prefix('/logout')->middleware($baseMiddleware)->group( function() {
    Route::post('', [\App\Http\Controllers\Web\Logout\IndexController::class, 'post']);
});

Route::prefix('/releases')->middleware($baseMiddleware)->group( function() {
    Route::get('', [\App\Http\Controllers\Web\Releases\IndexController::class, 'get']);
});

Route::prefix('/privacy')->middleware($baseMiddleware)->group( function() {
    Route::get('', [\App\Http\Controllers\Web\Privacy\IndexController::class, 'get']);
});

Route::prefix('/help')->middleware($baseMiddleware)->group( function() {
    Route::get('', [\App\Http\Controllers\Web\Help\IndexController::class, 'get']);
});

Route::prefix('/auth/google/')->middleware($baseMiddleware)->group( function() {
    Route::get('redirect', [\App\Http\Controllers\Web\Auth\Google\RedirectController::class, 'get'])->name(RouteServiceProvider::ROUTE_LOGIN);
    Route::get('callback', [\App\Http\Controllers\Web\Auth\Google\CallbackController::class, 'get']);
});

Route::prefix('/auth/yahoo/')->middleware($baseMiddleware)->group( function () {
    Route::get('redirect', [\App\Http\Controllers\Web\Auth\YahooJapan\RedirectController::class, 'get']);
    Route::get('callback', [\App\Http\Controllers\Web\Auth\YahooJapan\CallbackController::class, 'get']);
});

Route::prefix('/auth/debug/')->middleware(array_merge($baseMiddleware, [\App\Http\Middleware\OnlyDebugMode::class]))->group( function () {
    Route::get('login', [\App\Http\Controllers\Web\Auth\Debug\LoginController::class, 'get']);
});
