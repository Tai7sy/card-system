<?php
//以下为日志

class WxLog
{
    public static function DEBUG($msg)
    {
        \Illuminate\Support\Facades\Log::debug($msg);
    }

    public static function WARN($msg)
    {
        \Illuminate\Support\Facades\Log::warning($msg);
    }

    public static function ERROR($msg)
    {
        $debugInfo = debug_backtrace();
        $stack = "[";
        foreach ($debugInfo as $key => $val) {
            if (array_key_exists("file", $val)) {
                $stack .= ",file:" . $val["file"];
            }
            if (array_key_exists("line", $val)) {
                $stack .= ",line:" . $val["line"];
            }
            if (array_key_exists("function", $val)) {
                $stack .= ",function:" . $val["function"];
            }
        }
        $stack .= "]";
        \Illuminate\Support\Facades\Log::error($stack . $msg);
    }

    public static function INFO($msg)
    {
        \Illuminate\Support\Facades\Log::info($msg);
    }
}


