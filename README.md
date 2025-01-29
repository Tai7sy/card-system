<h1 align="center">CardSystem</h1>
<p align="center">
<a href="https://github.com/Tai7sy/card-system/releases"><img src="https://img.shields.io/badge/version-3.15-blue.svg?style=flat-square" alt="License"></a>
<img alt="PHP from Packagist badge" src="https://img.shields.io/badge/php-%3E%3D7.0.0-brightgreen.svg?style=flat-square">
<a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square" alt="License"></a>
<a href="https://app.travis-ci.com/github/Tai7sy/card-system"><img src="https://img.shields.io/travis/Tai7sy/card-system.svg?style=flat-square" alt="Travis"></a>
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
* [码支付](http://api3.xiuxiu888.com/i/29417)
* 即充宝/JCBPay
* 吉易支付/JIPAYS
* 思狐云支付/UigPay
* 幻兮支付
* [ZFBJK支付宝免签约辅助](http://www.zfbjk.com/show.asp?g=2&id=37214)
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
   
   目前可能会引发该错误可能是目录权限问题,请自行查阅本身在安装步骤中是否按照要求开放目录权限.
   
   还有一种情况可能就是新建文件夹的时候目录权限不够,可以查询日志查看哪个目录权限不够重新设置权限即可解决

 - 开启后台登录验证码无法登录, 如何关闭	

    修改数据库`systems`表的`vcode_login`字段为0, 然后参考第一个问题清空网站缓存
    
 - 使用网站自带验证码,驱动为普通验证码,请求中会弹窗提示500错误
    
    因为该验证码使用了PHP图片扩展,如果是宝塔安装请自行到宝塔的PHP扩展中安装'fileinfo',如果是手动PHP用户请自行安装该扩展库,具体安装请参考谷歌.
    
 - 因迁站/目录等原因致网站不读环境变量中的数据库信息
 
    包括迁站、目录、环境变更等都会导致出现该异常。原因是框架会将系统设置以文件的形式保存起来。env 函数也是读取有关 php 文件中的数组，所以迁站变更环境导致的无法读取数据库的问题需要清理相关文件后重新构造文件。常见的方法只有清理，没有构造，这就导致了部分 Issues 反馈数据库连接的名和用户名为 `forge` （默认无法读取环境变量对应两个值得默认值）的问题。有些人使用了不推荐的方式直接修改了默认的值。不建议的原因是程序更新、因为某些原因所致环境变量能够正常读取等导致再次出现异常。请使用下面的命令来解决该问题。

   ```
   cd /www/wwwroot/example.com  #进入网站目录
   rm -rf storage/framework/cache/data/*
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan config:cache
   ```
