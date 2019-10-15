<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Library;
use App\Library\Redis;
class Session {
    
    //操作驱动
    protected static $link;
    protected static $driver = 'redis';    //file

     /**
     * 生成实例
     *
     * @return null|static
     */
    public static function single()
    {
        if (is_null(self::$link)) {
            $driver = ucfirst(self::$driver);
            $class  = '\App\Library\Driver\\'.$driver.'SessionHandler';
            self::$link = new $class();
        }
        return self::$link;
    }
    
    public function __call($method, $params)
    {
        return call_user_func_array([self::single(), $method], $params);
    }
    
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([static::single(), $name], $arguments);
    }
}
