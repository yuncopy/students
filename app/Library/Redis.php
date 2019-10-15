<?php

/**
 * 2017年11月22日19:39:18
 * Angela
 * 功能：redis 操作类
 */
namespace App\Library;

class Redis{
    
    private $redis = null;
    private  $server = array(
        'host' => '127.0.0.1',
        'port' => 6379,
        'auth' =>'',
        'database' => 3
    );
    
     /**
     * 生成实例
     *
     * @return null|static
     */
     private function single()
    {
         // 检测是否安装redis 扩展
        if (!extension_loaded("redis")) {
            throw new \Exception('redis  extension not readable!');
        }
        if (is_null($this->redis)) {
            try {
                $this->redis = new \Redis();
                $redis_master = Config('redis.master');  // 自定义系统定义函数获取配置
                $config    = !empty($redis_master) ? $redis_master : $this->server;
                $this->redis->connect( $config['host'], $config['port'] );
                $this->redis->auth($config["auth"]);
                $this->redis->select($config["database"]);
            } catch (\Exception $e) {
                throw new \Exception("<br/>redis connect error:" . $e->getMessage());
            }
        }
        return $this->redis;
    }

    // 在对象中调用一个不可访问方法时，__call() 会被调用。
    public function __call($method, $params)
    {
        return call_user_func_array([$this->getRedis(), $method], $params);
    }
    
    // 在静态上下文中调用一个不可访问方法时，__callStatic() 会被调用。
    public static function __callStatic($name, $arguments)
    {
        return self::getRedis()->$name(...$arguments);
    }

    
    //获取redis 对象
    public  function  getRedis(){
        return $this->single();
    }
    

}
