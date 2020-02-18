<?php

return [
    'description' => '經典主題',
    'options' => [
        'list_type' => [
            'label' => '商品顯示方式',
            'type' => 'select',
            'values' => [
                'dropdown' => [
                    'label' => '下拉式'
                ],
                'button' => [
                    'label' => '按鈕式'
                ]
            ],
            'value' => 'dropdown'
        ]
    ]
];