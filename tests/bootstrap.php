<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
require dirname(__DIR__) . '/vendor/autoload.php';

// Never migrate or seed a normal installation through the regression suite.
$database = getenv('DB_DATABASE') ?: '';
if (getenv('APP_ENV') !== 'testing' || !str_ends_with($database, '_test')) {
    throw new RuntimeException('Tests require APP_ENV=testing and a dedicated DB_DATABASE ending in _test.');
}
$originalArgv = $_SERVER['argv'];
$_SERVER['argv'] = ['artisan', 'migrate'];
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
if (!App\System::query()->exists()) {
    Illuminate\Support\Facades\Artisan::call('db:seed', [
        '--class' => Database\Seeders\SystemSeeder::class, '--force' => true,
    ]);
}
$_SERVER['argv'] = $originalArgv;

Illuminate\Foundation\Bootstrap\HandleExceptions::flushState();
