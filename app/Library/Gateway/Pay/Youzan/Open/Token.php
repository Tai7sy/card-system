<?php

namespace Gateway\Pay\Youzan\Open;

use Exception;

class Token
{
    private $clientId;
    private $clientSecret;
    private $accessToken;
    private $refreshToken;

    private static $requestUrl = 'https://open.youzan.com/oauth/token';

    public function __construct($clientId, $clientSecret, $accessToken = null, $refreshToken = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }

    /**
     * 获取access_token
     *
     * @param $type
     * @param array $keys
     *
     * @return mixed
     */
    public function getToken($type, $keys = array())
    {
        $params = array();
        $params['client_id'] = $this->clientId;
        $params['client_secret'] = $this->clientSecret;
        if ($type === 'oauth') {
            $params['grant_type'] = 'authorization_code';
            $params['code'] = $keys['code'];
            $params['redirect_uri'] = $keys['redirect_uri'];
        } elseif ($type === 'refresh_token') {
            $params['grant_type'] = 'refresh_token';
            $params['refresh_token'] = $keys['refresh_token'];
        } elseif ($type === 'self') {
            $params['grant_type'] = 'silent';
            $params['kdt_id'] = $keys['kdt_id'];
        } elseif ($type === 'platform_init') {
            $params['grant_type'] = 'authorize_platform';
        } elseif ($type === 'platform') {
            $params['grant_type'] = 'authorize_platform';
            $params['kdt_id'] = $keys['kdt_id'];
        }

        return $this->parseResponse(
            Http::post(self::$requestUrl, $params)
        );
    }

    private function parseResponse($responseData)
    {
        $data = json_decode($responseData, true);
        if (null === $data) throw new Exception('response invalid, data: ' . $responseData);
        return $data;
    }
}