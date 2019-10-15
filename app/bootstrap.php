<?php
namespace App;
use \Illuminate\Database\Capsule\Manager as Capsule;
use \Illuminate\Events\Dispatcher;
use \Illuminate\Container\Container;
use App\Library\Session;

//Eloquent ORM
{
    $capsule = new Capsule;
    $database = require APP_PATH.'/config/database.php';  // 获取数据库信息
    foreach ($database as $key => $value){
        $capsule->addConnection($value,$key); // 创建链接
    }
    $capsule->setEventDispatcher(new Dispatcher(new Container));
    $capsule->setAsGlobal(); // 设置全局静态可访问
    $capsule->bootEloquent(); // 启动Eloquent

    
    /*说明文档  
     *  https://scotch.io/tutorials/debugging-queries-in-laravel#listening-for-query-events
     *  https://gist.github.com/junxy/cb7b8ea36f581ce40166
     *  register_shutdown_function
     */

    Capsule::listen(function($sql){
        if($sql){
            $query = get_object_vars($sql);
            $querytime = $query['time'];
            $querylog = APP_PATH.'/storage/log/'.date('Ymd').'_query.log';  // SQL查询日志
            $prep = $query['sql'];
            $bindings = $query['bindings'];
            $sql = str_replace(array('%', '?'), array('%%', '%s'), $prep);
            $sql = vsprintf($sql, $bindings);
            $time_now = date('Y-m-d H:i:s');
            $log = $time_now . ' | ' . $sql . ' | ' . $querytime . ' ms' . PHP_EOL;
            file_put_contents ( $querylog,  $log ,  FILE_APPEND );
        }
    });

   
}

//定义系统配置文件
{
    require APP_PATH.'/config/app.php';

}

// whoops 错误提示
{
    if( __DEBUG__){
        ini_set('display_errors', 'On');

        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();

    }else{
        ini_set('display_errors', 'Off');
    }

    Session::bootstrap();  // 启动SESSION
}

