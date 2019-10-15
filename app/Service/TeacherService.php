<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:导师服务
 * 
 */
namespace App\Service;
use App\Model\TeachersModel;
use App\Model\TutorsModel;

class TeacherService extends CommonService{
    
    /**
     * 填充编号
     */
    public static function autoNumber(){
        return (new TeachersModel())->autoNumber();
    }

    /**
     * 获取数据
     */
    public static function getList($where){
        
        $teachers = new TeachersModel();
        $dataTable = $teachers->getList($where);
        $tutorClass = (new TutorsModel())->tutorClass();
        $tutorIdName = array_column($tutorClass,'name', 'id');
        $dataTable->setFormatRowFunction(function ($row) use($tutorIdName) {
            return [
                'id'            =>  $row['id'],
                'number'        =>  $row['number'],
                'name'          =>  $row['name'],
                'tutor_class_id'=>  $row['tutor_class_id'],
                'tutor_class_name'=>  $tutorIdName[$row['tutor_class_id']],
                'email'         =>  $row['email'],
                'status'         =>  $row['status'],
                'action'        =>  $row['id']
            ];
        });
       return $dataTable->make();
    }
    
    
    /**
     *获取全部信息
     */
    public static function getAllsData(){
        return (new TeachersModel)->getAllsData();
    }
    

    /**
     * 插入数据
     */
    public static function insertData($data){
        if(is_array($data) && $data){
            return (new TeachersModel)->insertOrUpdate($data);
        }
        return false;
    }
    
    
    /**
     * 检测唯一性
     */
    public static function uniqueData($key,$val){
        if($key && $val){
            return (new TeachersModel)->getUniqueData($key,$val);
        }
        return false;
    }
    
    
    
    // 删除学生操作
    public static function DelOne($id){
        if($id){
            return (new TeachersModel())->delOne($id);
        }
        return false;
    }
    
    
     /**
     * 编辑数据
     */
    public static function updateData($data,$id){
        if(is_array($data) && $data && is_array($id)){
            return (new TeachersModel())->insertOrUpdate($data,$id);
        }
        return false;
    }
    
    /**
     * 获取单条记录
     */
    public static function getOneData($id){
        return (new TeachersModel)->getUniqueData($id);
    }
    
    
    
    
}