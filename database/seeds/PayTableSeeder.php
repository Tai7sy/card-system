<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $spae3141 = new \App\Pay(); $spae3141->name = '支付宝'; $spae3141->img = '/plugins/images/ali.png'; $spae3141->driver = 'Fakala'; $spae3141->way = 'alipay'; $spae3141->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $spae3141->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $spae3141->enabled = \App\Pay::ENABLED_PC; $spae3141->save(); $spae3141 = new \App\Pay(); $spae3141->name = '支付宝'; $spae3141->img = '/plugins/images/ali.png'; $spae3141->driver = 'Fakala'; $spae3141->way = 'alipaywap'; $spae3141->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $spae3141->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $spae3141->enabled = \App\Pay::ENABLED_MOBILE; $spae3141->save(); $spae3141 = new \App\Pay(); $spae3141->name = '微信'; $spae3141->img = '/plugins/images/wx.png'; $spae3141->driver = 'Fakala'; $spae3141->way = 'wx'; $spae3141->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $spae3141->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $spae3141->enabled = \App\Pay::ENABLED_PC; $spae3141->save(); $spae3141 = new \App\Pay(); $spae3141->name = '微信'; $spae3141->img = '/plugins/images/wx.png'; $spae3141->driver = 'Fakala'; $spae3141->way = 'wxwap'; $spae3141->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $spae3141->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $spae3141->enabled = \App\Pay::ENABLED_MOBILE; $spae3141->save(); $spae3141 = new \App\Pay(); $spae3141->name = '支付宝'; $spae3141->img = '/plugins/images/ali.png'; $spae3141->driver = 'Alipay'; $spae3141->way = 'pc'; $spae3141->comment = '支付宝 - 即时到账套餐(企业)V2'; $spae3141->config = '{
  "partner": "partner",
  "key": "key"
}'; $spae3141->enabled = \App\Pay::ENABLED_DISABLED; $spae3141->save(); $spae3141 = new \App\Pay(); $spae3141->name = '支付宝'; $spae3141->img = '/plugins/images/ali.png'; $spae3141->driver = 'Aliwap'; $spae3141->way = 'wap'; $spae3141->comment = '支付宝 - 高级手机网站支付V4'; $spae3141->config = '{
  "partner": "partner",
  "key": "key"
}'; $spae3141->enabled = \App\Pay::ENABLED_DISABLED; $spae3141->save(); $spae3141 = new \App\Pay(); $spae3141->name = '支付宝扫码'; $spae3141->img = '/plugins/images/ali.png'; $spae3141->driver = 'AliAop'; $spae3141->way = 'f2f'; $spae3141->comment = '支付宝 - 当面付'; $spae3141->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $spae3141->enabled = \App\Pay::ENABLED_DISABLED; $spae3141->save(); $spae3141 = new \App\Pay(); $spae3141->name = '支付宝'; $spae3141->img = '/plugins/images/ali.png'; $spae3141->driver = 'AliAop'; $spae3141->way = 'pc'; $spae3141->comment = '支付宝 - 电脑网站支付 (新)'; $spae3141->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $spae3141->enabled = \App\Pay::ENABLED_DISABLED; $spae3141->save(); $spae3141 = new \App\Pay(); $spae3141->name = '手机支付宝'; $spae3141->img = '/plugins/images/ali.png'; $spae3141->driver = 'AliAop'; $spae3141->way = 'mobile'; $spae3141->comment = '支付宝 - 手机网站支付 (新)'; $spae3141->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $spae3141->enabled = \App\Pay::ENABLED_DISABLED; $spae3141->save(); $spae3141 = new \App\Pay(); $spae3141->name = '微信扫码'; $spae3141->img = '/plugins/images/wx.png'; $spae3141->driver = 'WeChat'; $spae3141->way = 'NATIVE'; $spae3141->comment = '微信支付 - 扫码'; $spae3141->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $spae3141->enabled = \App\Pay::ENABLED_DISABLED; $spae3141->save(); $spae3141 = new \App\Pay(); $spae3141->name = '微信H5'; $spae3141->img = '/plugins/images/wx.png'; $spae3141->driver = 'WeChat'; $spae3141->way = 'MWEB'; $spae3141->comment = '微信支付 - H5 (需要开通权限)'; $spae3141->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $spae3141->enabled = \App\Pay::ENABLED_DISABLED; $spae3141->save(); $spae3141 = new \App\Pay(); $spae3141->name = '手机QQ'; $spae3141->img = '/plugins/images/qq.png'; $spae3141->driver = 'QPay'; $spae3141->way = 'NATIVE'; $spae3141->comment = '手机QQ - 扫码'; $spae3141->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $spae3141->enabled = \App\Pay::ENABLED_DISABLED; $spae3141->save(); $spae3141 = new \App\Pay(); $spae3141->name = '支付宝'; $spae3141->img = '/plugins/images/ali.png'; $spae3141->driver = 'Youzan'; $spae3141->way = 'alipay'; $spae3141->comment = '有赞支付 - 支付宝'; $spae3141->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $spae3141->enabled = \App\Pay::ENABLED_DISABLED; $spae3141->save(); $spae3141 = new \App\Pay(); $spae3141->name = '微信'; $spae3141->img = '/plugins/images/wx.png'; $spae3141->driver = 'Youzan'; $spae3141->way = 'wechat'; $spae3141->comment = '有赞支付 - 微信'; $spae3141->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $spae3141->enabled = \App\Pay::ENABLED_DISABLED; $spae3141->save(); $spae3141 = new \App\Pay(); $spae3141->name = '手机QQ'; $spae3141->img = '/plugins/images/qq.png'; $spae3141->driver = 'Youzan'; $spae3141->way = 'qq'; $spae3141->comment = '有赞支付 - 手机QQ'; $spae3141->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $spae3141->enabled = \App\Pay::ENABLED_DISABLED; $spae3141->save(); $spae3141 = new \App\Pay(); $spae3141->name = '支付宝'; $spae3141->img = '/plugins/images/ali.png'; $spae3141->driver = 'CodePay'; $spae3141->way = 'alipay'; $spae3141->comment = '码支付 - 支付宝'; $spae3141->config = '{
  "id": "id",
  "key": "key"
}'; $spae3141->fee_system = 0; $spae3141->enabled = \App\Pay::ENABLED_DISABLED; $spae3141->save(); $spae3141 = new \App\Pay(); $spae3141->name = '微信'; $spae3141->img = '/plugins/images/wx.png'; $spae3141->driver = 'CodePay'; $spae3141->way = 'weixin'; $spae3141->comment = '码支付 - 微信'; $spae3141->config = '{
  "id": "id",
  "key": "key"
}'; $spae3141->fee_system = 0; $spae3141->enabled = \App\Pay::ENABLED_DISABLED; $spae3141->save(); $spae3141 = new \App\Pay(); $spae3141->name = '手机QQ'; $spae3141->img = '/plugins/images/qq.png'; $spae3141->driver = 'CodePay'; $spae3141->way = 'qq'; $spae3141->comment = '码支付 - 手机QQ'; $spae3141->config = '{
  "id": "id",
  "key": "key"
}'; $spae3141->fee_system = 0; $spae3141->enabled = \App\Pay::ENABLED_DISABLED; $spae3141->save(); } public function run() { self::initPay(); } }