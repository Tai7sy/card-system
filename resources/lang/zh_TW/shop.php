<?php

return [

    /*
    | Lang for this project
    */

    'coupon' => [
        'required' => '請輸入優惠券',
        'invalid' => '您輸入的優惠券信息無效<br>如果沒有優惠券請不要填寫'
    ],

    'category' => [
        'required' => '請選擇商品分類',
        'not_found' => '商品分類未找到，請重新選擇',
        'password_error' => '分類密碼輸入錯誤',
    ],

    'product' => [
        'required' => '請選擇商品',
        'not_found' => '商品未找到，請重新選擇',
        'password_error' => '商品密碼輸入錯誤',
        'not_on_sell' => '該商品已下架',
        'buy_min' => '該商品最少購買 :num 件，請重新選擇',
        'buy_max' => '該商品限購 :num 件，請重新選擇',
        'out_of_stock' => '該商品庫存不足',
    ],

    'contact' => [
        'required' => '請輸入聯系方式',
    ],

    'pay' => [
        'not_found' => '支付方式未找到，請重新選擇',
        'driver_not_found' => '支付驅動未找到，請聯系客服',
        'internal_error' => '發生錯誤，下單失敗，請稍后重試',
        'verify_failed' => '支付驗證失敗，您可以稍后查看支付狀態。'
    ],

    'order' => [
        'not_found' => '訂單未找到',
        'expired' => '當前訂單長時間未支付已作廢, 請重新下單',
        'msg_product_manual_please_wait' => '您購買的為手動充值商品，請耐心等待處理',
        'msg_product_out_of_stock_not_send' => '商家庫存不足，因此沒有自動發貨，請聯系商家客服發貨',
        'msg_product_deleted' => '賣家已刪除此商品，請聯系客服退款',
    ],

    'search_type' => [
        'required' => '請選擇查詢類型'
    ],

    'please_select_category_or_product' => '請先選擇分類或商品',

    'please_wait' => '請稍后',
    'please_wait_for_pay' => '支付方式加載中，請稍后',

    'order_is_paid' => '訂單已支付',
    'order_process_failed_because' => '失敗原因:<br>:reason',
    'order_process_failed_default' => '訂單未支付成功<br>如果您已經支付請耐心等待或聯系客服解決',

    'you_are_sb' => '你玩你媽呢？'

];
