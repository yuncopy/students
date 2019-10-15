<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:AOC界面处理
 * 
 */
namespace App\Service;
use App\Model\UsersModel;
use App\Service\RoleService;

class UserService extends CommonService{
    
    /**
     * 获取表名
     */
    public static function getTableName(){
        return (new UsersModel)->getTable();
    }
    
    /**
     * 获取用户信息
     */
    public static function getInUsers($data=[]){
        return (new UsersModel)->getInUsers($data);
    }

    /**
     * 获取数据
     */
    public static function getList($where){
        
        $users = new UsersModel();
        $dataTable = $users->getList($where);
        $dataTable->setFormatRowFunction(function ($row){
            $Role = RoleService::customRole();
            $RolName = array_flip($Role);
            return [
                'id'            =>  $row['id'],
                'username'      =>  $row['username'],
                'email'         =>  $row['email'],
                'role_id'       =>  $RolName[$row['role_id']],
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
            return (new UsersModel)->insertOrUpdate($data);
        }
        return false;
    }
    
    /**
     * 删除数据
     */
    public static function delData($id){
        if($id){
            return (new UsersModel)->delOneUser($id);
        }
        return false;
    }
    
    /*
     * 重置密码
     */
    public static function repassData($id){
        if($id){
            return (new UsersModel)->reSetPasss($id);
        }
        return false;
    }
    
    /**
     * 操作全部重置密码  
     */
    public static function repassDataAll(){
        $all = (new UsersModel)->get()->toArray();
        $all_data = [];
        foreach ($all as $k=>$v){
            $id = $v['id'];
            if($id > 1){
                $username = trim($v['username']);
                $all_data[$id] = md5($username.__PASS__);
            }
        }

        $ids = implode(',', array_keys($all_data));
        $sql = "UPDATE mpa_users SET passwd = CASE id ";
        foreach ($all_data as $id => $ordinal) {
            $sql .= " WHEN {$id} THEN '{$ordinal}' ";
        }
        $sql .= "END WHERE id IN ($ids)";
        echo $sql;
    }
    
    
    
     /**
     * 修改数据
     */
    public static function updateData($data,$where){
        if(is_array($data) && $data && is_array($where) && $where){
            return (new UsersModel)->insertOrUpdate($data,$where);
        }
        return false;
    }
    
   public static function uniqueData($key,$val){
        if($key && $val){
            return (new UsersModel)->getUniqueData($key,$val);
        }
        return false;
    }
    
}