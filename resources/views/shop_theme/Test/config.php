<?php

return [
    'description' => '测试模板',
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
            'hidden' => true,
            'values' => [
                'list' => [
                    'label' => '列表式'
                ]
            ],
            'value' => 'list'
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