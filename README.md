## Require
PHP >= 7.0.0

## Install
 - Remember the root of website is `path_to_project/public` !
 - Renamed the .env.example file to .env
 - Change folder permission
```
chmod -R 666 storage/
chmod -R 666 bootstrap/cache/
chmod -R 777 app/Library/Pay/Aliqr/f2fpay/log/ #if using Aliqr
chmod -R 777 app/Library/Pay/Wechat/logs/ #if using Wechat
```
 - Config .env file (according to yourself)
 - Optimize
```
php composer.phar update --optimize-autoloader
php artisan key:generate
php artisan route:cache
php artisan config:cache
```
## Question
1. How to reinstall?
 - Run `php artisan migrate:fresh`
 - open your_host/install to initialize the database
2. Changing the .env does not work?
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
 - 修改目录权限(Linux)
```
chmod -R 666 storage/
chmod -R 666 bootstrap/cache/
chmod -R 777 app/Library/Pay/Aliqr/f2fpay/log/ #if using Aliqr
chmod -R 777 app/Library/Pay/Wechat/logs/ #if using Wechat
```
 - 优化
```
php composer.phar update --optimize-autoloader
php artisan key:generate
php artisan route:cache
php artisan config:cache
```
## Question
1. 如何重新安装?
- 运行 `php artisan migrate:fresh`
- 打开 /install 初始化数据库
2. 修改 .env 不起作用?
- 修改 .env 之后, 需要运行 `php artisan config:cache`


