<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:导师服务
 * 
 */
namespace App\Service;
use App\Model\TasksModel;

class TasksService extends CommonService{
    
    
    // 获取全部数据
    public static function getTasks(){
        return (new TasksModel())->getTasks();
    }


    /**
     * 插入数据
     */
    public static function insertData($data){
        if(is_array($data) && $data){
            return (new TasksModel)->insertOrUpdate($data);
        }
        return false;
    }


     /**
     * 编辑数据
     */
    public static function updateData($data,$id){
        if(is_array($data) && $data && is_array($id)){
            return (new TasksModel())->insertOrUpdate($data,$id);
        }
        return false;
    }

}