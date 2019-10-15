<?php

/**
 * Angela
 * 课程安排控制器
 * 2017年11月21日17:54:32
 * 
 */

namespace App\Controller;
use App\Service\ElectiveService;
use App\Service\TeacherService;
use App\Service\CategoryService;
use App\Service\SubjectsService;
use App\Service\SemesterService;
use App\Service\CourseService;
use App\Service\RoleService;
use App\Service\DefensesService;
use App\Library\Validate;
use App\Library\Crypt;
use App\Library\Logs;
class ElectiveController extends BaseController {
    
    
     /**
     * 2017年11月25日11:11:30
     * Angela
     * 加载页面
      * @param $id int 学生ID
      * @return void
     */
    public function index($id) {
        
        $teachers = TeacherService::getAllsData();
        $subjects = SubjectsService::getSubjects();
        $semesters = SemesterService::getAllsData();
        $weeks = CategoryService::getCates('week');  // 相关
        
        // 管理员(放开课程过期时间，全部都可以选择)
        $current_role = RoleService::currentRole();
        $selected = 0;
        if(in_array($current_role, [1,5]) && empty($id)){  // 是管理员并且尚未分配学生ID
            $selected = 1;
        }
        // 检测学生是否毕业 (答辩是否通过)
        $stu_result = null;
        if($id){
            if($this->checkDefenses($id)){  // 1 表示通过
                $stu_result = 1;
            }
            $student_id =  Crypt::encrypt($id);
        }
        
        $this->assign([
            'semesters'     =>  $semesters,
            'teachers'      =>  $teachers,
            'subjects'      =>  $subjects,
            'weeks'         =>  $weeks,
            'student_id'    =>  !empty($id) ? $student_id : 0,
            'selected'      =>  $selected,
            'stu_result'    =>  $stu_result
        ]);
        $this->assign([''=>!empty($id) ? $id : 0]);
        $this->display('elective.html');
    }
    
      /**
     * 2018年1月4日14:19:56
     * Angela
     * 获取课程数据
     */
    public function  getdata(){
        $where = null;
        $searchdata = $this->getPostDatas('POST','searchdata',false,'stripslashes');
        if($searchdata){
           $where = $searchdata;
        }
        $where.= is_null($where) ? 'status=1' : '&status=1';  // 只选可选课程
        $data =  ElectiveService::getList($where);
        echo json_encode($data);
    }
    
    /**
     * 查询已选列表
     */
    public function select(){
        $subjects = SubjectsService::getSubjects(); // 科目
        $semesters = SemesterService::getAllsData();
        $this->assign([
            'semesters' => $semesters,
            'subjects'  => $subjects
        ]);
        $this->display('electiveselect.html');
    }
    
    /**
     * 获取数据
     */
    public function selectData(){
        $where = null;
        $searchdata = $this->getPostDatas('POST','searchdata',false,'stripslashes');
        $teacher_name = $this->getPostDatas('POST','teacher_name',false,'stripslashes'); // 查询教师


        if($searchdata){
           $where = $searchdata;
        }
         // 搜索教师，单独搜索
        if($teacher_name && !$searchdata){
            $teacher = TeacherService::uniqueData('name', $teacher_name);
            $teacher_id = $teacher == false ? 'A1' : $teacher['id'];
            $where.= 'teacher_id='.$teacher_id;
        }
        
        $data =  ElectiveService::selectList($where);
        echo json_encode($data);
    }
    
    /**
     * 获取未选学生
     */
    public function notSelectedData(){
        // 查询未选课的学生ID
       $semester_id = $this->getPostDatas('POST','semester_id',false,'htmlspecialchars'); 
       $datas = ElectiveService::notGetSelect($semester_id);
       $datas = array_map(function($var){
           $var['id'] = Crypt::encrypt($var['id']);  // 加密
           return $var;
       }, $datas);
       return $this->returnData(200,$datas);
    }
    
    // 检测是否通过毕业（答辩通过）
    public function checkDefenses($student_id){
        $student_Defense =DefensesService::uniqueData('student_id',$student_id);  // 最新一条纪录
        if($student_Defense['result'] == 1){  // 1 表示通过
            return true;
        }
    }

    // =========选课==============
    public function addData(){
        if(IsPost()){
            
            $data = $this->getPostDatas('POST');
            $post_student_id = $this->getPostDatas('POST','student_id',false,'htmlspecialchars'); 
            $student_id = Crypt::decrypt($post_student_id);
            if(!$student_id){  // 检查是否为用户擅自修改信息
                return $this->returnData(600100); 
            }
            
            // 检测学生是否毕业 (答辩是否通过)
            if($this->checkDefenses($student_id)){  // 1 表示通过
                return $this->returnData(600101); 
            }
            
            $res = Validate::make([  // 数据验证
                [ 'course_id','required|isnull', '学期必填', Validate::MUST_VALIDATE ],
                [ 'student_id','required|isnull', '学生必选', Validate::MUST_VALIDATE ],
            ] ,$data);
           
            $user_id = RoleService::currentUID();
            $course_ids = $this->getPostDatas('POST','course_id',false); 
            $insertData = [];
            if($course_ids){
                 foreach ($course_ids as $value){
                    $course = CourseService::getOneData($value);
                    $elective_temp =[
                        'course_id'     =>  $value,
                        'student_id'    =>  $student_id,
                        'semester_id'   =>  $course['semester_id'],
                        'teacher_id'    =>  $course['teacher_id'],
                        'subject_id'    =>  $course['subject_id']
                    ];
                    $insertData[] = $elective_temp;
                }
            }
            
            
            // 处理数据入库
            $cont=null;
            $code = 600400;   // 失败
            try{
                
                $insertId = ElectiveService::insertDatas($insertData);
                
                $insertData['user_id'] = $user_id;  // 操作者
                Logs::info(json_encode($insertData)); // 纪录日志
                
            } catch ( \Illuminate\Database\QueryException $e){
                $message = $e->getMessage(); 
                //$cont['message'] = $message;
                $unique_course = 'idx_student_course';  // 索引名称， 保证同一学生只能在同一学期选一门课程
                $pos_course = strpos($message, $unique_course);
                if ($pos_course !== false)  $code = 600900;
                $cont['code'] = $e->getCode();
                $insertId = false;
            }
            //处理结果
            if($insertId){$code = 600200; } //成功
            return $this->returnData($code,$cont); 
        }
    }
    

    
       
    //删除操作
    public function delData($id){
        try{
            $data = ElectiveService::DelOne($id);  
        } catch ( \Illuminate\Database\QueryException $e){
            $data = false;
        }
        if($data){
           return $this->returnData(500500); 
        }
   }
    
    
    
    /**
     * 2018年1月4日16:10:45
     * Angela
     * 检测唯一性
     */
    public function uniqueName(){
       $name = $this->getPostDatas('POST','name',false,'htmlspecialchars');
       $value = $this->getPostDatas('POST','value',false,'htmlspecialchars');
       $res = CourseService::uniqueData($name,$value);
       if($res){
            echo json_encode(false); //json_encode(false) 表示有重复的号码;
       }else{
            echo json_encode(true); //必须是json_encode(true)
       }
    }
    
    
   
}
