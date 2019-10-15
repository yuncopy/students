<?php 
/**
 * 2017年12月30日11:06:32
 * Angela
 * CURL 请求
 * https://packagist.org/packages/php-curl-class/php-curl-class
 */
namespace App\Library;
use \Curl\Curl as Base;

class Curl {
	protected $link;

	//获取实例
	protected function driver() {
		$this->link = new Base();
		return $this;
	}

	public function __call( $method, $params ) {
		if ( ! $this->link ) {
			$this->driver();
		}

		return call_user_func_array( [ $this->link, $method ], $params );
	}

	public static function single() {
		static $link = null;
		if ( is_null( $link ) ) {
			$link = new static();
		}

		return $link;
	}

	public static function __callStatic( $name, $arguments ) {
		return call_user_func_array( [ static::single(), $name ], $arguments );
	}
}