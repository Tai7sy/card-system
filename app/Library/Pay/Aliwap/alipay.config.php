<?php
/* *
 * 配置文件
 * 版本：3.4
 * 修改日期：2016-03-08
 */
 
$alipay_config['partner']= '2088421538865921';//合作身份者ID，
$alipay_config['seller_id']	= $alipay_config['partner'];//收款支付宝账号
$alipay_config['key']= 'afrcvn5nzugzful4cto4wme3cj0i5x27';// MD5密钥，安全检验码


$alipay_config['sign_type'] = strtoupper('MD5');//签名方式
$alipay_config['input_charset']= strtolower('utf-8');//字符编码格式 目前支持utf-8


//ca证书路径地址，用于curl中ssl校验,  请保证cacert.pem文件在当前文件夹目录中
$alipay_config['cacert']    = getcwd().'\\cacert.pem';
//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$alipay_config['transport']    = 'https';


$alipay_config['payment_type'] = "1";// 支付类型 ，无需修改
$alipay_config['service'] = "alipay.wap.create.direct.pay.by.user";// 产品类型，无需修改


?>