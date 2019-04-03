<?php
 namespace Psy\Util; class Str { const UNVIS_RX = <<<'EOS'
/
    \\(?:
        ((?:040)|s)
        | (240)
        | (?: M-(.) )
        | (?: M\^(.) )
        | (?: \^(.) )
    )
/xS
EOS;
public static function unvis($input) { $output = \preg_replace_callback(self::UNVIS_RX, 'self::unvisReplace', $input); return \stripcslashes($output); } protected static function unvisReplace($match) { if (!empty($match[1])) { return "\x20"; } if (!empty($match[2])) { return "\xa0"; } if (isset($match[3]) && $match[3] !== '') { $chr = $match[3]; $cp = 0200; $cp |= \ord($chr); return \chr($cp); } if (isset($match[4]) && $match[4] !== '') { $chr = $match[4]; $cp = 0200; $cp |= ($chr === '?') ? 0177 : \ord($chr) & 037; return \chr($cp); } if (isset($match[5]) && $match[5] !== '') { $chr = $match[5]; $cp = 0; $cp |= ($chr === '?') ? 0177 : \ord($chr) & 037; return \chr($cp); } } } 