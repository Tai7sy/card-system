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

    'accepted'             => ':attribute 必须填写',
    'active_url'           => ':attribute 必须是一个合法的URL',
    'after'                => ':attribute 必须是 :date 之后的一个日期',
    'after_or_equal'       => ':attribute 必须是 :date 或之后的一个日期',
    'alpha'                => ':attribute 只能包含字母',
    'alpha_dash'           => ':attribute 只能包含字母、数字、中划线或下划线',
    'alpha_num'            => ':attribute 只能包含字母或数字',
    'array'                => ':attribute 必须是一个数组',
    'before'               => ':attribute 必须是 :date 之前的一个日期',
    'before_or_equal'      => ':attribute 必须是 :date 或之前的一个日期',
    'between'              => [
        'numeric' => ':attribute 必须在 :min 和 :max 之间',
        'file'    => ':attribute 必须在 :min 和 :max KB 之间',
        'string'  => ':attribute 必须在 :min 和 :max 个字符之间',
        'array'   => ':attribute 必须在 :min 和 :max 项之间',
    ],
    'boolean'              => ':attribute 字符必须是 true 或 false',
    'confirmed'            => ':attribute 二次确认不匹配',
    'date'                 => ':attribute 必须是一个合法的日期',
    'date_format'          => ':attribute 与给定的格式 :format 不一致',
    'different'            => ':attribute 和 :other 必须不同',
    'digits'               => ':attribute 必须是 :digits 位',
    'digits_between'       => ':attribute 必须在 :min 至 :max 位之间',
    'dimensions'           => ':attribute 无效的图片尺寸',
    'distinct'             => ':attribute 字段具有重复址',
    'email'                => ':attribute 必须是一个合法的电子邮件地址',
    'exists'               => '选定的 :attribute 是无效的',
    'file'                 => ':attribute 必须是一个文件',
    'filled'               => ':attribute 字段是必填项',
    'image'                => ':attribute 必须是 jpeg, png, bmp, 或者 gif 格式图像',
    'in'                   => '选定的 :attribute 是无效的',
    'in_array'             => ':attribute 字段在 :other 中不存在',
    'integer'              => ':attribute 必须是整数',
    'ip'                   => ':attribute 必须是一个合法的IP地址',
    'ipv4'                 => ':attribute 必须是一个合法的 IPv4 地址',
    'ipv6'                 => ':attribute 必须是一个合法的 IPv6 地址',
    'json'                 => ':attribute 必须是一个合法的 JSON 字符串',
    'max'                  => [
        'numeric' => ':attribute 的最大长度为 :max.',
        'file'    => ':attribute 大小至少为 :max ',
        'string'  => ':attribute 的长度至少为 :max 字符',
        'array'   => ':attribute 至少为 :max 项',
    ],
    'mimes'                => ':attribute 的文件类型必须为 type: :values.',
    'mimetypes'            => ':attribute 的文件类型必须为 type: :values.',
    'min'                  => [
        'numeric' => ':attribute 的最小长度为 :min 位',
        'file'    => ':attribute 大小至少为 :min ',
        'string'  => ':attribute 的最小长度为 :min 字符',
        'array'   => ':attribute 至少有 :min 项',
    ],
    'not_in'               => '选定的 :attribute 是无效的',
    'numeric'              => ':attribute 必须是数字',
    'present'              => ':attribute field must be present.',
    'regex'                => ':attribute 格式是无效的',
    'required'             => ':attribute 字段是必须的',
    'required_if'          => ':attribute 字段是必须的当 :other 是 :value.',
    'required_unless'      => ':attribute field is required unless :other is in :values.',
    'required_with'        => ':attribute 字段是必须的当 :values 存在时',
    'required_with_all'    => ':attribute 字段是必须的当 :values 存在时',
    'required_without'     => ':attribute 字段是必须的当 :values 存在时',
    'required_without_all' => ':attribute 字段是必须的当没有一个 :values 存在时',
    'same'                 => ':attribute 和 :other 必须',
    'size'                 => [
        'numeric' => ':attribute 必须是 :size 位',
        'file'    => ':attribute 必须是 :size ',
        'string'  => ':attribute 必须是 :size 个字符',
        'array'   => ':attribute 必须包括 :size 项',
    ],
    'string'               => ':attribute 必须是一个字符串',
    'timezone'             => ':attribute 必须是一个有效的时区',
    'unique'               => ':attribute 已存在',
    'uploaded'             => ':attribute 上传失败',
    'url'                  => ':attribute 无效的格式',
    'captcha'              => '验证码输入错误',
    'captcha_api'          => '验证码输入错误',

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
