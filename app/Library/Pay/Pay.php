<?php

namespace App\Library\Pay;


class Pay
{
    /**
     * @var ApiInterface
     */
    private $driver = null;

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
        // Log::debug('Pay.goPay, payway:' . $payway->driver . ', order_no:' . $order_no . ', amount:' . $amount);
        $this->driver = static::getDriver($payway->id, $payway->driver);

        $config = json_decode($payway->config, true);
        $config['payway'] = $payway->way;

        $this->driver->goPay($config, $order_no, $subject, $body, $amount);
        return true;
    }

    /**
     * @param string $pay_id
     * @param string $driver
     * @return ApiInterface
     * @throws \Exception
     */
    public static function getDriver($pay_id, $driver)
    {
        $driverName = 'App\\Library\\Pay\\' . ucfirst($driver) . '\Api';
        if (!class_exists($driverName)) {
            throw new \Exception('支付驱动未找到');
        }
        return new $driverName($pay_id);
    }


}
