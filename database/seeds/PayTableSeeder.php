<?php

use Illuminate\Database\Seeder;

class PayTableSeeder extends Seeder
{

    private function initPay()
    {
        $pay = new \App\Pay;
        $pay->name = '支付宝';
        $pay->driver = 'Alidirect';
        $pay->way = 'alipay';
        $pay->comment = 'www.zfbjk.com - 支付宝';
        $pay->config = '{
  "id": "商户ID",
  "key": "商户密钥",
  "pid": "支付宝PID"
}';
        $pay->enabled = true;
        $pay->fee_system = 0;
        $pay->save();

        $pay = new \App\Pay;
        $pay->name = '微信';
        $pay->driver = 'Alidirect';
        $pay->way = 'weixin';
        $pay->comment = 'www.zfbjk.com - 微信';
        $pay->config = '{
  "id": "商户ID",
  "key": "商户密钥"
}';
        $pay->enabled = true;
        $pay->fee_system = 0;
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

        $payway = new \App\PayWay;
        $payway->name = '前台支付方式-支付宝';
        $payway->img = '/plugins/images/ali.png';
        $payway->enabled = \App\PayWay::ENABLED_PC;
        $payway->channels = [[\App\Pay::where('driver', 'Alidirect')->where('way', 'alipay')->first()->id, 1]];
        $payway->saveOrFail();

        $payway = new \App\PayWay;
        $payway->name = '前台支付方式-支付宝手机';
        $payway->img = '/plugins/images/ali.png';
        $payway->enabled = \App\PayWay::ENABLED_MOBILE;
        $payway->channels = [[\App\Pay::where('driver', 'Alidirect')->where('way', 'alipay')->first()->id, 1]];
        $payway->saveOrFail();

        $payway = new \App\PayWay;
        $payway->name = '前台支付方式-微信';
        $payway->img = '/plugins/images/wx.png';
        $payway->enabled = \App\PayWay::ENABLED_PC;
        $payway->channels = [[\App\Pay::where('driver', 'Alidirect')->where('way', 'weixin')->first()->id, 1]];
        $payway->saveOrFail();

        $payway = new \App\PayWay;
        $payway->name = '前台支付方式-微信手机';
        $payway->img = '/plugins/images/wx.png';
        $payway->enabled = \App\PayWay::ENABLED_MOBILE;
        $payway->channels = [[\App\Pay::where('driver', 'Alidirect')->where('way', 'weixin')->first()->id, 1]];
        $payway->saveOrFail();
    }

    public function run()
    {
        self::initPay();
    }
}
