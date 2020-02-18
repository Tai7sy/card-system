<?php

return [
    'description' => 'Material Design 簡潔風格',
    'options' => [
        'list_type' => [
            'label' => '商品列表顯示方式',
            'type' => 'select',
            'values' => [
                'dropdown' => [
                    'label' => '下拉式'
                ],
                'button' => [
                    'label' => '按鈕式'
                ],
                'list' => [
                    'label' => '列表式'
                ]
            ],
            'value' => 'button'
        ],
        'single_mode' => [
            'label' => '單商品顯示方式',
            'type' => 'select',
            'values' => [
                'flow' => [
                    'label' => '上下平鋪'
                ],
                'flex' => [
                    'label' => '左右并排'
                ],
            ],
            'value' => 'flow'
        ],
        'background' => [
            'label' => '背景圖片',
            'type' => 'text',
            'inputType' => 'text',
            'placeholder' => '請填寫背景圖片URL',
            'value' => 'https://open.saintic.com/api/bingPic/'
        ],
        'show_background' => [
            'label' => '顯示背景圖片',
            'type' => 'checkbox',
            'value' => 1
        ],
    ]
];