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
    private $secret_id = '';
    private $secret_key = '';
    //日志集
    private $logset_id = '';
    //日志主题
    private $topic_ids = '';
    //接口host
    private $host = '';
    //请求方法
    private $method = 'GET';
    //过期时间（秒）
    private $expire = 300;

    /**
     * 日志集
     * @param string $logset_id
     */
    public function setLogsetId($logset_id)
    {
        $this->logset_id = $logset_id;
    }

    /**
     * 日志主题
     * @param string $topic_ids
     */
    public function setTopicIds($topic_ids)
    {
        $this->topic_ids = $topic_ids;
    }

    /**
     * 接口host
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }


    /**
     * 请求方法
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = strtoupper($method);
    }

    /**
     * 过期时间（秒）
     * @param int $expire
     */
    public function setExpire($expire)
    {
        $this->expire = $expire;
    }


    /**
     * QcloudClient constructor.
     * @param $config
     *
     * $config = array(
     * 'secret_id' => 'xxxxxxxxxxxxxxxxxxxx',
     * 'secret_key' => 'xxxxxxxxxxxxxxxxxxxxxxx',
     * 'logset_id' => 'xxxxxxxxxxxxxxxx',
     * 'topic_ids' => 'xxxxxxxxxxxxxxxxxxxx',
     * 'host' => 'ap-xxxxx.cls.tencentcs.com'
     * );
     */
    public function __construct($config)
    {
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }

    }

    /**
     * 获取签名
     * @param $path
     * @return Signature
     */
    private function getSignature($path)
    {
        //签名参数
        $sign_info = array(
            'secret_id' => $this->secret_id,
            'secret_key' => $this->secret_key,
            'method' => $this->method,
//            'path'=> '/searchlog',
            'path' => $path,
            'params' => array('logset_id' => $this->logset_id),
            'headers' => array('Host' => $this->host, 'User-Agent' => 'AuthSDK'),
            'expire' => $this->expire
        );
        return Signature::getSignature($sign_info);
    }

    /**
     * 查询日志
     * @param $params 参数
     * $params = array(
     * 'start_time' => '2021-05-27 22:04:20',
     * 'end_time' => '2021-05-27 22:04:29',
     * 'query_string' => 'message.schoolId:2133',
     * 'limit' => 10,
     * //  'context' => 'context= HTTP/1.1',
     * );
     * @return bool|mixed
     */
    public function search($params)
    {
        $path = '/searchlog';

        $url = 'http://' . $this->host . $path . '?';
        $auth = $this->getSignature($path);

        $params['logset_id'] = $this->logset_id;
        $params['topic_ids'] = $this->topic_ids;

        $rs = $this->callCurl(
            $url . http_build_query($params),
            array(),
            array(
                'token' => $auth,
                'method' => $this->method
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


