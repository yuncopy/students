<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;

class TasksModel extends EloquentModel{
    
    public $timestamps = false;
    protected $table = 'tasks'; // 表

    /**
     * 获取全部数据
     */
    public function getTasks(){
        // 获取待执行任务
        return $this->orderBy('id', 'desc')->where('status',1)->get()->toArray();
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
