<?php

return [
    'description' => '经典主题',
    'options' => [
        'list_type' => [
            'label' => '商品显示方式',
            'type' => 'select',
            'values' => [
                'dropdown' => [
                    'label' => '下拉式'
                ],
                'button' => [
                    'label' => '按钮式'
                ]
            ],
            'value' => 'dropdown'
        ]
    ]
];