<?php

// Check before Composer loads dependencies that require newer PHP syntax.
if (PHP_VERSION_ID < 80200) {
    header('Content-Type: text/plain; charset=UTF-8');
    exit('PHP版本过低：当前版本为 ' . PHP_VERSION . '，请将网站 PHP 升级到 8.2 或更高版本后重试。');
}


// hack for aliyun cdn
if (isset($_SERVER['HTTP_ALI_SWIFT_LOG_HOST'])) {
    $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_ALI_SWIFT_LOG_HOST'];
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_ALI_SWIFT_LOG_HOST'];
}


/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

// Suppress deprecations from retained legacy dependencies during autoload.
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
require __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__ . '/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
