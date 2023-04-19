<?php

echo 'debug: ' . __LINE__;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

echo 'debug: ' . __LINE__;
/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

echo 'debug: ' . __LINE__; system('echo debug: '.__LINE__.'>>/app/storage/logs/laravel.log');
/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

echo 'debug: ' . __LINE__; system('echo debug: '.__LINE__.'>>/app/storage/logs/laravel.log');
/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

echo 'debug: ' . __LINE__; system('echo debug: '.__LINE__.'>>/app/storage/logs/laravel.log');
$kernel = $app->make(Kernel::class);

echo 'debug: ' . __LINE__; system('echo debug: '.__LINE__.'>>/app/storage/logs/laravel.log');
$response = $kernel->handle(
    $request = Request::capture()
)->send();

echo 'debug: ' . __LINE__; system('echo debug: '.__LINE__.'>>/app/storage/logs/laravel.log');
$kernel->terminate($request, $response);
