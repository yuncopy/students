<?php

/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:角色服务
 * 
 */

namespace App\Service;
use App\Service\LoginService;
use App\Model\RolesModel;

class RoleService extends CommonService {
    
    
    // ==========================数据操作==============开始=================== 
    /**
     * 获取数据
     */
    public static function getList($where){
        
        $Roles = new RolesModel();
        $dataTable = $Roles->getList($where);
        $dataTable->setFormatRowFunction(function ($row){
            return [
                'id'            =>  $row['id'],
                'show_name'     =>  $row['show_name'],
                'name'          =>  $row['name'],
                'permission_id' =>  $row['permission_id'],
                'status'        =>  $row['status'],
                'action'        =>  $row['id']
            ];
        });
       return $dataTable->make();
    }
    
    
    /**
     * 插入数据
     */
    public static function insertData($data){
        if(is_array($data) && $data){
            return (new RolesModel)->insertOrUpdate($data);
        }
        return false;
    }
    
    /**
     * 删除数据
     */
    public static function delData($id){
        if($id){
            return (new RolesModel)->delOne($id);
        }
        return false;
    }
    
    
     /**
     * 修改数据
     */
    public static function updateData($data,$where){
        if(is_array($data) && $data && is_array($where) && $where){
            return (new RolesModel)->insertOrUpdate($data,$where);
        }
        return false;
    }
    
    
    /**
     * 唯一性检查
     */
    public static function uniqueData($key,$val=null){
        if($key){
            return (new RolesModel)->getUniqueData($key,$val);
        }
    }
    
    /**
     * 获取单条纪录
     */
    public static function getOneData($id){
        return (new RolesModel)->getOne($id);
    }
    
    
    
    
    
    // ==========================数据操作==============结束===================
    
    
    /**
     * 角色定义
     */
    public static function customRole($key=null) {
        // 键名，键值保持于数据中一致
        $data = (new RolesModel)->get()->toArray();
        $roleData = array_column($data, 'id' ,'name');
        /*
        $roleData = [
            'admin'     =>  1,
            'teacher'   =>  2,
            'student'   =>  3,
            'tutor'     =>  4
        ];*/
        return isset($roleData[$key]) ? (int)$roleData[$key] : $roleData;
    }
    
    
    /*
     * 当前角色ID 
     */
    public static function currentRole(){
        return LoginService::getLoginInfo('role_id');
    }
    
    /**
     * 当前角色 UID
     */
    public static function currentUID(){
       return LoginService::getLoginInfo('uid');
    }
    
    /**
     * 删除角色权限
     */
    public static function delPermission(){
       return LoginService::delPermission();
    }

    /**
     * 获取当前角色权限
     */
    public static function getCurrentRolePermission(){
        $role = self::currentRole();
        $permission = (new RolesModel)->getOne($role);
        return !empty($permission) ? $permission['permission_id'] : false;
    }
    

}
