<?php

use Illuminate\Database\Seeder;

class PayTableSeeder extends Seeder
{

    private function initPay(){
        $pay = new \App\Pay;
        $pay->name = '支付宝';
        $pay->img = '/plugins/images/ali.png';
        $pay->driver = 'Alipay';
        $pay->way = 'Alipay';
        $pay->comment = '支付宝 - 即时到账套餐(企业)V2';
        $pay->config = '{
  "partner": "partner",
  "key": "key"
}';
        $pay->enabled = \App\Pay::ENABLED_PC;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '支付宝';
        $pay->img = '/plugins/images/ali.png';
        $pay->driver = 'Aliwap';
        $pay->way = 'Aliwap';
        $pay->comment = '支付宝 - 高级手机网站支付V4';
        $pay->config = '{
  "partner": "partner",
  "key": "key"
}';
        $pay->enabled = \App\Pay::ENABLED_MOBILE;
        $pay->save();


        $pay = new \App\Pay;
        $pay->name = '支付宝扫码';
        $pay->img = '/plugins/images/ali.png';
        $pay->driver = 'Aliqr';
        $pay->way = 'Aliqr';
        $pay->comment = '支付宝 - 当面付';
        $pay->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}';
        $pay->enabled = \App\Pay::ENABLED_ALL;
        $pay->save();


        $pay = new \App\Pay;
        $pay->name = '微信扫码';
        $pay->img = '/plugins/images/wx.png';
        $pay->driver = 'Wechat';
        $pay->way = 'NATIVE';
        $pay->comment = '微信支付 - 扫码';
        $pay->config = '{
  "APPID": "APPID",
  "MCHID": "商户ID",
  "KEY": "KEY",
  "APPSECRET": "APPSECRET"
}';
        $pay->enabled = \App\Pay::ENABLED_ALL;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '微信H5';
        $pay->img = '/plugins/images/wx.png';
        $pay->driver = 'Wechat';
        $pay->way = 'MWEB';
        $pay->comment = '微信支付 - H5 (需要开通权限)';
        $pay->config = '{
  "APPID": "APPID",
  "MCHID": "商户ID",
  "KEY": "KEY",
  "APPSECRET": "APPSECRET"
}';
        $pay->enabled = \App\Pay::ENABLED_MOBILE;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '手机QQ';
        $pay->img = '/plugins/images/qq.png';
        $pay->driver = 'QPay';
        $pay->way = 'NATIVE';
        $pay->comment = '手机QQ - 扫码';
        $pay->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}';
        $pay->enabled = \App\Pay::ENABLED_ALL;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '支付宝';
        $pay->img = '/plugins/images/ali.png';
        $pay->driver = 'Youzan';
        $pay->way = 'alipay';
        $pay->comment = '有赞支付 - 支付宝';
        $pay->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}';
        $pay->enabled = \App\Pay::ENABLED_ALL;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '微信';
        $pay->img = '/plugins/images/wx.png';
        $pay->driver = 'Youzan';
        $pay->way = 'wechat';
        $pay->comment = '有赞支付 - 微信';
        $pay->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}';
        $pay->enabled = \App\Pay::ENABLED_ALL;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '手机QQ';
        $pay->img = '/plugins/images/qq.png';
        $pay->driver = 'Youzan';
        $pay->way = 'qq';
        $pay->comment = '有赞支付 - 手机QQ';
        $pay->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}';
        $pay->enabled = \App\Pay::ENABLED_ALL;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '支付宝';
        $pay->img = '/plugins/images/ali.png';
        $pay->driver = 'CodePay';
        $pay->way = 'alipay';
        $pay->comment = '码支付 - 支付宝';
        $pay->config = '{
  "id": "id",
  "key": "key"
}';
        $pay->fee_system = 0;
        $pay->enabled = \App\Pay::ENABLED_ALL;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '微信';
        $pay->img = '/plugins/images/wx.png';
        $pay->driver = 'CodePay';
        $pay->way = 'weixin';
        $pay->comment = '码支付 - 微信';
        $pay->config = '{
  "id": "id",
  "key": "key"
}';
        $pay->fee_system = 0;
        $pay->enabled = \App\Pay::ENABLED_ALL;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '手机QQ';
        $pay->img = '/plugins/images/qq.png';
        $pay->driver = 'CodePay';
        $pay->way = 'qq';
        $pay->comment = '码支付 - 手机QQ';
        $pay->config = '{
  "id": "id",
  "key": "key"
}';
        $pay->fee_system = 0;
        $pay->enabled = \App\Pay::ENABLED_ALL;
        $pay->save();

    }
    public function run()
    {
        self::initPay();
    }
}
