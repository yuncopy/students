<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Swoole
 *
 * @author Administrator
 */
/**
 * 异步处理任务，合并测试
 */

namespace App\Console;
error_reporting(E_ALL); //设置错误报告模式
include __DIR__.'/Tasks.php';
/**
 * 检查exec 函数是否启用
 */
if (!function_exists('exec')) {
    exit('exec function is disabled' . PHP_EOL);
}


class SwooleConsole {
    
    static public $pid_file = __DIR__."/server.pid";//pid文件
    static public $pid = null;   //pid
    static public $tasks = [];//所有任务
    public $works = [];
    public $max_process = 5;  //创建的进程数
    public static $swoole_table = NULL;
    static public $run_task_id = []; // 正在执行任务
    
    /**
     * 启动任务
     */
    static private function start(){
        
	try {
            if(file_exists(self::$pid_file)){
                //throw new \Exception("服务启动失败，pid文件已经存在!");
            }
            \swoole_set_process_name('php-ps:master');
            
            self::get_task();//获取任务
            self::daemon();  //守护进程
            self::register_signal();//注册监听的信号
            self::register_timer_task();// 注册定时器
            self::write_pid_file(); //写pid
            self::write_log('服务启动成功'); // 记录日志
            
        } catch (\Exception $e) {
            die('ERROR:'. $e->getMessage()); // 记录日志
        }
    }
    
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
        return posix_getpid();
    }
    
    /**
     * 注册监听信号，回收子进程
     */
    static private function register_signal(){
        \swoole_process::signal(SIGCHLD,function(){
            //SIGCHLD，子进程结束时，父进程会收到这个信号，子进程已关闭，回收它
            while($ret =  \swoole_process::wait(false)) {  //回收结束运行的子进程。
                $pid = $ret['pid'];  // 子进程pid
                if($pid){ 
                    $id = self::$run_task_id[$pid];
                    $rt = (new Tasks())->updateTask($id);  //修改数据库状态
                    if($rt){
                        self::write_log("执行完毕||修改状态ID:{$id}"); // 记录日志
                        unset(self::$run_task_id[$pid]);
                    }
                }
            }
        });
        
        \swoole_process::signal(SIGINT,function($signo){
            //命令行输入ctrl+c时发出此信号给主进程
            unlink(self::$pid_file);
            self::write_log("Ctrl+c服务退出成功");
            exit(1);
        });
        //重新载入任务
        \swoole_process::signal(SIGUSR1,function($signo){
            //收到此信号重载任务配置
            self::get_task();
            self::write_log("重载任务成功");
        });
        
        //自己正常退出
        \swoole_process::signal(SIGTERM,function($signo){
            //通常用来要求程序自己正常退出. shell命令kill缺省产生这个信号，此型号可以被阻塞，处理
            unlink(self::$pid_file);
            self::write_log("服务程序自己正常退出");
            exit(1);
        });
    }
    
    /**
     *注册定时器，执行脚本任务
     */
    static private function register_timer_task(){
        \swoole_timer_tick(1000,function(){
            try{
                self::run_job();  // 执行任务
            }catch(\Exception $e){
                self::write_log('注册定时器失败:'. $e->getMessage()); // 记录日志
            }
        });
    }
    
    
    /**
     * 执行任务函数
     */
    static private function run_job(){
        if(count(self::$tasks) > 0){
            foreach(self::$tasks as $task){
                self::create_process($task);  // 创建子进程执行函数
            }
        }else{
            //elf::write_log('当前没有可执行任务'); // 记录日志排错  
            //exit(1);// 退出主进程，仅适用本项目
            return true;
        }
    }

    

    /**
     * 获取任务列表
     */
    static private function get_task(){
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
     * 写日志
     */
    static private function write_log($content){
        $date= date('Ymd');
        $file = dirname(__FILE__)."/../../storage/log/swoole_{$date}.log";
        return file_put_contents($file, '['.date('Y-m-d H:i:s').'] || '.$content.PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
   /**
    * 创建进程函数
    */
    static private function create_process($task) {
        $process = new \swoole_process(function(\swoole_process $worker) use ($task) {
            \swoole_set_process_name("php-ps:{$task['id']}");
            $command = $task['command'];
            list($php,$srcipt) = explode(' ', $command);
            $worker->exec($php,[$srcipt]);  // 执行具体脚本
            
            self::write_log("command:{$php}||{$srcipt}"); // 执行日志
            
        }, true, true);
        $pid = $process->start();
        if(!$pid){
            self::write_log('子任务创建失败'); // 记录日志 
        }else{
            $id = $task['id'];
            self::write_log("任务{$task['name']}开始执行 || pid:{$pid}"); // 记录日志 
            self::$run_task_id[$pid] = $id;  // 执行任务
            unset(self::$tasks[$id]);
            return true;
        }
    }
    
     /**
     * 停止服务
     */
    static public function stop(){
        $pid_file = self::$pid_file;
        if(file_exists($pid_file) && $pid = file_get_contents($pid_file)){
            if(\swoole_process::kill($pid,0)){        //检查$pid进程是否存在
                \swoole_process::kill($pid);
                exec("kill -9 {$pid}");  // 主进程服务
                self::write_log('进程'.$pid.'已经结束，服务关闭成功'); // 记录日志 
            }else{
                unlink($pid_file);
                self::write_log('进程'.$pid.'不存在，删除pid文件'); // 记录日志 
            }
        }else{
            self::write_log('服务关闭失败，服务没有启动'.$pid_file); // 记录日志 
        }
    }
    /**
     * 重载配置
     */
    static public function reload(){
        $pid_file = self::$pid_file;
        if(file_exists($pid_file)){
            $pid = file_get_contents($pid_file);
            if($pid){
                $res = \swoole_process::kill($pid,SIGUSR1);
                if($res){
                    self::write_log('进程重载配置成功'); // 记录日志
                }
            }else{
                self::write_log('进程不存在，重载配置失败');
            }
        }else{
             self::write_log('服务未启动，重载配置失败');
        }
    }
    
    
    
    // 对外提供访问
    static public function  server_start(){
        return self::start();  // 启动服务
    }
    static public function  server_reload(){
        return self::reload();  // 启动服务
    }
    static public function  server_stop(){
        return self::stop();  // 启动服务
    }
    static public function  server_restart(){
       self::stop();  // 启动服务
       return self::start();  // 启动服务
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
        \App\Console\SwooleConsole::server_start(); //启动
        break;
    case 'stop':
         \App\Console\SwooleConsole::server_stop(); //停止
        break;
    case 'restart':
         \App\Console\SwooleConsole::server_restart();
        break;
    case 'reload':
         \App\Console\SwooleConsole::server_reload();
        break;
}
}
