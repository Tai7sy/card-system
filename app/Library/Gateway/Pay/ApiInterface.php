<?php
/**
 * Created by PhpStorm.
 * User: Wind
 * Date: 2017/8/23
 * Time: 下午5:51
 */

namespace Gateway\Pay;


interface ApiInterface
{

    /**
     * 需要传入支付方式ID (因为一个支付方式可能添加了多次)
     * ApiInterface constructor.
     * @param $id
     */
    public function __construct($id);

    /**
     * 提交支付
     * @param array $config 支付渠道配置
     * @param string $order_no 本系统的订单号
     * @param string $subject 商品名称
     * @param string $body 商品介绍
     * @param int $amount_cent 金额/分
     */
    function goPay($config, $order_no, $subject, $body, $amount_cent);


    /**
     * 验证支付是否成功 <br>
     * $config['isNotify'] = true 则为支付成功后后台通知消息 <br>
     * $config['out_trade_no'] = 'xx' 则可能为二维码查询页面异步查询 <br>
     * 其余由情况为 支付成功后前台回调
     * @param array $config 支付渠道配置
     * @param callable $successCallback 成功回调 (系统单号,交易金额/分,支付渠道单号)
     * @return true|string true 验证通过  string 失败原因
     */
    function verify($config, $successCallback);

    /**
     * 退款操作
     * @param array $config 支付渠道配置
     * @param string $order_no 订单号
     * @param string $pay_trade_no 支付渠道流水号
     * @param int $amount_cent 金额/分
     * @return true|string true 退款成功  string 失败原因
     * @throws \Throwable
     */
     function refund($config, $order_no, $pay_trade_no, $amount_cent);

}