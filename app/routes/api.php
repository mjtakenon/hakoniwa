<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//
//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

$baseMiddleware = [
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Http\Middleware\FrameGuard::class,
    'auth:sanctum',
];

Route::prefix('/islands')->middleware($baseMiddleware)->group( function() {
    Route::get('{island_id}', [\App\Http\Controllers\Api\Islands\DetailController::class, 'get'])
        ->where('island_id', '[0-9]+');

    Route::middleware(['auth:sanctum'])->group(function() {
        Route::put('{island_id}/plans', [\App\Http\Controllers\Api\Islands\PlansController::class, 'put'])
            ->where('island_id', '[0-9]+');
    });
});
