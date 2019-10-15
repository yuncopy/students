<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tasks
 *
 * @author Administrator
 */
namespace App\Console;

error_reporting(E_ALL); //设置错误报告模式
include __DIR__.'/Tasks.php';
include __DIR__.'/TestClass.php';
/**
 * Swoole 服务相关操作
 */
class Servers {
    
    static public $pid_file = __DIR__."/server.pid";//pid文件
    static public $pid = null;   //pid
    static public $swoole_table = NULL;
    static public $mpid = 0;
    static public $max_precess = 5;
    static public $tasks = [];//所有任务
    static public $works = [];
    /**
     * 启动任务
     */
    static public function start(){
	try {
            if(file_exists(self::$pid_file)){
                throw new \Exception("服务启动失败，pid文件已经存在!");
            }
            self::$swoole_table = new \swoole_table(1024);  //使用共享内存来保存数据
            self::$swoole_table->column('index', \swoole_table::TYPE_INT); //用于父子进程间数据交换
            self::$swoole_table->create(); //创建内存表
            \swoole_set_process_name(sprintf('php-ps:%s', 'master'));  // 主进程
            self::daemon();  // false 是否开启一个守护进程
            self::register_signal();  // 注册监听事件
            self::write_pid_file();// PID写入文件
            self::init_task(); // 初始化任务
            //self::register_signal_task();  // 注册定时
            self::write_log("服务程序正常启动"); // 记录日志
            self::run_task();  // 获取任务并执行
            //self::process_wait();
        } catch (\Exception $e) {
            self::write_log('ERROR:'. $e->getMessage()); // 记录日志
        }
    }
    
    /**
     * 关闭主进程
     */
    static public function stop(){
        $pid_file = self::$pid_file;
        if(file_exists($pid_file) && $pid = file_get_contents($pid_file)){
            if(\swoole_process::kill($pid,0)){   //检查$pid进程是否存在
                \swoole_process::kill($pid);
                self::write_log('进程'.$pid.'已经结束，CLI服务关闭成功'); // 记录日志 
            }else{
                unlink($pid_file);
                self::write_log('进程'.$pid.'不存在，删除pid文件'); // 记录日志 
            }
        }else{
            self::write_log('服务关闭失败，服务没有启动'.$pid_file); // 记录日志 
        }
    }
    
    /**
     * 重启主进程
     */
   static public function restart(){
       self::stop();
       self::write_log('服务正在重启......'); // 记录日志 
       self::start();
       self::write_log('服务重启动成功'); // 记录日志 
   }
    
    
    
    /**
     * 注册监听事件信号
     */
    static private function register_signal(){
        
        // 父进程监听SIGCHLD信号，对子进程退出回收资源处理
        \swoole_process::signal(SIGCHLD,function(){ 
            while($ret =  \swoole_process::wait(false)) {   // 回收子进程
                $pid = $ret['pid'];  // 子进程pid
                if($pid){ 
                   self::write_log("执行完毕||修改状态ID:{$pid}"); 
                }
            }
        });
        //  SIGINT 用于ctrl+c时发出此信号给主进程
        \swoole_process::signal(SIGINT,function($signo){
            unlink(self::$pid_file);
            self::write_log("Ctrl+c服务退出成功");
            exit(1);
        });
        //SIGUSR1用于重启全部的Worker进程
        \swoole_process::signal(SIGUSR1,function($signo){
            //收到此信号重载任务配置
            self::write_log("重载任务成功");
        });
        //SIGTERM用于停止服务器，SIGTERM 仅在1.7.21或更高版本可用
        \swoole_process::signal(SIGTERM,function($signo){
            unlink(self::$pid_file);
            self::write_log("服务程序CLI正常退出");
            exit(1);
        });
    }
    
     /**
     * 获取任务列表
     */
    static private function init_task(){
        // 读取使用数据库
        $tasks = (new Tasks())->getTasks();
        if(is_array($tasks)){
            foreach($tasks as $job){
                $id = $job['id'];
                self::$tasks[$id] = (array)$job;
            }
        }
    }
    
    /**
     *注册定时器，监听任务列表
     */
    static private function register_signal_task(){
        \swoole_timer_tick(1000,function(){
            try{
                self::run_task();  // 获取任务并执行
            }catch(\Exception $e){
                self::write_log('注册定时器失败:'. $e->getMessage()); // 记录日志
            }
        });
    }
    
    /**
     * 执行任务任务函数
     */
    static private function run_task(){
        if(self::$tasks){
            foreach(self::$tasks as $key =>$task){
                if($key < self::$max_precess){  // 限制进程数
                    self::write_log('启动子进程执行服务'.$task['id']); // 记录日志排错  
                    self::create_process($task);  // 创建子进程执行函数
                }
            }
        }else{
            self::write_log('当前没有可执行任务'); // 记录日志排错  
        }
    }
    
    public static function create_process($task,$index = null) {
        $swoole_table = self::$swoole_table;  // 共享内存空间
        if (is_null($index)) { //如果没有指定了索引，新建的子进程，开启计数
            $index = $swoole_table->get('index');
            if ($index === false) {
                $index = 0;
            } else {
                $index = $index['index'] + 1;
            }
        }
        $swoole_table->set('index', array(
            'index' => $index
        ));
        $process = new \swoole_process(function (\swoole_process $worker) use($index) {
            \swoole_set_process_name(sprintf('php-ps:%s', $index));
            call_user_func_array(array(
                'App\\Console\\TestClass',
                'runTest'
            ) , array(
                $index,
                '333'
            ));
            sleep(10);
        }, true, true);  // 提示输出 false, false
        $pid = $process->start();
        self::$works[$index] = $pid;
        return $pid;
    }
    
    # 重启进程执行
    public static function reboot_process($ret) {
        $pid = $ret['pid'];
        $index = array_search($pid, self::$works);
        if ($index !== false) {
            $index = intval($index);
            $new_pid = self::create_process($index);
            echo "rebootProcess: {$index}={$new_pid} Done\n";
            self::write_log("rebootProcess: {$index}={$new_pid} Done\n");
            return;
        }
        throw new \Exception('rebootProcess Error: no pid');
    }
    
    /*
    // 等待进程是否执行完毕
    public static function process_wait() {
        while (1) {
            if (count(self::$works)) {
                $ret = \swoole_process::wait();
                $pid = $ret['pid'];
                if ($ret) {
                    self::write_log("finshProcess: pid={$pid} Done\n");
                    //self::reboot_process($ret);
                }
            } else {
                break;
            }
        }
    }
     */
    
     /**
     * 设置守护进程
     */
    static private function daemon($daemon=true){
        if($daemon === true){
            \swoole_process::daemon();
        }
    }
    
     /**
     *写入当前进程pid到pid文件
      * @return boolean
     */
    static private function write_pid_file(){
        return file_put_contents(self::$pid_file,self::get_pid());
    }
    
   /**
     * 获取当前进程pid
     * @return int
     */
    static private function get_pid(){
         self::$mpid = posix_getpid();
         return self::$mpid;
    }
    
    /**
     * 写日志
     */
    static private function write_log($content){
        $date= date('Ymd');
        $file = '/tmp/swoole_'.$date.'.log';
        return file_put_contents($file, '['.date('Y-m-d H:i:s').'] || '.$content.PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

if (PHP_SAPI === 'cli' || empty($_SERVER['REMOTE_ADDR'])){
    $opts =  getopt('a:');
    if (isset($opts['help']) || $argc < 2) {
        echo '用法：php swoole-task.php -a 选项[start|stop|restart|reload]'.PHP_EOL;
        exit(1);
    }
    $cmd = $opts['a'];
    switch ($cmd){
        case 'start':
            Servers::start();
            break;
        case 'stop':
             Servers::stop();
            break;
        case 'restart':
             Servers::restart();
            break;
    }
}

