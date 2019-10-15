<?php

/**
 * 首页控制
 * 2017年11月21日17:54:32
 */
namespace App\Controller;

class DemoController extends CommonController{
    //put your code heindexre
    public function index()
    {
       (new ContentsModel())->index();
        //$redis = new \Redis();
        //$redis->set('name', 'zhansan');
        //$retval = $redis->get('name');

       \Logs::info('index',22222,['aaa'=>11111]);
       
       $url = 'http://www.baidu.com';
       $curl = new Curl();
       //$aa=$curl->get($url);
       //var_dump($aa);
       $this->assign('index.html', ['aa'=>'434']);
    }
    
    public function test(){
        echo 1111;
    }
}
