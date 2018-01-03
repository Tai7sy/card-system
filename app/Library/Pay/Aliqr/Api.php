<?php

namespace App\Library\Pay\Aliqr;

use App\Library\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

include 'Loader.php';

class Api implements ApiInterface
{
    //异步通知页面需要隐藏防止CC之类的验证导致返回失败
    private $url_notify = '';
    private $url_return = '';

    public function __construct()
    {
        $this->url_notify = SYS_URL_API . '/pay/notify/aliqr';
        $this->url_return = SYS_URL . '/pay/return/aliqr';
    }

    private function defineConfig(&$config)
    {
        $config['sign_type'] = 'RSA2';
        $config['charset'] = 'UTF-8';
        $config['gatewayUrl'] = 'https://openapi.alipay.com/gateway.do';

        $config['MaxQueryRetry'] = '10';
        $config['QueryDuration'] = '3';
        $config['notify_url'] = $this->url_notify;
    }

    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        $amount = sprintf('%.2f', $amount_cent / 100); //支付宝元为单位
        $this->defineConfig($config);

        var_dump($config);
        // 支付超时，线下扫码交易定义为5分钟
        $timeExpress = '5m';

        // 创建一个商品信息，参数含义分别为商品id（使用国标）、名称、单价（单位为分）、数量，如果需要添加商品类别，详见GoodsDetail
        $goods1 = new \GoodsDetail();
        $goods1->setGoodsId('card');
        $goods1->setGoodsName($subject);
        $goods1->setPrice($amount);
        $goods1->setQuantity(1);
        //得到商品1明细数组
        $goods1Arr = $goods1->getGoodsDetail();


        $goodsDetailList = array($goods1Arr);
        //第三方应用授权令牌,商户授权系统商开发模式下使用
        $appAuthToken = "";//根据真实值填写

        // 创建请求builder，设置请求参数
        $qrPayRequestBuilder = new \AlipayTradePrecreateContentBuilder();
        $qrPayRequestBuilder->setOutTradeNo($out_trade_no);
        $qrPayRequestBuilder->setTotalAmount($amount);
        $qrPayRequestBuilder->setTimeExpress($timeExpress);
        $qrPayRequestBuilder->setSubject($subject);
        $qrPayRequestBuilder->setBody($body);

        // (可选) 订单不可打折金额，可以配合商家平台配置折扣活动，如果酒水不参与打折，则将对应金额填写至此字段
        // 如果该值未传入,但传入了【订单总金额】,【打折金额】,则该值默认为【订单总金额】-【打折金额】
        // $qrPayRequestBuilder->setUndiscountableAmount($undiscountableAmount);

        // 业务扩展参数，目前可添加由支付宝分配的系统商编号(通过setSysServiceProviderId方法)，系统商开发使用,详情请咨询支付宝技术支持
        // $providerId = ""; //系统商pid,作为系统商返佣数据提取的依据
        // $extendParams = new \ExtendParams();
        // $extendParams->setSysServiceProviderId($providerId);
        // $extendParamsArr = $extendParams->getExtendParams();
        // $qrPayRequestBuilder->setExtendParams($extendParamsArr);

        $qrPayRequestBuilder->setGoodsDetailList($goodsDetailList);

        // (可选) 商户门店编号，通过门店号和商家后台可以配置精准到门店的折扣信息，详询支付宝技术支持
        // $qrPayRequestBuilder->setStoreId($storeId);

        // 商户操作员编号，添加此参数可以为商户操作员做销售统计
        // $qrPayRequestBuilder->setOperatorId($operatorId);

        // 支付宝的店铺编号
        // $qrPayRequestBuilder->setAlipayStoreId($alipayStoreId);

        $qrPayRequestBuilder->setAppAuthToken($appAuthToken);


        // 调用qrPay方法获取当面付应答
        $qrPay = new \AlipayTradeService($config);
        $qrPayResult = $qrPay->qrPay($qrPayRequestBuilder);

        //	根据状态值进行业务处理
        switch ($qrPayResult->getTradeStatus()) {
            case 'SUCCESS':
                $response = $qrPayResult->getResponse();
                //$qrcode = $qrPay->create_erweima_url($response->qr_code);
                //http://127.0.0.6/Home/Qrcode/qrcode?url=baidu.com
                header('location: /qrcode/pay/' . $out_trade_no . '/aliqr/' . base64_encode($response->qr_code));
                echo '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>正在跳转到支付渠道...</title></head><body><h1 style="text-align: center">正在跳转到支付渠道...</h1>';
                exit;
                break;
            case 'FAILED':
                if (!empty($qrPayResult->getResponse())) {
                    throw new \Exception('支付宝创建订单二维码失败!!!<br>' . serialize($qrPayResult->getResponse()));
                } else {
                    throw new \Exception('支付宝创建订单二维码失败!!!<br>未知错误');
                }
                break;
            case 'UNKNOWN':
                if (!empty($qrPayResult->getResponse())) {
                    throw new \Exception('系统异常，状态未知!!!<br>' . serialize($qrPayResult->getResponse()));
                } else {
                    throw new \Exception('系统异常，状态未知!!!<br>未知错误');
                }
                break;
            default:
                throw new \Exception('不支持的返回状态，创建订单二维码返回异常!!!');
                break;
        }
    }


    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        $this->defineConfig($config);

        $out_trade_no = trim(isset($config['out_trade_no']) ? $config['out_trade_no'] : $_REQUEST['out_trade_no']);

        // 构造查询业务请求参数对象
        $queryContentBuilder = new \AlipayTradeQueryContentBuilder();
        $queryContentBuilder->setOutTradeNo($out_trade_no);

        // 第三方应用授权令牌,商户授权系统商开发模式下使用
        // $queryContentBuilder->setAppAuthToken($appAuthToken);


        //初始化类对象，调用queryTradeResult方法获取查询应答
        $queryResponse = new \AlipayTradeService($config);
        $queryResult = $queryResponse->queryTradeResult($queryContentBuilder);

        //根据查询返回结果状态进行业务处理
        switch ($queryResult->getTradeStatus()) {
            case 'SUCCESS':
                $response = $queryResult->getResponse();
                $trade_no = $response->trade_no;//支付宝交易号
                $total_fee = (int)($response->receipt_amount * 100);
                $successCallback($out_trade_no, $total_fee, $trade_no);

                if ($isNotify) echo 'success';
                return true;

                break;
            case 'FAILED':
                if ($isNotify) echo 'success';
                return false;
                break;
            case 'UNKNOWN':
                if ($isNotify) echo 'success';
                return false;
                break;
            default:
                if ($isNotify) echo 'success';
                return false;
                break;
        }
    }
}