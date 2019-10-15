<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:导师服务
 * 
 */
namespace App\Service;
use App\Model\SubjectsModel;

class SubjectsService extends CommonService{
    
    
    /**
     * 填充编号
     */
    public static function autoNumber(){
        return (new SubjectsModel())->autoNumber();
    }
    
    /**
     * 获取数据
     */
    public static function getList($where){
        
        $teachers = new SubjectsModel();
        $dataTable = $teachers->getList($where);
        $dataTable->setFormatRowFunction(function ($row){
            return [
                'id'            =>  $row['id'],
                'name'          =>  $row['name'],
                'start_time'    =>  $row['start_time'],
                'end_time'      =>  $row['end_time'],
                'credit'        =>  $row['credit'],
                'create_time'   =>  $row['create_time'],
                'status'        =>  $row['status'],
                'action'        =>  $row['id']
            ];
        });
       return $dataTable->make();
    }
    
    
    // 获取全部数据
    public static function getSubjects(){
        return (new SubjectsModel())->getSubjects();
    }


    /**
     * 插入数据
     */
    public static function insertData($data){
        if(is_array($data) && $data){
            return (new SubjectsModel)->insertOrUpdate($data);
        }
        return false;
    }
    
    
    /**
     * 检测唯一性
     */
    public static function uniqueData($key,$val){
        if($key && $val){
            return (new SubjectsModel)->getUniqueData($key,$val);
        }
        return false;
    }
    
    
    
    // 删除学生操作
    public static function DelOne($id){
        if($id){
            return (new SubjectsModel())->delOne($id);
        }
        return false;
    }
    
    
     /**
     * 编辑数据
     */
    public static function updateData($data,$id){
        if(is_array($data) && $data && is_array($id)){
            return (new SubjectsModel())->insertOrUpdate($data,$id);
        }
        return false;
    }
    
    /**
     * 获取单条记录
     */
    public static function getOneData($id){
        return (new SubjectsModel)->getUniqueData($id);
    }
    
    
   
    
    
}