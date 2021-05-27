<?php
namespace QcloudClient;
/**
 * 签名
 * User: Administrator
 * Date: 2021/3/31
 * Time: 14:23
 */

class Signature
{

    /**
     * 获取签名
     * @param $sign_info
     * @return Signature
     */
    static public function getSignature($sign_info)
    {

        $secret_id = $sign_info['secret_id'];
        $secret_key = $sign_info['secret_key'];

        if (isset($sign_info['method'])) {
            $method = $sign_info['method'];
        }else{
            $method = 'GET';
        }

        if (isset($sign_info['path'])) {
            $path = $sign_info['path'];
        }else{
            $path = '/';
        }

        if (isset($sign_info['params'])) {
            $params = $sign_info['params'];
        }else{
            $params = array();
        }

        if (isset($sign_info['headers'])) {
            $headers = $sign_info['headers'];
        }else{
            $headers = array();
        }

        if (isset($sign_info['expire'])) {
            $expire = $sign_info['expire'];
        }else{
            $expire = 120;
        }

        return self::ceateSignature($secret_id, $secret_key, $method, $path, $params, $headers, $expire);
    }


    /**
     * 生成签名
     * @param $secret_id
     * @param $secret_key
     * @param string $method
     * @param string $path
     * @param array $params
     * @param array $headers
     * @param int $expire
     * @return string
     */
     static private function ceateSignature($secret_id, $secret_key, $method = 'GET', $path = '/', $params = array(), $headers = array(), $expire = 120)
    {
        $filter_headers = array();
        foreach ($headers as $k => $v) {
            $lower_key = strtolower($k);
            if ($lower_key == 'content-type' || $lower_key == 'content-md5' || $lower_key == 'host' || $lower_key[0] == 'x') {
                $filter_headers[$lower_key] = $v;
            }
        }
        $filter_params = array();
        foreach ($params as $k => $v) {
            $filter_params[strtolower($k)] = $v;
        }
        ksort($filter_params);
        ksort($filter_headers);
        $filter_headers = array_map('strtolower', $filter_headers);
        $uri_headers = http_build_query($filter_headers);
        $httpString = strtolower($method) . "\n" . urldecode($path) .
            "\n" . http_build_query($filter_params) . "\n" . $uri_headers . "\n";
        $signTime = (string)(time() - 60) . ';' . (string)(time() + $expire);
        #$signTime = "1510109254;1510109314";
        $stringToSign = "sha1\n" . $signTime . "\n" . sha1($httpString) . "\n";
        $signKey = hash_hmac('sha1', $signTime, $secret_key);
        $signature = hash_hmac('sha1', $stringToSign, $signKey);
        $sign_str =  "q-sign-algorithm=sha1&q-ak=$secret_id" .
            "&q-sign-time=$signTime&q-key-time=$signTime&q-header-list=" .
            join(";", array_keys($filter_headers)) . "&q-url-param-list=" .
            join(";", array_keys($filter_params)) . "&q-signature=$signature";

        return $sign_str;
    }

}


//测试
//echo Signature::signature(
//    'AKIDWpCI9ULTihlP4LHL3anlVyBguxTnvoRo',
//    'GH2e05vb2DAVBhyB9sMjrjKelbATP7Yj',
//    'GET',
//    '/searchlog',
//    array('logset_id' => 'd7f2ebd9-a11c-46ff-9e15-d58913630f54'),
//    array('Host' => 'ap-shanghai.cls.tencentcs.com', 'User-Agent' => 'AuthSDK'), 300);

//$arr = array(
//    'secret_id'=>'AKIDWpCI9ULTihlP4LHL3anlVyBguxTnvoRo',
//    'secret_key'=>'GH2e05vb2DAVBhyB9sMjrjKelbATP7Yj',
//    'method'=>'GET',
//    'path'=> '/searchlog',
//    'params'=>array('logset_id' => 'd7f2ebd9-a11c-46ff-9e15-d58913630f54'),
//    'headers'=> array('Host' => 'ap-shanghai.cls.tencentcs.com', 'User-Agent' => 'AuthSDK'),
//    'expire'=>300
//);
//echo Signature::getSignature($arr);