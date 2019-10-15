<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:课程安排服务
 * 
 */
namespace App\Service;
use App\Model\CoursesModel;
use App\Model\ElectivesModel;
use App\Model\StudentsModel;
use App\Model\SemesterModel;
use App\Model\TeachersModel;
use App\Model\SubjectsModel;

use App\Service\CategoryService;
use App\Library\Session;

class ElectiveService extends CommonService{
    
    public static $courseExportKey = 'electiveExport';
   
    /**
     * 获取选课数据
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
        
        $Semesters = new SemesterModel();
        $Teachers = new TeachersModel();
        $Subjects = new SubjectsModel();  
        
        $dataTable->setFormatRowFunction(function ($row) use($cates,$Semesters,$Teachers,$Subjects){
            
            // 处理数据
            $semester = $Semesters->getUniqueData($row['semester_id']);
            $teacher = $Teachers->getUniqueData($row['teacher_id']);
            $subject = $Subjects->getUniqueData($row['subject_id']);
            $weeks = array_column($cates['weeks'], 'name','id');
           
            $intervals = array_column($cates['intervals'], 'name','id');
            $rooms = array_column($cates['rooms'], 'name','id');
            
            $subject_start_time = $subject['start_time'];
            $subject_end_time = $subject['end_time'];
            $start_time = strtotime($subject_start_time.' 00:00:00');
            $end_time = strtotime($subject_end_time.' 23:59:59');
            $current_time = time();
            if( ($start_time < $current_time) && ($current_time <  $end_time)){
                $subject_status = 1;
            }else{
                $subject_status = 2;
            }
            
            return [
                'id'            =>  $row['id'],
                'semester_id'   =>  $semester['name'],
                'teacher_id'    =>  $teacher['name'],
                'subject_id'    =>  $subject['name'],
                'subject_credit'=>  $subject['credit'],
                'week_id'       =>  isset($weeks[$row['week_id']]) ? $weeks[$row['week_id']] : 'N/A',
                'interval_id'   =>  isset($intervals[$row['interval_id']]) ? $intervals[$row['interval_id']] : 'N/A',
                'room_id'       =>  isset($rooms[$row['room_id']]) ? $rooms[$row['room_id']] : 'N/A',
                'subject_status'=>  $subject_status,   // 根据课程时间判断是否可选   1 表示可选  2 表示已截止
                'start_end_time'=>  $subject_start_time.' - '.$subject_end_time,  // 选课时间
                'action'        =>  $row['id']
            ];
        });
        return $dataTable->make();
    }
    
    
    
    // 获取已选课程列表
    public static function selectList($searchdata){
        
        $elective = new ElectivesModel();
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
        }
        
        $dataTable = $elective->getList($where);
        $cates = CategoryService::getCates();
        
    
 
        $Semesters = new SemesterModel();
        $Teachers = new TeachersModel();
        $Subjects = new SubjectsModel();    
        $Students = new StudentsModel();
        $courses = new CoursesModel();
        
        
        $dataTable->setFormatRowFunction(function ($row) use($cates,$Semesters,$Teachers,$Subjects,$Students,$courses){
            
            // 处理数据
            $semester = $Semesters->getUniqueData($row['semester_id']);
            $teacher = $Teachers->getUniqueData($row['teacher_id']);
            $Students = $Students->getUniqueData($row['student_id']);
            $course =  $courses->getUniqueData($row['course_id']);
       
            $Students_Info = "[".$Students['number']."] ".$Students['username'];
            $subject = $Subjects->getUniqueData($row['subject_id']);
            $weeks = array_column($cates['weeks'], 'name','id');
            $intervals = array_column($cates['intervals'], 'name','id');
            $rooms = array_column($cates['rooms'], 'name','id');

            $subject_start_time = $subject['start_time'];
            $subject_end_time = $subject['end_time'];

            $rows = [
                'id'            =>  $row['id'],
                'semester_id'   =>  $semester['name'],
                'student_name'  =>  $Students_Info,
                'course_id'     =>  $row['course_id'],
                'student_id'    =>  $row['student_id'],
                'teacher_id'    =>  $teacher['name'],
                'subject_id'    =>  $subject['name'],
                'is_fraction'   =>  $row['is_fraction'],
                'subject_credit'=>  $subject['credit'],
                'week_id'       =>  isset($weeks[$course['week_id']]) ? $weeks[$course['week_id']] : 'N/A',
                'interval_id'   =>  isset($intervals[$course['interval_id']]) ? $intervals[$course['interval_id']]: 'N/A',
                'room_id'       =>  isset($rooms[$course['room_id']]) ? $rooms[$course['room_id']]: 'N/A',
                'start_end_time'=>  $subject_start_time.' - '.$subject_end_time,  // 选课时间
                'action'        =>  $row['id']
            ];
            $rows['rows'] = json_encode($rows);
            return $rows;
        });

        return $dataTable->make();
    }

    // 获取单条记录
    public static function getOneElectives($elective_id){
        if($elective_id){
            $model = new ElectivesModel();
            $Semesters = new SemesterModel();
            $Teachers = new TeachersModel();
            $Subjects = new SubjectsModel();
            $Students = new StudentsModel();

            $row = $model->getOneData($elective_id);
            if($row){

                $semester = $Semesters->getUniqueData($row['semester_id']);
                $teacher = $Teachers->getUniqueData($row['teacher_id']);
                $Students = $Students->getUniqueData($row['student_id']);
                $Subject = $Subjects->getUniqueData($row['subject_id']);


                $row_data =[
                    'elective_id'   => $elective_id,
                    'semester_id'   => $semester['id'],
                    'semester_name' => $semester['name'],
                    'teacher_id'    => $teacher['name'],
                    'teacher_name'  => $teacher['name'],
                    'student_number'=> $Students['number'],
                    'student_name'  => $Students['username'],
                    'subject_id'    => $Subject['name'],
                    'subject_name'  => $Subject['id'],
                    'subject_credit'=> $Subject['credit'],
                    'start_end_time'=> $Subject['start_time'].'~'.$Subject['end_time']  // 选课时间
                ];
                return $row_data;
            }
        }
        return false;
    }

    
    public static function getSelecteds($where){
        return (new ElectivesModel)->getSelecteds($where);
    }


    //获取未选课学生ID
    public static function notGetSelect($semester_id){
        
        // 某一个学期学生数据
        $selecteds = self::getSelecteds([
            "semester_id"=>$semester_id
        ]);  // 已选课学生
        $studentModel = new StudentsModel();
        $students = $studentModel->getStudents([
            "semester_id"=>['semester_id','=',$semester_id]
        ]);  // 某一个学期全部学生
        
        //取差集合
        $selected_student_id = array_column($selecteds,'student_id');
        $student_ids = array_column($students,'id');
        $student_id_diff = array_diff ( $student_ids ,  $selected_student_id );
        
        // 处理学生信息
        $student_temp = [];
        if($student_id_diff){
            $student_temp = array_map(function($student_id) use($studentModel){
               return  $studentModel->getUniqueData($student_id);
            }, $student_id_diff);
        }
        return  $student_temp;
    }

    


    /**
     * 插入数据
     */
    public static function insertData($data){
        if(is_array($data) && $data){
            return (new ElectivesModel)->insertOrUpdate($data);
        }
        return false;
    }
    
    
    /**
     * 插入数据
     */
    public static function insertDatas($data){
        if(is_array($data) && $data){
            return (new ElectivesModel)->insert($data);
        }
        return false;
    }
    
    
    /**
     * 检测唯一性
     */
    public static function uniqueData($key,$val){
        if($key && $val){
            return (new ElectivesModel)->getUniqueData($key,$val);
        }
        return false;
    }
    
    
    
    // 删除学生操作
    public static function DelOne($id){
        if($id){
            return (new ElectivesModel())->delOne($id);
        }
        return false;
    }
    
    
     /**
     * 编辑数据
     */
    public static function updateData($data,$id){
        if(is_array($data) && $data && is_array($id)){
            return (new ElectivesModel())->insertOrUpdate($data,$id);
        }
        return false;
    }
    
    /**
     * 获取单条记录
     */
    public static function getOneData($id){
        return (new ElectivesModel)->getUniqueData($id);
    }
    
    
    
    
}