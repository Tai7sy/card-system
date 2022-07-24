<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '支付宝'; $sp0aa32b->img = '/plugins/images/ali.png'; $sp0aa32b->driver = 'Fakala'; $sp0aa32b->way = 'alipay'; $sp0aa32b->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp0aa32b->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp0aa32b->enabled = \App\Pay::ENABLED_PC; $sp0aa32b->save(); $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '支付宝'; $sp0aa32b->img = '/plugins/images/ali.png'; $sp0aa32b->driver = 'Fakala'; $sp0aa32b->way = 'alipaywap'; $sp0aa32b->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp0aa32b->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp0aa32b->enabled = \App\Pay::ENABLED_MOBILE; $sp0aa32b->save(); $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '微信'; $sp0aa32b->img = '/plugins/images/wx.png'; $sp0aa32b->driver = 'Fakala'; $sp0aa32b->way = 'wx'; $sp0aa32b->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp0aa32b->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp0aa32b->enabled = \App\Pay::ENABLED_PC; $sp0aa32b->save(); $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '微信'; $sp0aa32b->img = '/plugins/images/wx.png'; $sp0aa32b->driver = 'Fakala'; $sp0aa32b->way = 'wxwap'; $sp0aa32b->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp0aa32b->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp0aa32b->enabled = \App\Pay::ENABLED_MOBILE; $sp0aa32b->save(); $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '支付宝'; $sp0aa32b->img = '/plugins/images/ali.png'; $sp0aa32b->driver = 'Alipay'; $sp0aa32b->way = 'pc'; $sp0aa32b->comment = '支付宝 - 即时到账套餐(企业)V2'; $sp0aa32b->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp0aa32b->enabled = \App\Pay::ENABLED_DISABLED; $sp0aa32b->save(); $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '支付宝'; $sp0aa32b->img = '/plugins/images/ali.png'; $sp0aa32b->driver = 'Aliwap'; $sp0aa32b->way = 'wap'; $sp0aa32b->comment = '支付宝 - 高级手机网站支付V4'; $sp0aa32b->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp0aa32b->enabled = \App\Pay::ENABLED_DISABLED; $sp0aa32b->save(); $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '支付宝扫码'; $sp0aa32b->img = '/plugins/images/ali.png'; $sp0aa32b->driver = 'AliAop'; $sp0aa32b->way = 'f2f'; $sp0aa32b->comment = '支付宝 - 当面付'; $sp0aa32b->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp0aa32b->enabled = \App\Pay::ENABLED_DISABLED; $sp0aa32b->save(); $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '支付宝'; $sp0aa32b->img = '/plugins/images/ali.png'; $sp0aa32b->driver = 'AliAop'; $sp0aa32b->way = 'pc'; $sp0aa32b->comment = '支付宝 - 电脑网站支付 (新)'; $sp0aa32b->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp0aa32b->enabled = \App\Pay::ENABLED_DISABLED; $sp0aa32b->save(); $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '手机支付宝'; $sp0aa32b->img = '/plugins/images/ali.png'; $sp0aa32b->driver = 'AliAop'; $sp0aa32b->way = 'mobile'; $sp0aa32b->comment = '支付宝 - 手机网站支付 (新)'; $sp0aa32b->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp0aa32b->enabled = \App\Pay::ENABLED_DISABLED; $sp0aa32b->save(); $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '微信扫码'; $sp0aa32b->img = '/plugins/images/wx.png'; $sp0aa32b->driver = 'WeChat'; $sp0aa32b->way = 'NATIVE'; $sp0aa32b->comment = '微信支付 - 扫码'; $sp0aa32b->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp0aa32b->enabled = \App\Pay::ENABLED_DISABLED; $sp0aa32b->save(); $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '微信H5'; $sp0aa32b->img = '/plugins/images/wx.png'; $sp0aa32b->driver = 'WeChat'; $sp0aa32b->way = 'MWEB'; $sp0aa32b->comment = '微信支付 - H5 (需要开通权限)'; $sp0aa32b->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp0aa32b->enabled = \App\Pay::ENABLED_DISABLED; $sp0aa32b->save(); $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '手机QQ'; $sp0aa32b->img = '/plugins/images/qq.png'; $sp0aa32b->driver = 'QPay'; $sp0aa32b->way = 'NATIVE'; $sp0aa32b->comment = '手机QQ - 扫码'; $sp0aa32b->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $sp0aa32b->enabled = \App\Pay::ENABLED_DISABLED; $sp0aa32b->save(); $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '支付宝'; $sp0aa32b->img = '/plugins/images/ali.png'; $sp0aa32b->driver = 'Youzan'; $sp0aa32b->way = 'alipay'; $sp0aa32b->comment = '有赞支付 - 支付宝'; $sp0aa32b->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp0aa32b->enabled = \App\Pay::ENABLED_DISABLED; $sp0aa32b->save(); $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '微信'; $sp0aa32b->img = '/plugins/images/wx.png'; $sp0aa32b->driver = 'Youzan'; $sp0aa32b->way = 'wechat'; $sp0aa32b->comment = '有赞支付 - 微信'; $sp0aa32b->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp0aa32b->enabled = \App\Pay::ENABLED_DISABLED; $sp0aa32b->save(); $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '手机QQ'; $sp0aa32b->img = '/plugins/images/qq.png'; $sp0aa32b->driver = 'Youzan'; $sp0aa32b->way = 'qq'; $sp0aa32b->comment = '有赞支付 - 手机QQ'; $sp0aa32b->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp0aa32b->enabled = \App\Pay::ENABLED_DISABLED; $sp0aa32b->save(); $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '支付宝'; $sp0aa32b->img = '/plugins/images/ali.png'; $sp0aa32b->driver = 'CodePay'; $sp0aa32b->way = 'alipay'; $sp0aa32b->comment = '码支付 - 支付宝'; $sp0aa32b->config = '{
  "id": "id",
  "key": "key"
}'; $sp0aa32b->fee_system = 0; $sp0aa32b->enabled = \App\Pay::ENABLED_DISABLED; $sp0aa32b->save(); $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '微信'; $sp0aa32b->img = '/plugins/images/wx.png'; $sp0aa32b->driver = 'CodePay'; $sp0aa32b->way = 'weixin'; $sp0aa32b->comment = '码支付 - 微信'; $sp0aa32b->config = '{
  "id": "id",
  "key": "key"
}'; $sp0aa32b->fee_system = 0; $sp0aa32b->enabled = \App\Pay::ENABLED_DISABLED; $sp0aa32b->save(); $sp0aa32b = new \App\Pay(); $sp0aa32b->name = '手机QQ'; $sp0aa32b->img = '/plugins/images/qq.png'; $sp0aa32b->driver = 'CodePay'; $sp0aa32b->way = 'qq'; $sp0aa32b->comment = '码支付 - 手机QQ'; $sp0aa32b->config = '{
  "id": "id",
  "key": "key"
}'; $sp0aa32b->fee_system = 0; $sp0aa32b->enabled = \App\Pay::ENABLED_DISABLED; $sp0aa32b->save(); } public function run() { self::initPay(); } }