<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:导师服务
 * 
 */
namespace App\Service;
use App\Model\UserRelationsModel;

class UserRelationService extends CommonService{
    
    /**
     * 插入数据
     */
    public static function insertData($data){
        if(is_array($data) && $data){
            return (new UserRelationsModel)->insertOrUpdate($data);
        }
        return false;
    }
    
    /**
     * 获取表名
     */
    public static function getTaleName(){
        return (new UserRelationsModel)->getTable();
    }

    

    // 删除操作
    public static function delData($where=[]){
        if(is_array($where) && $where){
            $one = self::getOneData($where);
            $res = (new UserRelationsModel())->delRelation($where);
            if($res){
                return $one;
            }
        }
        return false;
    }
    
    
     /**
     * 编辑数据
     */
    public static function updateData($data,$id){
        if(is_array($data) && $data && is_array($id)){
            return (new UserRelationsModel())->insertOrUpdate($data,$id);
        }
        return false;
    }
    
    /**
     * 获取单条记录
     */
    public static function getOneData($where){
        return (new UserRelationsModel)->getOneRelation($where);
    }
    
    
    
    
}