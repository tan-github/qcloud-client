<?php

namespace QcloudClient;
/**
 * 腾讯云日志客户端
 * User: Administrator
 * Date: 2021/3/31
 * Time: 14:23
 */

class QcloudClient
{
    //签名生成的token
    private $auth = '';
    //接口域名
    private $domain = '';

    /**
     * 接口域名
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * QcloudClient constructor.
     * @param $sign_info 签名参数
     * @param string $domain 接口域名
     */
    public function __construct($sign_info, $domain = '')
    {
        $this->auth = Signature::getSignature($sign_info);

        if ($domain) {
            $this->domain = $domain;
        }
    }

    /**
     * 查询日志
     * @param $params
     * @return bool|mixed
     */
    public function search($params)
    {
        $url = $this->domain . '/searchlog?';

        $rs = $this->callCurl(
            $url . http_build_query($params),
            array(),
            array(
                'token' => $this->auth,
                'method' => 'GET'
            )
        );

        return $rs;
    }


    /**
     * 调用curl
     * @param $url
     * @param array $data
     * @param array $params
     * @return bool|mixed
     */
    private function callCurl($url, $data = array(), $params = array())
    {
        $timeout = 5;
        if (isset($params['method']) && $params['method']) {
            $method = strtoupper($params['method']);
        } else {
            $method = 'POST';
        }

        $auth_username = isset($params['username']) ? trim($params['username']) : '';
        $auth_password = isset($params['password']) ? trim($params['password']) : '';

        if (isset($params['timeout']) && $params['timeout']) {
            $timeout = $params['timeout'];
        }

        $headers = array('Accept: application/json', 'Content-Type: application/json');

        if (isset($params['token']) && $params['token']) {
            $headers[] = 'Authorization:' . $params['token'];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);//5秒
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);//5秒

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $auth_username . ':' . $auth_password);

        switch ($method) {
            case 'GET' :
                break;
            case 'POST' :
                if ($data) {
                    $data = json_encode($data);
                }

                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            case 'PUT' :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'DELETE' :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        if (!curl_errno($ch)) {
            $response = json_decode(curl_exec($ch), true);
        } else {
            $response = false;
        }

        // 释放资源
        curl_close($ch);

        return $response;
    }
}


