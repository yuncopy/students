<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:导师服务
 * 
 */
namespace App\Service;
use App\Model\PermissionsModel;


class PermissionService extends CommonService{
    
    /**
     * 获取数据
     */
    public static function getList($where){
        
        $Permissions = new PermissionsModel();
        $dataTable = $Permissions->getList($where);
        $dataTable->setFormatRowFunction(function ($row){
            return [
                'id'            =>  $row['id'],
                'name'          =>  $row['name'],
                'method'        =>  $row['method'],
                'route'         =>  $row['route'],
                'status'        =>  $row['status'],
                'create_time'   =>  $row['create_time'],
                'pid'           =>  $row['pid'],
                'action'        =>  $row['id']
            ];
        });
       return $dataTable->make();
    }
    
    
    // 获取当前最大ID
    public static function getMaxId(){
        return (new PermissionsModel)->max('id');
    }
    
    
    
    /**
     * 插入数据
     */
    public static function insertData($data){
        if(is_array($data) && $data){
            return (new PermissionsModel)->insertOrUpdate($data);
        }
        return false;
    }
    
    
    /**
     * 检测唯一性
     */
    public static function uniqueData($key,$val){
        if($key && $val){
            return (new PermissionsModel())->getUniqueData($key,$val);
        }
        return false;
    }
    
    
    
    // 删除学生操作
    public static function DelOne($id){
        if($id){
            return (new PermissionsModel())->delOne($id);
        }
        return false;
    }
    
    
     /**
     * 编辑数据
     */
    public static function updateData($data,$id){
        if(is_array($data) && $data && is_array($id)){
            return (new PermissionsModel())->insertOrUpdate($data,$id);
        }
        return false;
    }
    
    /**
     * 获取单条记录
     */
    public static function getOneData($id){
        return (new PermissionsModel())->getUniqueData($id);
    }
    
    /**
     * 获取数据
     */
    public static function getDatas($ids){
        if($ids){
            return (new PermissionsModel())->getDatas($ids);
        }
    }
    
     /**
     *获取数据
     */
    public static function getWhereData($where){
        return (new PermissionsModel())->getWhereData($where);
    }
   
    
    
}