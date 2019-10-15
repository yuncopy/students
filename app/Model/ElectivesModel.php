<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model;
use LiveControl\EloquentDataTable\DataTable;

class ElectivesModel extends EloquentModel{
    
    public $timestamps = false;
    protected $table = 'electives'; // 已选课程表

    protected $switchAuth = true;  // 数据模型开启
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
            }),//->orderBy('student_id','desc'),  //下面以排序有关系
            ['id','semester_id','student_id','teacher_id','subject_id','course_id', 'create_time','status','is_fraction']
        );
        return $dataTable;
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
     * 查询信息
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
    
    // 删除信息
    public function delOne($id){
        //$del = $this->getConnection()->transaction(function () use($id){
            return $this->where('id',$id)->delete();
       // });
       // return $del;
    }
    
    
    // 查询已选课的所有学生
    public function getSelecteds($where=[]){
        if(is_array($where)){
            return $this->where(function($query) use ($where){
               foreach ($where as $key => $value){
                   $query->where($key, $value);
               }
            })->get()->toArray();
        }
        return false;
    }

    // 不需要过滤条件
    public function getOneData($id){
        $this->switchAuth = false;
        return $this->where('id', $id)->first()->toArray();
    }
    
}
