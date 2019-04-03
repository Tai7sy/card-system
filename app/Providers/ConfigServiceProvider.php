<?php

namespace App\Providers;

use App\System;
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

        if (app()->runningInConsole()) {
            return;
        }

        try {
            DB::connection()->getPdo();
            if (DB::table('migrations')->count() === 0) {
                die(" * No migration table, please install your website first.\r\n");
            }
            if (!Schema::hasTable('systems')) {
                die(" * No system table, please install your website first.\r\n");
            }
        } catch (\Exception $e) {
            if (config('database.connections.mysql.host') === '数据库地址') {
                die(" * Please change your config of database in '.env' file \r\n");
            } else {
                die(" * Database connection error: " . $e->getMessage() . "\r\n");
            }
        }

        try {
            config()->set([
                'app.name' => System::_get('app_name'),
                'app.url' => System::_get('app_url'),
                'app.url_api' => System::_get('app_url_api'),

                'services.geetest.id' => System::_get('vcode_geetest_id'),
                'services.geetest.key' => System::_get('vcode_geetest_key'),

                'mail.driver' => System::_get('mail_driver'),
                'mail.host' => System::_get('mail_smtp_host'),
                'mail.port' => System::_get('mail_smtp_port'),
                'mail.username' => System::_get('mail_smtp_username'),
                'mail.password' => System::_get('mail_smtp_password'),
                'mail.from.address' => System::_get('mail_smtp_from_address'),
                'mail.from.name' => System::_get('mail_smtp_from_name'),
                'mail.encryption' => System::_get('mail_smtp_encryption'),
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
                'filesystems.disks.oss.ssl' => (int)System::_get('storage_oss_is_ssl') === 1,
                'filesystems.disks.oss.isCName' => (int)System::_get('storage_oss_is_cname') === 1,

                'filesystems.disks.qiniu.access_key' => System::_get('storage_qiniu_access_key'),
                'filesystems.disks.qiniu.secret_key' => System::_get('storage_qiniu_secret_key'),
                'filesystems.disks.qiniu.bucket' => System::_get('storage_qiniu_bucket'),
                'filesystems.disks.qiniu.domains.default' => System::_get('storage_qiniu_domains_default'),
                'filesystems.disks.qiniu.domains.https' => System::_get('storage_qiniu_domains_https'),

            ]);
        } catch (\Exception $e) {
            \Log::error('Config init failed: ' . $e->getMessage(), ['exception' => $e]);
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
