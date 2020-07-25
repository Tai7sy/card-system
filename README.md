
### 关于

> 当前版本: 2.92

> [安装教程 V2.X](https://github.com/Tai7sy/card-system/wiki/BT%E9%9D%A2%E6%9D%BF%E5%AE%89%E8%A3%85%E6%95%99%E7%A8%8B---V2.X)

> [更新教程 V2.X](https://github.com/Tai7sy/card-system/wiki/V2.X-%E6%9B%B4%E6%96%B0%E6%95%99%E7%A8%8B)

> 支付网关: [card-gateway](https://github.com/Tai7sy/card-gateway)

> 广告: 商业版 [http://demo.fakaxitong.com/](http://demo.fakaxitong.com/)
### 常见问题
 - 修改 `.env` 文件无效 / 修改网站设置无效
 ```
 cd /www/wwwroot/example.com  #进入网站目录
 php artisan config:clear
 php artisan cache:clear
 ```
 - 忘记密码后重置
 ```
 cd /www/wwwroot/example.com  #进入网站目录
 php artisan reset:password admin@qq.com 123456
 ```
 - 500错误	
 ```	
 storage/logs 里面有错误详细内容, 可以自行参考解决, 或者附录log文件提交issue/mail	
 ```
