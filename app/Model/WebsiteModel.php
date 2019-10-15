<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;
use LiveControl\EloquentDataTable\DataTable;

class WebsiteModel extends EloquentModel{
    
    public $timestamps = false;
    protected $table = 'website'; // 表


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
            ['id','value','name','remark', 'create_time']
        );
        return $dataTable;
    }
    
    
    /**
     * 获取全部数据
     */
    public function getWebsite($key=null){
        if(is_null($key)){
            return $this->select('id','name','value')->orderBy('id', 'desc')->get()->toArray();
        }else{
            return $this->select('id','name','value')->orderBy('id', 'desc')->where('name',$key)->first()->toArray();
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
