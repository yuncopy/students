<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;
use LiveControl\EloquentDataTable\DataTable;

class TeachersModel extends EloquentModel{
    
    public $timestamps = false;
    protected $table = 'teachers'; // 导师表


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
            ['id', 'number', 'name', 'tutor_class_id', 'email','status']
        );
        return $dataTable;
    }
    
    
    /**
     * 自动填充使用ID
     */
    public function autoNumber(){
        return $this->max('id')+1;
    }

    //获取搜索引
    public function getAllsData(){
        return $this->select('id','name')->where("status","1")->get()->toArray();
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
    
    /**
     * 查询导师信息
     * @param string $name Description
     */
    public function getUniqueData($name,$value=null){
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
    
    // 删除导师信息
    public function delOne($id){
        $del = $this->getConnection()->transaction(function () use($id){
            return $this->where('id',$id)->delete();
        });
        return $del;
    }
    
}
