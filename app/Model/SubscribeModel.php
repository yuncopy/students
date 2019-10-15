<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;

class SubscribeModel extends EloquentModel{
    
    public $timestamps = false;
    protected $table = 'subscribe';


    /**
     * 2017年11月28日17:13:39
     * Angela
     * 添加数据/更新数据
     */
    public function insertOrUpdate($data,$where=[]){
        if(!empty($data) && !empty($where)){
            return $this->where(function($query) use ($where){
                foreach ($where as $key => $value){
                    $query->where($key, $value);
                }
            })->update($data);
        }else{
            return $this->insertGetId($data);
        }
        return false;
    }
    
    /**
     * 2017年11月28日18:24:00
     * Angela
     * 验证用户
     * 
     */
    public function checkLogin($msisdn,$pwd){
       $msisdn = '+60'.$msisdn;
       $login =  $this->where('msisdn', $msisdn)->where('pwd',$pwd)->where('mt_status','A')->first();
       if($login){
           return $login->toArray();
       }
       return false;
    }
    
    /**
     * 2017年12月13日12:52:18
     * Angela
     * 获取spTransID 
     */
    public function setSpTransID($msisdn,$pwd){
        $login = $this->checkLogin($msisdn,$pwd);
        if($login){
            $transID = $login['sptransid'];
            SubService::spTransIDSetAndGet($transID);  // 退订时使用
        }
    }
    
}
