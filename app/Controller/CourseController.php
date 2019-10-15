<?php

/**
 * Angela
 * 课程安排控制器
 * 2017年11月21日17:54:32
 * 
 */

namespace App\Controller;
use App\Service\CourseService;
use App\Service\TeacherService;
use App\Service\CategoryService;
use App\Service\SubjectsService;
use App\Service\SemesterService;
use App\Library\Validate;


class CourseController extends BaseController {
    
    // 添加课程安排
    public function addData($id){
        if(IsPost()){
             // 数据验证
            $data = $this->getPostDatas('POST');
            $room_id = $this->getPostDatas('POST','room_id',false,'trim');
            $res = Validate::make([
                [ 'teacher_id','required|isnull', '教师必填', Validate::MUST_VALIDATE ],
                [ 'semester_id','required|isnull', '学期必填', Validate::MUST_VALIDATE ],
                [ 'subject_id','required|isnull', '科目必填', Validate::MUST_VALIDATE ],
                //[ 'week_id','required|isnull', '星期必填', Validate::MUST_VALIDATE ],
                //[ 'interval_id','required|isnull', '时间段必填', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            $cont=null;
            $code = 500400;   // 失败
            try{
               if(!$room_id){ unset($data['room_id']); }  // 销毁
               
               $insertId = CourseService::insertData($data);
               
            } catch ( \Illuminate\Database\QueryException $e){
                
                $message = $e->getMessage(); 
                //$cont['message'] = $message;
                $unique_teacher = 'semester_teacher_class_subject_week_interval';  // 索引名称， 保证同一老师 上课唯一性 // 整条数据是否已存在
                $pos_teacher = strpos($message, $unique_teacher);
                if ($pos_teacher !== false)  $code = 500900;
                
                /*
                //semester_week_interval_room	semester_id, week_id, interval_id, room_id  Unique  使用数据库检测
                $unique_room = 'semester_week_interval_room'; // 索引名称， 学期-教室-星期-时段 是否已存在 
                $pos_room = strpos($message, $unique_room);
                if($pos_room !== false) $code = 500101;*/
                $cont['code'] = $e->getCode();
                $insertId = false;
                
            }
            //处理结果
            if($insertId){$code = 500102; } //成功
            return $this->returnData($code,$cont); 
            
        }else{
            $teacher = TeacherService::getOneData($id);  // 教师
            $subjects = SubjectsService::getSubjects(); // 科目
            $semesters = SemesterService::getAllsData();
            $cates = CategoryService::getCates();  // 相关
            $this->assign([
                'teacher'   =>  $teacher,
                'subjects'  =>  $subjects,
                'weeks'     =>  $cates['weeks'],
                'rooms'     =>  make_option_tree_for_select($cates['rooms']),
                'intervals' =>  $cates['intervals'],
                'semesters' =>  $semesters
            ]);
            $this->display('courseadd.html');
        }
    }
    

    /**
     * 2017年11月25日11:11:30
     * Angela
     * 加载页面
     */
    public function index() {
        
        $teachers = TeacherService::getAllsData();
        $subjects = SubjectsService::getSubjects();
        $semesters = SemesterService::getAllsData();
        $cates = CategoryService::getCates();  // 相关
        $this->assign([
            'semesters' => $semesters,
            'teachers'   =>  $teachers,
            'subjects'  =>  $subjects,
            'weeks'     =>  $cates['weeks'],
            'rooms'     =>  make_option_tree_for_select($cates['rooms']),
            'intervals' =>  $cates['intervals']
        ]);
        
        $this->display('course.html');
    }
    
    /**
     * 2018年1月4日14:19:56
     * Angela
     * 获取课程数据
     */
    public function  getdata(){
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
       
        $data =  CourseService::getList($where);
        echo json_encode($data);
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
    
    
   
    
   //删除操作
   public function delData($id){
        try{
            $data = CourseService::DelOne($id);  
        } catch ( \Illuminate\Database\QueryException $e){
            $data = false;
        }
        if($data){
           return $this->returnData(500500); 
        }
   }
   
   
    // 更新操作
    public function updateData($id){
        if(IsPost()){
            // 数据验证
            $data = $this->getPostDatas('POST');
            $id = $this->getPostDatas('POST','id',false,'htmlspecialchars');
            $room_id = $this->getPostDatas('POST','room_id',false,'trim');
            
            $res = Validate::make([
                [ 'id', 'regexp:/[0-9]+$/', '编号格式错误', Validate::MUST_VALIDATE ],
                [ 'teacher_id','required|isnull', '教师必填', Validate::MUST_VALIDATE ],
                [ 'semester_id','required|isnull', '学期必填', Validate::MUST_VALIDATE ],
                [ 'subject_id','required|isnull', '科目必填', Validate::MUST_VALIDATE ],
                //[ 'week_id','required|isnull', '星期必填', Validate::MUST_VALIDATE ],
                //[ 'interval_id','required|isnull', '时间段必填', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            $cont=null;
            $code = 500600;   // 失败
            try{
                if(!$room_id){ unset($data['room_id']);}  // 销毁
                unset($data['id']);// 销毁主键
                
                $updateId = CourseService::updateData($data,['id'=>$id]); // 执行修改
               
            } catch ( \Illuminate\Database\QueryException $e){
                
                $message = $e->getMessage(); 
                //$cont['message'] = $message;
                $unique_teacher = 'semester_teacher_class_subject_week_interval';  // 索引名称， 保证同一老师 上课唯一性 // 整条数据是否已存在
                $pos_teacher = strpos($message, $unique_teacher);
                if ($pos_teacher !== false)  $code = 500900;
                /*
                //semester_week_interval_room	semester_id, week_id, interval_id, room_id  Unique  使用数据库检测
                $unique_room = 'semester_week_interval_room'; // 索引名称， 学期-教室-星期-时段 是否已存在
                $pos_room = strpos($message, $unique_room);
                if($pos_room !== false) $code = 500101;
                 */
                $cont['code'] = $e->getCode();
                $updateId = false;
                
            }
            //处理结果
            if($updateId){$code = 500300; } //成功
            return  $this->returnData($code,$cont); 
            
        }else{
            $course = CourseService::getOneData($id);
            $teachers = TeacherService::getAllsData();  // 教师
            $subjects = SubjectsService::getSubjects(); // 科目
            $semesters = SemesterService::getAllsData();
            $cates = CategoryService::getCates();  // 相关
            $this->assign([
                'course'    =>  $course,
                'teachers'  =>  $teachers,
                'subjects'  =>  $subjects,
                'weeks'     =>  $cates['weeks'],
                'rooms'     =>  make_option_tree_for_select($cates['rooms']),
                'intervals' =>  $cates['intervals'],
                'semesters' =>  $semesters
            ]);
            $this->display('courseedit.html');
        }  
    }
    
    
    // 注销和启用
    public function cancelStart($id,$status){
        $message=null;
        try{
            $dataStatus = [ 2=>1,1=>2];
            $dataUpdate=[
                'status'=>$dataStatus[$status]
            ];
            $updateRes =  CourseService::updateData($dataUpdate,['id'=>$id]);
        } catch ( \Illuminate\Database\QueryException $e){
            $message = $e->getMessage(); 
            $updateRes = false;
        }
        //处理结果
        $code = 500600;   // 失败
        if($updateRes){
            $code = 500300; //成功
        }
        return $this->returnData($code,$message); 
    }
    
    
    
    
 
    
   
}
