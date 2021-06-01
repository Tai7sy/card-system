<?php

namespace Gateway\Pay\Youzan\Open;

class Http
{
    private static $boundary = '';

    public static function get($url, $params)
    {
        $url = $url . '?' . http_build_query($params);
        return self::http($url, 'GET');
    }

    public static function post($url, $params, $files = array())
    {
        $headers = array();
        if (!$files) {
            $body = http_build_query($params);
        } else {
            $body = self::buildHttpQueryMulti($params, $files);
            $headers[] = "Content-Type: multipart/form-data; boundary=" . self::$boundary;
        }
        return self::http($url, 'POST', $body, $headers);
    }

    private static function http($url, $method, $postFields = NULL, $headers = array())
    {
        $ci = curl_init();

        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_USERAGENT, 'X-YZ-Client 2.0.0 - PHP');
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_ENCODING, "");
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ci, CURLOPT_HEADER, FALSE);

        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($postFields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postFields);
                }
                break;
        }

        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);

        $response = curl_exec($ci);
        $httpCode = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $httpInfo = curl_getinfo($ci);

        curl_close($ci);
        return $response;
    }

    private static function buildHttpQueryMulti($params, $files)
    {
        if (!$params) return '';

        $pairs = array();

        self::$boundary = $boundary = uniqid('------------------');
        $MPBoundary = '--' . $boundary;
        $endMPBoundary = $MPBoundary . '--';
        $multipartBody = '';

        foreach ($params as $key => $value) {
            $multipartBody .= $MPBoundary . "\r\n";
            $multipartBody .= 'content-disposition: form-data; name="' . $key . "\"\r\n\r\n";
            $multipartBody .= $value . "\r\n";
        }

        foreach ($files as $key => $value) {
            if (!$value) {
                continue;
            }

            if (is_array($value)) {
                $url = $value['url'];
                if (isset($value['name'])) {
                    $filename = $value['name'];
                } else {
                    $parts = explode('?', basename($value['url']));
                    $filename = $parts[0];
                }
                $field = isset($value['field']) ? $value['field'] : $key;
            } else {
                $url = $value;
                $parts = explode('?', basename($url));
                $filename = $parts[0];
                $field = $key;
            }
            $content = file_get_contents($url);

            $multipartBody .= $MPBoundary . "\r\n";
            $multipartBody .= 'Content-Disposition: form-data; name="' . $field . '"; filename="' . $filename . '"' . "\r\n";
            $multipartBody .= "Content-Type: image/unknown\r\n\r\n";
            $multipartBody .= $content . "\r\n";
        }

        $multipartBody .= $endMPBoundary;
        return $multipartBody;
    }
}