<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:导师服务
 * 
 */
namespace App\Service;
use App\Model\TutorsModel;
use App\Model\TutorClasssModel;
use App\Library\Session;
use App\Library\Redis;

class TutorService extends CommonService{
    
    public static $tutorsKey = 'tutorsKey';
    /**
     * 获取数据
     */
    public static function getList($where){
        
        $Tutors = new TutorsModel();
        $dataTable = $Tutors->getList($where);
        $tutorClass = $Tutors->tutorClass();
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
     * 获取全部数据
     */
    public static function getTutors(){
        return (new TutorsModel)->getTutors();
    }
    
    /**
     * 设置缓存
     */
     public static function cacheTutors($data=null){
        $tutorsKey = self::$tutorsKey;
        $tutorsKeyRedis = $tutorsKey.'s';
        $Redis = new Redis;
        if(is_null($data)){
            $res =  Session::get($tutorsKey);  //获取数据
            if(!$res){
                $res = json_decode($Redis->get($tutorsKeyRedis),true);  //获取数据
            }
            return $res;
        }else{
            Session::set($tutorsKey , $data);  // 缓存数据
            $Redis->delete($tutorsKeyRedis);
            $Redis->setex($tutorsKeyRedis, 3600,json_encode($data));  // 缓存数据
            return $data;
        }
    }
    /**
     * 获取导师类别
     */
    public static function tutorClass(){
        return (new TutorsModel)->tutorClass();
    }
    

    /**
     * 插入数据
     */
    public static function insertData($data){
        if(is_array($data) && $data){
            return (new TutorsModel)->insertOrUpdate($data);
        }
        return false;
    }
    
    
    /**
     * 检测唯一性
     */
    public static function uniqueData($key,$val){
        if($key && $val){
            return (new TutorsModel)->getUniqueData($key,$val);
        }
        return false;
    }
    
    
    
    // 删除学生操作
    public static function TutorDelOne($id){
        if($id){
            return (new TutorsModel())->delTutor($id);
        }
        return false;
    }
    
    
     /**
     * 编辑数据
     */
    public static function updateData($data,$id){
        if(is_array($data) && $data && is_array($id)){
            return (new TutorsModel())->insertOrUpdate($data,$id);
        }
        return false;
    }
    
    /**
     * 获取单条记录
     */
    public static function getOneData($id){
        return (new StudentsModel)->getUniqueData($id);
    }
    
    
    
    //=========================类别信息操作===========================
    
     // 类别信息
    public static function getClassList($where){
        
        $TutorsClass = new TutorClasssModel();
        $dataTable = $TutorsClass->getList($where);
        $dataTable->setFormatRowFunction(function ($row){
            return [
                'id'            =>  $row['id'],
                'name'          =>  $row['name'],
                'create_time'   =>  $row['create_time'],
                'action'        =>  $row['id']
            ];
        });
       return $dataTable->make();
    }
    
    //类别名称唯一
    public static function uniqueClassName($key,$val){
        if($key && $val){
            return (new TutorClasssModel)->getUniqueData($key,$val);
        }
        return false;
    }
    
    // 类别编号
    public static function autoClassNumber(){
        
        return (new TutorClasssModel())->autoClassNumber();
        
    }
    
    // 添加
    public static function insertClassData($data){
        if(is_array($data) && $data){
            return (new TutorClasssModel)->insertOrUpdate($data);
        }
        return false;
    }
    
    //修改
    public static function updateClassData($data,$id){
        if(is_array($data) && $data && is_array($id)){
            return (new TutorClasssModel)->insertOrUpdate($data,$id);
        }
        return false;
    }
    
     //删除
    public static function delClassData($id){
        if($id){
             return (new TutorClasssModel)->delOne($id);
        }
        return false;
    }

    
}