<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:导师服务
 * 
 */
namespace App\Service;
use App\Model\WebsiteModel;
use App\Library\Session;

class WebsiteService extends CommonService{
    
    private static  $website = 'website';   // 缓存
   
    
    /**
     * 获取数据
     */
    public static function getList($where){
        
        $teachers = new WebsiteModel();
        $dataTable = $teachers->getList($where);
        $dataTable->setFormatRowFunction(function ($row){
            return [
                'id'            =>  $row['id'],
                'name'          =>  $row['name'],
                'value'         =>  $row['value'],
                'remark'        =>  $row['remark'],
                'create_time'   =>  $row['create_time'],
                'action'        =>  $row['id']
            ];
        });
       return $dataTable->make();
    }
    
    
    // 获取全部数据
    public static function getWebsite($key){
        $keyCache = self::$website;
        $websiteInfo = Session::get($keyCache);
        $websiteData = unserialize($websiteInfo);
        if(!$websiteInfo){
            $websiteData = (new WebsiteModel())->getWebsite($key);
            Session::set($keyCache, serialize($websiteData));
        }
        return $websiteData;
    }


    /**
     * 插入数据
     */
    public static function insertData($data){
        if(is_array($data) && $data){
            return (new WebsiteModel)->insertOrUpdate($data);
        }
        return false;
    }
    
    
    /**
     * 检测唯一性
     */
    public static function uniqueData($key,$val){
        if($key && $val){
            return (new WebsiteModel)->getUniqueData($key,$val);
        }
        return false;
    }
    
    
    
    // 删除学生操作
    public static function DelOne($id){
        if($id){
            return (new WebsiteModel())->delOne($id);
        }
        return false;
    }
    
    
     /**
     * 编辑数据
     */
    public static function updateData($data,$id){
        if(is_array($data) && $data && is_array($id)){
            return (new WebsiteModel())->insertOrUpdate($data,$id);
        }
        return false;
    }
    
    /**
     * 获取单条记录
     */
    public static function getOneData($id){
        return (new WebsiteModel)->getUniqueData($id);
    }
    
    
   
    
    
}