<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $sp680e79 = new \App\Pay(); $sp680e79->name = '支付宝'; $sp680e79->img = '/plugins/images/ali.png'; $sp680e79->driver = 'Fakala'; $sp680e79->way = 'alipay'; $sp680e79->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp680e79->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp680e79->enabled = \App\Pay::ENABLED_PC; $sp680e79->save(); $sp680e79 = new \App\Pay(); $sp680e79->name = '支付宝'; $sp680e79->img = '/plugins/images/ali.png'; $sp680e79->driver = 'Fakala'; $sp680e79->way = 'alipaywap'; $sp680e79->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp680e79->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp680e79->enabled = \App\Pay::ENABLED_MOBILE; $sp680e79->save(); $sp680e79 = new \App\Pay(); $sp680e79->name = '微信'; $sp680e79->img = '/plugins/images/wx.png'; $sp680e79->driver = 'Fakala'; $sp680e79->way = 'wx'; $sp680e79->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp680e79->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp680e79->enabled = \App\Pay::ENABLED_PC; $sp680e79->save(); $sp680e79 = new \App\Pay(); $sp680e79->name = '微信'; $sp680e79->img = '/plugins/images/wx.png'; $sp680e79->driver = 'Fakala'; $sp680e79->way = 'wxwap'; $sp680e79->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp680e79->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp680e79->enabled = \App\Pay::ENABLED_MOBILE; $sp680e79->save(); $sp680e79 = new \App\Pay(); $sp680e79->name = '支付宝'; $sp680e79->img = '/plugins/images/ali.png'; $sp680e79->driver = 'Alipay'; $sp680e79->way = 'pc'; $sp680e79->comment = '支付宝 - 即时到账套餐(企业)V2'; $sp680e79->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp680e79->enabled = \App\Pay::ENABLED_DISABLED; $sp680e79->save(); $sp680e79 = new \App\Pay(); $sp680e79->name = '支付宝'; $sp680e79->img = '/plugins/images/ali.png'; $sp680e79->driver = 'Aliwap'; $sp680e79->way = 'wap'; $sp680e79->comment = '支付宝 - 高级手机网站支付V4'; $sp680e79->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp680e79->enabled = \App\Pay::ENABLED_DISABLED; $sp680e79->save(); $sp680e79 = new \App\Pay(); $sp680e79->name = '支付宝扫码'; $sp680e79->img = '/plugins/images/ali.png'; $sp680e79->driver = 'AliAop'; $sp680e79->way = 'f2f'; $sp680e79->comment = '支付宝 - 当面付'; $sp680e79->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp680e79->enabled = \App\Pay::ENABLED_DISABLED; $sp680e79->save(); $sp680e79 = new \App\Pay(); $sp680e79->name = '支付宝'; $sp680e79->img = '/plugins/images/ali.png'; $sp680e79->driver = 'AliAop'; $sp680e79->way = 'pc'; $sp680e79->comment = '支付宝 - 电脑网站支付 (新)'; $sp680e79->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp680e79->enabled = \App\Pay::ENABLED_DISABLED; $sp680e79->save(); $sp680e79 = new \App\Pay(); $sp680e79->name = '手机支付宝'; $sp680e79->img = '/plugins/images/ali.png'; $sp680e79->driver = 'AliAop'; $sp680e79->way = 'mobile'; $sp680e79->comment = '支付宝 - 手机网站支付 (新)'; $sp680e79->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp680e79->enabled = \App\Pay::ENABLED_DISABLED; $sp680e79->save(); $sp680e79 = new \App\Pay(); $sp680e79->name = '微信扫码'; $sp680e79->img = '/plugins/images/wx.png'; $sp680e79->driver = 'WeChat'; $sp680e79->way = 'NATIVE'; $sp680e79->comment = '微信支付 - 扫码'; $sp680e79->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp680e79->enabled = \App\Pay::ENABLED_DISABLED; $sp680e79->save(); $sp680e79 = new \App\Pay(); $sp680e79->name = '微信H5'; $sp680e79->img = '/plugins/images/wx.png'; $sp680e79->driver = 'WeChat'; $sp680e79->way = 'MWEB'; $sp680e79->comment = '微信支付 - H5 (需要开通权限)'; $sp680e79->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp680e79->enabled = \App\Pay::ENABLED_DISABLED; $sp680e79->save(); $sp680e79 = new \App\Pay(); $sp680e79->name = '手机QQ'; $sp680e79->img = '/plugins/images/qq.png'; $sp680e79->driver = 'QPay'; $sp680e79->way = 'NATIVE'; $sp680e79->comment = '手机QQ - 扫码'; $sp680e79->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $sp680e79->enabled = \App\Pay::ENABLED_DISABLED; $sp680e79->save(); $sp680e79 = new \App\Pay(); $sp680e79->name = '支付宝'; $sp680e79->img = '/plugins/images/ali.png'; $sp680e79->driver = 'Youzan'; $sp680e79->way = 'alipay'; $sp680e79->comment = '有赞支付 - 支付宝'; $sp680e79->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp680e79->enabled = \App\Pay::ENABLED_DISABLED; $sp680e79->save(); $sp680e79 = new \App\Pay(); $sp680e79->name = '微信'; $sp680e79->img = '/plugins/images/wx.png'; $sp680e79->driver = 'Youzan'; $sp680e79->way = 'wechat'; $sp680e79->comment = '有赞支付 - 微信'; $sp680e79->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp680e79->enabled = \App\Pay::ENABLED_DISABLED; $sp680e79->save(); $sp680e79 = new \App\Pay(); $sp680e79->name = '手机QQ'; $sp680e79->img = '/plugins/images/qq.png'; $sp680e79->driver = 'Youzan'; $sp680e79->way = 'qq'; $sp680e79->comment = '有赞支付 - 手机QQ'; $sp680e79->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp680e79->enabled = \App\Pay::ENABLED_DISABLED; $sp680e79->save(); $sp680e79 = new \App\Pay(); $sp680e79->name = '支付宝'; $sp680e79->img = '/plugins/images/ali.png'; $sp680e79->driver = 'CodePay'; $sp680e79->way = 'alipay'; $sp680e79->comment = '码支付 - 支付宝'; $sp680e79->config = '{
  "id": "id",
  "key": "key"
}'; $sp680e79->fee_system = 0; $sp680e79->enabled = \App\Pay::ENABLED_DISABLED; $sp680e79->save(); $sp680e79 = new \App\Pay(); $sp680e79->name = '微信'; $sp680e79->img = '/plugins/images/wx.png'; $sp680e79->driver = 'CodePay'; $sp680e79->way = 'weixin'; $sp680e79->comment = '码支付 - 微信'; $sp680e79->config = '{
  "id": "id",
  "key": "key"
}'; $sp680e79->fee_system = 0; $sp680e79->enabled = \App\Pay::ENABLED_DISABLED; $sp680e79->save(); $sp680e79 = new \App\Pay(); $sp680e79->name = '手机QQ'; $sp680e79->img = '/plugins/images/qq.png'; $sp680e79->driver = 'CodePay'; $sp680e79->way = 'qq'; $sp680e79->comment = '码支付 - 手机QQ'; $sp680e79->config = '{
  "id": "id",
  "key": "key"
}'; $sp680e79->fee_system = 0; $sp680e79->enabled = \App\Pay::ENABLED_DISABLED; $sp680e79->save(); } public function run() { self::initPay(); } }