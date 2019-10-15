<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;
use App\Service\LoginService;

class ChangePassModel extends EloquentModel{
    
    public $timestamps = false;
    protected $table = 'change_pass'; // 表


    /**
     * 检测是否已经修改密码，添加修改密码
     */
    public function checkInitPass(){
        $uid = LoginService::getLoginInfo('uid');
        $isChange = $this->where('user_id',$uid)->first();
        if($isChange){
            return true;  //$isChange->toArray();
        }else{
            return false;
        }
    }
    
    /**
     * 2017年12月8日14:29:32
     * Angela
     * 修改内容和添加
     */
    public function insertOrUpdate($data,$where=[]){
        if(!empty($data) && !empty($where)){
            return $this->where(function($query) use ($where){
                foreach ($where as $key => $value){
                    $query->where($key, $value);
                }
            })->update($data);
        }else if(!$where){
            return $this->insertGetId($data);
        }
        return false;
    }
}
