<?php
/**
 * qpayMachAPI.php 业务调用方可做二次封装
 * Created by HelloWorld
 * vers: v1.0.0
 * User: Tencent.com
 */
require_once('qpayMchUtil.class.php');

class QpayMchAPI
{
    protected $url;
    protected $isSSL;
    protected $timeout;

    /**
     * QpayMchAPI constructor.
     *
     * @param string $url 接口url
     * @param boolean $isSSL 是否使用证书发送请求
     * @param int $timeout 超时
     */
    public function __construct($url, $isSSL, $timeout = 5)
    {
        $this->url = $url;
        $this->isSSL = $isSSL;
        $this->timeout = $timeout;
    }

    public function notify_params()
    {
        $xml = file_get_contents('php://input');
        return QpayMchUtil::xmlToArray($xml);
    }

    public function notify_verify($params, $config)
    {
        if (!isset($params['sign']))
            return false;
        $sign = QpayMchUtil::getSign($params, $config['mch_key']);
        return $sign === $params['sign'];
    }

    public function req($params, $config)
    {
        $ret = array();
        //商户号
        $params['mch_id'] = $config['mch_id'];
        //随机字符串
        $params['nonce_str'] = QpayMchUtil::createNoncestr();
        //签名
        $params['sign'] = QpayMchUtil::getSign($params, $config['mch_key']);
        //生成xml
        $xml = QpayMchUtil::arrayToXml($params);

        if (isset($this->isSSL)) {
            $ret = QpayMchUtil::reqByCurlSSLPost($xml, $this->url, $this->timeout, $config);
        } else {
            $ret = QpayMchUtil::reqByCurlNormalPost($xml, $this->url, $this->timeout);
        }
        return $ret;
    }

}