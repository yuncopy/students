<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tasks
 *
 * @author Administrator
 */
namespace App\Console;

class Tasks {
    //put your code here
    static $_instance=null;
    static $_connect=null;
    
    static public function getInstance() {
        if(!(self::$_instance instanceof self)) {
                self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * 连接数据库
     */
    public function connect() {
        if(!self::$_connect) {
            $default =  [
                'driver'    => 'mysql',
                'host'      => '192.168.4.9',
                'database'  => 'mpa',
                'username'  => 'root',
                'password'  => 'root',
                'port'      => '3306',
                'prefix'    =>  'mpa_'
            ];
            $driver = $default['driver'];
            $host = $default['host'];
            $port = $default['port'];
            $dbname = $default['database'];
            $username = $default['username'];
            $password = $default['password'];
            try {
                $dbh = new \PDO("{$driver}:host={$host};port={$port};dbname={$dbname}", $username, $password, 
                        array(
                            \PDO::ATTR_PERSISTENT => true,
                            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
                        ));
             } catch (Exception $e) {
                $dbh =  $e->getMessage();
             }
        };	
        return $dbh;
    }
    
    /**
     * 获取任务列表
     */
    public function getTasks(){
        $dbh = self::getInstance()->connect();
        try {
            $query = "SELECT id,name,command FROM mpa_tasks WHERE status = ?";
            $stmt = $dbh->prepare($query);
            if (!$stmt) {
               throw new Exception("errorInfo:".$dbh->errorInfo());
            }
            $stmt->execute(array(1));
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException  $e) {
            $result = $e->getMessage();
        }
        return $result;
    }
    
    
    /**
     * 修改任务
     */
    public function updateTask($id=null){
        
        $dbh = self::getInstance()->connect();
        try {
            $query = "UPDATE mpa_tasks SET `status` = ? , result = ? WHERE id = ?";
            $stmt = $dbh->prepare($query);
            if (!$stmt) {
               throw new Exception("errorInfo:".$dbh->errorInfo());
            }
            $date = date('Y-m-d H:i:s');
            $stmt->execute(array(2,"执行成功:{$date}",$id));
            $result = $stmt->rowCount();
        } catch (PDOException  $e) {
            $e->getMessage();
            $result = false;
        }
        return $result;
    }
    
}