<?php
/**
 * 2017年11月22日19:38:50
 * Angela
 * 功能：日志类
 */
namespace App\Library;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
class Logs{
    /**
     * @param string  $file 文件名
     * @param string $message 消息内容
     * @param array $context 数组
     * 
     * @return Logger
     */
    public static function info($file,$message='',$context=[]){
       // Create the logger
        $logger = new Logger('MPA_logger');
        // Now add some handlers
        $func_num = func_num_args();
        if($func_num == 1){  //传递一个参数,当成说明
            $message = func_get_arg(0); //   取得指定位置的参数值，$arg_num位置index从0开始n-1。 
            $file = false;
        }else if($func_num == 2 && is_array($message)){  //传递两个参数,第二个参数是数组
            $message_array = func_get_args();   //返回包含所有参数的数组 
            $message = $message_array[0];
            $context = $message_array[1];
            $file = false;
        }
        $file_path = __LOG__.date('Ymd').'.log';  // 默认文件
        if($file) $file_path =__LOG__.date('Ymd')."_{$file}.log";  // 自定义文件
        $logger->pushHandler(new StreamHandler( $file_path , Logger::DEBUG ));
        $logger->pushHandler(new FirePHPHandler());
        // You can now use your logger
        $logger->addInfo($message, $context);
    }
   
}