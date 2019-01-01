<?php

namespace App\Library;

class CurlRequest
{
    /**
     * @param $url
     * @param int $method 0=GET 1=POST
     * @param string $post_data
     * @param string $cookie
     * @param array $headers
     * @param int $timeout 单位 秒
     * @return string
     */
    private static function curl($url, $method = 0, $post_data = '', &$cookie = '', $headers = [], $timeout = 5)
    {
        if (!isset($headers['Accept'])) {
            $headers['Accept'] = "*
    public static function get($url, &$cookie = '', $headers = [], $timeout = 5)
    {
        return self::curl($url, 0, '', $cookie, $headers, $timeout);
    }

    /**
     * @param string $url
     * @param string $post_data
     * @param string $cookie
     * @param array $headers
     * @param int $timeout
     * @return string
     */
    public static function post($url, $post_data = '', &$cookie = '', $headers = [], $timeout = 5)
    {
        return self::curl($url, 1, $post_data, $cookie, $headers, $timeout);
    }


    /**
     * 合并网页Cookie
     * @param string $old 旧Cookie
     * @param string $new 新Cookie
     * @return string 返回新Cookie
     */
    public static function combineCookie($old, $new)
    {
        $old_array = explode(';', $old);
        $new_array = explode(';', $new);
        foreach ($old_array as $line) {
            if (self::cookieIsExists($new_array, self::cookieGetName($line)) == false) {
                array_push($new_array, $line);
            }
        }
        $newCookie = '';
        foreach ($new_array as $line) {
            if (substr($line, -8, 8) != '=deleted' && strlen($line) > 1) {
                $newCookie .= $line . '; ';
            }
        }
        return substr($newCookie, 0, strlen($newCookie) - 2);
    }

    public static function cookieGetName($singleCookie)
    {
        $index = strpos($singleCookie, '=');
        return substr($singleCookie, 0, $index);
    }

    public static function cookieGetValue($singleCookie)
    {
        $index = strpos($singleCookie, '=');
        $b = substr($singleCookie, $index + 1, strlen($singleCookie) - $index);
        return $b;
    }

    /**
     * 网页_取单条Cookie
     * @param string $cookie
     * @param string $name
     * @param boolean $withName 附带名称 默认不带
     * @return string
     */
    public static function cookieGet($cookie, $name, $withName = false)
    {
        $cookie = str_replace(' ', '', $cookie);
        if (substr($cookie, -1, 1) != ';') {
            $cookie = ';' . $cookie . ';';
        } else {
            $cookie = ';' . $cookie;
        }
        $t = Helper::str_between($cookie, ';' . $name . '=', ';');
        if (!$withName || $t == '') {
            return $t;
        } else {
            return $name . '=' . $t;
        }
    }

    /**
     * 传入一个cookie名称 检查cookie数组中是否已存在
     * @param array $allCookie 所有cookie
     * @param string $searchName 要查找的cookie名
     * @return bool
     */
    private static function cookieIsExists($allCookie, $searchName)
    {
        foreach ($allCookie as $line) {
            if (self::cookieGetName($line) == $searchName) {
                return true;
            }
        }
        return false;
    }

    function test()
    {
        $b = self::combineCookie('a=1;b=2;c=3', 'c=5');
        var_dump($b);
    }
}