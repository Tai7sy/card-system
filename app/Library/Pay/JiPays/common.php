<?php


/**
 * 生成待签名的字符串
 */
function create_link_string($para)
{
    //除去数组中的空值和签名参数
    $tmp = [];
    foreach ($para as $k => $v) {
        if ($k == 'sign' || $k == 'sign_type' || strval($v) === '') continue;
        $tmp[$k] = $v;
    }

    //对数组排序
    ksort($tmp);
    reset($tmp);

    //把数组所有元素，按照'参数=参数值'的模式用'&'字符拼接成字符串
    $arg = '';
    foreach ($tmp as $k => $v) {
        $arg .= $k . '=' . strval($v) . '&';
    }
    $arg = trim($arg, '&');

    //如果存在转义字符，那么去掉转义
    if (get_magic_quotes_gpc()) $arg = stripslashes($arg);

    return $arg;
}

/**
 * 发送HTTP请求方法
 * @param  string $url 请求URL
 * @param  array $params 请求参数
 * @param  string $method 请求方法GET/POST
 * @param  array $header 需要发送的请求header
 * @return
 */
function curl_http($url, $params = '', $method = 'GET', $header = array())
{
    $opts = array(
        CURLOPT_TIMEOUT => 5,
        CURLOPT_RETURNTRANSFER => 1,// 显示输出结果
        CURLOPT_HEADER => 0,// 过滤HTTP头
        CURLOPT_FOLLOWLOCATION => 1,// 跳转选项,如果出现错误码:3, 优先检查这里
        CURLOPT_SSL_VERIFYPEER => 0,// SSL证书认证
        CURLOPT_SSL_VERIFYHOST => 0,// 证书认证
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,// 强制协议为1.0
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,// 强制使用IPV4
    );

    //添加header
    if ($header) $opts[CURLOPT_HTTPHEADER] = $header;

    if (is_array($params)) $params = http_build_query($params);
    /* 根据请求类型设置特定参数 */
    switch (strtoupper($method)) {
        case 'GET':
            $opts[CURLOPT_URL] = $params ? $url . '?' . $params : $url;
            $opts[CURLOPT_CUSTOMREQUEST] = 'GET';
            break;

        case 'POST':
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;

        default:
            //
            break;
    }

    /* 初始化并执行curl请求 */
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $result = curl_exec($ch);

    /* 判断请求响应 */
    if ($result) {
        curl_close($ch);
        return $result;
    } else {
        $error = curl_errno($ch);
        curl_close($ch);
        exit('请求发起失败,错误码:' . $error);
    }
}

/**
 * 生成rsa签名
 * $string 待签名的字符串
 */
function create_rsa_sign($string)
{
    require('./config.php');

    ($rsa_private_key) or die('私钥信息尚未配置,请检查');

    $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . wordwrap($rsa_private_key, 64, "\n", true) . "\n-----END RSA PRIVATE KEY-----";
    openssl_sign($string, $sign, $private_key, OPENSSL_ALGO_SHA256);
    $sign = base64_encode($sign);
    return $sign;
}


/**
 * 判断订单签名
 * @param array $params 待签名的数组
 * @param string $key
 * @return bool
 */
function check_sign($params, $key)
{
    if (empty($params)) return false;
    if (!is_array($params)) return false;
    if (!isset($params['sign'])) return false;


    //拼接待签名的参数
    $string = create_link_string($params);

    //判断签名
    switch (strtoupper($params['sign_type'])) {
        case 'RSA2':
            ($web_public_key) or die('尚未设置网关RSA公钥');
            /**
             * 处理base64编码加号变空格的问题
             */
            $sign = str_replace(' ', '+', $params['sign']);
            /**
             * 拼接密钥
             */
            $pub_key = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($web_public_key, 64, "\n", true) . "\n-----END PUBLIC KEY-----";
            /**
             * 验证签名
             */
            $result = (bool)openssl_verify($string, base64_decode($sign), $pub_key, OPENSSL_ALGO_SHA256);

            return $result;
            break;

        case 'SHA256':
            $sign = hash('sha256', $string . '&key=' . $md5_key);
            return boolval($sign == $params['sign']);
            break;

        default:
        case 'MD5':
            $sign = md5($string . '&key=' . $md5_key);
            return boolval($sign == $params['sign']);
            break;
    }
}

/**
 * 判断是否微信访问
 * @return bool
 */
function is_weixin()
{
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if (strpos($ua, 'MicroMessenger') !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * 检测是否使用手机访问
 * @access public
 * @return bool
 */
function is_mobile()
{
    if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
        return true;
    } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
        return true;
    } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
        return true;
    } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
        return true;
    } else {
        return false;
    }
}
