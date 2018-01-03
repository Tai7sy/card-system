<?php

class AlipayF2FRefundResult
{
    private $tradeStatus;
    private $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function AlipayF2FPayResult($response)
    {
        $this->__construct($response);
    }

    public function setTradeStatus($tradeStatus)
    {
       $this->tradeStatus = $tradeStatus;
    }

    public function getTradeStatus()
    {
        return $this->tradeStatus;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}

?>