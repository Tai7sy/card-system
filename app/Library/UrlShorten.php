<?php

namespace App\Library;

class UrlShorten
{
    public static function shorten($long_url, $shorten_type = false)
    {
        if ($shorten_type === false) {
            $shorten_type = \App\System::_get('domain_shorten');
        }
        if ($shorten_type === '1' || $shorten_type === 'url.cn') {
            $shorten = UrlShorten::url_cn($long_url);
        } elseif ($shorten_type === '2' || $shorten_type === 't.cn') {
            $shorten = UrlShorten::t_cn($long_url);
        } elseif ($shorten_type === 'w.url.cn') {
            $shorten = UrlShorten::w_url_cn($long_url);
        } elseif ($shorten_type === 'custom') {
            $shorten = UrlShorten::custom($long_url);
        } else {
            return $long_url;
        }
    }


    /**
     * 获取新浪短连接 t.cn
     * @param $long_url
     * @return string
     */
    public static function t_cn_official($long_url)
    {
        $long_url = urlencode($long_url);
        $apiKey = '';
        $apiUrl = 'http://api.t.sina.com.cn/short_url/shorten.json?source=' . $apiKey . '&url_long=' . $long_url;
        $curlObj = curl_init();
        curl_setopt($curlObj, CURLOPT_URL, $apiUrl);
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlObj, CURLOPT_HEADER, 0);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        $response = curl_exec($curlObj);
        curl_close($curlObj);
        $json = json_decode($response, true);
        return (isset($json['url_short']) && strstr($json['url_short'], 'http://')) ? $json['url_short'] : null;
    }

    /**
     * 获取新浪短连接 t.cn
     * @param $long_url
     * @return string
     */
    public static function t_cn($long_url)
    {
        $long_url = urlencode($long_url);
        $apiUrl = 'https://i.alapi.cn/url/?url=' . $long_url;
        $curlObj = curl_init();
        curl_setopt($curlObj, CURLOPT_URL, $apiUrl);
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlObj, CURLOPT_HEADER, 0);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        $response = curl_exec($curlObj);
        curl_close($curlObj);
        $json = json_decode($response, true);
        return (isset($json['shortUrl']) && strstr($json['shortUrl'], 'http')) ? $json['shortUrl'] : null;
    }

    /**
     * 获取腾讯短连接 url.cn
     * @param $long_url
     * @return string
     */
    public static function url_cn($long_url)
    {
        $long_url = urlencode($long_url);
        $apiUrl = 'https://api.uomg.com/api/long2dwz?dwzapi=urlcn&url=' . $long_url;
        $curlObj = curl_init();
        curl_setopt($curlObj, CURLOPT_URL, $apiUrl);
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlObj, CURLOPT_HEADER, 0);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($curlObj, CURLOPT_TIMEOUT, 5);
        curl_setopt($curlObj, CURLOPT_CONNECTTIMEOUT, 5);
        $response = curl_exec($curlObj);
        curl_close($curlObj);
        $json = json_decode($response, true);
        return (isset($json['ae_url']) && strstr($json['ae_url'], 'http')) ? $json['ae_url'] : null;
    }

    /**
     * 微信自带的短网址系统, 需要微信公众号
     * @param $long_url
     */
    public static function w_url_cn($long_url)
    {
        // 开源版不支持
        return null;
    }

    public static function custom($long_url)
    {
        $api_id = '';
        $api_key = '';
        $long_url = urlencode($long_url);
        $apiUrl = 'http://api.his.cat/api/url/shorten.json?id=' . $api_id . '&key=' . $api_key . '&url=' . $long_url;
        $curlObj = curl_init();
        curl_setopt($curlObj, CURLOPT_URL, $apiUrl);
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlObj, CURLOPT_HEADER, 0);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($curlObj, CURLOPT_TIMEOUT, 5);
        curl_setopt($curlObj, CURLOPT_CONNECTTIMEOUT, 5);
        $response = curl_exec($curlObj);
        curl_close($curlObj);
        $json = json_decode($response, true);
        return (isset($json['data']) && isset($json['data']['short_url']) && strstr($json['data']['short_url'], 'http')) ? $json['data']['short_url'] : null;
    }
}