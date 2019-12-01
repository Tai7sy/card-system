<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $sp1b768e = new \App\Pay(); $sp1b768e->name = '支付宝'; $sp1b768e->img = '/plugins/images/ali.png'; $sp1b768e->driver = 'Fakala'; $sp1b768e->way = 'alipay'; $sp1b768e->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp1b768e->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp1b768e->enabled = \App\Pay::ENABLED_PC; $sp1b768e->save(); $sp1b768e = new \App\Pay(); $sp1b768e->name = '支付宝'; $sp1b768e->img = '/plugins/images/ali.png'; $sp1b768e->driver = 'Fakala'; $sp1b768e->way = 'alipaywap'; $sp1b768e->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp1b768e->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp1b768e->enabled = \App\Pay::ENABLED_MOBILE; $sp1b768e->save(); $sp1b768e = new \App\Pay(); $sp1b768e->name = '微信'; $sp1b768e->img = '/plugins/images/wx.png'; $sp1b768e->driver = 'Fakala'; $sp1b768e->way = 'wx'; $sp1b768e->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp1b768e->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp1b768e->enabled = \App\Pay::ENABLED_PC; $sp1b768e->save(); $sp1b768e = new \App\Pay(); $sp1b768e->name = '微信'; $sp1b768e->img = '/plugins/images/wx.png'; $sp1b768e->driver = 'Fakala'; $sp1b768e->way = 'wxwap'; $sp1b768e->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp1b768e->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp1b768e->enabled = \App\Pay::ENABLED_MOBILE; $sp1b768e->save(); $sp1b768e = new \App\Pay(); $sp1b768e->name = '支付宝'; $sp1b768e->img = '/plugins/images/ali.png'; $sp1b768e->driver = 'Alipay'; $sp1b768e->way = 'pc'; $sp1b768e->comment = '支付宝 - 即时到账套餐(企业)V2'; $sp1b768e->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp1b768e->enabled = \App\Pay::ENABLED_DISABLED; $sp1b768e->save(); $sp1b768e = new \App\Pay(); $sp1b768e->name = '支付宝'; $sp1b768e->img = '/plugins/images/ali.png'; $sp1b768e->driver = 'Aliwap'; $sp1b768e->way = 'wap'; $sp1b768e->comment = '支付宝 - 高级手机网站支付V4'; $sp1b768e->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp1b768e->enabled = \App\Pay::ENABLED_DISABLED; $sp1b768e->save(); $sp1b768e = new \App\Pay(); $sp1b768e->name = '支付宝扫码'; $sp1b768e->img = '/plugins/images/ali.png'; $sp1b768e->driver = 'AliAop'; $sp1b768e->way = 'f2f'; $sp1b768e->comment = '支付宝 - 当面付'; $sp1b768e->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp1b768e->enabled = \App\Pay::ENABLED_DISABLED; $sp1b768e->save(); $sp1b768e = new \App\Pay(); $sp1b768e->name = '支付宝'; $sp1b768e->img = '/plugins/images/ali.png'; $sp1b768e->driver = 'AliAop'; $sp1b768e->way = 'pc'; $sp1b768e->comment = '支付宝 - 电脑网站支付 (新)'; $sp1b768e->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp1b768e->enabled = \App\Pay::ENABLED_DISABLED; $sp1b768e->save(); $sp1b768e = new \App\Pay(); $sp1b768e->name = '手机支付宝'; $sp1b768e->img = '/plugins/images/ali.png'; $sp1b768e->driver = 'AliAop'; $sp1b768e->way = 'mobile'; $sp1b768e->comment = '支付宝 - 手机网站支付 (新)'; $sp1b768e->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp1b768e->enabled = \App\Pay::ENABLED_DISABLED; $sp1b768e->save(); $sp1b768e = new \App\Pay(); $sp1b768e->name = '微信扫码'; $sp1b768e->img = '/plugins/images/wx.png'; $sp1b768e->driver = 'WeChat'; $sp1b768e->way = 'NATIVE'; $sp1b768e->comment = '微信支付 - 扫码'; $sp1b768e->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp1b768e->enabled = \App\Pay::ENABLED_DISABLED; $sp1b768e->save(); $sp1b768e = new \App\Pay(); $sp1b768e->name = '微信H5'; $sp1b768e->img = '/plugins/images/wx.png'; $sp1b768e->driver = 'WeChat'; $sp1b768e->way = 'MWEB'; $sp1b768e->comment = '微信支付 - H5 (需要开通权限)'; $sp1b768e->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp1b768e->enabled = \App\Pay::ENABLED_DISABLED; $sp1b768e->save(); $sp1b768e = new \App\Pay(); $sp1b768e->name = '手机QQ'; $sp1b768e->img = '/plugins/images/qq.png'; $sp1b768e->driver = 'QPay'; $sp1b768e->way = 'NATIVE'; $sp1b768e->comment = '手机QQ - 扫码'; $sp1b768e->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $sp1b768e->enabled = \App\Pay::ENABLED_DISABLED; $sp1b768e->save(); $sp1b768e = new \App\Pay(); $sp1b768e->name = '支付宝'; $sp1b768e->img = '/plugins/images/ali.png'; $sp1b768e->driver = 'Youzan'; $sp1b768e->way = 'alipay'; $sp1b768e->comment = '有赞支付 - 支付宝'; $sp1b768e->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp1b768e->enabled = \App\Pay::ENABLED_DISABLED; $sp1b768e->save(); $sp1b768e = new \App\Pay(); $sp1b768e->name = '微信'; $sp1b768e->img = '/plugins/images/wx.png'; $sp1b768e->driver = 'Youzan'; $sp1b768e->way = 'wechat'; $sp1b768e->comment = '有赞支付 - 微信'; $sp1b768e->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp1b768e->enabled = \App\Pay::ENABLED_DISABLED; $sp1b768e->save(); $sp1b768e = new \App\Pay(); $sp1b768e->name = '手机QQ'; $sp1b768e->img = '/plugins/images/qq.png'; $sp1b768e->driver = 'Youzan'; $sp1b768e->way = 'qq'; $sp1b768e->comment = '有赞支付 - 手机QQ'; $sp1b768e->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp1b768e->enabled = \App\Pay::ENABLED_DISABLED; $sp1b768e->save(); $sp1b768e = new \App\Pay(); $sp1b768e->name = '支付宝'; $sp1b768e->img = '/plugins/images/ali.png'; $sp1b768e->driver = 'CodePay'; $sp1b768e->way = 'alipay'; $sp1b768e->comment = '码支付 - 支付宝'; $sp1b768e->config = '{
  "id": "id",
  "key": "key"
}'; $sp1b768e->fee_system = 0; $sp1b768e->enabled = \App\Pay::ENABLED_DISABLED; $sp1b768e->save(); $sp1b768e = new \App\Pay(); $sp1b768e->name = '微信'; $sp1b768e->img = '/plugins/images/wx.png'; $sp1b768e->driver = 'CodePay'; $sp1b768e->way = 'weixin'; $sp1b768e->comment = '码支付 - 微信'; $sp1b768e->config = '{
  "id": "id",
  "key": "key"
}'; $sp1b768e->fee_system = 0; $sp1b768e->enabled = \App\Pay::ENABLED_DISABLED; $sp1b768e->save(); $sp1b768e = new \App\Pay(); $sp1b768e->name = '手机QQ'; $sp1b768e->img = '/plugins/images/qq.png'; $sp1b768e->driver = 'CodePay'; $sp1b768e->way = 'qq'; $sp1b768e->comment = '码支付 - 手机QQ'; $sp1b768e->config = '{
  "id": "id",
  "key": "key"
}'; $sp1b768e->fee_system = 0; $sp1b768e->enabled = \App\Pay::ENABLED_DISABLED; $sp1b768e->save(); } public function run() { self::initPay(); } }