<h1 align="center">CardSystem</h1>
<p align="center">
<a href="https://github.com/Tai7sy/card-system/releases"><img src="https://img.shields.io/badge/version-3.12-blue.svg?style=flat-square" alt="License"></a>
<img alt="PHP from Packagist badge" src="https://img.shields.io/badge/php-%3E%3D7.0.0-brightgreen.svg?style=flat-square">
<a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square" alt="License"></a>
<a href="https://travis-ci.org/Tai7sy/card-system"><img src="https://img.shields.io/travis/Tai7sy/card-system.svg?style=flat-square" alt="Travis"></a>
<br><br>
<a href="https://github.com/Tai7sy/card-system/wiki/%E5%AE%89%E8%A3%85%E6%95%99%E7%A8%8B---BT%E9%9D%A2%E6%9D%BF">安装文档</a>&nbsp;&nbsp;
<a href="https://github.com/Tai7sy/card-system/wiki/%E6%9B%B4%E6%96%B0%E6%95%99%E7%A8%8B">更新文档</a>
</p>

## 介绍

一款高效安全的发卡平台。

支持的支付渠道：
* 支付宝（电脑支付/手机支付/当面付/V2版即时到账/V4版高级手机网站支付）
* 微信支付（扫码支付/H5支付/JSAPI支付）
* QQ钱包
* [商业版接口](https://www.anfaka.com/docs/api)
* [CoinPayments](https://www.coinpayments.net/index.php?ref=f818644d99b71d425b556573a5a44313)（支持上千种数字货币）
* Blockchain.com（BTC）
* [MugglePay](https://github.com/Tai7sy/card-gateway/tree/master/Pay/MugglePay)
* PayPal
* 付呗
* 有赞支付
* 恒隆支付
* PayJS
* 黔贵金服
* 云创支付
* [码支付](https://codepay.fateqq.com/i/29417)
* 即充宝/JCBPay
* 吉易支付/JIPAYS
* 思狐云支付/UigPay
* 幻兮支付
* ...

更多请参考 [card-gateway](https://github.com/Tai7sy/card-gateway)


## 常见问题
 - 修改 `.env` 文件无效 / 修改网站设置无效
 
   请清空网站缓存, 操作如下
   ```
   cd /www/wwwroot/example.com  #进入网站目录
   rm -rf storage/framework/cache/data/*
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   ```

 - 忘记密码后重置
   ```
   cd /www/wwwroot/example.com  #进入网站目录
   php artisan reset:password admin@qq.com 123456
   ```

 - 提示500错误 / 未知错误

   `storage/logs` 里面有错误详细内容, 可以自行参考解决, 或者附录log文件提交issue/mail	

 - 开启后台登录验证码无法登录, 如何关闭	

    修改数据库`systems`表的`vcode_login`字段为0, 然后参考第一个问题清空网站缓存



## 商业版

商业版在当前版本基础上增加了许多功能，是一款成熟完善的发卡平台系统，欢迎咨询。

测试站 [http://demo.fakaxitong.com/](http://demo.fakaxitong.com/)

