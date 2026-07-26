<?php

return [
    'default' => env('LOG_CHANNEL', env('APP_LOG', 'daily')),
    'deprecations' => ['channel' => 'null', 'trace' => false],
    'channels' => [
        'single' => ['driver' => 'single', 'path' => storage_path('logs/card_' . PHP_SAPI . '.log'), 'level' => env('APP_LOG_LEVEL', 'debug')],
        'daily' => ['driver' => 'daily', 'path' => storage_path('logs/card_' . PHP_SAPI . '.log'), 'level' => env('APP_LOG_LEVEL', 'debug'), 'days' => 0],
        'syslog' => ['driver' => 'syslog', 'level' => env('APP_LOG_LEVEL', 'debug')],
        'errorlog' => ['driver' => 'errorlog', 'level' => env('APP_LOG_LEVEL', 'debug')],
        'null' => ['driver' => 'monolog', 'handler' => Monolog\Handler\NullHandler::class],
        'emergency' => ['path' => storage_path('logs/laravel.log')],
    ],
];
