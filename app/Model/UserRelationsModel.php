<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;

class UserRelationsModel extends EloquentModel{
    
    public $timestamps = false;
    protected $table = 'user_relations'; // 导师表

    
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
    
    
    // 删除关系信息
    public function delRelation($where=[]){
        if(is_array($where) && $where){
            return $this->where(function($query) use ($where){
                foreach ($where as $key => $value){
                    $query->where($key, $value);
                }
            })->delete();
        }
    }
    
    
    /**
     * 查询单条信息
     */
    public function getOneRelation($where){
        if(is_array($where) && $where){
            return $this->where(function($query) use ($where){
                foreach ($where as $key => $value){
                    $query->where($key, $value);
                }
            })->first()->toArray();   
        }
        return false;
    }
}
