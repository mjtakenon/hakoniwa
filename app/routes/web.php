<?php

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
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Http\Middleware\FrameGuard::class,
];

Route::prefix('/')->middleware(array_merge($baseMiddleware, []))->group( function() {
    Route::get('', [\App\Http\Controllers\IndexController::class, 'get']);
});

Route::prefix('/auth/google/')->middleware(array_merge($baseMiddleware, []))->group( function() {
    Route::get('redirect', [\App\Http\Controllers\Auth\Google\RedirectController::class, 'get']);
    Route::get('callback', [\App\Http\Controllers\Auth\Google\CallbackController::class, 'get']);
});
