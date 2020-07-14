<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute 必須填寫',
    'active_url'           => ':attribute 必須是一個合法的URL',
    'after'                => ':attribute 必須是 :date 之后的一個日期',
    'after_or_equal'       => ':attribute 必須是 :date 或之后的一個日期',
    'alpha'                => ':attribute 只能包含字母',
    'alpha_dash'           => ':attribute 只能包含字母、數字、中劃線或下劃線',
    'alpha_num'            => ':attribute 只能包含字母或數字',
    'array'                => ':attribute 必須是一個數組',
    'before'               => ':attribute 必須是 :date 之前的一個日期',
    'before_or_equal'      => ':attribute 必須是 :date 或之前的一個日期',
    'between'              => [
        'numeric' => ':attribute 必須在 :min 和 :max 之間',
        'file'    => ':attribute 必須在 :min 和 :max KB 之間',
        'string'  => ':attribute 必須在 :min 和 :max 個字符之間',
        'array'   => ':attribute 必須在 :min 和 :max 項之間',
    ],
    'boolean'              => ':attribute 字符必須是 true 或 false',
    'confirmed'            => ':attribute 二次確認不匹配',
    'date'                 => ':attribute 必須是一個合法的日期',
    'date_format'          => ':attribute 與給定的格式 :format 不一致',
    'different'            => ':attribute 和 :other 必須不同',
    'digits'               => ':attribute 必須是 :digits 位',
    'digits_between'       => ':attribute 必須在 :min 至 :max 位之間',
    'dimensions'           => ':attribute 無效的圖片尺寸',
    'distinct'             => ':attribute 字段具有重復址',
    'email'                => ':attribute 必須是一個合法的電子郵件地址',
    'exists'               => '選定的 :attribute 是無效的',
    'file'                 => ':attribute 必須是一個文件',
    'filled'               => ':attribute 字段是必填項',
    'image'                => ':attribute 必須是 jpeg, png, bmp, 或者 gif 格式圖像',
    'in'                   => '選定的 :attribute 是無效的',
    'in_array'             => ':attribute 字段在 :other 中不存在',
    'integer'              => ':attribute 必須是整數',
    'ip'                   => ':attribute 必須是一個合法的IP地址',
    'ipv4'                 => ':attribute 必須是一個合法的 IPv4 地址',
    'ipv6'                 => ':attribute 必須是一個合法的 IPv6 地址',
    'json'                 => ':attribute 必須是一個合法的 JSON 字符串',
    'max'                  => [
        'numeric' => ':attribute 的最大長度為 :max.',
        'file'    => ':attribute 大小至少為 :max ',
        'string'  => ':attribute 的長度至少為 :max 字符',
        'array'   => ':attribute 至少為 :max 項',
    ],
    'mimes'                => ':attribute 的文件類型必須為 type: :values.',
    'mimetypes'            => ':attribute 的文件類型必須為 type: :values.',
    'min'                  => [
        'numeric' => ':attribute 的最小長度為 :min 位',
        'file'    => ':attribute 大小至少為 :min ',
        'string'  => ':attribute 的最小長度為 :min 字符',
        'array'   => ':attribute 至少有 :min 項',
    ],
    'not_in'               => '選定的 :attribute 是無效的',
    'numeric'              => ':attribute 必須是數字',
    'present'              => ':attribute field must be present.',
    'regex'                => ':attribute 格式是無效的',
    'required'             => ':attribute 字段是必須的',
    'required_if'          => ':attribute 字段是必須的當 :other 是 :value.',
    'required_unless'      => ':attribute field is required unless :other is in :values.',
    'required_with'        => ':attribute 字段是必須的當 :values 存在時',
    'required_with_all'    => ':attribute 字段是必須的當 :values 存在時',
    'required_without'     => ':attribute 字段是必須的當 :values 存在時',
    'required_without_all' => ':attribute 字段是必須的當沒有一個 :values 存在時',
    'same'                 => ':attribute 和 :other 必須',
    'size'                 => [
        'numeric' => ':attribute 必須是 :size 位',
        'file'    => ':attribute 必須是 :size ',
        'string'  => ':attribute 必須是 :size 個字符',
        'array'   => ':attribute 必須包括 :size 項',
    ],
    'string'               => ':attribute 必須是一個字符串',
    'timezone'             => ':attribute 必須是一個有效的時區',
    'unique'               => ':attribute 已存在',
    'uploaded'             => ':attribute 上傳失敗',
    'url'                  => ':attribute 無效的格式',
    'captcha'              => '驗證碼輸入錯誤',
    'captcha_api'          => '驗證碼輸入錯誤',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
