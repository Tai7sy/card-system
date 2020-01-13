<?php

use Illuminate\Database\Seeder;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sets = [
            'app_name' => 'XX小店',
            'app_title' => '自动发卡, 自动发货',
            'app_url' => 'http://www.example.com',
            'app_url_api' => 'http://www.example.com',

            'company' => '©2019 Windy',

            'keywords' => '在线发卡系统',
            'description' => '我是一个发卡系统, 这里填写描述',
            'shop_bkg' => 'http://api.izhao.me/img',
            'shop_ann' => '欢迎来到XXX小店',
            'shop_ann_pop' => '',
            'shop_inventory' => 1,



            'js_tj' => '<div style="display: none"><script src="https://s22.cnzz.com/z_stat.php?id=1272914459&web_id=1272914459" language="JavaScript"></script></div>',
            'js_kf' => '',


            'vcode_driver' => 'geetest',
            'vcode_login' => '0', // 登录验证码
            'vcode_shop_buy' => '0',   // 下单
            'vcode_shop_search' => '0',// 查询订单

            'storage_driver' => 'local',

            'order_query_day' => '30',
            'order_clean_unpay_open' => '0',
            'order_clean_unpay_day' => '7',

            'mail_driver' => 'smtp',
            'mail_smtp_host' => 'smtp.mailtrap.io',
            'mail_smtp_port' => '25',
            'mail_smtp_username' => 'xxx',
            'mail_smtp_password' => 'xxx',
            'mail_smtp_from_address' => 'hello@example.com',
            'mail_smtp_from_name' => 'test',
            'mail_smtp_encryption' => 'null'

        ];
        $systems = [];
        foreach ($sets as $k => $v) {
            $systems[] = [
                'name' => $k,
                'value' => $v
            ];
        }
        DB::table('systems')->insert($systems);
    }
}
