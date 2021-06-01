
# WeChat 官方接口

使用说明
 * 驱动 WeChat 
 * 方式 NATIVE / MWEB / JSAPI
   - NATIVE 扫码支付
   - MWEB H5支付(需开通权限)
   - JSAPI 微信内支付(此方式无序手动配置, 会自动检测在微信内从而自动使用此方式)
 * 配置 (JSON格式)
    ```json
    {
        "APPID": "APPID",
        "APPSECRET": "APP_SECRET",
        "MCHID": "商户ID",
        "KEY": "KEY",
        "sub_mch_id": "子商户ID(可选)",
        "sub_appid": "子商户APPID(可选)",
    }
    ```
