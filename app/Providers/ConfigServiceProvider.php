<?php

namespace App\Providers;

use App\System;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // config('filesystems.default');


        config()->set([
            'app.project' => 'card_free',
            'app.version' => '3.15',
        ]);

        try {
            config()->set([

                'app.name' => System::_get('app_name'),
                'app.title' => System::_get('app_title'),

                'app.url' => System::_get('app_url'),
                'app.url_api' => System::_get('app_url_api'),

                'app.logo' => System::_get('logo'),
                'app.description' => System::_get('description'),
                'app.keywords' => System::_get('keywords'),
                'app.company' => System::_get('company'),
                'app.icp' => System::_get('icp'),

                'services.geetest.id' => System::_get('vcode_geetest_id'),
                'services.geetest.key' => System::_get('vcode_geetest_key'),

                'mail.driver' => System::_get('mail_driver'),
                'mail.host' => System::_get('mail_smtp_host'),
                'mail.port' => System::_get('mail_smtp_port'),
                'mail.username' => System::_get('mail_smtp_username'),
                'mail.password' => System::_get('mail_smtp_password'),
                'mail.from.address' => System::_get('mail_smtp_from_address'),
                'mail.from.name' => System::_get('mail_smtp_from_name'),
                'mail.encryption' => System::_get('mail_smtp_encryption') === 'null' ? null : System::_get('mail_smtp_encryption'),
                'services.sendcloud.api_user' => System::_get('sendcloud_user'),
                'services.sendcloud.api_key' => System::_get('sendcloud_key'),

                'filesystems.default' => System::_get('storage_driver'),

                // 'filesystems.disks.local.url' => System::_get('app_url') . '/storage',

                'filesystems.disks.s3.key' => System::_get('storage_s3_access_key'),
                'filesystems.disks.s3.secret' => System::_get('storage_s3_secret_key'),
                'filesystems.disks.s3.region' => System::_get('storage_s3_region'),
                'filesystems.disks.s3.bucket' => System::_get('storage_s3_bucket'),

                'filesystems.disks.oss.access_id' => System::_get('storage_oss_access_key'),
                'filesystems.disks.oss.access_key' => System::_get('storage_oss_secret_key'),
                'filesystems.disks.oss.bucket' => System::_get('storage_oss_bucket'),
                'filesystems.disks.oss.endpoint' => System::_get('storage_oss_endpoint'),
                'filesystems.disks.oss.cdnDomain' => System::_get('storage_oss_cdn_domain'),
                'filesystems.disks.oss.ssl' => System::_getInt('storage_oss_is_ssl') === 1,
                'filesystems.disks.oss.isCName' => System::_getInt('storage_oss_is_cname') === 1,

                'filesystems.disks.qiniu.access_key' => System::_get('storage_qiniu_access_key'),
                'filesystems.disks.qiniu.secret_key' => System::_get('storage_qiniu_secret_key'),
                'filesystems.disks.qiniu.bucket' => System::_get('storage_qiniu_bucket'),
                'filesystems.disks.qiniu.domains.default' => System::_get('storage_qiniu_domains_default'),
                'filesystems.disks.qiniu.domains.https' => System::_get('storage_qiniu_domains_https'),

            ]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            if (strpos($error, "Base table or view not found:") !== FALSE && strpos($error, "`systems`") !== FALSE) {
                // 安装过程, 不校验
                return;
            }

            \Log::error('Config init failed: ' . $error, ['exception' => $e]);

            if (strpos($error, "'forge'@'localhost'") !== FALSE || strpos($error, "database 'forge'") !== FALSE) {
                $error .= "\r\n<br>
\r\n<br>
可能的原因：\r\n<br>
1. <code>.env</code>文件无法正常工作，请参考安装教程进行安装\r\n<br>
2. 目录权限没有正常配置，请参考安装教程进行安装\r\n<br>
3. 配置文件缓存未清理，请参考README进行清理<br>";
            }
            die("[FATAL ERROR] \r\n<br>" . $error);
        }


        if (!function_exists('gmp_add') && !function_exists('bcadd')) {
            die("[FATAL ERROR] \r\n<br> PHP环境不符合，请安装 BC Math 或 GMP 扩展.");
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
