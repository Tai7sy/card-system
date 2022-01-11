<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '支付宝'; $sp3c5b44->driver = 'Fakala'; $sp3c5b44->way = 'alipay'; $sp3c5b44->comment = '安发卡支付渠道'; $sp3c5b44->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '支付宝'; $sp3c5b44->driver = 'Fakala'; $sp3c5b44->way = 'alipaywap'; $sp3c5b44->comment = '安发卡支付渠道'; $sp3c5b44->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '微信'; $sp3c5b44->driver = 'Fakala'; $sp3c5b44->way = 'wx'; $sp3c5b44->comment = '安发卡支付渠道'; $sp3c5b44->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '微信'; $sp3c5b44->driver = 'Fakala'; $sp3c5b44->way = 'wxwap'; $sp3c5b44->comment = '安发卡支付渠道'; $sp3c5b44->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '支付宝'; $sp3c5b44->driver = 'Alipay'; $sp3c5b44->way = 'pc'; $sp3c5b44->comment = '支付宝 - 即时到账套餐(企业)V2'; $sp3c5b44->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '支付宝'; $sp3c5b44->driver = 'Aliwap'; $sp3c5b44->way = 'wap'; $sp3c5b44->comment = '支付宝 - 高级手机网站支付V4'; $sp3c5b44->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '支付宝扫码'; $sp3c5b44->driver = 'AliAop'; $sp3c5b44->way = 'f2f'; $sp3c5b44->comment = '支付宝 - 当面付'; $sp3c5b44->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '支付宝'; $sp3c5b44->driver = 'AliAop'; $sp3c5b44->way = 'pc'; $sp3c5b44->comment = '支付宝 - 电脑网站支付 (新)'; $sp3c5b44->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '手机支付宝'; $sp3c5b44->driver = 'AliAop'; $sp3c5b44->way = 'mobile'; $sp3c5b44->comment = '支付宝 - 手机网站支付 (新)'; $sp3c5b44->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '微信扫码'; $sp3c5b44->driver = 'WeChat'; $sp3c5b44->way = 'NATIVE'; $sp3c5b44->comment = '微信支付 - 扫码'; $sp3c5b44->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '微信扫码'; $sp3c5b44->driver = 'WeChat'; $sp3c5b44->way = 'JSAPI'; $sp3c5b44->comment = '微信支付 - 扫码'; $sp3c5b44->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '微信H5'; $sp3c5b44->driver = 'WeChat'; $sp3c5b44->way = 'MWEB'; $sp3c5b44->comment = '微信支付 - H5 (需要开通权限)'; $sp3c5b44->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '手机QQ'; $sp3c5b44->driver = 'QPay'; $sp3c5b44->way = 'NATIVE'; $sp3c5b44->comment = '手机QQ - 扫码'; $sp3c5b44->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '支付宝'; $sp3c5b44->driver = 'Youzan'; $sp3c5b44->way = 'alipay'; $sp3c5b44->comment = '有赞支付 - 支付宝'; $sp3c5b44->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '微信'; $sp3c5b44->driver = 'Youzan'; $sp3c5b44->way = 'wechat'; $sp3c5b44->comment = '有赞支付 - 微信'; $sp3c5b44->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '手机QQ'; $sp3c5b44->driver = 'Youzan'; $sp3c5b44->way = 'qq'; $sp3c5b44->comment = '有赞支付 - 手机QQ'; $sp3c5b44->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '支付宝'; $sp3c5b44->driver = 'CodePay'; $sp3c5b44->way = 'alipay'; $sp3c5b44->comment = '码支付 - 支付宝'; $sp3c5b44->config = '{
  "id": "id",
  "key": "key"
}'; $sp3c5b44->fee_system = 0; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '微信'; $sp3c5b44->driver = 'CodePay'; $sp3c5b44->way = 'weixin'; $sp3c5b44->comment = '码支付 - 微信'; $sp3c5b44->config = '{
  "id": "id",
  "key": "key"
}'; $sp3c5b44->fee_system = 0; $sp3c5b44->save(); $sp3c5b44 = new \App\Pay(); $sp3c5b44->name = '手机QQ'; $sp3c5b44->driver = 'CodePay'; $sp3c5b44->way = 'qq'; $sp3c5b44->comment = '码支付 - 手机QQ'; $sp3c5b44->config = '{
  "id": "id",
  "key": "key"
}'; $sp3c5b44->fee_system = 0; $sp3c5b44->save(); $sp8d0c1f = new \App\PayWay(); $sp8d0c1f->name = '前台支付方式-支付宝'; $sp8d0c1f->img = '/plugins/images/ali.png'; $sp8d0c1f->enabled = \App\PayWay::ENABLED_PC; $sp8d0c1f->channels = array(array(\App\Pay::where('driver', 'AliAop')->where('way', 'pc')->first()->id, 1)); $sp8d0c1f->saveOrFail(); $sp8d0c1f = new \App\PayWay(); $sp8d0c1f->name = '前台支付方式-支付宝手机'; $sp8d0c1f->img = '/plugins/images/ali.png'; $sp8d0c1f->enabled = \App\PayWay::ENABLED_MOBILE; $sp8d0c1f->channels = array(array(\App\Pay::where('driver', 'AliAop')->where('way', 'mobile')->first()->id, 1)); $sp8d0c1f->saveOrFail(); $sp8d0c1f = new \App\PayWay(); $sp8d0c1f->name = '前台支付方式-微信'; $sp8d0c1f->img = '/plugins/images/wx.png'; $sp8d0c1f->enabled = \App\PayWay::ENABLED_PC; $sp8d0c1f->channels = array(array(\App\Pay::where('driver', 'WeChat')->where('way', 'NATIVE')->first()->id, 1)); $sp8d0c1f->saveOrFail(); $sp8d0c1f = new \App\PayWay(); $sp8d0c1f->name = '前台支付方式-微信手机'; $sp8d0c1f->img = '/plugins/images/wx.png'; $sp8d0c1f->enabled = \App\PayWay::ENABLED_MOBILE; $sp8d0c1f->channels = array(array(\App\Pay::where('driver', 'WeChat')->where('way', 'JSAPI')->first()->id, 1)); $sp8d0c1f->saveOrFail(); $sp8d0c1f = new \App\PayWay(); $sp8d0c1f->name = '前台支付方式-QQ'; $sp8d0c1f->img = '/plugins/images/qq.png'; $sp8d0c1f->enabled = \App\PayWay::ENABLED_ALL; $sp8d0c1f->channels = array(array(\App\Pay::where('driver', 'QPay')->first()->id, 1)); $sp8d0c1f->saveOrFail(); } public function run() { self::initPay(); } }