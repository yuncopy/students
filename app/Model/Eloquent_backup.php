<?php

/**
 * 2017年11月21日14:58:47
 * Angela 
 * 功能： 数据基类
 */
// https://laravel.com/api/5.1/Illuminate/Database.html
namespace App\Models;

use \Illuminate\Database\Capsule\Manager as Capsule; // 原始定义  https://d.laravel-china.org/
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use \Illuminate\Database\Eloquent\Model as Model; //基础Model  手册
class EloquentModel extends Model{
    
    static private $_instance;   //static可以保存值不丢失
    static private $_dbConnect;   // 链接资源
    private $_dbConfig = array(
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'database',
        'username'  => 'root',
        'password'  => 'password',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    );//保存数据库的配置信息，默认信息

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);  // 避免出错
        $this->_dbConfig = Config('database.connections');  // 获取配置信息
        $_db = self::getInstance();
        try{
            $_db->connect();  // 链接数据库
        }catch(Exception $e){
            die("sorry,error was happend.".$e->getMessage());
        }
    }
    
    //单例方法,用于访问实例的公共的静态方法
    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self(); //由类的自身来进行实例化
        }
        return self::$_instance;
    }
    
    // 实际链接数据库
    public function connect() {
        
        $config = $this->_dbConfig; //连接数据库
        $capsule = new Capsule;
        foreach ($config as $key => $value){
            $capsule->addConnection($value,$key); // 创建链接
        }
        $capsule->setEventDispatcher(new Dispatcher(new Container));
        $capsule->setAsGlobal(); // 设置全局静态可访问
        $capsule->bootEloquent(); // 启动Eloquent
        self::$_dbConnect = $capsule;
        if(!self::$_dbConnect){
            throw new Exception("mysql connect error".mysql_error());
        }
        return self::$_dbConnect;
    }


}

