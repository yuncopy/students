<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:课程安排服务
 * 
 */
namespace App\Service;
use App\Model\CoursesModel;
use App\Service\TeacherService;
use App\Service\CategoryService;
use App\Service\SubjectsService;
use App\Service\SemesterService;
use App\Library\Session;

class CourseService extends CommonService{
    
    public static $courseExportKey = 'courseExport';
   
    /**
     * 获取数据
     */
    public static function getList($searchdata){
        
        $course = new CoursesModel();
        $where = $output = [];
        if($searchdata){
            parse_str ( $searchdata ,  $output );
            foreach ($output as $key => $value){
                if($value){  // enrolment
                    if(is_array($value)){
                        $start = $value[0];
                        $end = $value[1];
                        if(strlen($start) > 1) $where[] = [$key,'>=',$start];
                        if( strlen($end) > 1 ) $where[] = [$key,'<=',$end];
                    }else{
                        $where[] = [$key,'=',$value];
                    }
                }
            }
            // 使用SESSION 缓存搜索条件，方便使用再导出数据查询
            Session::set(self::$courseExportKey , $where);
        }
        
        $dataTable = $course->getList($where);
        
        $cates = CategoryService::getCates();
        
        $dataTable->setFormatRowFunction(function ($row) use($cates){
            
            // 处理数据
            $semester = SemesterService::getOneData($row['semester_id']);
            $teacher = TeacherService::getOneData($row['teacher_id']);
            $subject = SubjectsService::getOneData($row['subject_id']);
            $weeks = array_column($cates['weeks'], 'name','id');
            $intervals = array_column($cates['intervals'], 'name','id');
            $rooms = array_column($cates['rooms'], 'name','id');
            
            return [
                'id'            =>  $row['id'],
                'semester_id'   =>  $semester['name'],
                'teacher_id'    =>  $teacher['name'],
                'subject_name'  =>  $subject['name'],
                'subject_id'    =>  $row['subject_id'],
                'subject_credit'=>  $subject['credit'],
                'week_id'       =>  isset($weeks[$row['week_id']]) ? $weeks[$row['week_id']] : 'N/A',
                'interval_id'   =>  isset($intervals[$row['interval_id']]) ? $intervals[$row['interval_id']] : 'N/A',
                'room_id'       =>  isset($rooms[$row['room_id']]) ? $rooms[$row['room_id']] : 'N/A',
                'status'        =>  $row['status'],
                'create_time'   =>  date('Y-m-d', strtotime($row['create_time'])),
                'action'        =>  $row['id']
            ];
        });
        return $dataTable->make();
    }
    
    
    // 获取数据
    public static function getWhereData($data){
        return (new CoursesModel)->getWhereData($data);
    }

    /**
     * 插入数据
     */
    public static function insertData($data){
        if(is_array($data) && $data){
            return (new CoursesModel)->insertOrUpdate($data);
        }
        return false;
    }
    
    
    /**
     * 检测唯一性
     */
    public static function uniqueData($key,$val){
        if($key && $val){
            return (new CoursesModel)->getUniqueData($key,$val);
        }
        return false;
    }
    
    
    
    // 删除学生操作
    public static function DelOne($id){
        if($id){
            return (new CoursesModel())->delOne($id);
        }
        return false;
    }
    
    
     /**
     * 编辑数据
     */
    public static function updateData($data,$id){
        if(is_array($data) && $data && is_array($id)){
            return (new CoursesModel())->insertOrUpdate($data,$id);
        }
        return false;
    }
    
    /**
     * 获取单条记录
     */
    public static function getOneData($id){
        return (new CoursesModel)->getUniqueData($id);
    }
    
    
    
    
}