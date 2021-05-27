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
        $sign_info = array(
            'secret_id'=>'xxxxxxxxxxxxxxxxxxxx',
            'secret_key'=>'xxxxxxxxxxxxxxxxxxxxxx',
            'method'=>'GET',
            'path'=> '/searchlog',
            'params'=>array('logset_id' => 'xxxxxxxxxxxxxxxxxxx'),
            'headers'=> array('Host' => 'ap-shanghai.cls.tencentcs.com', 'User-Agent' => 'AuthSDK'),
            'expire'=>300
        );
        //查询参数
        $params = array(
            'logset_id' => 'xxxxxxxxxxxxxxxxxxxxx',
            'topic_ids' => 'xxxxxxxxxxxxxxxxxxx',
            'start_time' => '2021-03-31 00:00:01',
            'end_time' => '2021-03-31 23:00:00',
            'query_string' => 'aa:5',
            'limit' => 10,
            // 'context' => 'context= HTTP/1.1',
        );

        require_once 'Signature.php';
        require_once 'QcloudClient.php';
        $test = new QcloudClient($sign_info,'http://ap-shanghai.cls.tencentcs.com');
//        $test->setDomain('http://ap-shanghai.cls.tencentcs.com');
        $rs = $test->search($params);

        var_dump($rs);
    }
}

(new Test())->test();