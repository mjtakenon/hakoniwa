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
    \App\Http\Middleware\MaintenanceFilter::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Http\Middleware\FrameGuard::class,
    'throttle:api',
];

Route::prefix('/islands/{island_id}')->middleware(array_merge($baseMiddleware, ['auth:sanctum']))->group( function() {
    Route::get('/', [\App\Http\Controllers\Api\Islands\DetailController::class, 'get'])
        ->where('island_id', '[0-9]+');

    Route::patch('/', [\App\Http\Controllers\Api\Islands\DetailController::class, 'patch'])
        ->where('island_id', '[0-9]+');

    Route::put('/plans', [\App\Http\Controllers\Api\Islands\PlansController::class, 'put'])
        ->where('island_id', '[0-9]+');

    Route::post('/comments', [\App\Http\Controllers\Api\Islands\CommentsController::class, 'post'])
        ->middleware('throttle:update_comments')
        ->where('island_id', '[0-9]+');
});
