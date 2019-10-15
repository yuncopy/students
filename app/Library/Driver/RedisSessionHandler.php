<?php

/**
 * 2017年12月29日10:08:09
 * Angela
 *  
 */
namespace App\Library\Driver;
use App\Library\Driver\BaseSession;

class RedisSessionHandler implements AbstractSession {
	use BaseSession;
    private  $server = array(
        'host' => '127.0.0.1',
        'port' => 6379,
        'auth' =>'',
        'database' => 6,
        'time_out'=>3
    ); // 默认配置

        /**
	 * Redis连接对象
	 * @access private
	 * @var Object
	 */
	private $redis;
        
	public function open() {
            $redis_master = Config('redis.master');  // 自定义系统定义函数获取配置

            $config      = !empty($redis_master) ? $redis_master : $this->server;
            $this->redis = new \Redis();

            $this->redis->connect( $config['host'], $config['port'] ,$config['time_out']);

            if ( ! empty( $config['auth'] ) ) {
                    $this->redis->auth( $config['auth'] );
            }
            $this->redis->select( (int) $config['database'] );
	}
	//获得
	function read() {
            $sessionId = $this->session_id;
            $data = $this->redis->get($sessionId);
            $this->expire ? $this->redis->expire($sessionId, $this->expire) :  false;
            return $data ? json_decode( $data,true ) : [ ];
	}
	//写入
	function write() {
            $sessionId = $this->session_id;
            $this->redis->set( $sessionId , json_encode( $this->items ,JSON_UNESCAPED_UNICODE) );
            $this->expire ? $this->redis->expire($sessionId, $this->expire) :  false;
            return  true;
	}
	//垃圾回收
	function gc() {
            
	}
}
