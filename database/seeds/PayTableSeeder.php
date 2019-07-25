<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $spbe896f = new \App\Pay(); $spbe896f->name = '支付宝'; $spbe896f->img = '/plugins/images/ali.png'; $spbe896f->driver = 'Fakala'; $spbe896f->way = 'alipay'; $spbe896f->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $spbe896f->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $spbe896f->enabled = \App\Pay::ENABLED_PC; $spbe896f->save(); $spbe896f = new \App\Pay(); $spbe896f->name = '支付宝'; $spbe896f->img = '/plugins/images/ali.png'; $spbe896f->driver = 'Fakala'; $spbe896f->way = 'alipaywap'; $spbe896f->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $spbe896f->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $spbe896f->enabled = \App\Pay::ENABLED_MOBILE; $spbe896f->save(); $spbe896f = new \App\Pay(); $spbe896f->name = '微信'; $spbe896f->img = '/plugins/images/wx.png'; $spbe896f->driver = 'Fakala'; $spbe896f->way = 'wx'; $spbe896f->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $spbe896f->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $spbe896f->enabled = \App\Pay::ENABLED_PC; $spbe896f->save(); $spbe896f = new \App\Pay(); $spbe896f->name = '微信'; $spbe896f->img = '/plugins/images/wx.png'; $spbe896f->driver = 'Fakala'; $spbe896f->way = 'wxwap'; $spbe896f->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $spbe896f->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $spbe896f->enabled = \App\Pay::ENABLED_MOBILE; $spbe896f->save(); $spbe896f = new \App\Pay(); $spbe896f->name = '支付宝'; $spbe896f->img = '/plugins/images/ali.png'; $spbe896f->driver = 'Alipay'; $spbe896f->way = 'pc'; $spbe896f->comment = '支付宝 - 即时到账套餐(企业)V2'; $spbe896f->config = '{
  "partner": "partner",
  "key": "key"
}'; $spbe896f->enabled = \App\Pay::ENABLED_DISABLED; $spbe896f->save(); $spbe896f = new \App\Pay(); $spbe896f->name = '支付宝'; $spbe896f->img = '/plugins/images/ali.png'; $spbe896f->driver = 'Aliwap'; $spbe896f->way = 'wap'; $spbe896f->comment = '支付宝 - 高级手机网站支付V4'; $spbe896f->config = '{
  "partner": "partner",
  "key": "key"
}'; $spbe896f->enabled = \App\Pay::ENABLED_DISABLED; $spbe896f->save(); $spbe896f = new \App\Pay(); $spbe896f->name = '支付宝扫码'; $spbe896f->img = '/plugins/images/ali.png'; $spbe896f->driver = 'AliAop'; $spbe896f->way = 'f2f'; $spbe896f->comment = '支付宝 - 当面付'; $spbe896f->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $spbe896f->enabled = \App\Pay::ENABLED_DISABLED; $spbe896f->save(); $spbe896f = new \App\Pay(); $spbe896f->name = '支付宝'; $spbe896f->img = '/plugins/images/ali.png'; $spbe896f->driver = 'AliAop'; $spbe896f->way = 'pc'; $spbe896f->comment = '支付宝 - 电脑网站支付 (新)'; $spbe896f->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $spbe896f->enabled = \App\Pay::ENABLED_DISABLED; $spbe896f->save(); $spbe896f = new \App\Pay(); $spbe896f->name = '手机支付宝'; $spbe896f->img = '/plugins/images/ali.png'; $spbe896f->driver = 'AliAop'; $spbe896f->way = 'mobile'; $spbe896f->comment = '支付宝 - 手机网站支付 (新)'; $spbe896f->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $spbe896f->enabled = \App\Pay::ENABLED_DISABLED; $spbe896f->save(); $spbe896f = new \App\Pay(); $spbe896f->name = '微信扫码'; $spbe896f->img = '/plugins/images/wx.png'; $spbe896f->driver = 'WeChat'; $spbe896f->way = 'NATIVE'; $spbe896f->comment = '微信支付 - 扫码'; $spbe896f->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $spbe896f->enabled = \App\Pay::ENABLED_DISABLED; $spbe896f->save(); $spbe896f = new \App\Pay(); $spbe896f->name = '微信H5'; $spbe896f->img = '/plugins/images/wx.png'; $spbe896f->driver = 'WeChat'; $spbe896f->way = 'MWEB'; $spbe896f->comment = '微信支付 - H5 (需要开通权限)'; $spbe896f->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $spbe896f->enabled = \App\Pay::ENABLED_DISABLED; $spbe896f->save(); $spbe896f = new \App\Pay(); $spbe896f->name = '手机QQ'; $spbe896f->img = '/plugins/images/qq.png'; $spbe896f->driver = 'QPay'; $spbe896f->way = 'NATIVE'; $spbe896f->comment = '手机QQ - 扫码'; $spbe896f->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $spbe896f->enabled = \App\Pay::ENABLED_DISABLED; $spbe896f->save(); $spbe896f = new \App\Pay(); $spbe896f->name = '支付宝'; $spbe896f->img = '/plugins/images/ali.png'; $spbe896f->driver = 'Youzan'; $spbe896f->way = 'alipay'; $spbe896f->comment = '有赞支付 - 支付宝'; $spbe896f->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $spbe896f->enabled = \App\Pay::ENABLED_DISABLED; $spbe896f->save(); $spbe896f = new \App\Pay(); $spbe896f->name = '微信'; $spbe896f->img = '/plugins/images/wx.png'; $spbe896f->driver = 'Youzan'; $spbe896f->way = 'wechat'; $spbe896f->comment = '有赞支付 - 微信'; $spbe896f->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $spbe896f->enabled = \App\Pay::ENABLED_DISABLED; $spbe896f->save(); $spbe896f = new \App\Pay(); $spbe896f->name = '手机QQ'; $spbe896f->img = '/plugins/images/qq.png'; $spbe896f->driver = 'Youzan'; $spbe896f->way = 'qq'; $spbe896f->comment = '有赞支付 - 手机QQ'; $spbe896f->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $spbe896f->enabled = \App\Pay::ENABLED_DISABLED; $spbe896f->save(); $spbe896f = new \App\Pay(); $spbe896f->name = '支付宝'; $spbe896f->img = '/plugins/images/ali.png'; $spbe896f->driver = 'CodePay'; $spbe896f->way = 'alipay'; $spbe896f->comment = '码支付 - 支付宝'; $spbe896f->config = '{
  "id": "id",
  "key": "key"
}'; $spbe896f->fee_system = 0; $spbe896f->enabled = \App\Pay::ENABLED_DISABLED; $spbe896f->save(); $spbe896f = new \App\Pay(); $spbe896f->name = '微信'; $spbe896f->img = '/plugins/images/wx.png'; $spbe896f->driver = 'CodePay'; $spbe896f->way = 'weixin'; $spbe896f->comment = '码支付 - 微信'; $spbe896f->config = '{
  "id": "id",
  "key": "key"
}'; $spbe896f->fee_system = 0; $spbe896f->enabled = \App\Pay::ENABLED_DISABLED; $spbe896f->save(); $spbe896f = new \App\Pay(); $spbe896f->name = '手机QQ'; $spbe896f->img = '/plugins/images/qq.png'; $spbe896f->driver = 'CodePay'; $spbe896f->way = 'qq'; $spbe896f->comment = '码支付 - 手机QQ'; $spbe896f->config = '{
  "id": "id",
  "key": "key"
}'; $spbe896f->fee_system = 0; $spbe896f->enabled = \App\Pay::ENABLED_DISABLED; $spbe896f->save(); } public function run() { self::initPay(); } }