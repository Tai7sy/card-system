<?php
 namespace Psy\Util; class Json { public static function encode($val, $opt = 0) { $opt |= JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE; return \json_encode($val, $opt); } } 