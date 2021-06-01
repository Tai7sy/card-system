
# BitPay

## 注册地址 
 * [MugglePay.com](https://merchants.mugglepay.com/user/register?ref=MP8E0ED1D96500)
 
## 使用说明
 * 驱动 MugglePay 
 * 方式 COIN / ALIPAY / WECHAT
   - COIN 数字货币
   - ALIPAY 支付宝
   - WECHAT 微信
 * 配置 (JSON格式)
    ```json
    {
        "app_secret": "your app_secret"
    }
    ```
## 注册流程
 1. 先跟点击上方注册地址，获取邀请码
 2. 注册登录[商家后台](https://merchants.mugglepay.com)
 3. 选择"个人设置"->“API认证”->“用在后台服务器（SSP等php后台）”，点击“添加密钥”，获得后端认证码（请注意，是”后端“）。获取app_secret，填入card-system配置文件中。
<img src="https://cdn.mugglepay.com/docs/whmcs/getapi.png" />

## 认证审核
 请去个人设置中，完成认证工作，点亮笑脸☺。<br />
 <img width="300" src="https://user-images.githubusercontent.com/50819254/59549161-21656f80-8f8c-11e9-8127-3b369ab85b4f.jpg" />

请确认您已经开通了需要的权限。
注意，如果你只有 “加密货币”，那只能接受加密货币。如需要开通其他支付方式，请按照该页面的流程操作。

<img src="https://cdn.mugglepay.com/docs/whmcs/permission.png" />

## FAQ
可以参考数字货币匿名支付[常见问题](https://github.com/bitpaydev/docs/blob/master/FAQ.md)。
如果支付遇到问题，可以联系[技术交流群](https://t.me/joinchat/GLKSKhUnE4GvEAPgqtChAQ)。
