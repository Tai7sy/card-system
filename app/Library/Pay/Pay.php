<?php

namespace App\Library\Pay;

class Pay
{

    //异步通知页面需要隐藏防止CC之类的验证导致返回失败
    private $url_notify = '';
    private $url_return = '';
    /**
     * @var ApiInterface
     */
    private $driver = null;

    public function __construct()
    {
        $this->url_notify = SYS_URL_API;
        $this->url_return = SYS_URL;
    }

    /**
     * @param \App\Pay $payway
     * @param $order_no
     * @param string $subject
     * @param string $body
     * @param int $amount 单位 分
     * @return bool|string true | 失败原因
     * @throws \Exception
     */
    public function goPay($payway, $order_no, $subject, $body, $amount)
    {
        $this->driver = static::getDriver($payway->driver);

        $config = json_decode($payway->config, true);
        $config['payway'] = $payway->way;

        $this->driver->goPay($config, $order_no, $subject, $body, $amount);
        return true;
    }

    /**
     * @param string $driver
     * @return ApiInterface
     * @throws \Exception
     */
    public static function getDriver($driver)
    {
        $driverName = 'App\\Library\\Pay\\' . ucfirst($driver) . '\Api';
        if (!class_exists($driverName)) {
            throw new \Exception('支付方式未找到');
        }
        return new $driverName;
    }


}
