## Require
PHP >= 5.6.4

## Install
 - Remember the root of website is `path_to_project/public` !
 - Change folder permission
```
chmod -R 666 storage/
chmod -R 666 bootstrap/cache/
```
 - Config .env file (according to yourself)
 - Import card.sql to your database
 - Optimize (Optional)
```
php artisan route:cache
php artisan config:cache
```
## Question
1. Changing the .env does not work?
 - After changing the .env, you have to run `php artisan config:cache`

</br>
</br>

## 要求
PHP >= 5.6.4

## 安装
 - 网页根目录是 `项目路径/public`, 配置网站时请注意!
 - 修改 .env 配置文件 (app名称, 数据库, 邮件等)
 - 将 card.sql 导入到数据库
 - 修改目录权限(Linux)
```
chmod -R 666 storage/
chmod -R 666 bootstrap/cache/
```
 - 优化 (可选)
```
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


