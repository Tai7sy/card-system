<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $sp1596b5 = new \App\Pay(); $sp1596b5->name = '支付宝'; $sp1596b5->img = '/plugins/images/ali.png'; $sp1596b5->driver = 'Fakala'; $sp1596b5->way = 'alipay'; $sp1596b5->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp1596b5->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp1596b5->enabled = \App\Pay::ENABLED_PC; $sp1596b5->save(); $sp1596b5 = new \App\Pay(); $sp1596b5->name = '支付宝'; $sp1596b5->img = '/plugins/images/ali.png'; $sp1596b5->driver = 'Fakala'; $sp1596b5->way = 'alipaywap'; $sp1596b5->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp1596b5->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp1596b5->enabled = \App\Pay::ENABLED_MOBILE; $sp1596b5->save(); $sp1596b5 = new \App\Pay(); $sp1596b5->name = '微信'; $sp1596b5->img = '/plugins/images/wx.png'; $sp1596b5->driver = 'Fakala'; $sp1596b5->way = 'wx'; $sp1596b5->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp1596b5->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp1596b5->enabled = \App\Pay::ENABLED_PC; $sp1596b5->save(); $sp1596b5 = new \App\Pay(); $sp1596b5->name = '微信'; $sp1596b5->img = '/plugins/images/wx.png'; $sp1596b5->driver = 'Fakala'; $sp1596b5->way = 'wxwap'; $sp1596b5->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp1596b5->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp1596b5->enabled = \App\Pay::ENABLED_MOBILE; $sp1596b5->save(); $sp1596b5 = new \App\Pay(); $sp1596b5->name = '支付宝'; $sp1596b5->img = '/plugins/images/ali.png'; $sp1596b5->driver = 'Alipay'; $sp1596b5->way = 'pc'; $sp1596b5->comment = '支付宝 - 即时到账套餐(企业)V2'; $sp1596b5->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp1596b5->enabled = \App\Pay::ENABLED_DISABLED; $sp1596b5->save(); $sp1596b5 = new \App\Pay(); $sp1596b5->name = '支付宝'; $sp1596b5->img = '/plugins/images/ali.png'; $sp1596b5->driver = 'Aliwap'; $sp1596b5->way = 'wap'; $sp1596b5->comment = '支付宝 - 高级手机网站支付V4'; $sp1596b5->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp1596b5->enabled = \App\Pay::ENABLED_DISABLED; $sp1596b5->save(); $sp1596b5 = new \App\Pay(); $sp1596b5->name = '支付宝扫码'; $sp1596b5->img = '/plugins/images/ali.png'; $sp1596b5->driver = 'AliAop'; $sp1596b5->way = 'f2f'; $sp1596b5->comment = '支付宝 - 当面付'; $sp1596b5->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp1596b5->enabled = \App\Pay::ENABLED_DISABLED; $sp1596b5->save(); $sp1596b5 = new \App\Pay(); $sp1596b5->name = '支付宝'; $sp1596b5->img = '/plugins/images/ali.png'; $sp1596b5->driver = 'AliAop'; $sp1596b5->way = 'pc'; $sp1596b5->comment = '支付宝 - 电脑网站支付 (新)'; $sp1596b5->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp1596b5->enabled = \App\Pay::ENABLED_DISABLED; $sp1596b5->save(); $sp1596b5 = new \App\Pay(); $sp1596b5->name = '手机支付宝'; $sp1596b5->img = '/plugins/images/ali.png'; $sp1596b5->driver = 'AliAop'; $sp1596b5->way = 'mobile'; $sp1596b5->comment = '支付宝 - 手机网站支付 (新)'; $sp1596b5->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp1596b5->enabled = \App\Pay::ENABLED_DISABLED; $sp1596b5->save(); $sp1596b5 = new \App\Pay(); $sp1596b5->name = '微信扫码'; $sp1596b5->img = '/plugins/images/wx.png'; $sp1596b5->driver = 'WeChat'; $sp1596b5->way = 'NATIVE'; $sp1596b5->comment = '微信支付 - 扫码'; $sp1596b5->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp1596b5->enabled = \App\Pay::ENABLED_DISABLED; $sp1596b5->save(); $sp1596b5 = new \App\Pay(); $sp1596b5->name = '微信H5'; $sp1596b5->img = '/plugins/images/wx.png'; $sp1596b5->driver = 'WeChat'; $sp1596b5->way = 'MWEB'; $sp1596b5->comment = '微信支付 - H5 (需要开通权限)'; $sp1596b5->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp1596b5->enabled = \App\Pay::ENABLED_DISABLED; $sp1596b5->save(); $sp1596b5 = new \App\Pay(); $sp1596b5->name = '手机QQ'; $sp1596b5->img = '/plugins/images/qq.png'; $sp1596b5->driver = 'QPay'; $sp1596b5->way = 'NATIVE'; $sp1596b5->comment = '手机QQ - 扫码'; $sp1596b5->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $sp1596b5->enabled = \App\Pay::ENABLED_DISABLED; $sp1596b5->save(); $sp1596b5 = new \App\Pay(); $sp1596b5->name = '支付宝'; $sp1596b5->img = '/plugins/images/ali.png'; $sp1596b5->driver = 'Youzan'; $sp1596b5->way = 'alipay'; $sp1596b5->comment = '有赞支付 - 支付宝'; $sp1596b5->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp1596b5->enabled = \App\Pay::ENABLED_DISABLED; $sp1596b5->save(); $sp1596b5 = new \App\Pay(); $sp1596b5->name = '微信'; $sp1596b5->img = '/plugins/images/wx.png'; $sp1596b5->driver = 'Youzan'; $sp1596b5->way = 'wechat'; $sp1596b5->comment = '有赞支付 - 微信'; $sp1596b5->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp1596b5->enabled = \App\Pay::ENABLED_DISABLED; $sp1596b5->save(); $sp1596b5 = new \App\Pay(); $sp1596b5->name = '手机QQ'; $sp1596b5->img = '/plugins/images/qq.png'; $sp1596b5->driver = 'Youzan'; $sp1596b5->way = 'qq'; $sp1596b5->comment = '有赞支付 - 手机QQ'; $sp1596b5->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp1596b5->enabled = \App\Pay::ENABLED_DISABLED; $sp1596b5->save(); $sp1596b5 = new \App\Pay(); $sp1596b5->name = '支付宝'; $sp1596b5->img = '/plugins/images/ali.png'; $sp1596b5->driver = 'CodePay'; $sp1596b5->way = 'alipay'; $sp1596b5->comment = '码支付 - 支付宝'; $sp1596b5->config = '{
  "id": "id",
  "key": "key"
}'; $sp1596b5->fee_system = 0; $sp1596b5->enabled = \App\Pay::ENABLED_DISABLED; $sp1596b5->save(); $sp1596b5 = new \App\Pay(); $sp1596b5->name = '微信'; $sp1596b5->img = '/plugins/images/wx.png'; $sp1596b5->driver = 'CodePay'; $sp1596b5->way = 'weixin'; $sp1596b5->comment = '码支付 - 微信'; $sp1596b5->config = '{
  "id": "id",
  "key": "key"
}'; $sp1596b5->fee_system = 0; $sp1596b5->enabled = \App\Pay::ENABLED_DISABLED; $sp1596b5->save(); $sp1596b5 = new \App\Pay(); $sp1596b5->name = '手机QQ'; $sp1596b5->img = '/plugins/images/qq.png'; $sp1596b5->driver = 'CodePay'; $sp1596b5->way = 'qq'; $sp1596b5->comment = '码支付 - 手机QQ'; $sp1596b5->config = '{
  "id": "id",
  "key": "key"
}'; $sp1596b5->fee_system = 0; $sp1596b5->enabled = \App\Pay::ENABLED_DISABLED; $sp1596b5->save(); } public function run() { self::initPay(); } }