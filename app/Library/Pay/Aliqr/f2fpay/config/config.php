<?php
$config = array (
		//签名方式,默认为RSA2(RSA2048)
		'sign_type' => "RSA2",

		//支付宝公钥
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAn0S86Mi0ExS44RUiHH+7dbwq1qFj6k5lEQPQpXcnAvcE8TRS1DhXeFZ1Yush575mphFJ3rssD7WodGOgspnNw5omUtlV1Ot2zaiU5/NzAe8jP3GoLOvgamP/IsbE0iJn6NRsV5g7YfU7S9VxsqDSRyXPR9b4gO590uUHwoaVqClJaQsmHoiN2V97FTaobPE87z8Bed+9sXcQdPMQo0G2Jfv8DudApboQ/o8Kx0Cdj8+yP9SRidrsfhH6AJfGdLBtjKfDZYYeQHocXHqMKxMkgC++mCSKmPw9bdWcbwEIWAi3gDIHf5is4nXhX1Ur5J+Q7O3hVNvKmv2ttjmIjbG4nwIDAQAB",

		//商户私钥
		'merchant_private_key' => "MIIEowIBAAKCAQEAxbXFP9yF5dNM2WZrlo/BPdlWHO/uP46RlmMf9jFkkIlJ/ecKcegD8Ig7PeI8N8vQgLQ1goGqeuOCh7e7icH0WgO4+gAjn4dNlBlr7LT9Squ/q/scA525ewGKRfbEzg8hTZZr/ygyYK1OLb8bLj712CSPCYwRFeUHgCoM7kvK7T8UjXqdezSpbFSfzWuLRTp+J+bBSEngXSSYLRuNGGnx8tb2IXKdBWV46NDvQAIP3GvrEEcBEdwwQTvpHKnSpe6PdmirVDfnKxjspo6Xg9iKGSYm8/LUx1Cp8ZAOS1Aa9BZSUff2OokWHn3mQq7l6rgGW3M8QQSogizz9sCiXOXjewIDAQABAoIBAGqkj07RDlBXVz50plfCFsPbtT1KlcImjpUFxrmvDsV/qC+SLeoe8fEOKpZIr56RC1hA3BQXCPPcQA4gpsG5oqnjFCWVaaGlA818oOA/B9j9JxgHDKdOrjuRKxpt7t4O/zGwj3REJFhNYrv+NxUM1OLPZXAK4zRiI1eYVv06WKz6xNF0d0QgvX5mAEkMsduSvc48ZKo/dgRNMZnVmtL8jkpHgBllnldUrKwGMfgoK6/qu3s2MqsQm+cd6uKmm3rEUpVFkVNk2R5OnkdMJXoNmbDBP+hQfhan+YgAXbDVndlVRFS3u0XuU/mXI8CQZm26Ib+CalKz8+U78RZNvES4z5ECgYEA4TD/M4fdSkBtU3T6F51jBpbByTi8mazddvTeU6skB8H4uvGIX23TQ/EgTzqyhfI2GRIQWa1HV60zQZln3rv3Ji7yotzJfQPVb17iPD83UVaau4tBdyRmqDigCuCenRdGGR7FFiJbCjML3vec5T8nI8ACy7AgBOiqt3jQclgIdjUCgYEA4MJHaXuW+PsClKQDH2MnzGBrcLhYKZBCkW6lVCFtIKTIbjE/4oLwXZqVDFAlqtmqsaMDAXj60agyS4lyI6RyKmTKHw3fYlLj+rllfxlyKnn1tf0l8ULBkRjZfk10p69IbSc8FkuHEHQmObLjfPf6Y3UDP4BhMeEWqcyFYWBSaO8CgYBDHEIBzmyxcy/hnkvl+76GOSl5WVJDjhPWHrWfQ7KSKUmCFPcqUWWTqumRExIe/wcGQ67hutU3GOPcmeYtp+DI3VbsGFZnzluuPB26DLDkqaWlXKPe6Nc7LNztQPGkKUp3tAbSSNzO1lWkVjDI1SBJ9DAhGdUBAYHW+cdNPFURPQKBgHgioDUboGjZEloNCLZAM8ekSgloH4en2k0ODUTY0RG/wnpTzMfwITaHJ9/6/wdBbWGhtjuVRMvH0Ypany25+mP6l6bdrWdK0xzy9/Nl0IYvxkxOUXeRrRE3bRl7oTz4Dgh6Dj4EhN8glkYE/l+P1MBQjTnmDw2wOILuFOUlrSb1AoGBAKy4WxmACvYC2AVCIwWOGlUizrMsrGCTs5uLFDOZ9OepqiTfBCJ+9/+6VYolagsh0pqMna58/Q/8o7OVmEzZQCTG274lpWO7S0UC+0QGOC3s0KDPPML39ihWkJ1VWhv9uyXKmlHPygTp46YjRhnmdtjAVreSlg+VNi25eve8s+DX",

		//编码格式
		'charset' => "UTF-8",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//应用ID
		'app_id' => "2017011305048262",

		//异步通知地址,只有扫码支付预下单可用
		'notify_url' => "http://www.baidu.com",

		//最大查询重试次数
		'MaxQueryRetry' => "10",

		//查询间隔
		'QueryDuration' => "3"
);