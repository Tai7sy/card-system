<?php

return [

    /*
    | Lang for this project
    */

    'coupon' => [
        'required' => 'Please input your coupon code.',
        'invalid' => 'The coupon your input is invalid<br>If your dont have a coupon, please dont input.'
    ],

    'category' => [
        'required' => 'Please select a category.',
        'not_found' => 'The category cant be found.',
        'password_error' => 'The category password is error.',
    ],

    'product' => [
        'required' => 'Please select a product.',
        'not_found' => 'The product cannot be found.',
        'password_error' => 'The product password is error.',
        'not_on_sell' => 'The product is not on sell.',
        'buy_min' => 'This product requires at least :num item(s), please select again',
        'buy_max' => 'This product has a limit of :num item(s), please select again',
        'out_of_stock' => 'The product is out of stock.',
    ],

    'contact' => [
        'required' => 'Please input your contact information.',
    ],

    'pay' => [
        'not_found' => 'Payment method not found, please select again.',
        'driver_not_found' => 'Payment method not found, please contact customer service.',
        'internal_error' => 'An error occurred, the order failed, please try again later',
        'verify_failed' => 'Payment verification failed, you can check the payment status later.'
    ],

    'order' => [
        'not_found' => 'Order not found.',
        'expired' => 'The current order has not been paid for a long time and has been cancelled. Please re-order',
        'msg_product_manual_please_wait' => 'Your purchase is manual recharge, please be patient.',
        'msg_product_out_of_stock_not_send' => 'Merchant inventory is insufficient, so there is no automatic delivery, please contact the merchant customer service to ship.',
        'msg_product_deleted' => 'Seller has deleted this product, please contact customer service for refund',
    ],

    'search_type' => [
        'required' => 'Please select a method to query your order.'
    ],

    'please_select_category_or_product' => 'Please select a category or product first.',

    'please_wait' => 'Please wait...',
    'please_wait_for_pay' => 'Please wait for payment to load.',

    'order_is_paid' => 'Order paid',
    'order_process_failed_because' => 'Failed:<br>:reason',
    'order_process_failed_default' => 'Failed<br>If you have paid please wait or contact customer service to resolve',

    'you_are_sb' => 'Please do not attack.'

];
