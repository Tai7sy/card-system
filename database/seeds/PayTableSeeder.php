<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '支付宝'; $sp91f0ec->img = '/plugins/images/ali.png'; $sp91f0ec->driver = 'Fakala'; $sp91f0ec->way = 'alipay'; $sp91f0ec->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp91f0ec->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp91f0ec->enabled = \App\Pay::ENABLED_PC; $sp91f0ec->save(); $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '支付宝'; $sp91f0ec->img = '/plugins/images/ali.png'; $sp91f0ec->driver = 'Fakala'; $sp91f0ec->way = 'alipaywap'; $sp91f0ec->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp91f0ec->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp91f0ec->enabled = \App\Pay::ENABLED_MOBILE; $sp91f0ec->save(); $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '微信'; $sp91f0ec->img = '/plugins/images/wx.png'; $sp91f0ec->driver = 'Fakala'; $sp91f0ec->way = 'wx'; $sp91f0ec->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp91f0ec->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp91f0ec->enabled = \App\Pay::ENABLED_PC; $sp91f0ec->save(); $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '微信'; $sp91f0ec->img = '/plugins/images/wx.png'; $sp91f0ec->driver = 'Fakala'; $sp91f0ec->way = 'wxwap'; $sp91f0ec->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp91f0ec->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp91f0ec->enabled = \App\Pay::ENABLED_MOBILE; $sp91f0ec->save(); $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '支付宝'; $sp91f0ec->img = '/plugins/images/ali.png'; $sp91f0ec->driver = 'Alipay'; $sp91f0ec->way = 'pc'; $sp91f0ec->comment = '支付宝 - 即时到账套餐(企业)V2'; $sp91f0ec->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp91f0ec->enabled = \App\Pay::ENABLED_DISABLED; $sp91f0ec->save(); $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '支付宝'; $sp91f0ec->img = '/plugins/images/ali.png'; $sp91f0ec->driver = 'Aliwap'; $sp91f0ec->way = 'wap'; $sp91f0ec->comment = '支付宝 - 高级手机网站支付V4'; $sp91f0ec->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp91f0ec->enabled = \App\Pay::ENABLED_DISABLED; $sp91f0ec->save(); $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '支付宝扫码'; $sp91f0ec->img = '/plugins/images/ali.png'; $sp91f0ec->driver = 'AliAop'; $sp91f0ec->way = 'f2f'; $sp91f0ec->comment = '支付宝 - 当面付'; $sp91f0ec->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp91f0ec->enabled = \App\Pay::ENABLED_DISABLED; $sp91f0ec->save(); $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '支付宝'; $sp91f0ec->img = '/plugins/images/ali.png'; $sp91f0ec->driver = 'AliAop'; $sp91f0ec->way = 'pc'; $sp91f0ec->comment = '支付宝 - 电脑网站支付 (新)'; $sp91f0ec->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp91f0ec->enabled = \App\Pay::ENABLED_DISABLED; $sp91f0ec->save(); $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '手机支付宝'; $sp91f0ec->img = '/plugins/images/ali.png'; $sp91f0ec->driver = 'AliAop'; $sp91f0ec->way = 'mobile'; $sp91f0ec->comment = '支付宝 - 手机网站支付 (新)'; $sp91f0ec->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp91f0ec->enabled = \App\Pay::ENABLED_DISABLED; $sp91f0ec->save(); $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '微信扫码'; $sp91f0ec->img = '/plugins/images/wx.png'; $sp91f0ec->driver = 'WeChat'; $sp91f0ec->way = 'NATIVE'; $sp91f0ec->comment = '微信支付 - 扫码'; $sp91f0ec->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp91f0ec->enabled = \App\Pay::ENABLED_DISABLED; $sp91f0ec->save(); $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '微信H5'; $sp91f0ec->img = '/plugins/images/wx.png'; $sp91f0ec->driver = 'WeChat'; $sp91f0ec->way = 'MWEB'; $sp91f0ec->comment = '微信支付 - H5 (需要开通权限)'; $sp91f0ec->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp91f0ec->enabled = \App\Pay::ENABLED_DISABLED; $sp91f0ec->save(); $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '手机QQ'; $sp91f0ec->img = '/plugins/images/qq.png'; $sp91f0ec->driver = 'QPay'; $sp91f0ec->way = 'NATIVE'; $sp91f0ec->comment = '手机QQ - 扫码'; $sp91f0ec->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $sp91f0ec->enabled = \App\Pay::ENABLED_DISABLED; $sp91f0ec->save(); $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '支付宝'; $sp91f0ec->img = '/plugins/images/ali.png'; $sp91f0ec->driver = 'Youzan'; $sp91f0ec->way = 'alipay'; $sp91f0ec->comment = '有赞支付 - 支付宝'; $sp91f0ec->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp91f0ec->enabled = \App\Pay::ENABLED_DISABLED; $sp91f0ec->save(); $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '微信'; $sp91f0ec->img = '/plugins/images/wx.png'; $sp91f0ec->driver = 'Youzan'; $sp91f0ec->way = 'wechat'; $sp91f0ec->comment = '有赞支付 - 微信'; $sp91f0ec->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp91f0ec->enabled = \App\Pay::ENABLED_DISABLED; $sp91f0ec->save(); $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '手机QQ'; $sp91f0ec->img = '/plugins/images/qq.png'; $sp91f0ec->driver = 'Youzan'; $sp91f0ec->way = 'qq'; $sp91f0ec->comment = '有赞支付 - 手机QQ'; $sp91f0ec->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp91f0ec->enabled = \App\Pay::ENABLED_DISABLED; $sp91f0ec->save(); $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '支付宝'; $sp91f0ec->img = '/plugins/images/ali.png'; $sp91f0ec->driver = 'CodePay'; $sp91f0ec->way = 'alipay'; $sp91f0ec->comment = '码支付 - 支付宝'; $sp91f0ec->config = '{
  "id": "id",
  "key": "key"
}'; $sp91f0ec->fee_system = 0; $sp91f0ec->enabled = \App\Pay::ENABLED_DISABLED; $sp91f0ec->save(); $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '微信'; $sp91f0ec->img = '/plugins/images/wx.png'; $sp91f0ec->driver = 'CodePay'; $sp91f0ec->way = 'weixin'; $sp91f0ec->comment = '码支付 - 微信'; $sp91f0ec->config = '{
  "id": "id",
  "key": "key"
}'; $sp91f0ec->fee_system = 0; $sp91f0ec->enabled = \App\Pay::ENABLED_DISABLED; $sp91f0ec->save(); $sp91f0ec = new \App\Pay(); $sp91f0ec->name = '手机QQ'; $sp91f0ec->img = '/plugins/images/qq.png'; $sp91f0ec->driver = 'CodePay'; $sp91f0ec->way = 'qq'; $sp91f0ec->comment = '码支付 - 手机QQ'; $sp91f0ec->config = '{
  "id": "id",
  "key": "key"
}'; $sp91f0ec->fee_system = 0; $sp91f0ec->enabled = \App\Pay::ENABLED_DISABLED; $sp91f0ec->save(); } public function run() { self::initPay(); } }