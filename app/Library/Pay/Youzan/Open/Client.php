<?php

namespace App\Library\Pay\Youzan\Open;

use Exception;

class Client
{
    private static $requestUrl = 'https://open.youzan.com/api/oauthentry/';
    private $accessToken;

    public function __construct($accessToken)
    {
        if ('' == $accessToken) throw new Exception('access_token不能为空');
        $this->accessToken = $accessToken;
    }

    public function get($method, $apiVersion, $params = array())
    {
        return $this->parseResponse(
            Http::get($this->url($method, $apiVersion), $this->buildRequestParams($method, $params))
        );
    }

    public function post($method, $apiVersion, $params = array(), $files = array())
    {
        return $this->parseResponse(
            Http::post($this->url($method, $apiVersion), $this->buildRequestParams($method, $params), $files)
        );
    }

    public function url($method, $apiVersion)
    {
        $method_array = explode(".", $method);
        $method = '/' . $apiVersion . '/' . $method_array[count($method_array) - 1];
        array_pop($method_array);
        $method = implode(".", $method_array) . $method;
        $url = self::$requestUrl . $method;
        return $url;
    }

    private function parseResponse($responseData)
    {
        $data = json_decode($responseData, true);
        if (null === $data) throw new Exception('response invalid, data: ' . $responseData);
        return $data;
    }

    private function buildRequestParams($method, $apiParams)
    {
        if (!is_array($apiParams)) $apiParams = array();
        $pairs = $this->getCommonParams($this->accessToken, $method);
        foreach ($apiParams as $k => $v) {
            if (isset($pairs[$k])) throw new Exception('参数名冲突');
            $pairs[$k] = $v;
        }

        return $pairs;
    }

    private function getCommonParams($accessToken, $method)
    {
        $params = array();
        $params[Protocol::TOKEN_KEY] = $accessToken;
        $params[Protocol::METHOD_KEY] = $method;
        return $params;
    }

}