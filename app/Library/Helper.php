<?php


namespace App\Library;


class Helper
{
    public static function getMysqlDate($addDays = 0)
    {
        return date('Y-m-d', time() + $addDays * 24 * 3600);
    }

    /**
     * 获取真实IP地址
     * @return array|false|string
     */
    public static function getIP()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $IPAddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $IPAddress = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $IPAddress = @$_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $IPAddress = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("HTTP_CLIENT_IP")) {
                $IPAddress = getenv("HTTP_CLIENT_IP");
            } else {
                $IPAddress = getenv("REMOTE_ADDR");
            }
        }

        if (strpos($IPAddress, ',') !== FALSE) {
            $IPAddArr = explode(',', $IPAddress);
            return $IPAddArr[0];
        }
        return $IPAddress;
    }


    /**
     * 获取访问这个页面的ip, 如果是cdn的话返回cdn ip
     * @return array|false|string
     */
    public static function getClientIP()
    {
        if (isset($_SERVER)) {
            $IPAddress = $_SERVER["REMOTE_ADDR"];
        } else {
            $IPAddress = getenv("REMOTE_ADDR");
        }
        if (strpos($IPAddress, ',') !== FALSE) {
            $IPAddArr = explode(',', $IPAddress);
            return $IPAddArr[0];
        }
        return $IPAddress;
    }

    /**
     * @param string $str
     * @param string|array $words abc|def
     * @return bool|string
     */
    public static function filterWords($str, $words)
    {
        if (!$str) return false;

        if (!is_array($words)) {
            $words = explode('|', $words);
        }
        foreach ($words as $word) {
            if ($word && strpos($str, $word) !== FALSE) {
                return $word;
            }
        }

        return false;
    }

    public static function is_idcard($id_card)
    {
        if (strlen($id_card) == 18) {
            return self::idcard_checksum18($id_card);
        } elseif ((strlen($id_card) == 15)) {
            $id_card = self::idcard_15to18($id_card);
            return self::idcard_checksum18($id_card);
        } else {
            return false;
        }
    }

    // 计算身份证校验码，根据国家标准GB 11643-1999
    private static function idcard_verify_number($idcard_base)
    {
        if (strlen($idcard_base) != 17) {
            return false;
        }
        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++) {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }

    // 将15位身份证升级到18位
    private static function idcard_15to18($idcard)
    {
        if (strlen($idcard) != 15) {
            return false;
        } else {
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
                $idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
            } else {
                $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
            }
        }

        $idcard = $idcard . self::idcard_verify_number($idcard);
        return $idcard;
    }

    // 18位身份证校验码有效性检查
    private static function idcard_checksum18($idcard)
    {
        if (strlen($idcard) != 18) {
            return false;
        }
        $idcard_base = substr($idcard, 0, 17);
        if (self::idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
            return false;
        } else {
            return true;
        }
    }

    public static function strBetween($str, $mark1, $mark2)
    {
        $st = stripos($str, $mark1);
        if ($st === false) return '';
        $ed = stripos($str, $mark2, $st + strlen($mark1));
        if ($ed === false || $st >= $ed) return '';
        $len = strlen($mark1);
        $ret = substr($str, $st + $len, ($ed - $st - $len));
        return $ret;
    }

    public static function formatUrl($url){
        // 格式: http:// 开头 且末尾没有斜杠
        if (!starts_with($url, 'http://') && !starts_with($url, 'https://')) {
            $url = 'http://' . $url;
        }
        while (ends_with($url, '/')) {
            $url = substr($url, 0, -1);
        }
        return $url;
    }
}