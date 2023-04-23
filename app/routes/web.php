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

Route::prefix('/')->middleware(array_merge($baseMiddleware))->group( function() {
    Route::get('', [\App\Http\Controllers\IndexController::class, 'get'])->name('home');
});

Route::prefix('/islands')->middleware(array_merge($baseMiddleware))->group( function() {
    Route::get('{island_id}', [\App\Http\Controllers\Islands\DetailController::class, 'get'])
        ->where('island_id', '[0-9]+');
});

Route::prefix('/islands')->middleware(array_merge($baseMiddleware, ['auth:sanctum']))->group( function() {
    Route::get('{island_id}/plans', [\App\Http\Controllers\Islands\PlansController::class, 'get'])
        ->where('island_id', '[0-9]+');
    Route::put('{island_id}/plans', [\App\Http\Controllers\Islands\PlansController::class, 'put'])
        ->where('island_id', '[0-9]+');
//    Route::get('{island_id}/bbs', [\App\Http\Controllers\Islands\BbsController::class, 'get']);
//    Route::post('{island_id}/bbs', [\App\Http\Controllers\Islands\BbsController::class, 'post']);
});

Route::prefix('/register')->middleware(array_merge($baseMiddleware, ['auth:sanctum']))->group( function() {
    Route::get('', [\App\Http\Controllers\Register\IndexController::class, 'get']);
    Route::post('', [\App\Http\Controllers\Register\IndexController::class, 'post']);
});

Route::prefix('/logout')->middleware(array_merge($baseMiddleware))->group( function() {
    Route::post('', [\App\Http\Controllers\Logout\IndexController::class, 'post']);
});

Route::prefix('/auth/google/')->middleware(array_merge($baseMiddleware, []))->group( function() {
    Route::get('redirect', [\App\Http\Controllers\Auth\Google\RedirectController::class, 'get'])->name('login');
    Route::get('callback', [\App\Http\Controllers\Auth\Google\CallbackController::class, 'get']);
});

Route::prefix('/test/')->middleware()->group( function() {
    Route::get('{id}', [\App\Http\Controllers\Test\DetailController::class, 'get']);
});

//, ['auth:sanctum']
