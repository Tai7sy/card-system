<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $sp1840d6 = new \App\Pay(); $sp1840d6->name = '支付宝'; $sp1840d6->img = '/plugins/images/ali.png'; $sp1840d6->driver = 'Fakala'; $sp1840d6->way = 'alipay'; $sp1840d6->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp1840d6->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp1840d6->enabled = \App\Pay::ENABLED_PC; $sp1840d6->save(); $sp1840d6 = new \App\Pay(); $sp1840d6->name = '支付宝'; $sp1840d6->img = '/plugins/images/ali.png'; $sp1840d6->driver = 'Fakala'; $sp1840d6->way = 'alipaywap'; $sp1840d6->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp1840d6->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp1840d6->enabled = \App\Pay::ENABLED_MOBILE; $sp1840d6->save(); $sp1840d6 = new \App\Pay(); $sp1840d6->name = '微信'; $sp1840d6->img = '/plugins/images/wx.png'; $sp1840d6->driver = 'Fakala'; $sp1840d6->way = 'wx'; $sp1840d6->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp1840d6->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp1840d6->enabled = \App\Pay::ENABLED_PC; $sp1840d6->save(); $sp1840d6 = new \App\Pay(); $sp1840d6->name = '微信'; $sp1840d6->img = '/plugins/images/wx.png'; $sp1840d6->driver = 'Fakala'; $sp1840d6->way = 'wxwap'; $sp1840d6->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp1840d6->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp1840d6->enabled = \App\Pay::ENABLED_MOBILE; $sp1840d6->save(); $sp1840d6 = new \App\Pay(); $sp1840d6->name = '支付宝'; $sp1840d6->img = '/plugins/images/ali.png'; $sp1840d6->driver = 'Alipay'; $sp1840d6->way = 'pc'; $sp1840d6->comment = '支付宝 - 即时到账套餐(企业)V2'; $sp1840d6->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp1840d6->enabled = \App\Pay::ENABLED_DISABLED; $sp1840d6->save(); $sp1840d6 = new \App\Pay(); $sp1840d6->name = '支付宝'; $sp1840d6->img = '/plugins/images/ali.png'; $sp1840d6->driver = 'Aliwap'; $sp1840d6->way = 'wap'; $sp1840d6->comment = '支付宝 - 高级手机网站支付V4'; $sp1840d6->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp1840d6->enabled = \App\Pay::ENABLED_DISABLED; $sp1840d6->save(); $sp1840d6 = new \App\Pay(); $sp1840d6->name = '支付宝扫码'; $sp1840d6->img = '/plugins/images/ali.png'; $sp1840d6->driver = 'AliAop'; $sp1840d6->way = 'f2f'; $sp1840d6->comment = '支付宝 - 当面付'; $sp1840d6->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp1840d6->enabled = \App\Pay::ENABLED_DISABLED; $sp1840d6->save(); $sp1840d6 = new \App\Pay(); $sp1840d6->name = '支付宝'; $sp1840d6->img = '/plugins/images/ali.png'; $sp1840d6->driver = 'AliAop'; $sp1840d6->way = 'pc'; $sp1840d6->comment = '支付宝 - 电脑网站支付 (新)'; $sp1840d6->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp1840d6->enabled = \App\Pay::ENABLED_DISABLED; $sp1840d6->save(); $sp1840d6 = new \App\Pay(); $sp1840d6->name = '手机支付宝'; $sp1840d6->img = '/plugins/images/ali.png'; $sp1840d6->driver = 'AliAop'; $sp1840d6->way = 'mobile'; $sp1840d6->comment = '支付宝 - 手机网站支付 (新)'; $sp1840d6->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp1840d6->enabled = \App\Pay::ENABLED_DISABLED; $sp1840d6->save(); $sp1840d6 = new \App\Pay(); $sp1840d6->name = '微信扫码'; $sp1840d6->img = '/plugins/images/wx.png'; $sp1840d6->driver = 'WeChat'; $sp1840d6->way = 'NATIVE'; $sp1840d6->comment = '微信支付 - 扫码'; $sp1840d6->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp1840d6->enabled = \App\Pay::ENABLED_DISABLED; $sp1840d6->save(); $sp1840d6 = new \App\Pay(); $sp1840d6->name = '微信H5'; $sp1840d6->img = '/plugins/images/wx.png'; $sp1840d6->driver = 'WeChat'; $sp1840d6->way = 'MWEB'; $sp1840d6->comment = '微信支付 - H5 (需要开通权限)'; $sp1840d6->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp1840d6->enabled = \App\Pay::ENABLED_DISABLED; $sp1840d6->save(); $sp1840d6 = new \App\Pay(); $sp1840d6->name = '手机QQ'; $sp1840d6->img = '/plugins/images/qq.png'; $sp1840d6->driver = 'QPay'; $sp1840d6->way = 'NATIVE'; $sp1840d6->comment = '手机QQ - 扫码'; $sp1840d6->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $sp1840d6->enabled = \App\Pay::ENABLED_DISABLED; $sp1840d6->save(); $sp1840d6 = new \App\Pay(); $sp1840d6->name = '支付宝'; $sp1840d6->img = '/plugins/images/ali.png'; $sp1840d6->driver = 'Youzan'; $sp1840d6->way = 'alipay'; $sp1840d6->comment = '有赞支付 - 支付宝'; $sp1840d6->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp1840d6->enabled = \App\Pay::ENABLED_DISABLED; $sp1840d6->save(); $sp1840d6 = new \App\Pay(); $sp1840d6->name = '微信'; $sp1840d6->img = '/plugins/images/wx.png'; $sp1840d6->driver = 'Youzan'; $sp1840d6->way = 'wechat'; $sp1840d6->comment = '有赞支付 - 微信'; $sp1840d6->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp1840d6->enabled = \App\Pay::ENABLED_DISABLED; $sp1840d6->save(); $sp1840d6 = new \App\Pay(); $sp1840d6->name = '手机QQ'; $sp1840d6->img = '/plugins/images/qq.png'; $sp1840d6->driver = 'Youzan'; $sp1840d6->way = 'qq'; $sp1840d6->comment = '有赞支付 - 手机QQ'; $sp1840d6->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp1840d6->enabled = \App\Pay::ENABLED_DISABLED; $sp1840d6->save(); $sp1840d6 = new \App\Pay(); $sp1840d6->name = '支付宝'; $sp1840d6->img = '/plugins/images/ali.png'; $sp1840d6->driver = 'CodePay'; $sp1840d6->way = 'alipay'; $sp1840d6->comment = '码支付 - 支付宝'; $sp1840d6->config = '{
  "id": "id",
  "key": "key"
}'; $sp1840d6->fee_system = 0; $sp1840d6->enabled = \App\Pay::ENABLED_DISABLED; $sp1840d6->save(); $sp1840d6 = new \App\Pay(); $sp1840d6->name = '微信'; $sp1840d6->img = '/plugins/images/wx.png'; $sp1840d6->driver = 'CodePay'; $sp1840d6->way = 'weixin'; $sp1840d6->comment = '码支付 - 微信'; $sp1840d6->config = '{
  "id": "id",
  "key": "key"
}'; $sp1840d6->fee_system = 0; $sp1840d6->enabled = \App\Pay::ENABLED_DISABLED; $sp1840d6->save(); $sp1840d6 = new \App\Pay(); $sp1840d6->name = '手机QQ'; $sp1840d6->img = '/plugins/images/qq.png'; $sp1840d6->driver = 'CodePay'; $sp1840d6->way = 'qq'; $sp1840d6->comment = '码支付 - 手机QQ'; $sp1840d6->config = '{
  "id": "id",
  "key": "key"
}'; $sp1840d6->fee_system = 0; $sp1840d6->enabled = \App\Pay::ENABLED_DISABLED; $sp1840d6->save(); } public function run() { self::initPay(); } }