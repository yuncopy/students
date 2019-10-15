<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
*/
namespace App\Console;
/**
 * Description of Process
 *
 * @author Administrator
 */
class Process {
    public $mpid = 0;
    public $max_precess = 5;
    public $works = [];
    public $swoole_table = NULL;
    //public $new_index=0;
    //代替从数据库中读取的内容
    public $task = [
        ['uid' => 1, 'uname' => 'bot', 'hash' => 1, 'handle' => 'test'], 
        ['uid' => 2, 'uname' => 'bot1', 'hash' => 2, 'handle' => 'test'], 
        ['uid' => 3, 'uname' => 'bot2', 'hash' => 3, 'handle' => 'test'], 
        ['uid' => 4, 'uname' => 'bot3', 'hash' => 4, 'handle' => 'test'], 
        ['uid' => 2, 'uname' => 'bot4', 'hash' => 2, 'handle' => 'test'], 
        ['uid' => 3, 'uname' => 'bot5', 'hash' => 3, 'handle' => 'test'], 
        ['uid' => 4, 'uname' => 'bot6', 'hash' => 1, 'handle' => 'test']
    ];
    
    function test($index, $task) {
        print_r("[" . date('Y-m-d H:i:s') . "]" . 'work-index:' . $index . '处理' . $task['uname'] . '完成' . PHP_EOL);
    }
    public function __construct() {
        try {
            $this->swoole_table = new \swoole_table(1024);
            $this->swoole_table->column('index', \swoole_table::TYPE_INT); //用于父子进程间数据交换
            $this->swoole_table->create();
            \swoole_set_process_name(sprintf('php-ps:%s', 'master'));
            $this->mpid = posix_getpid();
            $this->run();
            $this->processWait();
        }
        catch(Exception $e) {
            die('ALL ERROR: ' . $e->getMessage());
        }
    }
    public function run() {
        for ($i = 0; $i < $this->max_precess; $i++) {
            $this->CreateProcess();
        }
    }
    private function getTask($index) {
        $_return = [];
        foreach ($this->task as $v) {
            if ($v['hash'] == $index) {
                $_return[] = $v;
            }
        }
        return $_return;
    }
    

    public function CreateProcess($index = null) {
        if (is_null($index)) { //如果没有指定了索引，新建的子进程，开启计数
            $index = $this->swoole_table->get('index');
            if ($index === false) {
                $index = 0;
            } else {
                $index = $index['index'] + 1;
            }
            print_r($index);
        }
        $this->swoole_table->set('index', array(
            'index' => $index
        ));
        $process = new \swoole_process(function (\swoole_process $worker) use($index) {
            \swoole_set_process_name(sprintf('php-ps:%s', $index));
            $task = $this->getTask($index);
            foreach ($task as $v) {
                call_user_func_array(array(
                    $this,
                    $v['handle']
                ) , array(
                    $index,
                    $v
                ));
            }
            sleep(20);
        }
        , false, false);
        $pid = $process->start();
        $this->works[$index] = $pid;
        return $pid;
    }
    /*
    public function CreateProcess($index=null){ 
        $process = new \swoole_process(function(\swoole_process $worker) use ($index) { 
            if(is_null($index)){//如果没有指定了索引，新建的子进程，开启计数 
                $index = $this->swoole_table->get('index');
                if($index === false){ 
                    $index['index'] = 0;
                }else{ 
                    $index['index']++;      
                }    
            }
            
            $this->swoole_table->set('index',array('index'=>$index['index']));//类似于定义一个全局变量，这里必须用数组（文档要求的）
            \swoole_set_process_name("php-ps:{$index['index']}");

            for($j=0;$j<16000;$j++){
                $this->checkMpid($worker);
                echo "msg:{$j}\n";
                sleep(1);
            }
        },false, false);
        $pid = $process->start();
        $index=$this->swoole_table->get('index');
        $index=$index['index'];
        $this->works[$index] = $pid;
        return $pid;
    }*/

    public function checkMpid(&$worker){
        //子进程监听主进程的地方
        $kill = \swoole_process::kill($this->mpid,0);
        if(!$kill){ //import! check whether master process is running  
            file_put_contents('/tmp/runtime.log', "Master process exited, I [{$worker['pid']}] also quit\n", FILE_APPEND);
            $worker->exit();
        }
    }
    
    public function rebootProcess($ret) {
        $pid = $ret['pid'];
        $index = array_search($pid, $this->works);
        if ($index !== false) {
            $index = intval($index);
            $new_pid = $this->CreateProcess($index);
            echo "rebootProcess: {$index}={$new_pid} Done\n";
            return;
        }
        throw new \Exception('rebootProcess Error: no pid');
    }
    public function processWait() {
        while (1) {
            if (count($this->works)) {
                $ret = \swoole_process::wait();
                if ($ret) {
                    $this->rebootProcess($ret);
                }
            } else {
                break;
            }
        }
    }
}
$process = new Process();

