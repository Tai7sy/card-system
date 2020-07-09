<?php

return [
    'description' => 'Material Design 简洁风格',
    'options' => [
        'list_type' => [
            'label' => '商品列表显示方式',
            'type' => 'select',
            'values' => [
                'dropdown' => [
                    'label' => '下拉式'
                ],
                'button' => [
                    'label' => '按钮式'
                ],
                'list' => [
                    'label' => '列表式'
                ]
            ],
            'value' => 'button'
        ],
        'single_mode' => [
            'label' => '单商品显示方式',
            'type' => 'select',
            'values' => [
                'flow' => [
                    'label' => '上下平铺'
                ],
                'flex' => [
                    'label' => '左右并排'
                ],
            ],
            'value' => 'flow'
        ],
        'background' => [
            'label' => '背景图片',
            'type' => 'text',
            'inputType' => 'text',
            'placeholder' => '请填写背景图片URL',
            'value' => 'https://open.saintic.com/api/bingPic/'
        ],
        'show_background' => [
            'label' => '显示背景图片',
            'type' => 'checkbox',
            'value' => 1
        ],
    ]
];