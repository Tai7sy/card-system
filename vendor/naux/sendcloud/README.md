<p align="center">
  <br>
  <b>创造不息，交付不止</b>
  <br>
  <a href="https://www.yousails.com">
    <img src="https://yousails.com/banners/brand.png" width=350>
  </a>
</p>

# Laravel-SendCloud
Laravel 5.X 的 SendCloud 驱动

##### 优点：
普通发送方式完全兼容官方用法，可随时修改配置文件改为其他驱动，而不需要改动业务代码

## 安装

在项目目录下执行

```
composer require naux/sendcloud
```

## 配置

修改 `config/app.php`，添加服务提供者

```php
'providers' => [
   // 添加这行
    Naux\Mail\SendCloudServiceProvider::class,
];
```

在 `.env` 中配置你的密钥， 并修改邮件驱动为 `sendcloud`

```ini
MAIL_DRIVER=sendcloud

SEND_CLOUD_USER=   # 创建的 api_user
SEND_CLOUD_KEY=    # 分配的 api_key
```

## 使用

#### 普通发送：
用法完全和系统自带的一样, 具体请参照官方文档： http://laravel.com/docs/5.1/mail

```php
Mail::send('emails.welcome', $data, function ($message) {
    $message->from('us@example.com', 'Laravel');

    $message->to('foo@example.com')->cc('bar@example.com');
});
```

#### 模板发送
用法和普通发送类似，不过需要将 `body` 设置为 `SendCloudTemplate` 对象

> 使用模板发送不与其他邮件驱动兼容 ！！！

```php
// 模板变量
$bind_data = ['url' => 'http://naux.me'];
$template = new SendCloudTemplate('模板名', $bind_data);

Mail::raw($template, function ($message) {
    $message->from('us@example.com', 'Laravel');

    $message->to('foo@example.com')->cc('bar@example.com');
});
```
