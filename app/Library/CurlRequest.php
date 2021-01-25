<?php

namespace App\Library;

use Illuminate\Support\Facades\Log;


class CurlRequest
{
    /**
     * @param $url
     * @param int $method 0=GET 1=POST
     * @param string $post_data
     * @param array $headers
     * @param int $timeout 单位 秒
     * @param string|false $cookie
     * @param int|bool $httpCode
     * @param string|false $error
     * @return string
     */
    public static function curl($url, $method = 0, $post_data = '', $headers = [], $timeout = 5, &$cookie = false, &$httpCode = false, &$error = false)
    {
        if (!isset($headers['Accept']) && !isset($headers['accept'])) {
            $headers['Accept'] = "*
    public static function get($url, $headers = [], $timeout = 5, &$cookie = false)
    {
        return self::curl($url, 0, '', $headers, $timeout, $cookie);
    }

    /**
     * @param string $url
     * @param string $post_data
     * @param array $headers
     * @param int $timeout 单位 秒
     * @param string|false $cookie
     * @return string
     */
    public static function post($url, $post_data = '', $headers = [], $timeout = 5, &$cookie = false)
    {
        return self::curl($url, 1, $post_data, $headers, $timeout, $cookie);
    }

    /**
     * @param string $url
     * @param string $filepath
     * @param bool $progress
     * @return bool
     * @throws \Exception
     */
    public static function download($url, $filepath, $progress = false)
    {
        // $this->comment($url . "\n --> \n" . $filepath);

        $fp = fopen($filepath, 'w+');
        if (!$fp) {
            throw new \Exception("cant open file: " . $filepath);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_BUFFERSIZE,128);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FILE, $fp);

        if ($progress) {
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function (
                $resource, $download_size, $downloaded, $upload_size, $uploaded
            ) {
                if ($download_size > 0)
                    echo("\r    download: " . sprintf('%.2f', $downloaded / $download_size * 100) . '%');
            }
            );
            curl_setopt($ch, CURLOPT_NOPROGRESS, false); // needed to make progress function work
        }
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36');
        if (defined('MY_PROXY')) {
            $proxy = MY_PROXY;
            $proxy_type = CURLPROXY_HTTP;
            if (strpos($proxy, 'http://') || strpos($proxy, 'https://')) {
                $proxy = str_replace('http://', $proxy, $proxy);
                $proxy = str_replace('https://', $proxy, $proxy);
                $proxy_type = CURLPROXY_HTTP;
            } elseif (strpos($proxy, 'socks4://')) {
                $proxy = str_replace('socks4://', $proxy, $proxy);
                $proxy_type = CURLPROXY_SOCKS4;
            } elseif (strpos($proxy, 'socks4a://')) {
                $proxy = str_replace('socks4a://', $proxy, $proxy);
                $proxy_type = CURLPROXY_SOCKS4A;
            } elseif (strpos($proxy, 'socks5://')) {
                $proxy = str_replace('socks5://', $proxy, $proxy);
                $proxy_type = CURLPROXY_SOCKS5_HOSTNAME;
            }

            curl_setopt($ch, CURLOPT_PROXY, $proxy); // '121.42.144.136:58082'
            curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy_type); // 默认值: CURLPROXY_HTTP
            //curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);

            if (defined('MY_PROXY_PASS'))
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, MY_PROXY_PASS);
        }
        curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            throw new \Exception("curl_error: " . curl_error($ch));
        }
        curl_close($ch);
        return true;
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
        return substr($newCookie, 0, strlen($newCookie) - 1);
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
