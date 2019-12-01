<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '支付宝'; $sp1d1f3d->img = '/plugins/images/ali.png'; $sp1d1f3d->driver = 'Fakala'; $sp1d1f3d->way = 'alipay'; $sp1d1f3d->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp1d1f3d->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp1d1f3d->enabled = \App\Pay::ENABLED_PC; $sp1d1f3d->save(); $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '支付宝'; $sp1d1f3d->img = '/plugins/images/ali.png'; $sp1d1f3d->driver = 'Fakala'; $sp1d1f3d->way = 'alipaywap'; $sp1d1f3d->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp1d1f3d->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp1d1f3d->enabled = \App\Pay::ENABLED_MOBILE; $sp1d1f3d->save(); $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '微信'; $sp1d1f3d->img = '/plugins/images/wx.png'; $sp1d1f3d->driver = 'Fakala'; $sp1d1f3d->way = 'wx'; $sp1d1f3d->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp1d1f3d->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp1d1f3d->enabled = \App\Pay::ENABLED_PC; $sp1d1f3d->save(); $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '微信'; $sp1d1f3d->img = '/plugins/images/wx.png'; $sp1d1f3d->driver = 'Fakala'; $sp1d1f3d->way = 'wxwap'; $sp1d1f3d->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp1d1f3d->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp1d1f3d->enabled = \App\Pay::ENABLED_MOBILE; $sp1d1f3d->save(); $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '支付宝'; $sp1d1f3d->img = '/plugins/images/ali.png'; $sp1d1f3d->driver = 'Alipay'; $sp1d1f3d->way = 'pc'; $sp1d1f3d->comment = '支付宝 - 即时到账套餐(企业)V2'; $sp1d1f3d->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp1d1f3d->enabled = \App\Pay::ENABLED_DISABLED; $sp1d1f3d->save(); $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '支付宝'; $sp1d1f3d->img = '/plugins/images/ali.png'; $sp1d1f3d->driver = 'Aliwap'; $sp1d1f3d->way = 'wap'; $sp1d1f3d->comment = '支付宝 - 高级手机网站支付V4'; $sp1d1f3d->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp1d1f3d->enabled = \App\Pay::ENABLED_DISABLED; $sp1d1f3d->save(); $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '支付宝扫码'; $sp1d1f3d->img = '/plugins/images/ali.png'; $sp1d1f3d->driver = 'AliAop'; $sp1d1f3d->way = 'f2f'; $sp1d1f3d->comment = '支付宝 - 当面付'; $sp1d1f3d->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp1d1f3d->enabled = \App\Pay::ENABLED_DISABLED; $sp1d1f3d->save(); $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '支付宝'; $sp1d1f3d->img = '/plugins/images/ali.png'; $sp1d1f3d->driver = 'AliAop'; $sp1d1f3d->way = 'pc'; $sp1d1f3d->comment = '支付宝 - 电脑网站支付 (新)'; $sp1d1f3d->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp1d1f3d->enabled = \App\Pay::ENABLED_DISABLED; $sp1d1f3d->save(); $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '手机支付宝'; $sp1d1f3d->img = '/plugins/images/ali.png'; $sp1d1f3d->driver = 'AliAop'; $sp1d1f3d->way = 'mobile'; $sp1d1f3d->comment = '支付宝 - 手机网站支付 (新)'; $sp1d1f3d->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp1d1f3d->enabled = \App\Pay::ENABLED_DISABLED; $sp1d1f3d->save(); $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '微信扫码'; $sp1d1f3d->img = '/plugins/images/wx.png'; $sp1d1f3d->driver = 'WeChat'; $sp1d1f3d->way = 'NATIVE'; $sp1d1f3d->comment = '微信支付 - 扫码'; $sp1d1f3d->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp1d1f3d->enabled = \App\Pay::ENABLED_DISABLED; $sp1d1f3d->save(); $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '微信H5'; $sp1d1f3d->img = '/plugins/images/wx.png'; $sp1d1f3d->driver = 'WeChat'; $sp1d1f3d->way = 'MWEB'; $sp1d1f3d->comment = '微信支付 - H5 (需要开通权限)'; $sp1d1f3d->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp1d1f3d->enabled = \App\Pay::ENABLED_DISABLED; $sp1d1f3d->save(); $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '手机QQ'; $sp1d1f3d->img = '/plugins/images/qq.png'; $sp1d1f3d->driver = 'QPay'; $sp1d1f3d->way = 'NATIVE'; $sp1d1f3d->comment = '手机QQ - 扫码'; $sp1d1f3d->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $sp1d1f3d->enabled = \App\Pay::ENABLED_DISABLED; $sp1d1f3d->save(); $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '支付宝'; $sp1d1f3d->img = '/plugins/images/ali.png'; $sp1d1f3d->driver = 'Youzan'; $sp1d1f3d->way = 'alipay'; $sp1d1f3d->comment = '有赞支付 - 支付宝'; $sp1d1f3d->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp1d1f3d->enabled = \App\Pay::ENABLED_DISABLED; $sp1d1f3d->save(); $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '微信'; $sp1d1f3d->img = '/plugins/images/wx.png'; $sp1d1f3d->driver = 'Youzan'; $sp1d1f3d->way = 'wechat'; $sp1d1f3d->comment = '有赞支付 - 微信'; $sp1d1f3d->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp1d1f3d->enabled = \App\Pay::ENABLED_DISABLED; $sp1d1f3d->save(); $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '手机QQ'; $sp1d1f3d->img = '/plugins/images/qq.png'; $sp1d1f3d->driver = 'Youzan'; $sp1d1f3d->way = 'qq'; $sp1d1f3d->comment = '有赞支付 - 手机QQ'; $sp1d1f3d->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp1d1f3d->enabled = \App\Pay::ENABLED_DISABLED; $sp1d1f3d->save(); $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '支付宝'; $sp1d1f3d->img = '/plugins/images/ali.png'; $sp1d1f3d->driver = 'CodePay'; $sp1d1f3d->way = 'alipay'; $sp1d1f3d->comment = '码支付 - 支付宝'; $sp1d1f3d->config = '{
  "id": "id",
  "key": "key"
}'; $sp1d1f3d->fee_system = 0; $sp1d1f3d->enabled = \App\Pay::ENABLED_DISABLED; $sp1d1f3d->save(); $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '微信'; $sp1d1f3d->img = '/plugins/images/wx.png'; $sp1d1f3d->driver = 'CodePay'; $sp1d1f3d->way = 'weixin'; $sp1d1f3d->comment = '码支付 - 微信'; $sp1d1f3d->config = '{
  "id": "id",
  "key": "key"
}'; $sp1d1f3d->fee_system = 0; $sp1d1f3d->enabled = \App\Pay::ENABLED_DISABLED; $sp1d1f3d->save(); $sp1d1f3d = new \App\Pay(); $sp1d1f3d->name = '手机QQ'; $sp1d1f3d->img = '/plugins/images/qq.png'; $sp1d1f3d->driver = 'CodePay'; $sp1d1f3d->way = 'qq'; $sp1d1f3d->comment = '码支付 - 手机QQ'; $sp1d1f3d->config = '{
  "id": "id",
  "key": "key"
}'; $sp1d1f3d->fee_system = 0; $sp1d1f3d->enabled = \App\Pay::ENABLED_DISABLED; $sp1d1f3d->save(); } public function run() { self::initPay(); } }