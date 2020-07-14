<?php

return [

    /*
    | Lang for this project
    */

    'coupon' => [
        'required' => '请输入优惠券',
        'invalid' => '您输入的优惠券信息无效<br>如果没有优惠券请不要填写'
    ],

    'category' => [
        'required' => '请选择商品分类',
        'not_found' => '商品分类未找到，请重新选择',
        'password_error' => '分类密码输入错误',
    ],

    'product' => [
        'required' => '请选择商品',
        'not_found' => '商品未找到，请重新选择',
        'password_error' => '商品密码输入错误',
        'not_on_sell' => '该商品已下架',
        'buy_min' => '该商品最少购买 :num 件，请重新选择',
        'buy_max' => '该商品限购 :num 件，请重新选择',
        'out_of_stock' => '该商品库存不足',
    ],

    'contact' => [
        'required' => '请输入联系方式',
    ],

    'pay' => [
        'not_found' => '支付方式未找到，请重新选择',
        'driver_not_found' => '支付驱动未找到，请联系客服',
        'internal_error' => '发生错误，下单失败，请稍后重试',
        'verify_failed' => '支付验证失败，您可以稍后查看支付状态。'
    ],

    'order' => [
        'not_found' => '订单未找到',
        'expired' => '当前订单长时间未支付已作废, 请重新下单',
        'msg_product_manual_please_wait' => '您购买的为手动充值商品，请耐心等待处理',
        'msg_product_out_of_stock_not_send' => '商家库存不足，因此没有自动发货，请联系商家客服发货',
        'msg_product_deleted' => '卖家已删除此商品，请联系客服退款',
    ],

    'search_type' => [
        'required' => '请选择查询类型'
    ],

    'please_select_category_or_product' => '请先选择分类或商品',

    'please_wait' => '请稍后',
    'please_wait_for_pay' => '支付方式加载中，请稍后',

    'order_is_paid' => '订单已支付',
    'order_process_failed_because' => '失败原因:<br>:reason',
    'order_process_failed_default' => '订单未支付成功<br>如果您已经支付请耐心等待或联系客服解决',

    'you_are_sb' => '你玩你妈呢？'

];
