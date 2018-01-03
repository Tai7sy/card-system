<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{

    private function initPay(){
        $pay = new \App\Pay;
        $pay->name = '支付宝';
        $pay->img = '/plugins/images/ali.png';
        $pay->driver = 'Alipay';
        $pay->way = 'Alipay';
        $pay->config = '{
  "partner": "partner",
  "key": "key"
}';
        $pay->enabled = \App\Pay::ENABLED_PC;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '手机支付宝';
        $pay->img = '/plugins/images/ali.png';
        $pay->driver = 'Aliwap';
        $pay->way = 'Aliwap';
        $pay->config = '{
  "partner": "partner",
  "key": "key"
}';
        $pay->enabled = \App\Pay::ENABLED_MOBILE;
        $pay->save();


        $pay = new \App\Pay;
        $pay->name = '支付宝扫码(当面付)';
        $pay->img = '/plugins/images/ali.png';
        $pay->driver = 'Aliqr';
        $pay->way = 'Aliqr';
        $pay->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}';
        $pay->enabled = \App\Pay::ENABLED_ALL;
        $pay->save();


        $pay = new \App\Pay;
        $pay->name = '微信扫码支付';
        $pay->img = '/plugins/images/wx.png';
        $pay->driver = 'Wechat';
        $pay->way = 'Wechat';
        $pay->config = '{
  "APPID": "APPID",
  "MCHID": "商户ID",
  "KEY": "KEY",
  "APPSECRET": "APPSECRET"
}';
        $pay->enabled = \App\Pay::ENABLED_ALL;
        $pay->save();


    }

    private function initDataBase()
    {
        $group = new \App\Group;
        $group->name = '测试分组';
        $group->enabled = true;
        $group->save();
        $group_id = \App\Group::first()->id;


        $good = new \App\Good;
        $good->group_id = $group_id;
        $good->name = '测试商品';
        $good->description = '这里是测试商品的一段描述, 可以插入html文本';
        $good->all_count = 10;
        $good->price = 1;
        $good->enabled = true;
        $good->save();
        $goods_id = \App\Good::first()->id;

        $card = new \App\Card;
        $card->good_id = $goods_id;
        $card->card = '123456';
        $card->status = \App\Card::STATUS_NORMAL;
        $card->type = \App\Card::TYPE_REPEAT;
        $card->save();


        self::initPay();



        // set increase to 1000
        \App\Order::insert([
            'id' => 1000,
            'order_no' => '123456',
            'good_id' => $good->id,
            'count' => 0,
            'email' => '',
            'email_sent' => false,
            'amount' => 0,
            'pay_id' => 0,
            'paid' => false,
            'created_at' => date('Y-m-d H:i:s', time())
        ]);
        \App\Order::where('id', 1000)->delete();

    }


    private function check_readable_r($dir)
    {
        if (is_dir($dir)) {
            if (is_readable($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (!self::check_readable_r($dir . "/" . $object)) return false;
                        else continue;
                    }
                }
                echo $dir.'   ...... <span style="color: green">R</span><br>';
                return true;
            } else {
                echo $dir.'   ...... <span style="color: red">R</span><br>';
                return false;
            }

        } else if (file_exists($dir)) {
            return (is_readable($dir));
        }
        echo $dir.'   ...... 文件不存在<br>';
        return false;
    }

    private function check_writable_r($dir)
    {
        if (is_dir($dir)) {
            if (is_writable($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (!self::check_writable_r($dir . "/" . $object)) return false;
                        else continue;
                    }
                }
                echo $dir.'   ...... <span style="color: green">W</span><br>';
                return true;
            } else {
                echo $dir.'   ...... <span style="color: red">W</span><br>';
                return false;
            }

        } else if (file_exists($dir)) {
            return (is_writable($dir));
        }
        echo $dir.'   ...... 文件不存在<br>';
        return false;
    }

    private function checkPathPermission($path)
    {
        self::check_readable_r($path);
        self::check_writable_r($path);
    }



    public function install()
    {
        $var = [];

        $db = '';
        if (\App\User::count() == 0) {
            \App\User::create([
                'username' => 'admin',
                'password' => bcrypt('admin')
            ]);

            $db.= '管理账号已创建, 初始账号: admin 初始密码: admin<br>';

            self::initDataBase();

            $db.= '测试分组创建完毕<br>';
            $db.= '测试商品创建完毕<br>';
            $db.= '支付方式初始化完毕<br>';

            @ob_start();
            self::checkPathPermission(base_path('storage'));
            self::checkPathPermission(base_path('bootstrap/cache'));
            $var['permission'] = @ob_get_clean();

        }else{
            $db.= '产品已经安装，请勿重复安装<br>';
        }


        $var['db'] = $db;


        return view('install', [
            'var' => $var
        ]);
    }


    public function index()
    {
        return view('welcome');
    }

    public function login()
    {
        return response()->redirectTo('/#/admin/login');
    }

    public function admin()
    {
        return response()->redirectTo('/#/admin');
    }


    public function test()
    {
        $appName = config('mail.send');
        var_dump($appName);
        exit;
    }
}
