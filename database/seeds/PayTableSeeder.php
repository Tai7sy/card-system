<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $spd46fd8 = new \App\Pay(); $spd46fd8->name = '支付宝'; $spd46fd8->img = '/plugins/images/ali.png'; $spd46fd8->driver = 'Fakala'; $spd46fd8->way = 'alipay'; $spd46fd8->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $spd46fd8->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $spd46fd8->enabled = \App\Pay::ENABLED_PC; $spd46fd8->save(); $spd46fd8 = new \App\Pay(); $spd46fd8->name = '支付宝'; $spd46fd8->img = '/plugins/images/ali.png'; $spd46fd8->driver = 'Fakala'; $spd46fd8->way = 'alipaywap'; $spd46fd8->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $spd46fd8->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $spd46fd8->enabled = \App\Pay::ENABLED_MOBILE; $spd46fd8->save(); $spd46fd8 = new \App\Pay(); $spd46fd8->name = '微信'; $spd46fd8->img = '/plugins/images/wx.png'; $spd46fd8->driver = 'Fakala'; $spd46fd8->way = 'wx'; $spd46fd8->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $spd46fd8->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $spd46fd8->enabled = \App\Pay::ENABLED_PC; $spd46fd8->save(); $spd46fd8 = new \App\Pay(); $spd46fd8->name = '微信'; $spd46fd8->img = '/plugins/images/wx.png'; $spd46fd8->driver = 'Fakala'; $spd46fd8->way = 'wxwap'; $spd46fd8->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $spd46fd8->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $spd46fd8->enabled = \App\Pay::ENABLED_MOBILE; $spd46fd8->save(); $spd46fd8 = new \App\Pay(); $spd46fd8->name = '支付宝'; $spd46fd8->img = '/plugins/images/ali.png'; $spd46fd8->driver = 'Alipay'; $spd46fd8->way = 'pc'; $spd46fd8->comment = '支付宝 - 即时到账套餐(企业)V2'; $spd46fd8->config = '{
  "partner": "partner",
  "key": "key"
}'; $spd46fd8->enabled = \App\Pay::ENABLED_DISABLED; $spd46fd8->save(); $spd46fd8 = new \App\Pay(); $spd46fd8->name = '支付宝'; $spd46fd8->img = '/plugins/images/ali.png'; $spd46fd8->driver = 'Aliwap'; $spd46fd8->way = 'wap'; $spd46fd8->comment = '支付宝 - 高级手机网站支付V4'; $spd46fd8->config = '{
  "partner": "partner",
  "key": "key"
}'; $spd46fd8->enabled = \App\Pay::ENABLED_DISABLED; $spd46fd8->save(); $spd46fd8 = new \App\Pay(); $spd46fd8->name = '支付宝扫码'; $spd46fd8->img = '/plugins/images/ali.png'; $spd46fd8->driver = 'AliAop'; $spd46fd8->way = 'f2f'; $spd46fd8->comment = '支付宝 - 当面付'; $spd46fd8->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $spd46fd8->enabled = \App\Pay::ENABLED_DISABLED; $spd46fd8->save(); $spd46fd8 = new \App\Pay(); $spd46fd8->name = '支付宝'; $spd46fd8->img = '/plugins/images/ali.png'; $spd46fd8->driver = 'AliAop'; $spd46fd8->way = 'pc'; $spd46fd8->comment = '支付宝 - 电脑网站支付 (新)'; $spd46fd8->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $spd46fd8->enabled = \App\Pay::ENABLED_DISABLED; $spd46fd8->save(); $spd46fd8 = new \App\Pay(); $spd46fd8->name = '手机支付宝'; $spd46fd8->img = '/plugins/images/ali.png'; $spd46fd8->driver = 'AliAop'; $spd46fd8->way = 'mobile'; $spd46fd8->comment = '支付宝 - 手机网站支付 (新)'; $spd46fd8->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $spd46fd8->enabled = \App\Pay::ENABLED_DISABLED; $spd46fd8->save(); $spd46fd8 = new \App\Pay(); $spd46fd8->name = '微信扫码'; $spd46fd8->img = '/plugins/images/wx.png'; $spd46fd8->driver = 'WeChat'; $spd46fd8->way = 'NATIVE'; $spd46fd8->comment = '微信支付 - 扫码'; $spd46fd8->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $spd46fd8->enabled = \App\Pay::ENABLED_DISABLED; $spd46fd8->save(); $spd46fd8 = new \App\Pay(); $spd46fd8->name = '微信H5'; $spd46fd8->img = '/plugins/images/wx.png'; $spd46fd8->driver = 'WeChat'; $spd46fd8->way = 'MWEB'; $spd46fd8->comment = '微信支付 - H5 (需要开通权限)'; $spd46fd8->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $spd46fd8->enabled = \App\Pay::ENABLED_DISABLED; $spd46fd8->save(); $spd46fd8 = new \App\Pay(); $spd46fd8->name = '手机QQ'; $spd46fd8->img = '/plugins/images/qq.png'; $spd46fd8->driver = 'QPay'; $spd46fd8->way = 'NATIVE'; $spd46fd8->comment = '手机QQ - 扫码'; $spd46fd8->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $spd46fd8->enabled = \App\Pay::ENABLED_DISABLED; $spd46fd8->save(); $spd46fd8 = new \App\Pay(); $spd46fd8->name = '支付宝'; $spd46fd8->img = '/plugins/images/ali.png'; $spd46fd8->driver = 'Youzan'; $spd46fd8->way = 'alipay'; $spd46fd8->comment = '有赞支付 - 支付宝'; $spd46fd8->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $spd46fd8->enabled = \App\Pay::ENABLED_DISABLED; $spd46fd8->save(); $spd46fd8 = new \App\Pay(); $spd46fd8->name = '微信'; $spd46fd8->img = '/plugins/images/wx.png'; $spd46fd8->driver = 'Youzan'; $spd46fd8->way = 'wechat'; $spd46fd8->comment = '有赞支付 - 微信'; $spd46fd8->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $spd46fd8->enabled = \App\Pay::ENABLED_DISABLED; $spd46fd8->save(); $spd46fd8 = new \App\Pay(); $spd46fd8->name = '手机QQ'; $spd46fd8->img = '/plugins/images/qq.png'; $spd46fd8->driver = 'Youzan'; $spd46fd8->way = 'qq'; $spd46fd8->comment = '有赞支付 - 手机QQ'; $spd46fd8->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $spd46fd8->enabled = \App\Pay::ENABLED_DISABLED; $spd46fd8->save(); $spd46fd8 = new \App\Pay(); $spd46fd8->name = '支付宝'; $spd46fd8->img = '/plugins/images/ali.png'; $spd46fd8->driver = 'CodePay'; $spd46fd8->way = 'alipay'; $spd46fd8->comment = '码支付 - 支付宝'; $spd46fd8->config = '{
  "id": "id",
  "key": "key"
}'; $spd46fd8->fee_system = 0; $spd46fd8->enabled = \App\Pay::ENABLED_DISABLED; $spd46fd8->save(); $spd46fd8 = new \App\Pay(); $spd46fd8->name = '微信'; $spd46fd8->img = '/plugins/images/wx.png'; $spd46fd8->driver = 'CodePay'; $spd46fd8->way = 'weixin'; $spd46fd8->comment = '码支付 - 微信'; $spd46fd8->config = '{
  "id": "id",
  "key": "key"
}'; $spd46fd8->fee_system = 0; $spd46fd8->enabled = \App\Pay::ENABLED_DISABLED; $spd46fd8->save(); $spd46fd8 = new \App\Pay(); $spd46fd8->name = '手机QQ'; $spd46fd8->img = '/plugins/images/qq.png'; $spd46fd8->driver = 'CodePay'; $spd46fd8->way = 'qq'; $spd46fd8->comment = '码支付 - 手机QQ'; $spd46fd8->config = '{
  "id": "id",
  "key": "key"
}'; $spd46fd8->fee_system = 0; $spd46fd8->enabled = \App\Pay::ENABLED_DISABLED; $spd46fd8->save(); } public function run() { self::initPay(); } }