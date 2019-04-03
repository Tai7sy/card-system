<?php
 namespace Illuminate\Support\Facades; use Psr\Log\LoggerInterface; class Log extends Facade { protected static function getFacadeAccessor() { return LoggerInterface::class; } } 