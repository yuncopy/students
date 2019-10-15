<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;

class CategoryModel extends EloquentModel{
    
    public $timestamps = false;
    protected $table_cates = [
       'week'       =>  'weeks', 
       'interval'   =>  'intervals',
       'room'       =>  'rooms',
       'grade'      =>  'grades',
       'credit'     =>  'credits',
    ]; 
    
    // 操作实例
    public  function bdTable($table){
        if($table){
            $_instance = $GLOBALS['capsule'];
            return  $_instance::table($table); 
        }
    }
    
            
    /**
     * 获取类型类型(课程相关)
     */
    public function getCates( $cate = null ){
        $tables = $this->table_cates;
        $cateData = [];
        if($cate){
            $cateObject = $this->bdTable($tables[$cate])->get();
             if($cateObject){
                $cateData =  $cateObject->toArray();
            }
        }else{
            foreach ($tables as $value){
                $cateObject = $this->bdTable($value)->get();
                if($cateObject){
                    $cateData [$value] =  $cateObject->toArray();
                }
            }
        }
        return $cateData;
    }
    
    
    /**
     * 唯一性
     */
    public function  getUniqueData($data){
        $tables = $this->table_cates;
        $res = $this->bdTable($tables[$data[2]])->where($data[0], $data[1])->first();
        return !empty($res) ? $res : false;
    }

    

    /**
     * 2017年12月8日14:29:32
     * Angela
     * 修改内容和添加
     */
    public function insertOrUpdate($cate,$data,$where=[]){
        
        $tables = $this->table_cates;
        $tableObject = $this->bdTable($tables[$cate]);
        
        if(!empty($data) && !empty($where)){
            return $tableObject->where(function($query) use ($where){
                foreach ($where as $key => $value){
                    $query->where($key, $value);
                }
            })->update($data);
        }else if(!$where){
            $Id = $tableObject->insertGetId($data);
            //echo $this->toSql();
            return $Id;
        }
        return false;
    }
    
  
    
    // 删除信息
    public function delData($cate,$id){
        try{
            $tables = $this->table_cates;
            $tableObject = $this->bdTable($tables[$cate]);
            $delRes =  $tableObject->where('id',$id)->delete();
        } catch ( \Illuminate\Database\QueryException $e){
            $delRes = false;
        }
        return $delRes;
    }
    
}
