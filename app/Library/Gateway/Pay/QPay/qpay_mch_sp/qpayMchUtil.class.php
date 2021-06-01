<?php
/**
 * qpayMachAPI.php
 * Created by HelloWorld
 * vers: v1.0.0
 * User: Tencent.com
 */
require_once('qpayMch.config.php');

class QpayMchUtil
{

    /**
     * 生成随机串
     * @param int $length
     *
     * @return string
     */
    public static function createNoncestr($length = 32)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 将参数转为hash形式, 去除sign参数
     * @param $params
     *
     * @return string
     */
    public static function buildQueryStr($params)
    {
        $arrTmp = array();
        foreach ($params as $k => $v) {
            //参数为空不参与签名
            if ($k != 'sign' && $v != "" && !is_array($v)) {
                array_push($arrTmp, "$k=$v");
            }
        }
        return implode('&', $arrTmp);
    }

    /**
     * 获取参数签名
     * @param $params
     *
     * @param $mch_key
     * @return string
     */
    public static function getSign($params, $mch_key)
    {
        //第一步：对参数按照key=value的格式，并按照参数名ASCII字典序排序
        ksort($params);
        $stringA = QpayMchUtil::buildQueryStr($params);
        //第二步：拼接API密钥并md5
        $stringA = $stringA . '&key=' . $mch_key;
        $stringA = md5($stringA);
        //转成大写
        $sign = strtoupper($stringA);
        return $sign;
    }

    /**
     * 数组转换成xml字符串
     * @param $arr
     * @return string
     */
    public static function arrayToXml($arr)
    {
        $xml = '<xml>';
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<$key>$val</$key>";
            } else
                $xml .= "<$key><![CDATA[$val]]></$key>";
        }
        $xml .= '</xml>';
        return $xml;
    }

    /**
     * xml转换成数组
     * @param $xml
     * @return array|mixed|object
     */
    public static function xmlToArray($xml)
    {
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $arr;
    }

    /**
     * 通用curl 请求接口。post方式
     * @param     $params
     * @param     $url
     * @param int $timeout
     *
     * @return bool|mixed
     */
    public static function reqByCurlNormalPost($params, $url, $timeout = 10)
    {
        return QpayMchUtil::_reqByCurl($params, $url, $timeout, false);
    }

    /**
     * 使用ssl证书请求接口。post方式
     * @param     $params
     * @param     $url
     * @param array $ssl
     * @param int $timeout
     * @return bool|mixed
     */
    public static function reqByCurlSSLPost($params, $url, $timeout, $ssl)
    {
        return QpayMchUtil::_reqByCurl($params, $url, $timeout, $ssl);
    }

    private static function _reqByCurl($params, $url, $timeout = 10, $needSSL = false)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //是否使用ssl证书
        if (isset($needSSL) && is_array($needSSL)) {
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $needSSL['ssl_cert']);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $needSSL['ssl_key']);
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $ret = curl_exec($ch);
        if ($ret) {
            curl_close($ch);
            //log($ret); //业务记录交互流水。注：流水日志影响性能，如请求量过大，请慎重设计日志。
            return $ret;
        } else {
            $error = curl_errno($ch);
            // log($error); //业务记录错误日志
            // print_r($error);
            curl_close($ch);
            throw new Exception('curl_exec failed with ' . $error);
        }
    }

}
