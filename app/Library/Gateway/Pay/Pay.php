<?php

namespace Gateway\Pay;


class Pay
{
    /**
     * @param \App\Pay $payway
     * @return ApiInterface
     * @throws \Exception
     */
    public static function getDriver($payway)
    {
        if (!defined('SYS_NAME')) define('SYS_NAME', config('app.name'));
        if (!defined('SYS_URL')) define('SYS_URL', config('app.url'));
        if (!defined('SYS_URL_API')) define('SYS_URL_API', config('app.url_api'));

        $driverName = 'Gateway\\Pay\\' . ucfirst($payway->driver) . '\Api';
        if (!class_exists($driverName)) {
            throw new \Exception('支付驱动未找到');
        }
        return new $driverName($payway->id);
    }

}
