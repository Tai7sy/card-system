> 商业版预览: [http://fakala.xyz/](http://fakala.xyz/)

> 开源版当前版本: 1.4

[BT面板安装教程](https://github.com/Tai7sy/card-system/wiki/BT%E9%9D%A2%E6%9D%BF%E5%AE%89%E8%A3%85%E6%95%99%E7%A8%8B)<br><br>



## Require
PHP >= 7.0.0

## Install
 - Remember the root of website is `path_to_project/public` !
 - Change folder permission
```
chmod -R 777 storage/
chmod -R 777 bootstrap/cache/
chmod -R 777 app/Library/Pay/Aliqr/f2fpay/log/ #if using Aliqr
chmod -R 777 app/Library/Pay/Wechat/logs/ #if using Wechat
```
 - Renamed the .env.example file to .env
 - Config .env file (according to yourself)
 - Import card.sql to your database
 - Optimize (Optional)
```
php artisan key:generate
php artisan route:cache
php artisan config:cache
```
## Question
1. Changing the .env does not work?
 - After changing the .env, you have to run `php artisan config:cache`

</br>
</br>

## 要求
PHP >= 7.0.0

## 安装
 - 网页根目录是 `项目路径/public`, 配置网站时请注意!
 - 复制 .env.example 到 .env
```
copy .env.example .env #win
cp .env.example .env #linux
```
 - 修改 .env 配置文件 (app名称, 数据库, 邮件等)
 - 将 card.sql 导入到数据库
 - 修改目录权限(Linux)
```
chmod -R 777 storage/
chmod -R 777 bootstrap/cache/
chmod -R 777 app/Library/Pay/Aliqr/f2fpay/log/ #如果使用了支付宝当面付
chmod -R 777 app/Library/Pay/Wechat/logs/ #如果使用了微信支付
```
 - 优化 (可选)
```
php artisan key:generate
php artisan route:cache
php artisan config:cache
```

## 说明
 - /admin 管理目录
 - 初始账号密码 admin
 - 目录权限一定要正确配置!

## Question
1. 修改 .env 不起作用?
- 修改 .env 之后, 需要运行 `php artisan config:cache`


