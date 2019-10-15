<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;
use LiveControl\EloquentDataTable\DataTable;

class RolesModel extends EloquentModel{
    
    public $timestamps = false;
    protected $table = 'roles';
    
    
    /**
     * 获取数据
     */
    public function getList($where){
        
        $dataTable = new DataTable(
            $this->where(function($query) use ($where){
                if($where){
                    foreach ( $where as  $value){
                        $query->where($value[0],$value[1] ,$value[2]);
                    }
                }
            }),
            ['id', 'show_name', 'name','permission_id','status','create_time']
        );
        return $dataTable;
    }

    /**
     * 2017年11月28日17:13:39
     * Angela
     * 添加数据/更新数据
     */
    public  function insertOrUpdate($data,$where=[]){
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
     * 2017年12月11日10:57:16
     * Angela
     * 删除数据
     */
    public function delOne($id){
        return $this->where('id', $id)->delete();
    }
    
    /**
     * 2017年12月11日11:16:44
     * Angela
     * 获取单条数据
     */
    public function getOne($id){
        return $this->where('id', $id)->first()->toArray();
    }
    
    /**
     * 查询信息
     * @param string $name Description
     */
    public function getUniqueData($name,$value){
        if(is_null($value)){
            $value = $name;
            $name = 'id';
        }
        $res = $this->where($name, $value)->first();
        if($res){
            return  $res->toArray();
        }
        return false;       
    }
    
    
   
    
}
