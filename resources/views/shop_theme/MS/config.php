<?php

// 模板来源:
// https://www.microsoftstore.com.cn/surface/surface-pro/p/mic2539
return [
    'description' => 'Microsoft Shop',
    'options' => [
        'theme' => [
            'label' => '风格',
            'type' => 'radio',
            'values' => [
                'light' => [
                    'label' => '浅色主题'
                ],
                'dark' => [
                    'label' => '深色主题'
                ]
            ],
            'value' => 'light'
        ],
        'list_type' => [
            'label' => '商品显示方式',
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
        'test' => [
            'label' => '测试text',
            'type' => 'text',
            'inputType' => 'text',
            'size' => 16,
            'value' => '默认值'
        ],
        'test_textarea' => [
            'label' => '测试textarea',
            'type' => 'textarea',
            'rows' => 5,
            'value' => '默认值'
        ],
        'test_checkbox' => [
            'label' => '测试checkbox',
            'type' => 'checkbox',
            'value' => 1
        ]
    ]
];