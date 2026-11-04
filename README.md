<h1 align="center">CardSystem</h1>
<p align="center">
<a href="https://github.com/Tai7sy/card-system/releases"><img src="https://img.shields.io/badge/version-3.21-blue.svg?style=flat-square" alt="License"></a>
<img alt="PHP from Packagist badge" src="https://img.shields.io/badge/php-%3E%3D8.2-brightgreen.svg?style=flat-square">
<a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square" alt="License"></a>
<a href="https://github.com/Tai7sy/card-system/actions/workflows/ci.yml"><img src="https://github.com/Tai7sy/card-system/actions/workflows/ci.yml/badge.svg" alt="CI"></a>
<br><br>
<a href="https://github.com/Tai7sy/card-system/wiki/%E5%AE%89%E8%A3%85%E6%95%99%E7%A8%8B---BT%E9%9D%A2%E6%9D%BF">安装文档</a>&nbsp;&nbsp;
<a href="https://github.com/Tai7sy/card-system/wiki/%E6%9B%B4%E6%96%B0%E6%95%99%E7%A8%8B">更新文档</a>
</p>

## 介绍

一款高效安全的发卡平台。

## 支持的支付渠道

* 支付宝（电脑支付/手机支付/当面付/V2版即时到账/V4版高级手机网站支付）
* 微信支付（扫码支付/H5支付/JSAPI支付）
* QQ钱包
* 易支付
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

## 源码安装

环境：PHP 8.2+、Composer 2、MySQL 5.7+、Node.js 22+、npm、Git。
PHP 扩展要求见 `composer.json`。以下命令均在项目根目录执行。

```sh
git submodule update --init --recursive
composer install
npm ci
npm run build
```

复制 `.env.example` 为 `.env`，填写数据库配置后执行：

```sh
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
```

网站入口设为 `public`。默认后台账号 `admin@qq.com`，密码 `123456`，请立即修改。

## 源码更新

先备份数据库和 `.env`，处理本地修改后执行：

```sh
git pull --ff-only
git submodule update --init --recursive
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize:clear
```

更新时不要重新执行初始化种子。

## 开发与测试

在不同终端启动所需服务：

```sh
php artisan serve       # 后端：8000
npm run dev:admin       # 管理后台：8081
npm run dev:shop        # 商城：8080
```

API 默认代理至 `http://127.0.0.1:8000`，可通过对应前端目录 `.env.local` 中的 `DEV_API_TARGET` 修改。
单独编译前端使用 `npm run build:admin` 或 `npm run build:shop`。

测试前执行 `composer install`，并将 `DB_*` 配置指向名称以 `_test` 结尾的专用数据库：

```sh
composer test
composer test:aws        # 离线测试
```

## 构建发布包

需要 `tar`，并确保子模块已初始化、工作区及子模块无未提交修改。

```sh
composer build
```

输出：`build/releases/card-system-3.21.tar.gz`。

## 常见问题

以下命令均在网站根目录执行。

### 修改 `.env` 或网站设置后未生效

```sh
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### 忘记后台密码

替换为后台账号和新密码：

```sh
php artisan reset:password admin@example.com 'your-new-password'
```

### 出现 500 错误或未知错误

查看 `storage/logs`，按日志提示排查。分享日志前请隐去账号、密钥和个人信息。

### 开启后台登录验证码后无法登录

将 `systems` 表中 `name = vcode_login_admin` 的记录的 `value` 改为 `0`，然后清理缓存。

### 普通验证码请求返回 500

查看错误日志，检查 PHP 的 `gd`、`fileinfo` 等必要扩展是否启用。

## 许可证

[MIT](LICENSE) · [支付网关许可证](app/Library/Gateway/LICENSE)
