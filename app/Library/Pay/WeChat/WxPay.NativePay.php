<?php
require_once __DIR__ . "/lib/WxPay.Api.php";

/**
 *
 * 刷卡支付实现类
 * @author widyhu
 *
 */
class NativePay
{
    /**
     *
     * 生成扫描支付URL,模式一
     * @param $productId
     * @return string
     * @throws WxPayException
     */
    public function GetPrePayUrl($productId)
    {
        $biz = new WxPayBizPayUrl();
        $biz->SetProduct_id($productId);
        $values = WxpayApi::bizpayurl($biz);
        $url = "weixin://wxpay/bizpayurl?" . $this->ToUrlParams($values);
        return $url;
    }

    /**
     *
     * 参数数组转换为url参数
     * @param array $urlObj
     * @return string
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            $buff .= $k . "=" . $v . "&";
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 统一下单
     * @param \WxPayUnifiedOrder $input
     * @return string|成功时返回，其他抛异常
     * @throws WxPayException
     */
    public function unifiedOrder($input)
    {
        return WxPayApi::unifiedOrder($input);
    }
}