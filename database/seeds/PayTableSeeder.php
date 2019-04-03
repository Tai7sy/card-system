<?php
use Illuminate\Database\Seeder; class PayTableSeeder extends Seeder { private function initPay() { $sp8f21c2 = new \App\Pay(); $sp8f21c2->name = '支付宝'; $sp8f21c2->img = '/plugins/images/ali.png'; $sp8f21c2->driver = 'Fakala'; $sp8f21c2->way = 'alipay'; $sp8f21c2->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp8f21c2->config = '{
  "gateway": "http://card.evil5.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp8f21c2->enabled = \App\Pay::ENABLED_PC; $sp8f21c2->save(); $sp8f21c2 = new \App\Pay(); $sp8f21c2->name = '支付宝'; $sp8f21c2->img = '/plugins/images/ali.png'; $sp8f21c2->driver = 'Fakala'; $sp8f21c2->way = 'alipaywap'; $sp8f21c2->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp8f21c2->config = '{
  "gateway": "http://card.evil5.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp8f21c2->enabled = \App\Pay::ENABLED_MOBILE; $sp8f21c2->save(); $sp8f21c2 = new \App\Pay(); $sp8f21c2->name = '微信'; $sp8f21c2->img = '/plugins/images/wx.png'; $sp8f21c2->driver = 'Fakala'; $sp8f21c2->way = 'wx'; $sp8f21c2->comment = 'alipay、alipaywap、wx、wxwap、qq、qqwap'; $sp8f21c2->config = '{
  "gateway": "http://card.evil5.com",
  "api_id": "your api_id",
  "api_key": "your api_key"
}'; $sp8f21c2->enabled = \App\Pay::ENABLED_ALL; $sp8f21c2->save(); $sp8f21c2 = new \App\Pay(); $sp8f21c2->name = '支付宝'; $sp8f21c2->img = '/plugins/images/ali.png'; $sp8f21c2->driver = 'Alipay'; $sp8f21c2->way = 'Alipay'; $sp8f21c2->comment = '支付宝 - 即时到账套餐(企业)V2'; $sp8f21c2->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp8f21c2->enabled = \App\Pay::ENABLED_PC; $sp8f21c2->save(); $sp8f21c2 = new \App\Pay(); $sp8f21c2->name = '支付宝'; $sp8f21c2->img = '/plugins/images/ali.png'; $sp8f21c2->driver = 'Aliwap'; $sp8f21c2->way = 'Aliwap'; $sp8f21c2->comment = '支付宝 - 高级手机网站支付V4'; $sp8f21c2->config = '{
  "partner": "partner",
  "key": "key"
}'; $sp8f21c2->enabled = \App\Pay::ENABLED_MOBILE; $sp8f21c2->save(); $sp8f21c2 = new \App\Pay(); $sp8f21c2->name = '支付宝扫码'; $sp8f21c2->img = '/plugins/images/ali.png'; $sp8f21c2->driver = 'Aliqr'; $sp8f21c2->way = 'Aliqr'; $sp8f21c2->comment = '支付宝 - 当面付'; $sp8f21c2->config = '{
  "app_id": "app_id",
  "alipay_public_key": "alipay_public_key",
  "merchant_private_key": "merchant_private_key"
}'; $sp8f21c2->enabled = \App\Pay::ENABLED_ALL; $sp8f21c2->save(); $sp8f21c2 = new \App\Pay(); $sp8f21c2->name = '微信扫码'; $sp8f21c2->img = '/plugins/images/wx.png'; $sp8f21c2->driver = 'WeChat'; $sp8f21c2->way = 'NATIVE'; $sp8f21c2->comment = '微信支付 - 扫码'; $sp8f21c2->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp8f21c2->enabled = \App\Pay::ENABLED_ALL; $sp8f21c2->save(); $sp8f21c2 = new \App\Pay(); $sp8f21c2->name = '微信H5'; $sp8f21c2->img = '/plugins/images/wx.png'; $sp8f21c2->driver = 'WeChat'; $sp8f21c2->way = 'MWEB'; $sp8f21c2->comment = '微信支付 - H5 (需要开通权限)'; $sp8f21c2->config = '{
  "APPID": "APPID",
  "APPSECRET": "APPSECRET",
  "MCHID": "商户ID",
  "KEY": "KEY"
}'; $sp8f21c2->enabled = \App\Pay::ENABLED_MOBILE; $sp8f21c2->save(); $sp8f21c2 = new \App\Pay(); $sp8f21c2->name = '手机QQ'; $sp8f21c2->img = '/plugins/images/qq.png'; $sp8f21c2->driver = 'QPay'; $sp8f21c2->way = 'NATIVE'; $sp8f21c2->comment = '手机QQ - 扫码'; $sp8f21c2->config = '{
  "mch_id": "mch_id",
  "mch_key": "mch_key"
}'; $sp8f21c2->enabled = \App\Pay::ENABLED_ALL; $sp8f21c2->save(); $sp8f21c2 = new \App\Pay(); $sp8f21c2->name = '支付宝'; $sp8f21c2->img = '/plugins/images/ali.png'; $sp8f21c2->driver = 'Youzan'; $sp8f21c2->way = 'alipay'; $sp8f21c2->comment = '有赞支付 - 支付宝'; $sp8f21c2->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp8f21c2->enabled = \App\Pay::ENABLED_ALL; $sp8f21c2->save(); $sp8f21c2 = new \App\Pay(); $sp8f21c2->name = '微信'; $sp8f21c2->img = '/plugins/images/wx.png'; $sp8f21c2->driver = 'Youzan'; $sp8f21c2->way = 'wechat'; $sp8f21c2->comment = '有赞支付 - 微信'; $sp8f21c2->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp8f21c2->enabled = \App\Pay::ENABLED_ALL; $sp8f21c2->save(); $sp8f21c2 = new \App\Pay(); $sp8f21c2->name = '手机QQ'; $sp8f21c2->img = '/plugins/images/qq.png'; $sp8f21c2->driver = 'Youzan'; $sp8f21c2->way = 'qq'; $sp8f21c2->comment = '有赞支付 - 手机QQ'; $sp8f21c2->config = '{
  "client_id": "client_id",
  "client_secret": "client_secret",
  "kdt_id": "kdt_id"
}'; $sp8f21c2->enabled = \App\Pay::ENABLED_ALL; $sp8f21c2->save(); $sp8f21c2 = new \App\Pay(); $sp8f21c2->name = '支付宝'; $sp8f21c2->img = '/plugins/images/ali.png'; $sp8f21c2->driver = 'CodePay'; $sp8f21c2->way = 'alipay'; $sp8f21c2->comment = '码支付 - 支付宝'; $sp8f21c2->config = '{
  "id": "id",
  "key": "key"
}'; $sp8f21c2->fee_system = 0; $sp8f21c2->enabled = \App\Pay::ENABLED_ALL; $sp8f21c2->save(); $sp8f21c2 = new \App\Pay(); $sp8f21c2->name = '微信'; $sp8f21c2->img = '/plugins/images/wx.png'; $sp8f21c2->driver = 'CodePay'; $sp8f21c2->way = 'weixin'; $sp8f21c2->comment = '码支付 - 微信'; $sp8f21c2->config = '{
  "id": "id",
  "key": "key"
}'; $sp8f21c2->fee_system = 0; $sp8f21c2->enabled = \App\Pay::ENABLED_ALL; $sp8f21c2->save(); $sp8f21c2 = new \App\Pay(); $sp8f21c2->name = '手机QQ'; $sp8f21c2->img = '/plugins/images/qq.png'; $sp8f21c2->driver = 'CodePay'; $sp8f21c2->way = 'qq'; $sp8f21c2->comment = '码支付 - 手机QQ'; $sp8f21c2->config = '{
  "id": "id",
  "key": "key"
}'; $sp8f21c2->fee_system = 0; $sp8f21c2->enabled = \App\Pay::ENABLED_ALL; $sp8f21c2->save(); } public function run() { self::initPay(); } }