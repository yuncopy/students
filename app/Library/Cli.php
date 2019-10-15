<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Library;
use FusePump\Cli\Utils as Utils;
/**
 * Description of Cli
 *https://packagist.org/packages/fusepump/cli.php
 * @author Administrator
 */
class Cli {
    protected $link;

    //获取实例
    protected function driver() {
            $this->link = new Utils();
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
