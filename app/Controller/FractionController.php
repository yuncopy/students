<?php

/**
 * Angela
 * 学生管理控制器
 * 2017年11月21日17:54:32
 * 
 */

namespace App\Controller;
use App\Service\StudentService;
use App\Service\SubjectsService;
use App\Service\SemesterService;
use App\Service\FractionService;
use App\Service\ElectiveService;
use App\Service\CategoryService;
use App\Service\CourseService;
use App\Library\Validate;


class FractionController extends BaseController {

    /**
     * 2017年11月25日11:11:30
     * Angela
     * 加载页面
     */
    public function index() {
        
        $subjects = SubjectsService::getSubjects(); // 科目  
        $semesters = SemesterService::getAllsData();
        $grades = CategoryService::getCates('grade');  // 相关
        $this->assign([
            'semesters' => $semesters,
            'subjects'  => $subjects,
            'scores'    => $grades
        ]);
        $this->display('fraction.html');
    }
    
    
    // 学生成绩（学生自己自己查看）
    public function student(){
        $this->display('fractionstudent.html');
    }


    /**
     *
     * 批量导入学生信息
     * 使用SQL形式
     *
     */
    public function bathadd(){
        // 配置课程对应ID
        $s = [
            '1'=>'城市管理学',
            '2'=>'外国语（英语）',
            '3'=>'非营利组织管理',
            '4'=>'福利经济学',
            '5'=>'公共部门人力资源管理',
            '6'=>'公共工程管理概论',
            '7'=>'公共管理学',
            '8'=>'公共行政理论',
            '9'=>'公共经济学',
            '10'=>'公共伦理',
            '11'=>'公共危机管理',
            '12'=>'公共政策分析',
            '13'=>'公文写作',
            '14'=>'经济学研究方法',
            '15'=>'区域规划理论与实践',
            '16'=>'社会保障理论与政策',
            '17'=>'社会风险管理',
            '18'=>'社会阶层与利益集团分析',
            '19'=>'社会风险管理',
            '20'=>'社会研究方法',
            '21'=>'社会主义建设理论与实践',
            '22'=>'世界经济专题',
            '23'=>'城市管理学',
            '24'=>'宪法与行政法',
            '25'=>'英语',
            '26'=>'政治学'
        ];





    }



    /**
     * 2018年1月4日16:10:45
     * Angela
     * 录入成绩
     */
    public function  adddata($id){
        if(IsPost()){
            // 数据验证
            $data = $this->getPostDatas('POST');
            $score_input = $this->getPostDatas('POST','score',false,'stripslashes');
            $score = intval($score_input);
            if(($score < 0 ) || ($score > 100)){
               return  $this->returnData(200700); 
            }

            //检测是否已录入成绩
            /* 学生可以刷成绩
            $id = trim($data['elective_id']);
            $ret = ElectiveService::getOneData($id);
            if($ret['is_fraction'] === 1){
                return  $this->returnData(201700);
            }*/

            if(isset($data['course_id']) && !empty($data['course_id'])){  // 从已选课程中录入信息
                // 查询课程信息
                $course_id = $this->getPostDatas('POST','course_id',false,'htmlspecialchars');
                $course = CourseService::getOneData($course_id);
                $dataInsert = [
                    'student_id'    => $data['student_id'],
                    'semester_id'   => $course['semester_id'],
                    'subject_id'    => $course['subject_id'],
                    'score'         => $data['score'],
                    'comment'       => $data['comment'],
                    'elective_id'   => $data['elective_id']
                ];
                $data = $dataInsert; //重新赋值
            }
            
             
            $res = Validate::make([
                [ 'student_id', 'regexp:/[0-9]+$/', '学生ID格式错误', Validate::MUST_VALIDATE ],
                [ 'semester_id','required|isnull', '学生学期必填', Validate::MUST_VALIDATE ],
                [ 'elective_id','required|isnull', '已课ID', Validate::MUST_VALIDATE ],
                [ 'subject_id', 'required|isnull', '学生科目必选', Validate::MUST_VALIDATE ],
                [ 'score', 'required|isnull', '学生分数必选', Validate::MUST_VALIDATE ],
                //[ 'score', 'regexp:/[0-9]{1,3}$/', '学生分数格式错误', Validate::MUST_VALIDATE ],
                [ 'comment', 'required|isnull', '学生分数必选', Validate::MUST_VALIDATE ],
            ] ,$data);
            
            // 处理数据入库
            $code = 200900;   // 失败
            $cont = [];
            $capsule = $GLOBALS['capsule'];
            $capsule::beginTransaction(); // 开启事务
            try{

                $insertRes = FractionService::insertData($data); // 录入成绩
                $updateRes = ElectiveService::updateData(['is_fraction'=>1],['id'=>$id]);  // 更新状态
                if($insertRes){   // && $updateRes
                    $capsule::commit();  // 提交
                }

            } catch ( \Illuminate\Database\QueryException $e){

                $capsule::rollBack(); //回滚

                /*  允许学生重新刷分，允许多添加成绩 UNIQUE KEY `semester_student_subject_score` (`semester_id`,`student_id`,`subject_id`)
                 //alter table mpa_fractions drop index  semester_student_subject_score;
                $message = $e->getMessage();
                $unique_fraction = 'semester_student_subject_score';  // 索引名称， 保证同一学期，同一个学生，同一个科目只能只有一个成绩
                $pos_fraction = strpos($message, $unique_fraction);
                ($pos_fraction !== false) ?  $code = 200901 : false;
                $cont['code'] = $e->getCode();
                */
                $insertRes = false;
            }
            
            //处理结果
            if($insertRes){  // && $updateRes
                $code = 200800; //成功
            }
            echo $this->returnData($code,$cont); 
        }else{
            
            $student = StudentService::getOneData($id);
            // 查询学生是否进行选课
            $dataElective = ElectiveService::selectList('student_id='.$id);
            $subjects = null;
            if($dataElective['data']){
                $subjects = $dataElective['data'];
            }
            $semesters = SemesterService::getAllsData();
            $this->assign([
                'student'=>$student,
                'semesters' => $semesters,
                'subjects'  =>  $subjects,
                'elective'=> siteURL('/elective/index/'.$id.'.html'),
                'student_id'=> $id
            ]);
            $this->display('fractionadd.html'); 
        }
    }
    
    
    
      /**
     * 2018年1月4日14:19:56
     * Angela
     * 获取数据
     */
    public function  getdata(){
        $where = null;
        $searchdata = $this->getPostDatas('POST','searchdata',false,'stripslashes');
        $student_name = $this->getPostDatas('POST','student_name',false,'stripslashes'); // 查询教师
        //p($searchdata);
        if($searchdata){
           $where = $searchdata;
        }
        
        // 搜索学生名称，单独搜索
        if($student_name && !$searchdata){
            $student = StudentService::uniqueData('username', $student_name);
            $teacher_id = $student == false ? 'A1' : $student['id'];
            $where.= 'student_id='.$teacher_id;
        }

       //p($where);
        $data =  FractionService::getList($where);

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
       $res = FractionService::uniqueData($name,$value);
       if($res){
            echo json_encode(false); //json_encode(false) 表示有重复的号码;
       }else{
            echo json_encode(true); //必须是json_encode(true)
       }
    }
    
    
   //删除操作
   public function delData($id){
        try{
            $data = FractionService::DelOne($id);  
        } catch ( \Illuminate\Database\QueryException $e){
            $data = false;
        }
        if($data){
           return $this->returnData(200905); 
        }
   }
   
   
    // 更新操作
    public function updateData(){
         if(IsPost()){
            // 数据验证
            $id = $this->getPostDatas('POST','id',false,'htmlspecialchars');
            $score = intval($this->getPostDatas('POST','score',false,'htmlspecialchars'));
            if(($score < 0 ) || ($score > 100)){
               return  $this->returnData(200700); 
            }
 
            $res = Validate::make([
                [ 'id','regexp:/[0-9]+$/', '学生ID有误', Validate::MUST_VALIDATE ],
                [ 'score', 'regexp:/[0-9]{1,3}$/', '学生分数格式错误', Validate::MUST_VALIDATE ]
            ] ,['id'=>$id,'score'=>$score]);
            
            // 处理数据入库
            $dataUpdate = [
                'score'=>$score
            ];
            //p($dataUpdate);
            try{
                
                $updateRes =  FractionService::updateData($dataUpdate,['id'=>$id]);
                
            } catch ( \Illuminate\Database\QueryException $e){
                $updateRes = false;
            }
            
            //处理结果
            $code = 200902;   // 失败
            if($updateRes){
                $code = 200903; //成功
            }
            echo $this->returnData($code); 
        }  
    }
   
}
