<?php


class ContentBuilder
{
    //第三方应用授权令牌
    private $appAuthToken;

    //异步通知地址(仅扫码支付使用)
    private $notifyUrl;

    public function setAppAuthToken($appAuthToken)
    {
        $this->appAuthToken = $appAuthToken;
    }

    public function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;
    }

    public function getAppAuthToken()
    {
        return $this->appAuthToken;
    }

    public function getNotifyUrl()
    {
        return $this->notifyUrl;
    }
}