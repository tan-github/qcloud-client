<?php
namespace QcloudClient;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/27
 * Time: 15:59
 */

class Test
{
    public function test()
    {
        /*************************** 测试 *******************************/
        //签名参数
        $config = array(
            'secret_id' => 'xxxxxxxxxxxxxxxxxxxx',
            'secret_key' => 'xxxxxxxxxxxxxxxxxxxxxxx',
            'logset_id' => 'xxxxxxxxxxxxxxxx',
            'topic_ids' => 'xxxxxxxxxxxxxxxxxxxx',
            'host' => 'ap-xxxxx.cls.tencentcs.com'
        );

        //查询参数
        $params = array(
            'start_time' => '2021-05-27 22:04:20',
            'end_time' => '2021-05-27 22:04:29',
            'query_string' => 'message:2133',
            'limit' => 10,
//            'context' => 'context= HTTP/1.1',
        );

        require_once 'Signature.php';
        require_once 'QcloudClient.php';
        $test = new QcloudClient($config);
        $rs = $test->search($params);

        var_dump($rs);
    }
}

(new Test())->test();