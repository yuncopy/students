<?php

namespace App\Library\Driver;

/**
 * 声明接口
 */
interface AbstractSession {
    
    public function open();
    
    public function read();
    
    public function gc();
    
    public function flush();
}
