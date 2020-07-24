<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $sp7023fd = new \App\Pay(); $sp7023fd->name = '支付宝'; $sp7023fd->img = '/plugins/images/ali.png'; $sp7023fd->driver = 'Fakala'; $sp7023fd->way = 'alipay'; $sp7023fd->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp7023fd->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp7023fd->enabled = \App\Pay::ENABLED_PC; $sp7023fd->save(); $sp7023fd = new \App\Pay(); $sp7023fd->name = '支付宝'; $sp7023fd->img = '/plugins/images/ali.png'; $sp7023fd->driver = 'Fakala'; $sp7023fd->way = 'alipaywap'; $sp7023fd->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp7023fd->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp7023fd->enabled = \App\Pay::ENABLED_MOBILE; $sp7023fd->save(); $sp7023fd = new \App\Pay(); $sp7023fd->name = '微信'; $sp7023fd->img = '/plugins/images/wx.png'; $sp7023fd->driver = 'Fakala'; $sp7023fd->way = 'wx'; $sp7023fd->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp7023fd->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp7023fd->enabled = \App\Pay::ENABLED_PC; $sp7023fd->save(); $sp7023fd = new \App\Pay(); $sp7023fd->name = '微信'; $sp7023fd->img = '/plugins/images/wx.png'; $sp7023fd->driver = 'Fakala'; $sp7023fd->way = 'wxwap'; $sp7023fd->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp7023fd->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp7023fd->enabled = \App\Pay::ENABLED_MOBILE; $sp7023fd->save(); $sp7023fd = new \App\Pay(); $sp7023fd->name = '支付宝'; $sp7023fd->img = '/plugins/images/ali.png'; $sp7023fd->driver = 'Alipay'; $sp7023fd->way = 'pc'; $sp7023fd->comment = '支付宝 - 即时到账套餐(企业)V2'; $sp7023fd->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp7023fd->enabled = \App\Pay::ENABLED_DISABLED; $sp7023fd->save(); $sp7023fd = new \App\Pay(); $sp7023fd->name = '支付宝'; $sp7023fd->img = '/plugins/images/ali.png'; $sp7023fd->driver = 'Aliwap'; $sp7023fd->way = 'wap'; $sp7023fd->comment = '支付宝 - 高级手机网站支付V4'; $sp7023fd->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp7023fd->enabled = \App\Pay::ENABLED_DISABLED; $sp7023fd->save(); $sp7023fd = new \App\Pay(); $sp7023fd->name = '支付宝扫码'; $sp7023fd->img = '/plugins/images/ali.png'; $sp7023fd->driver = 'AliAop'; $sp7023fd->way = 'f2f'; $sp7023fd->comment = '支付宝 - 当面付'; $sp7023fd->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp7023fd->enabled = \App\Pay::ENABLED_DISABLED; $sp7023fd->save(); $sp7023fd = new \App\Pay(); $sp7023fd->name = '支付宝'; $sp7023fd->img = '/plugins/images/ali.png'; $sp7023fd->driver = 'AliAop'; $sp7023fd->way = 'pc'; $sp7023fd->comment = '支付宝 - 电脑网站支付 (新)'; $sp7023fd->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp7023fd->enabled = \App\Pay::ENABLED_DISABLED; $sp7023fd->save(); $sp7023fd = new \App\Pay(); $sp7023fd->name = '手机支付宝'; $sp7023fd->img = '/plugins/images/ali.png'; $sp7023fd->driver = 'AliAop'; $sp7023fd->way = 'mobile'; $sp7023fd->comment = '支付宝 - 手机网站支付 (新)'; $sp7023fd->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp7023fd->enabled = \App\Pay::ENABLED_DISABLED; $sp7023fd->save(); $sp7023fd = new \App\Pay(); $sp7023fd->name = '微信扫码'; $sp7023fd->img = '/plugins/images/wx.png'; $sp7023fd->driver = 'WeChat'; $sp7023fd->way = 'NATIVE'; $sp7023fd->comment = '微信支付 - 扫码'; $sp7023fd->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp7023fd->enabled = \App\Pay::ENABLED_DISABLED; $sp7023fd->save(); $sp7023fd = new \App\Pay(); $sp7023fd->name = '微信H5'; $sp7023fd->img = '/plugins/images/wx.png'; $sp7023fd->driver = 'WeChat'; $sp7023fd->way = 'MWEB'; $sp7023fd->comment = '微信支付 - H5 (需要开通权限)'; $sp7023fd->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp7023fd->enabled = \App\Pay::ENABLED_DISABLED; $sp7023fd->save(); $sp7023fd = new \App\Pay(); $sp7023fd->name = '手机QQ'; $sp7023fd->img = '/plugins/images/qq.png'; $sp7023fd->driver = 'QPay'; $sp7023fd->way = 'NATIVE'; $sp7023fd->comment = '手机QQ - 扫码'; $sp7023fd->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $sp7023fd->enabled = \App\Pay::ENABLED_DISABLED; $sp7023fd->save(); $sp7023fd = new \App\Pay(); $sp7023fd->name = '支付宝'; $sp7023fd->img = '/plugins/images/ali.png'; $sp7023fd->driver = 'Youzan'; $sp7023fd->way = 'alipay'; $sp7023fd->comment = '有赞支付 - 支付宝'; $sp7023fd->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp7023fd->enabled = \App\Pay::ENABLED_DISABLED; $sp7023fd->save(); $sp7023fd = new \App\Pay(); $sp7023fd->name = '微信'; $sp7023fd->img = '/plugins/images/wx.png'; $sp7023fd->driver = 'Youzan'; $sp7023fd->way = 'wechat'; $sp7023fd->comment = '有赞支付 - 微信'; $sp7023fd->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp7023fd->enabled = \App\Pay::ENABLED_DISABLED; $sp7023fd->save(); $sp7023fd = new \App\Pay(); $sp7023fd->name = '手机QQ'; $sp7023fd->img = '/plugins/images/qq.png'; $sp7023fd->driver = 'Youzan'; $sp7023fd->way = 'qq'; $sp7023fd->comment = '有赞支付 - 手机QQ'; $sp7023fd->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp7023fd->enabled = \App\Pay::ENABLED_DISABLED; $sp7023fd->save(); $sp7023fd = new \App\Pay(); $sp7023fd->name = '支付宝'; $sp7023fd->img = '/plugins/images/ali.png'; $sp7023fd->driver = 'CodePay'; $sp7023fd->way = 'alipay'; $sp7023fd->comment = '码支付 - 支付宝'; $sp7023fd->config = '{
  "id": "id",
  "key": "key"
}'; $sp7023fd->fee_system = 0; $sp7023fd->enabled = \App\Pay::ENABLED_DISABLED; $sp7023fd->save(); $sp7023fd = new \App\Pay(); $sp7023fd->name = '微信'; $sp7023fd->img = '/plugins/images/wx.png'; $sp7023fd->driver = 'CodePay'; $sp7023fd->way = 'weixin'; $sp7023fd->comment = '码支付 - 微信'; $sp7023fd->config = '{
  "id": "id",
  "key": "key"
}'; $sp7023fd->fee_system = 0; $sp7023fd->enabled = \App\Pay::ENABLED_DISABLED; $sp7023fd->save(); $sp7023fd = new \App\Pay(); $sp7023fd->name = '手机QQ'; $sp7023fd->img = '/plugins/images/qq.png'; $sp7023fd->driver = 'CodePay'; $sp7023fd->way = 'qq'; $sp7023fd->comment = '码支付 - 手机QQ'; $sp7023fd->config = '{
  "id": "id",
  "key": "key"
}'; $sp7023fd->fee_system = 0; $sp7023fd->enabled = \App\Pay::ENABLED_DISABLED; $sp7023fd->save(); } public function run() { self::initPay(); } }