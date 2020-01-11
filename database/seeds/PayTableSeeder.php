<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $sp243273 = new \App\Pay(); $sp243273->name = '支付宝'; $sp243273->driver = 'Fakala'; $sp243273->way = 'alipay'; $sp243273->comment = '安发卡支付渠道'; $sp243273->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '支付宝'; $sp243273->driver = 'Fakala'; $sp243273->way = 'alipaywap'; $sp243273->comment = '安发卡支付渠道'; $sp243273->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '微信'; $sp243273->driver = 'Fakala'; $sp243273->way = 'wx'; $sp243273->comment = '安发卡支付渠道'; $sp243273->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '微信'; $sp243273->driver = 'Fakala'; $sp243273->way = 'wxwap'; $sp243273->comment = '安发卡支付渠道'; $sp243273->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '支付宝'; $sp243273->driver = 'Alipay'; $sp243273->way = 'pc'; $sp243273->comment = '支付宝 - 即时到账套餐(企业)V2'; $sp243273->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '支付宝'; $sp243273->driver = 'Aliwap'; $sp243273->way = 'wap'; $sp243273->comment = '支付宝 - 高级手机网站支付V4'; $sp243273->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '支付宝扫码'; $sp243273->driver = 'AliAop'; $sp243273->way = 'f2f'; $sp243273->comment = '支付宝 - 当面付'; $sp243273->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '支付宝'; $sp243273->driver = 'AliAop'; $sp243273->way = 'pc'; $sp243273->comment = '支付宝 - 电脑网站支付 (新)'; $sp243273->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '手机支付宝'; $sp243273->driver = 'AliAop'; $sp243273->way = 'mobile'; $sp243273->comment = '支付宝 - 手机网站支付 (新)'; $sp243273->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '微信扫码'; $sp243273->driver = 'WeChat'; $sp243273->way = 'NATIVE'; $sp243273->comment = '微信支付 - 扫码'; $sp243273->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '微信扫码'; $sp243273->driver = 'WeChat'; $sp243273->way = 'JSAPI'; $sp243273->comment = '微信支付 - 扫码'; $sp243273->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '微信H5'; $sp243273->driver = 'WeChat'; $sp243273->way = 'MWEB'; $sp243273->comment = '微信支付 - H5 (需要开通权限)'; $sp243273->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '手机QQ'; $sp243273->driver = 'QPay'; $sp243273->way = 'NATIVE'; $sp243273->comment = '手机QQ - 扫码'; $sp243273->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '支付宝'; $sp243273->driver = 'Youzan'; $sp243273->way = 'alipay'; $sp243273->comment = '有赞支付 - 支付宝'; $sp243273->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '微信'; $sp243273->driver = 'Youzan'; $sp243273->way = 'wechat'; $sp243273->comment = '有赞支付 - 微信'; $sp243273->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '手机QQ'; $sp243273->driver = 'Youzan'; $sp243273->way = 'qq'; $sp243273->comment = '有赞支付 - 手机QQ'; $sp243273->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '支付宝'; $sp243273->driver = 'CodePay'; $sp243273->way = 'alipay'; $sp243273->comment = '码支付 - 支付宝'; $sp243273->config = '{
  "id": "id",
  "key": "key"
}'; $sp243273->fee_system = 0; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '微信'; $sp243273->driver = 'CodePay'; $sp243273->way = 'weixin'; $sp243273->comment = '码支付 - 微信'; $sp243273->config = '{
  "id": "id",
  "key": "key"
}'; $sp243273->fee_system = 0; $sp243273->save(); $sp243273 = new \App\Pay(); $sp243273->name = '手机QQ'; $sp243273->driver = 'CodePay'; $sp243273->way = 'qq'; $sp243273->comment = '码支付 - 手机QQ'; $sp243273->config = '{
  "id": "id",
  "key": "key"
}'; $sp243273->fee_system = 0; $sp243273->save(); $sp9a7c37 = new \App\PayWay(); $sp9a7c37->name = '前台支付方式-支付宝'; $sp9a7c37->img = '/plugins/images/ali.png'; $sp9a7c37->enabled = \App\PayWay::ENABLED_PC; $sp9a7c37->channels = array(array(\App\Pay::where('driver', 'AliAop')->where('way', 'pc')->first()->id, 1)); $sp9a7c37->saveOrFail(); $sp9a7c37 = new \App\PayWay(); $sp9a7c37->name = '前台支付方式-支付宝手机'; $sp9a7c37->img = '/plugins/images/ali.png'; $sp9a7c37->enabled = \App\PayWay::ENABLED_MOBILE; $sp9a7c37->channels = array(array(\App\Pay::where('driver', 'AliAop')->where('way', 'mobile')->first()->id, 1)); $sp9a7c37->saveOrFail(); $sp9a7c37 = new \App\PayWay(); $sp9a7c37->name = '前台支付方式-微信'; $sp9a7c37->img = '/plugins/images/wx.png'; $sp9a7c37->enabled = \App\PayWay::ENABLED_PC; $sp9a7c37->channels = array(array(\App\Pay::where('driver', 'WeChat')->where('way', 'NATIVE')->first()->id, 1)); $sp9a7c37->saveOrFail(); $sp9a7c37 = new \App\PayWay(); $sp9a7c37->name = '前台支付方式-微信手机'; $sp9a7c37->img = '/plugins/images/wx.png'; $sp9a7c37->enabled = \App\PayWay::ENABLED_MOBILE; $sp9a7c37->channels = array(array(\App\Pay::where('driver', 'WeChat')->where('way', 'JSAPI')->first()->id, 1)); $sp9a7c37->saveOrFail(); $sp9a7c37 = new \App\PayWay(); $sp9a7c37->name = '前台支付方式-QQ'; $sp9a7c37->img = '/plugins/images/qq.png'; $sp9a7c37->enabled = \App\PayWay::ENABLED_ALL; $sp9a7c37->channels = array(array(\App\Pay::where('driver', 'QPay')->first()->id, 1)); $sp9a7c37->saveOrFail(); } public function run() { self::initPay(); } }