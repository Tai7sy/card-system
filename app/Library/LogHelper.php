<?php


namespace App\Library;


class LogHelper
{

    public static function setLogFile($file)
    {
        \Log::getMonolog()->setHandlers([]);
        \Log::useDailyFiles(storage_path() . '/logs/' . $file . '.log', 0, config('app.log_level'));
    }
}