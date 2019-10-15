<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:导师服务
 * 
 */
namespace App\Service;
use App\Model\ProposalsModel;
use App\Service\StudentService;
use App\Service\SemesterService;

class ProposalsService extends CommonService{
    
    
   
    
    /**
     * 获取数据
     */
    public static function getList($where){
        
        $Proposals = new ProposalsModel();
        $dataTable = $Proposals->getList($where);
        $dataTable->setFormatRowFunction(function ($row){
            $student = StudentService::getOneData($row['student_id']);
           $semester = SemesterService::getOneData($row['semester_id']);
            return [
                'id'            =>  $row['id'],
                'start_time'    =>  $row['start_time'],
                'semester_id'   =>  $semester['name'],
                'student_id'    =>  $student['username'],
                'name'          =>  $row['name'],
                'row'           =>  json_encode($row),
                'result'        =>  $row['result'],
                'member'        =>  $row['member'],
                'remark'        =>  $row['remark'],
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
            return (new ProposalsModel)->insertOrUpdate($data);
        }
        return false;
    }
    
    
    /**
     * 检测唯一性
     */
    public static function uniqueData($key,$val){
        if($key && $val){
            return (new ProposalsModel)->getUniqueData($key,$val);
        }
        return false;
    }
    
    
    
    // 删除学生操作
    public static function DelOne($id){
        if($id){
            return (new ProposalsModel())->delOne($id);
        }
        return false;
    }
    
    
     /**
     * 编辑数据
     */
    public static function updateData($data,$id){
        if(is_array($data) && $data && is_array($id)){
            return (new ProposalsModel())->insertOrUpdate($data,$id);
        }
        return false;
    }
    
    /**
     * 获取单条记录
     */
    public static function getOneData($id){
        return (new ProposalsModel())->getUniqueData($id);
    }
    
    
  
   
    
    
}