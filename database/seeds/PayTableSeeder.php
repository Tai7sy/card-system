<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $sp880618 = new \App\Pay(); $sp880618->name = '支付宝'; $sp880618->img = '/plugins/images/ali.png'; $sp880618->driver = 'Fakala'; $sp880618->way = 'alipay'; $sp880618->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp880618->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp880618->enabled = \App\Pay::ENABLED_PC; $sp880618->save(); $sp880618 = new \App\Pay(); $sp880618->name = '支付宝'; $sp880618->img = '/plugins/images/ali.png'; $sp880618->driver = 'Fakala'; $sp880618->way = 'alipaywap'; $sp880618->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp880618->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp880618->enabled = \App\Pay::ENABLED_MOBILE; $sp880618->save(); $sp880618 = new \App\Pay(); $sp880618->name = '微信'; $sp880618->img = '/plugins/images/wx.png'; $sp880618->driver = 'Fakala'; $sp880618->way = 'wx'; $sp880618->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp880618->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp880618->enabled = \App\Pay::ENABLED_PC; $sp880618->save(); $sp880618 = new \App\Pay(); $sp880618->name = '微信'; $sp880618->img = '/plugins/images/wx.png'; $sp880618->driver = 'Fakala'; $sp880618->way = 'wxwap'; $sp880618->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp880618->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp880618->enabled = \App\Pay::ENABLED_MOBILE; $sp880618->save(); $sp880618 = new \App\Pay(); $sp880618->name = '支付宝'; $sp880618->img = '/plugins/images/ali.png'; $sp880618->driver = 'Alipay'; $sp880618->way = 'pc'; $sp880618->comment = '支付宝 - 即时到账套餐(企业)V2'; $sp880618->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp880618->enabled = \App\Pay::ENABLED_DISABLED; $sp880618->save(); $sp880618 = new \App\Pay(); $sp880618->name = '支付宝'; $sp880618->img = '/plugins/images/ali.png'; $sp880618->driver = 'Aliwap'; $sp880618->way = 'wap'; $sp880618->comment = '支付宝 - 高级手机网站支付V4'; $sp880618->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp880618->enabled = \App\Pay::ENABLED_DISABLED; $sp880618->save(); $sp880618 = new \App\Pay(); $sp880618->name = '支付宝扫码'; $sp880618->img = '/plugins/images/ali.png'; $sp880618->driver = 'AliAop'; $sp880618->way = 'f2f'; $sp880618->comment = '支付宝 - 当面付'; $sp880618->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp880618->enabled = \App\Pay::ENABLED_DISABLED; $sp880618->save(); $sp880618 = new \App\Pay(); $sp880618->name = '支付宝'; $sp880618->img = '/plugins/images/ali.png'; $sp880618->driver = 'AliAop'; $sp880618->way = 'pc'; $sp880618->comment = '支付宝 - 电脑网站支付 (新)'; $sp880618->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp880618->enabled = \App\Pay::ENABLED_DISABLED; $sp880618->save(); $sp880618 = new \App\Pay(); $sp880618->name = '手机支付宝'; $sp880618->img = '/plugins/images/ali.png'; $sp880618->driver = 'AliAop'; $sp880618->way = 'mobile'; $sp880618->comment = '支付宝 - 手机网站支付 (新)'; $sp880618->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp880618->enabled = \App\Pay::ENABLED_DISABLED; $sp880618->save(); $sp880618 = new \App\Pay(); $sp880618->name = '微信扫码'; $sp880618->img = '/plugins/images/wx.png'; $sp880618->driver = 'WeChat'; $sp880618->way = 'NATIVE'; $sp880618->comment = '微信支付 - 扫码'; $sp880618->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp880618->enabled = \App\Pay::ENABLED_DISABLED; $sp880618->save(); $sp880618 = new \App\Pay(); $sp880618->name = '微信H5'; $sp880618->img = '/plugins/images/wx.png'; $sp880618->driver = 'WeChat'; $sp880618->way = 'MWEB'; $sp880618->comment = '微信支付 - H5 (需要开通权限)'; $sp880618->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp880618->enabled = \App\Pay::ENABLED_DISABLED; $sp880618->save(); $sp880618 = new \App\Pay(); $sp880618->name = '手机QQ'; $sp880618->img = '/plugins/images/qq.png'; $sp880618->driver = 'QPay'; $sp880618->way = 'NATIVE'; $sp880618->comment = '手机QQ - 扫码'; $sp880618->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $sp880618->enabled = \App\Pay::ENABLED_DISABLED; $sp880618->save(); $sp880618 = new \App\Pay(); $sp880618->name = '支付宝'; $sp880618->img = '/plugins/images/ali.png'; $sp880618->driver = 'Youzan'; $sp880618->way = 'alipay'; $sp880618->comment = '有赞支付 - 支付宝'; $sp880618->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp880618->enabled = \App\Pay::ENABLED_DISABLED; $sp880618->save(); $sp880618 = new \App\Pay(); $sp880618->name = '微信'; $sp880618->img = '/plugins/images/wx.png'; $sp880618->driver = 'Youzan'; $sp880618->way = 'wechat'; $sp880618->comment = '有赞支付 - 微信'; $sp880618->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp880618->enabled = \App\Pay::ENABLED_DISABLED; $sp880618->save(); $sp880618 = new \App\Pay(); $sp880618->name = '手机QQ'; $sp880618->img = '/plugins/images/qq.png'; $sp880618->driver = 'Youzan'; $sp880618->way = 'qq'; $sp880618->comment = '有赞支付 - 手机QQ'; $sp880618->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp880618->enabled = \App\Pay::ENABLED_DISABLED; $sp880618->save(); $sp880618 = new \App\Pay(); $sp880618->name = '支付宝'; $sp880618->img = '/plugins/images/ali.png'; $sp880618->driver = 'CodePay'; $sp880618->way = 'alipay'; $sp880618->comment = '码支付 - 支付宝'; $sp880618->config = '{
  "id": "id",
  "key": "key"
}'; $sp880618->fee_system = 0; $sp880618->enabled = \App\Pay::ENABLED_DISABLED; $sp880618->save(); $sp880618 = new \App\Pay(); $sp880618->name = '微信'; $sp880618->img = '/plugins/images/wx.png'; $sp880618->driver = 'CodePay'; $sp880618->way = 'weixin'; $sp880618->comment = '码支付 - 微信'; $sp880618->config = '{
  "id": "id",
  "key": "key"
}'; $sp880618->fee_system = 0; $sp880618->enabled = \App\Pay::ENABLED_DISABLED; $sp880618->save(); $sp880618 = new \App\Pay(); $sp880618->name = '手机QQ'; $sp880618->img = '/plugins/images/qq.png'; $sp880618->driver = 'CodePay'; $sp880618->way = 'qq'; $sp880618->comment = '码支付 - 手机QQ'; $sp880618->config = '{
  "id": "id",
  "key": "key"
}'; $sp880618->fee_system = 0; $sp880618->enabled = \App\Pay::ENABLED_DISABLED; $sp880618->save(); } public function run() { self::initPay(); } }