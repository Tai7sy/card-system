<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $sp5de949 = new \App\Pay(); $sp5de949->name = '支付宝'; $sp5de949->img = '/plugins/images/ali.png'; $sp5de949->driver = 'Fakala'; $sp5de949->way = 'alipay'; $sp5de949->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp5de949->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp5de949->enabled = \App\Pay::ENABLED_PC; $sp5de949->save(); $sp5de949 = new \App\Pay(); $sp5de949->name = '支付宝'; $sp5de949->img = '/plugins/images/ali.png'; $sp5de949->driver = 'Fakala'; $sp5de949->way = 'alipaywap'; $sp5de949->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp5de949->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp5de949->enabled = \App\Pay::ENABLED_MOBILE; $sp5de949->save(); $sp5de949 = new \App\Pay(); $sp5de949->name = '微信'; $sp5de949->img = '/plugins/images/wx.png'; $sp5de949->driver = 'Fakala'; $sp5de949->way = 'wx'; $sp5de949->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp5de949->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp5de949->enabled = \App\Pay::ENABLED_PC; $sp5de949->save(); $sp5de949 = new \App\Pay(); $sp5de949->name = '微信'; $sp5de949->img = '/plugins/images/wx.png'; $sp5de949->driver = 'Fakala'; $sp5de949->way = 'wxwap'; $sp5de949->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp5de949->config = '{
  "gateway": "https://www.327ka.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp5de949->enabled = \App\Pay::ENABLED_MOBILE; $sp5de949->save(); $sp5de949 = new \App\Pay(); $sp5de949->name = '支付宝'; $sp5de949->img = '/plugins/images/ali.png'; $sp5de949->driver = 'Alipay'; $sp5de949->way = 'pc'; $sp5de949->comment = '支付宝 - 即时到账套餐(企业)V2'; $sp5de949->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp5de949->enabled = \App\Pay::ENABLED_DISABLED; $sp5de949->save(); $sp5de949 = new \App\Pay(); $sp5de949->name = '支付宝'; $sp5de949->img = '/plugins/images/ali.png'; $sp5de949->driver = 'Aliwap'; $sp5de949->way = 'wap'; $sp5de949->comment = '支付宝 - 高级手机网站支付V4'; $sp5de949->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp5de949->enabled = \App\Pay::ENABLED_DISABLED; $sp5de949->save(); $sp5de949 = new \App\Pay(); $sp5de949->name = '支付宝扫码'; $sp5de949->img = '/plugins/images/ali.png'; $sp5de949->driver = 'AliAop'; $sp5de949->way = 'f2f'; $sp5de949->comment = '支付宝 - 当面付'; $sp5de949->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp5de949->enabled = \App\Pay::ENABLED_DISABLED; $sp5de949->save(); $sp5de949 = new \App\Pay(); $sp5de949->name = '支付宝'; $sp5de949->img = '/plugins/images/ali.png'; $sp5de949->driver = 'AliAop'; $sp5de949->way = 'pc'; $sp5de949->comment = '支付宝 - 电脑网站支付 (新)'; $sp5de949->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp5de949->enabled = \App\Pay::ENABLED_DISABLED; $sp5de949->save(); $sp5de949 = new \App\Pay(); $sp5de949->name = '手机支付宝'; $sp5de949->img = '/plugins/images/ali.png'; $sp5de949->driver = 'AliAop'; $sp5de949->way = 'mobile'; $sp5de949->comment = '支付宝 - 手机网站支付 (新)'; $sp5de949->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp5de949->enabled = \App\Pay::ENABLED_DISABLED; $sp5de949->save(); $sp5de949 = new \App\Pay(); $sp5de949->name = '微信扫码'; $sp5de949->img = '/plugins/images/wx.png'; $sp5de949->driver = 'WeChat'; $sp5de949->way = 'NATIVE'; $sp5de949->comment = '微信支付 - 扫码'; $sp5de949->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp5de949->enabled = \App\Pay::ENABLED_DISABLED; $sp5de949->save(); $sp5de949 = new \App\Pay(); $sp5de949->name = '微信H5'; $sp5de949->img = '/plugins/images/wx.png'; $sp5de949->driver = 'WeChat'; $sp5de949->way = 'MWEB'; $sp5de949->comment = '微信支付 - H5 (需要开通权限)'; $sp5de949->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp5de949->enabled = \App\Pay::ENABLED_DISABLED; $sp5de949->save(); $sp5de949 = new \App\Pay(); $sp5de949->name = '手机QQ'; $sp5de949->img = '/plugins/images/qq.png'; $sp5de949->driver = 'QPay'; $sp5de949->way = 'NATIVE'; $sp5de949->comment = '手机QQ - 扫码'; $sp5de949->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $sp5de949->enabled = \App\Pay::ENABLED_DISABLED; $sp5de949->save(); $sp5de949 = new \App\Pay(); $sp5de949->name = '支付宝'; $sp5de949->img = '/plugins/images/ali.png'; $sp5de949->driver = 'Youzan'; $sp5de949->way = 'alipay'; $sp5de949->comment = '有赞支付 - 支付宝'; $sp5de949->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp5de949->enabled = \App\Pay::ENABLED_DISABLED; $sp5de949->save(); $sp5de949 = new \App\Pay(); $sp5de949->name = '微信'; $sp5de949->img = '/plugins/images/wx.png'; $sp5de949->driver = 'Youzan'; $sp5de949->way = 'wechat'; $sp5de949->comment = '有赞支付 - 微信'; $sp5de949->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp5de949->enabled = \App\Pay::ENABLED_DISABLED; $sp5de949->save(); $sp5de949 = new \App\Pay(); $sp5de949->name = '手机QQ'; $sp5de949->img = '/plugins/images/qq.png'; $sp5de949->driver = 'Youzan'; $sp5de949->way = 'qq'; $sp5de949->comment = '有赞支付 - 手机QQ'; $sp5de949->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp5de949->enabled = \App\Pay::ENABLED_DISABLED; $sp5de949->save(); $sp5de949 = new \App\Pay(); $sp5de949->name = '支付宝'; $sp5de949->img = '/plugins/images/ali.png'; $sp5de949->driver = 'CodePay'; $sp5de949->way = 'alipay'; $sp5de949->comment = '码支付 - 支付宝'; $sp5de949->config = '{
  "id": "id",
  "key": "key"
}'; $sp5de949->fee_system = 0; $sp5de949->enabled = \App\Pay::ENABLED_DISABLED; $sp5de949->save(); $sp5de949 = new \App\Pay(); $sp5de949->name = '微信'; $sp5de949->img = '/plugins/images/wx.png'; $sp5de949->driver = 'CodePay'; $sp5de949->way = 'weixin'; $sp5de949->comment = '码支付 - 微信'; $sp5de949->config = '{
  "id": "id",
  "key": "key"
}'; $sp5de949->fee_system = 0; $sp5de949->enabled = \App\Pay::ENABLED_DISABLED; $sp5de949->save(); $sp5de949 = new \App\Pay(); $sp5de949->name = '手机QQ'; $sp5de949->img = '/plugins/images/qq.png'; $sp5de949->driver = 'CodePay'; $sp5de949->way = 'qq'; $sp5de949->comment = '码支付 - 手机QQ'; $sp5de949->config = '{
  "id": "id",
  "key": "key"
}'; $sp5de949->fee_system = 0; $sp5de949->enabled = \App\Pay::ENABLED_DISABLED; $sp5de949->save(); } public function run() { self::initPay(); } }