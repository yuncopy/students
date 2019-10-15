<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:导师服务
 * 
 */
namespace App\Service;
use App\Model\SemesterModel;

class SemesterService extends CommonService{
    
    
    /**
     * 填充编号
     */
    public static function autoNumber($name='id'){
        return (new SemesterModel())->autoNumber($name);
    }
    
    /**
     * 获取数据
     */
    public static function getList($where){
        
        $semesters = new SemesterModel();
        $dataTable = $semesters->getList($where);
        $dataTable->setFormatRowFunction(function ($row){
            return [
                'id'            =>  $row['id'],
                'name'          =>  $row['name'],
                'sort'          =>  $row['sort'],
                'create_time'   =>  $row['create_time'],
                'status'        =>  $row['status'],
                'action'        =>  $row['id']
            ];
        });
       return $dataTable->make();
    }
    
    /**
     * 获取全部数据
     */
    public static function getAllsData(){
       return (new SemesterModel)->getAllsData();
    }

    /**
     * 插入数据
     */
    public static function insertData($data){
        if(is_array($data) && $data){
            return (new SemesterModel)->insertOrUpdate($data);
        }
        return false;
    }
    
    
    /**
     * 检测唯一性
     */
    public static function uniqueData($key,$val){
        if($key && $val){
            return (new SemesterModel)->getUniqueData($key,$val);
        }
        return false;
    }
    
    
    
    // 删除学生操作
    public static function DelOne($id){
        if($id){
            return (new SemesterModel())->delOne($id);
        }
        return false;
    }
    
    
     /**
     * 编辑数据
     */
    public static function updateData($data,$id){
        if(is_array($data) && $data && is_array($id)){
            return (new SemesterModel())->insertOrUpdate($data,$id);
        }
        return false;
    }
    
    /**
     * 获取单条记录
     */
    public static function getOneData($id){
        return (new SemesterModel)->getUniqueData($id);
    }
    
    
    //降序
    public static function descData($id){
        return (new SemesterModel)->descData($id,'sort');
    }
    
    
    //升序
    public static function ascData($id){
        return (new SemesterModel)->ascData($id,'sort');
    }
    
}