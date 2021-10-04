<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $sp7fc279 = new \App\Pay(); $sp7fc279->name = '支付宝'; $sp7fc279->img = '/plugins/images/ali.png'; $sp7fc279->driver = 'Fakala'; $sp7fc279->way = 'alipay'; $sp7fc279->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp7fc279->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp7fc279->enabled = \App\Pay::ENABLED_PC; $sp7fc279->save(); $sp7fc279 = new \App\Pay(); $sp7fc279->name = '支付宝'; $sp7fc279->img = '/plugins/images/ali.png'; $sp7fc279->driver = 'Fakala'; $sp7fc279->way = 'alipaywap'; $sp7fc279->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp7fc279->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp7fc279->enabled = \App\Pay::ENABLED_MOBILE; $sp7fc279->save(); $sp7fc279 = new \App\Pay(); $sp7fc279->name = '微信'; $sp7fc279->img = '/plugins/images/wx.png'; $sp7fc279->driver = 'Fakala'; $sp7fc279->way = 'wx'; $sp7fc279->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp7fc279->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp7fc279->enabled = \App\Pay::ENABLED_PC; $sp7fc279->save(); $sp7fc279 = new \App\Pay(); $sp7fc279->name = '微信'; $sp7fc279->img = '/plugins/images/wx.png'; $sp7fc279->driver = 'Fakala'; $sp7fc279->way = 'wxwap'; $sp7fc279->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp7fc279->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp7fc279->enabled = \App\Pay::ENABLED_MOBILE; $sp7fc279->save(); $sp7fc279 = new \App\Pay(); $sp7fc279->name = '支付宝'; $sp7fc279->img = '/plugins/images/ali.png'; $sp7fc279->driver = 'Alipay'; $sp7fc279->way = 'pc'; $sp7fc279->comment = '支付宝 - 即时到账套餐(企业)V2'; $sp7fc279->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp7fc279->enabled = \App\Pay::ENABLED_DISABLED; $sp7fc279->save(); $sp7fc279 = new \App\Pay(); $sp7fc279->name = '支付宝'; $sp7fc279->img = '/plugins/images/ali.png'; $sp7fc279->driver = 'Aliwap'; $sp7fc279->way = 'wap'; $sp7fc279->comment = '支付宝 - 高级手机网站支付V4'; $sp7fc279->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp7fc279->enabled = \App\Pay::ENABLED_DISABLED; $sp7fc279->save(); $sp7fc279 = new \App\Pay(); $sp7fc279->name = '支付宝扫码'; $sp7fc279->img = '/plugins/images/ali.png'; $sp7fc279->driver = 'AliAop'; $sp7fc279->way = 'f2f'; $sp7fc279->comment = '支付宝 - 当面付'; $sp7fc279->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp7fc279->enabled = \App\Pay::ENABLED_DISABLED; $sp7fc279->save(); $sp7fc279 = new \App\Pay(); $sp7fc279->name = '支付宝'; $sp7fc279->img = '/plugins/images/ali.png'; $sp7fc279->driver = 'AliAop'; $sp7fc279->way = 'pc'; $sp7fc279->comment = '支付宝 - 电脑网站支付 (新)'; $sp7fc279->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp7fc279->enabled = \App\Pay::ENABLED_DISABLED; $sp7fc279->save(); $sp7fc279 = new \App\Pay(); $sp7fc279->name = '手机支付宝'; $sp7fc279->img = '/plugins/images/ali.png'; $sp7fc279->driver = 'AliAop'; $sp7fc279->way = 'mobile'; $sp7fc279->comment = '支付宝 - 手机网站支付 (新)'; $sp7fc279->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp7fc279->enabled = \App\Pay::ENABLED_DISABLED; $sp7fc279->save(); $sp7fc279 = new \App\Pay(); $sp7fc279->name = '微信扫码'; $sp7fc279->img = '/plugins/images/wx.png'; $sp7fc279->driver = 'WeChat'; $sp7fc279->way = 'NATIVE'; $sp7fc279->comment = '微信支付 - 扫码'; $sp7fc279->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp7fc279->enabled = \App\Pay::ENABLED_DISABLED; $sp7fc279->save(); $sp7fc279 = new \App\Pay(); $sp7fc279->name = '微信H5'; $sp7fc279->img = '/plugins/images/wx.png'; $sp7fc279->driver = 'WeChat'; $sp7fc279->way = 'MWEB'; $sp7fc279->comment = '微信支付 - H5 (需要开通权限)'; $sp7fc279->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp7fc279->enabled = \App\Pay::ENABLED_DISABLED; $sp7fc279->save(); $sp7fc279 = new \App\Pay(); $sp7fc279->name = '手机QQ'; $sp7fc279->img = '/plugins/images/qq.png'; $sp7fc279->driver = 'QPay'; $sp7fc279->way = 'NATIVE'; $sp7fc279->comment = '手机QQ - 扫码'; $sp7fc279->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $sp7fc279->enabled = \App\Pay::ENABLED_DISABLED; $sp7fc279->save(); $sp7fc279 = new \App\Pay(); $sp7fc279->name = '支付宝'; $sp7fc279->img = '/plugins/images/ali.png'; $sp7fc279->driver = 'Youzan'; $sp7fc279->way = 'alipay'; $sp7fc279->comment = '有赞支付 - 支付宝'; $sp7fc279->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp7fc279->enabled = \App\Pay::ENABLED_DISABLED; $sp7fc279->save(); $sp7fc279 = new \App\Pay(); $sp7fc279->name = '微信'; $sp7fc279->img = '/plugins/images/wx.png'; $sp7fc279->driver = 'Youzan'; $sp7fc279->way = 'wechat'; $sp7fc279->comment = '有赞支付 - 微信'; $sp7fc279->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp7fc279->enabled = \App\Pay::ENABLED_DISABLED; $sp7fc279->save(); $sp7fc279 = new \App\Pay(); $sp7fc279->name = '手机QQ'; $sp7fc279->img = '/plugins/images/qq.png'; $sp7fc279->driver = 'Youzan'; $sp7fc279->way = 'qq'; $sp7fc279->comment = '有赞支付 - 手机QQ'; $sp7fc279->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp7fc279->enabled = \App\Pay::ENABLED_DISABLED; $sp7fc279->save(); $sp7fc279 = new \App\Pay(); $sp7fc279->name = '支付宝'; $sp7fc279->img = '/plugins/images/ali.png'; $sp7fc279->driver = 'CodePay'; $sp7fc279->way = 'alipay'; $sp7fc279->comment = '码支付 - 支付宝'; $sp7fc279->config = '{
  "id": "id",
  "key": "key"
}'; $sp7fc279->fee_system = 0; $sp7fc279->enabled = \App\Pay::ENABLED_DISABLED; $sp7fc279->save(); $sp7fc279 = new \App\Pay(); $sp7fc279->name = '微信'; $sp7fc279->img = '/plugins/images/wx.png'; $sp7fc279->driver = 'CodePay'; $sp7fc279->way = 'weixin'; $sp7fc279->comment = '码支付 - 微信'; $sp7fc279->config = '{
  "id": "id",
  "key": "key"
}'; $sp7fc279->fee_system = 0; $sp7fc279->enabled = \App\Pay::ENABLED_DISABLED; $sp7fc279->save(); $sp7fc279 = new \App\Pay(); $sp7fc279->name = '手机QQ'; $sp7fc279->img = '/plugins/images/qq.png'; $sp7fc279->driver = 'CodePay'; $sp7fc279->way = 'qq'; $sp7fc279->comment = '码支付 - 手机QQ'; $sp7fc279->config = '{
  "id": "id",
  "key": "key"
}'; $sp7fc279->fee_system = 0; $sp7fc279->enabled = \App\Pay::ENABLED_DISABLED; $sp7fc279->save(); } public function run() { self::initPay(); } }