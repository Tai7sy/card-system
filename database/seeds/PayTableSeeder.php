<?php

use Illuminate\Database\Seeder;

class PayTableSeeder extends Seeder
{

    private function initPay()
    {
        $pay = new \App\Pay;
        $pay->name = '支付宝 电脑';
        $pay->driver = 'Fakala';
        $pay->way = 'alipay';
        $pay->comment = '安发卡支付 https://www.anfaka.com';
        $pay->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "你的 API_ID",
  "api_key": "你的 API_KEY"
}';
        $pay->enabled = true;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '支付宝 手机';
        $pay->driver = 'Fakala';
        $pay->way = 'alipaywap';
        $pay->comment = '安发卡支付 https://www.anfaka.com';
        $pay->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "你的 API_ID",
  "api_key": "你的 API_KEY"
}';
        $pay->enabled = true;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '微信 电脑';
        $pay->driver = 'Fakala';
        $pay->way = 'wx';
        $pay->comment = '安发卡支付 https://www.anfaka.com';
        $pay->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "你的 API_ID",
  "api_key": "你的 API_KEY"
}';
        $pay->enabled = true;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '微信 手机';
        $pay->driver = 'Fakala';
        $pay->way = 'wxwap';
        $pay->comment = '安发卡支付 https://www.anfaka.com';
        $pay->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "你的 API_ID",
  "api_key": "你的 API_KEY"
}';
        $pay->enabled = true;
        $pay->save();


        $pay = new \App\Pay;
        $pay->name = '支付宝';
        $pay->driver = 'Alipay';
        $pay->way = 'pc';
        $pay->comment = '支付宝 - 即时到账套餐(企业)V2';
        $pay->config = '{
  "partner": "partner",
  "key": "key"
}';
        $pay->enabled = true;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '支付宝';
        $pay->driver = 'Aliwap';
        $pay->way = 'wap';
        $pay->comment = '支付宝 - 高级手机网站支付V4';
        $pay->config = '{
  "partner": "partner",
  "key": "key"
}';
        $pay->enabled = true;
        $pay->save();


        $pay = new \App\Pay;
        $pay->name = '支付宝扫码';
        $pay->driver = 'AliAop';
        $pay->way = 'f2f';
        $pay->comment = '支付宝 - 当面付';
        $pay->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}';
        $pay->enabled = true;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '支付宝';
        $pay->driver = 'AliAop';
        $pay->way = 'pc';
        $pay->comment = '支付宝 - 电脑网站支付 (新)';
        $pay->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}';
        $pay->enabled = true;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '手机支付宝';
        $pay->driver = 'AliAop';
        $pay->way = 'mobile';
        $pay->comment = '支付宝 - 手机网站支付 (新)';
        $pay->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}';
        $pay->enabled = true;
        $pay->save();


        $pay = new \App\Pay;
        $pay->name = '微信扫码';
        $pay->driver = 'WeChat';
        $pay->way = 'NATIVE';
        $pay->comment = '微信支付 - 扫码';
        $pay->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}';
        $pay->enabled = true;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '微信扫码';
        $pay->driver = 'WeChat';
        $pay->way = 'JSAPI';
        $pay->comment = '微信支付 - 扫码';
        $pay->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}';
        $pay->enabled = true;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '微信H5';
        $pay->driver = 'WeChat';
        $pay->way = 'MWEB';
        $pay->comment = '微信支付 - H5 (需要开通权限)';
        $pay->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}';
        $pay->enabled = true;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '手机QQ';
        $pay->driver = 'QPay';
        $pay->way = 'NATIVE';
        $pay->comment = '手机QQ - 扫码';
        $pay->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}';
        $pay->enabled = true;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '支付宝';
        $pay->driver = 'Youzan';
        $pay->way = 'alipay';
        $pay->comment = '有赞支付 - 支付宝';
        $pay->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}';
        $pay->enabled = true;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '微信';
        $pay->driver = 'Youzan';
        $pay->way = 'wechat';
        $pay->comment = '有赞支付 - 微信';
        $pay->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}';
        $pay->enabled = true;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '手机QQ';
        $pay->driver = 'Youzan';
        $pay->way = 'qq';
        $pay->comment = '有赞支付 - 手机QQ';
        $pay->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}';
        $pay->enabled = true;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '支付宝';
        $pay->driver = 'CodePay';
        $pay->way = 'alipay';
        $pay->comment = '码支付 - 支付宝';
        $pay->config = '{
  "id": "id",
  "key": "key"
}';
        $pay->enabled = true;
        $pay->fee_system = 0;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '微信';
        $pay->driver = 'CodePay';
        $pay->way = 'weixin';
        $pay->comment = '码支付 - 微信';
        $pay->config = '{
  "id": "id",
  "key": "key"
}';
        $pay->enabled = true;
        $pay->fee_system = 0;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '手机QQ';
        $pay->driver = 'CodePay';
        $pay->way = 'qq';
        $pay->comment = '码支付 - 手机QQ';
        $pay->config = '{
  "id": "id",
  "key": "key"
}';
        $pay->enabled = true;
        $pay->fee_system = 0;
        $pay->save();

        $payway = new \App\PayWay;
        $payway->name = '前台支付方式-支付宝';
        $payway->img = '/plugins/images/ali.png';
        $payway->enabled = \App\PayWay::ENABLED_PC;
        $payway->channels = [[\App\Pay::where('driver', 'Fakala')->where('way', 'alipay')->first()->id, 1]];
        $payway->saveOrFail();

        $payway = new \App\PayWay;
        $payway->name = '前台支付方式-支付宝手机';
        $payway->img = '/plugins/images/ali.png';
        $payway->enabled = \App\PayWay::ENABLED_MOBILE;
        $payway->channels = [[\App\Pay::where('driver', 'Fakala')->where('way', 'alipaywap')->first()->id, 1]];
        $payway->saveOrFail();

        $payway = new \App\PayWay;
        $payway->name = '前台支付方式-微信';
        $payway->img = '/plugins/images/wx.png';
        $payway->enabled = \App\PayWay::ENABLED_PC;
        $payway->channels = [[\App\Pay::where('driver', 'Fakala')->where('way', 'wx')->first()->id, 1]];
        $payway->saveOrFail();

        $payway = new \App\PayWay;
        $payway->name = '前台支付方式-微信手机';
        $payway->img = '/plugins/images/wx.png';
        $payway->enabled = \App\PayWay::ENABLED_MOBILE;
        $payway->channels = [[\App\Pay::where('driver', 'Fakala')->where('way', 'wxwap')->first()->id, 1]];
        $payway->saveOrFail();

        $payway = new \App\PayWay;
        $payway->name = '前台支付方式-QQ';
        $payway->img = '/plugins/images/qq.png';
        $payway->enabled = \App\PayWay::ENABLED_ALL;
        $payway->channels = [[\App\Pay::where('driver', 'QPay')->first()->id, 1]];
        $payway->saveOrFail();
    }

    public function run()
    {
        self::initPay();
    }
}
