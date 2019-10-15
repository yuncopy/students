<?php

/**
 * Angela
 * 学生管理控制器
 * 2017年11月21日17:54:32
 * 
 */

namespace App\Controller;
use App\Service\StudentService;
use App\Service\TutorService;
use App\Service\UploadService;
use App\Service\SemesterService;
use App\Service\UserRelationService;
use App\Service\RoleService;
use App\Service\UserService;
use App\Service\TasksService;
use App\Library\Validate;
use App\Library\Cli;


class StudentController extends BaseController {

    /**
     * 2017年11月25日11:11:30
     * Angela
     * 加载页面
     */
    public function index() {
        $tutors = TutorService::getTutors();
        $semesters = SemesterService::getAllsData();
        $tutors_key_name= array_column($tutors, 'name','id');
        TutorService::cacheTutors($tutors_key_name);// 设置缓存
        $this->assign(['tutors'=>  $tutors,'semesters'=>$semesters]);
        $this->display('student.html');
    }
    
    /**
     * 2018年1月4日14:19:56
     * Angela
     * 获取学生数据
     */
    public function  getdata(){
        $where = [];
        $searchdata = $this->getPostDatas('POST','searchdata',false,'stripslashes');
        if($searchdata){
           $where = $searchdata;
        }
        $data =  StudentService::getList($where);
        echo json_encode($data);
    }
    
    //获取某个条件的学生
    public function whereData(){
        $post = $this->getPostDatas('POST');
        $data =  StudentService::getWhereData($post);
        echo json_encode($data);
    }

        /**
     * 2018年1月4日16:10:45
     * Angela
     * 添加学生
     */
    public function  adddata(){
        if(IsPost()){
            // 数据验证
            $data = $this->getPostDatas('POST');
            $res = Validate::make([
                [ 'number', 'regexp:/[A-Za-z0-9]{2,32}$/', '学生学号格式错误', Validate::MUST_VALIDATE ],
                [ 'username','required|isnull', '学生姓名必填', Validate::MUST_VALIDATE ],
                [ 'my_mobile', 'phone', '学生手机号格式错误', Validate::MUST_VALIDATE ],
                [ 'email', 'email', '邮箱格式错误', Validate::MUST_VALIDATE ],
                [ 'enrolment', 'required|isnull', '入学时间必选', Validate::MUST_VALIDATE ],
                [ 'tutor_id', 'required|isnull', '导师必选', Validate::MUST_VALIDATE ],
                [ 'status', 'required|isnull', '学生状态必选', Validate::MUST_VALIDATE ],
                [ 'gender', 'required|isnull', '学生性别必选', Validate::MUST_VALIDATE ],
                [ 'semester_id', 'required|isnull', '学期必选', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            
            $capsule = $GLOBALS['capsule'];
            $insertRes = true;
            $capsule::transaction(function () use ($data,&$insertRes){
                try{
                     // 学生表入库
                    $insertRes = StudentService::insertData($data); // 添加学生表数据库
                    
                     // 用户表
                    $role_id = RoleService::customRole('student');
                    $pass = __PASS__ == false ? trim($data['number']) : __PASS__;
                    $insertUserId = UserService::insertData([
                        'username'  => $data['number'],
                        'passwd'    => $pass_hash = md5($pass.__SALT__),
                        'role_id'   => $role_id,
                        'email'     => $data['email']
                    ]);
                    
                     // 关系表
                    $relation = UserRelationService::insertData([
                        'role_id'  => $role_id,
                        'user_id'  => $insertUserId,
                        'relation_id'=>$insertRes
                    ]);  // 入关系表
                    
                } catch ( \Illuminate\Database\QueryException $e){
                    
                    $insertRes = false;
                }
            });
           
            //处理结果
            $code = 200400;   // 失败
            if($insertRes){
                $code = 200200; //成功
            }
            echo $this->returnData($code); 
        }else{
           $data = TutorService::getTutors();
           $semesters  = SemesterService::getAllsData();
           $status=__STUDENT_STATUS__;
           $genders=__STUDENT_GENDER__;
           //1 在读 2 开题 3 结业 4 毕业 5 肄业
           $this->assign([
               'tutors'     =>  $data,
               'semesters'  =>  $semesters,
               'status'=>$status,
               'genders'=>$genders
            ]);
           $this->display('studentadd.html'); 
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
       $res = StudentService::uniqueData($name,$value);
       if($res){
            echo json_encode(false); //json_encode(false) 表示有重复的号码;
       }else{
            echo json_encode(true); //必须是json_encode(true)
       }
    }
    
    /**
     * 导出学生
     */
    public function studentExport(){
        return StudentService::exportData();
    }
    
    /**
     * 上传表格
     */
    public function studentUpload(){
        $data = UploadService::uploadStudent();  // 上传数据
        if($data){
            return StudentService::studentImportFile($data); // 缓存数据
        }
        return false;
    }
    
    /**
     * 执行批量导入
     */
    public function  studentImport(){
        //同步处理
        $report = StudentService::studentImportData();  // 批量导入的数据
        if($report === true){
            $status = 105200;
        }else{
            $report = strstr($report,'(',true);  // 获取数据库提示信息
            $status = 105400;
        }
        echo $this->returnData( $status,$report);
        
        
       //$filePath = null;
       //if($report){ // 处理表格中为空的数据
           //$filePath = StudentService::exportData( $report  , __UPLOAD__ );  //生成表格
           //StudentService::studentRepeatFile($filePath);  // 取出数据
      //
       /*添加任务 使用SWOOlE 处理  ，不提倡使用 exec
       $console=APP_PATH."/app/Console/";
       if(!Cli::exec("/usr/bin/php {$console}Servers.php -a restart")){
           throw new \Exception('执行失败');
       }else{
           throw new \Exception('重启服务成功');
       }*/
    }
    
    /**
     * 获取导入进度信息
     */
    public function studentProcess(){
        // 读取文件
        $data = $this->getPostDatas('POST');
        if($data){
            $file_name = $data['file_name'];
        }
        echo $this->returnData( 200 ,$data);
    }

      /**
     * 导出批量导出学生信息时存在重复的数据
     */
    public function  studentRepeat(){
        $filepath = StudentService::studentRepeatFile(null);
        if(file_exists($filepath)){
            $fileinfo = pathinfo($filepath);
            $filename = $fileinfo['basename'];//下载新的文件名
            header('Content-Type: application/vnd.ms-excel' );
            header('Content-Disposition: attachment; filename="' .$filename. '"');//下载后的新文件名
            return downLoadChunked($filepath);
        }
        die('Not found File');
    }
    
    
    /**
     * 删除操作
     */
    public function stuendtDel($id){
        
        try{
            $data = true;
            $capsule = $GLOBALS['capsule'];
            $capsule::transaction(function () use ($id){
                
                 // 删除学生信息
                $data = StudentService::studentDelOne($id);
                
                // 删除关系表
                $role_id = RoleService::customRole('student');
                $relation = UserRelationService::delData([
                    'role_id'       => $role_id,
                    'relation_id'   =>  $id
                ]);
   
                // 删除用户信息
                UserService::delData($relation['user_id']);
                
            }, 3);  // 3 第二个参数，该参数定义在发生死锁时，应该重新尝试事务的次数
        } catch ( \Illuminate\Database\QueryException $e){
            $data = false;
        }
        
        if($data){
           return $this->returnData(200500); 
        }
    }
    
    
    
    /**
     * 编辑操作
     */
    public function stuendtUpdate($id){
      
         if(IsPost()){
            // 数据验证
            $id = $this->getPostDatas('POST','id',false,'htmlspecialchars');
            $data = $this->getPostDatas('POST');
            unset($data['id']); //销毁
            $res = Validate::make([
                [ 'number', 'regexp:/[A-Za-z0-9]{2,32}$/', '学生学号格式错误', Validate::MUST_VALIDATE ],
                [ 'username','required|isnull', '学生姓名必填', Validate::MUST_VALIDATE ],
                [ 'my_mobile', 'phone', '学生手机号格式错误', Validate::MUST_VALIDATE ],
                [ 'email', 'email', '邮箱格式错误', Validate::MUST_VALIDATE ],
                [ 'enrolment', 'required|isnull', '入学时间必选', Validate::MUST_VALIDATE ],
                [ 'tutor_id', 'required|isnull', '导师必选', Validate::MUST_VALIDATE ],
                [ 'semester_id', 'required|isnull', '学期必选', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            $updateRes = StudentService::updateData($data,['id'=>$id]);
            //处理结果
            $code = 200600;   // 失败
            if($updateRes){
                $code = 200300; //成功
            }
            echo $this->returnData($code); 
        }else if($id){
           $dataOne = StudentService::getOneData($id);
           $data = TutorService::getTutors();
           $semesters  = SemesterService::getAllsData();
           $this->assign(['tutors'=>$data]);
           $this->assign(['student'=>$dataOne]);
           $this->assign(['semesters'=>$semesters]);
           $this->display('studentedit.html'); 
        }
    }
}
