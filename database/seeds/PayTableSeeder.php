<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '支付宝'; $sp56a5d4->img = '/plugins/images/ali.png'; $sp56a5d4->driver = 'Fakala'; $sp56a5d4->way = 'alipay'; $sp56a5d4->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp56a5d4->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp56a5d4->enabled = \App\Pay::ENABLED_PC; $sp56a5d4->save(); $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '支付宝'; $sp56a5d4->img = '/plugins/images/ali.png'; $sp56a5d4->driver = 'Fakala'; $sp56a5d4->way = 'alipaywap'; $sp56a5d4->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp56a5d4->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp56a5d4->enabled = \App\Pay::ENABLED_MOBILE; $sp56a5d4->save(); $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '微信'; $sp56a5d4->img = '/plugins/images/wx.png'; $sp56a5d4->driver = 'Fakala'; $sp56a5d4->way = 'wx'; $sp56a5d4->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp56a5d4->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp56a5d4->enabled = \App\Pay::ENABLED_PC; $sp56a5d4->save(); $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '微信'; $sp56a5d4->img = '/plugins/images/wx.png'; $sp56a5d4->driver = 'Fakala'; $sp56a5d4->way = 'wxwap'; $sp56a5d4->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp56a5d4->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp56a5d4->enabled = \App\Pay::ENABLED_MOBILE; $sp56a5d4->save(); $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '支付宝'; $sp56a5d4->img = '/plugins/images/ali.png'; $sp56a5d4->driver = 'Alipay'; $sp56a5d4->way = 'pc'; $sp56a5d4->comment = '支付宝 - 即时到账套餐(企业)V2'; $sp56a5d4->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp56a5d4->enabled = \App\Pay::ENABLED_DISABLED; $sp56a5d4->save(); $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '支付宝'; $sp56a5d4->img = '/plugins/images/ali.png'; $sp56a5d4->driver = 'Aliwap'; $sp56a5d4->way = 'wap'; $sp56a5d4->comment = '支付宝 - 高级手机网站支付V4'; $sp56a5d4->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp56a5d4->enabled = \App\Pay::ENABLED_DISABLED; $sp56a5d4->save(); $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '支付宝扫码'; $sp56a5d4->img = '/plugins/images/ali.png'; $sp56a5d4->driver = 'AliAop'; $sp56a5d4->way = 'f2f'; $sp56a5d4->comment = '支付宝 - 当面付'; $sp56a5d4->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp56a5d4->enabled = \App\Pay::ENABLED_DISABLED; $sp56a5d4->save(); $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '支付宝'; $sp56a5d4->img = '/plugins/images/ali.png'; $sp56a5d4->driver = 'AliAop'; $sp56a5d4->way = 'pc'; $sp56a5d4->comment = '支付宝 - 电脑网站支付 (新)'; $sp56a5d4->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp56a5d4->enabled = \App\Pay::ENABLED_DISABLED; $sp56a5d4->save(); $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '手机支付宝'; $sp56a5d4->img = '/plugins/images/ali.png'; $sp56a5d4->driver = 'AliAop'; $sp56a5d4->way = 'mobile'; $sp56a5d4->comment = '支付宝 - 手机网站支付 (新)'; $sp56a5d4->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp56a5d4->enabled = \App\Pay::ENABLED_DISABLED; $sp56a5d4->save(); $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '微信扫码'; $sp56a5d4->img = '/plugins/images/wx.png'; $sp56a5d4->driver = 'WeChat'; $sp56a5d4->way = 'NATIVE'; $sp56a5d4->comment = '微信支付 - 扫码'; $sp56a5d4->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp56a5d4->enabled = \App\Pay::ENABLED_DISABLED; $sp56a5d4->save(); $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '微信H5'; $sp56a5d4->img = '/plugins/images/wx.png'; $sp56a5d4->driver = 'WeChat'; $sp56a5d4->way = 'MWEB'; $sp56a5d4->comment = '微信支付 - H5 (需要开通权限)'; $sp56a5d4->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp56a5d4->enabled = \App\Pay::ENABLED_DISABLED; $sp56a5d4->save(); $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '手机QQ'; $sp56a5d4->img = '/plugins/images/qq.png'; $sp56a5d4->driver = 'QPay'; $sp56a5d4->way = 'NATIVE'; $sp56a5d4->comment = '手机QQ - 扫码'; $sp56a5d4->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $sp56a5d4->enabled = \App\Pay::ENABLED_DISABLED; $sp56a5d4->save(); $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '支付宝'; $sp56a5d4->img = '/plugins/images/ali.png'; $sp56a5d4->driver = 'Youzan'; $sp56a5d4->way = 'alipay'; $sp56a5d4->comment = '有赞支付 - 支付宝'; $sp56a5d4->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp56a5d4->enabled = \App\Pay::ENABLED_DISABLED; $sp56a5d4->save(); $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '微信'; $sp56a5d4->img = '/plugins/images/wx.png'; $sp56a5d4->driver = 'Youzan'; $sp56a5d4->way = 'wechat'; $sp56a5d4->comment = '有赞支付 - 微信'; $sp56a5d4->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp56a5d4->enabled = \App\Pay::ENABLED_DISABLED; $sp56a5d4->save(); $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '手机QQ'; $sp56a5d4->img = '/plugins/images/qq.png'; $sp56a5d4->driver = 'Youzan'; $sp56a5d4->way = 'qq'; $sp56a5d4->comment = '有赞支付 - 手机QQ'; $sp56a5d4->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp56a5d4->enabled = \App\Pay::ENABLED_DISABLED; $sp56a5d4->save(); $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '支付宝'; $sp56a5d4->img = '/plugins/images/ali.png'; $sp56a5d4->driver = 'CodePay'; $sp56a5d4->way = 'alipay'; $sp56a5d4->comment = '码支付 - 支付宝'; $sp56a5d4->config = '{
  "id": "id",
  "key": "key"
}'; $sp56a5d4->fee_system = 0; $sp56a5d4->enabled = \App\Pay::ENABLED_DISABLED; $sp56a5d4->save(); $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '微信'; $sp56a5d4->img = '/plugins/images/wx.png'; $sp56a5d4->driver = 'CodePay'; $sp56a5d4->way = 'weixin'; $sp56a5d4->comment = '码支付 - 微信'; $sp56a5d4->config = '{
  "id": "id",
  "key": "key"
}'; $sp56a5d4->fee_system = 0; $sp56a5d4->enabled = \App\Pay::ENABLED_DISABLED; $sp56a5d4->save(); $sp56a5d4 = new \App\Pay(); $sp56a5d4->name = '手机QQ'; $sp56a5d4->img = '/plugins/images/qq.png'; $sp56a5d4->driver = 'CodePay'; $sp56a5d4->way = 'qq'; $sp56a5d4->comment = '码支付 - 手机QQ'; $sp56a5d4->config = '{
  "id": "id",
  "key": "key"
}'; $sp56a5d4->fee_system = 0; $sp56a5d4->enabled = \App\Pay::ENABLED_DISABLED; $sp56a5d4->save(); } public function run() { self::initPay(); } }