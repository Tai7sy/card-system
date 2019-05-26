<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $spf13a06 = new \App\Pay(); $spf13a06->name = '支付宝'; $spf13a06->img = '/plugins/images/ali.png'; $spf13a06->driver = 'Fakala'; $spf13a06->way = 'alipay'; $spf13a06->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $spf13a06->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $spf13a06->enabled = \App\Pay::ENABLED_PC; $spf13a06->save(); $spf13a06 = new \App\Pay(); $spf13a06->name = '支付宝'; $spf13a06->img = '/plugins/images/ali.png'; $spf13a06->driver = 'Fakala'; $spf13a06->way = 'alipaywap'; $spf13a06->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $spf13a06->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $spf13a06->enabled = \App\Pay::ENABLED_MOBILE; $spf13a06->save(); $spf13a06 = new \App\Pay(); $spf13a06->name = '微信'; $spf13a06->img = '/plugins/images/wx.png'; $spf13a06->driver = 'Fakala'; $spf13a06->way = 'wx'; $spf13a06->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $spf13a06->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $spf13a06->enabled = \App\Pay::ENABLED_PC; $spf13a06->save(); $spf13a06 = new \App\Pay(); $spf13a06->name = '微信'; $spf13a06->img = '/plugins/images/wx.png'; $spf13a06->driver = 'Fakala'; $spf13a06->way = 'wxwap'; $spf13a06->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $spf13a06->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $spf13a06->enabled = \App\Pay::ENABLED_MOBILE; $spf13a06->save(); $spf13a06 = new \App\Pay(); $spf13a06->name = '支付宝'; $spf13a06->img = '/plugins/images/ali.png'; $spf13a06->driver = 'Alipay'; $spf13a06->way = 'pc'; $spf13a06->comment = '支付宝 - 即时到账套餐(企业)V2'; $spf13a06->config = '{
  "partner": "partner",
  "key": "key"
}'; $spf13a06->enabled = \App\Pay::ENABLED_DISABLED; $spf13a06->save(); $spf13a06 = new \App\Pay(); $spf13a06->name = '支付宝'; $spf13a06->img = '/plugins/images/ali.png'; $spf13a06->driver = 'Aliwap'; $spf13a06->way = 'wap'; $spf13a06->comment = '支付宝 - 高级手机网站支付V4'; $spf13a06->config = '{
  "partner": "partner",
  "key": "key"
}'; $spf13a06->enabled = \App\Pay::ENABLED_DISABLED; $spf13a06->save(); $spf13a06 = new \App\Pay(); $spf13a06->name = '支付宝扫码'; $spf13a06->img = '/plugins/images/ali.png'; $spf13a06->driver = 'AliAop'; $spf13a06->way = 'f2f'; $spf13a06->comment = '支付宝 - 当面付'; $spf13a06->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $spf13a06->enabled = \App\Pay::ENABLED_DISABLED; $spf13a06->save(); $spf13a06 = new \App\Pay(); $spf13a06->name = '支付宝'; $spf13a06->img = '/plugins/images/ali.png'; $spf13a06->driver = 'AliAop'; $spf13a06->way = 'pc'; $spf13a06->comment = '支付宝 - 电脑网站支付 (新)'; $spf13a06->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $spf13a06->enabled = \App\Pay::ENABLED_DISABLED; $spf13a06->save(); $spf13a06 = new \App\Pay(); $spf13a06->name = '手机支付宝'; $spf13a06->img = '/plugins/images/ali.png'; $spf13a06->driver = 'AliAop'; $spf13a06->way = 'mobile'; $spf13a06->comment = '支付宝 - 手机网站支付 (新)'; $spf13a06->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $spf13a06->enabled = \App\Pay::ENABLED_DISABLED; $spf13a06->save(); $spf13a06 = new \App\Pay(); $spf13a06->name = '微信扫码'; $spf13a06->img = '/plugins/images/wx.png'; $spf13a06->driver = 'WeChat'; $spf13a06->way = 'NATIVE'; $spf13a06->comment = '微信支付 - 扫码'; $spf13a06->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $spf13a06->enabled = \App\Pay::ENABLED_DISABLED; $spf13a06->save(); $spf13a06 = new \App\Pay(); $spf13a06->name = '微信H5'; $spf13a06->img = '/plugins/images/wx.png'; $spf13a06->driver = 'WeChat'; $spf13a06->way = 'MWEB'; $spf13a06->comment = '微信支付 - H5 (需要开通权限)'; $spf13a06->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $spf13a06->enabled = \App\Pay::ENABLED_DISABLED; $spf13a06->save(); $spf13a06 = new \App\Pay(); $spf13a06->name = '手机QQ'; $spf13a06->img = '/plugins/images/qq.png'; $spf13a06->driver = 'QPay'; $spf13a06->way = 'NATIVE'; $spf13a06->comment = '手机QQ - 扫码'; $spf13a06->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $spf13a06->enabled = \App\Pay::ENABLED_DISABLED; $spf13a06->save(); $spf13a06 = new \App\Pay(); $spf13a06->name = '支付宝'; $spf13a06->img = '/plugins/images/ali.png'; $spf13a06->driver = 'Youzan'; $spf13a06->way = 'alipay'; $spf13a06->comment = '有赞支付 - 支付宝'; $spf13a06->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $spf13a06->enabled = \App\Pay::ENABLED_DISABLED; $spf13a06->save(); $spf13a06 = new \App\Pay(); $spf13a06->name = '微信'; $spf13a06->img = '/plugins/images/wx.png'; $spf13a06->driver = 'Youzan'; $spf13a06->way = 'wechat'; $spf13a06->comment = '有赞支付 - 微信'; $spf13a06->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $spf13a06->enabled = \App\Pay::ENABLED_DISABLED; $spf13a06->save(); $spf13a06 = new \App\Pay(); $spf13a06->name = '手机QQ'; $spf13a06->img = '/plugins/images/qq.png'; $spf13a06->driver = 'Youzan'; $spf13a06->way = 'qq'; $spf13a06->comment = '有赞支付 - 手机QQ'; $spf13a06->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $spf13a06->enabled = \App\Pay::ENABLED_DISABLED; $spf13a06->save(); $spf13a06 = new \App\Pay(); $spf13a06->name = '支付宝'; $spf13a06->img = '/plugins/images/ali.png'; $spf13a06->driver = 'CodePay'; $spf13a06->way = 'alipay'; $spf13a06->comment = '码支付 - 支付宝'; $spf13a06->config = '{
  "id": "id",
  "key": "key"
}'; $spf13a06->fee_system = 0; $spf13a06->enabled = \App\Pay::ENABLED_DISABLED; $spf13a06->save(); $spf13a06 = new \App\Pay(); $spf13a06->name = '微信'; $spf13a06->img = '/plugins/images/wx.png'; $spf13a06->driver = 'CodePay'; $spf13a06->way = 'weixin'; $spf13a06->comment = '码支付 - 微信'; $spf13a06->config = '{
  "id": "id",
  "key": "key"
}'; $spf13a06->fee_system = 0; $spf13a06->enabled = \App\Pay::ENABLED_DISABLED; $spf13a06->save(); $spf13a06 = new \App\Pay(); $spf13a06->name = '手机QQ'; $spf13a06->img = '/plugins/images/qq.png'; $spf13a06->driver = 'CodePay'; $spf13a06->way = 'qq'; $spf13a06->comment = '码支付 - 手机QQ'; $spf13a06->config = '{
  "id": "id",
  "key": "key"
}'; $spf13a06->fee_system = 0; $spf13a06->enabled = \App\Pay::ENABLED_DISABLED; $spf13a06->save(); } public function run() { self::initPay(); } }