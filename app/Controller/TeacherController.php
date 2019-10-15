<?php

/**
 * Angela
 * 教师管理控制器
 * 2017年11月21日17:54:32
 * 
 */

namespace App\Controller;
use App\Service\TeacherService;
use App\Service\TutorService;
use App\Service\UserRelationService;
use App\Service\RoleService;
use App\Service\UserService;
use App\Library\Validate;


class TeacherController extends BaseController {
    

    /**
     * 2017年11月25日11:11:30
     * Angela
     * 加载页面
     */
    public function index() {
        
        $tutorclass =  TutorService::tutorClass();
        $this->assign(['number'=>'A'.TeacherService::autoNumber()]);  //取消编号记录
        $this->assign(['tutorclass'=>$tutorclass]);
        $this->display('teacher.html');
    }
    
    /**
     * 2018年1月4日14:19:56
     * Angela
     * 获取教师数据
     */
    public function  getdata(){
        $where = [];
        try {
            $data =  TeacherService::getList($where);
        } catch (Exception $exc) {
            $data=[
                'draw'=>1,
                'data'=>[],
                'recordsFiltered'=>1,
                'recordsTotal'=>1,
                'message'=>$exc->getMessage()
            ];
        }
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
       $res = TeacherService::uniqueData($name,$value);
       if($res){
            echo json_encode(false); //json_encode(false) 表示有重复的号码;
       }else{
            echo json_encode(true); //必须是json_encode(true)
       }
    }
    
    
    /**
     * 2018年1月4日16:10:45
     * Angela
     * 添加教师
     */
    public function  addData(){
        if(IsPost()){
            // 数据验证
            $data = $this->getPostDatas('POST');
            $res = Validate::make([
                [ 'number', 'regexp:/[A-Za-z0-9]{2,120}$/', '导师编号格式错误', Validate::MUST_VALIDATE ],
                [ 'name','required|isnull', '导师姓名必填', Validate::MUST_VALIDATE ],
                [ 'email', 'email', '邮箱格式错误', Validate::MUST_VALIDATE ],
                [ 'tutor_class_id', 'required|isnull', '导师职称必选', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            try{
                $capsule = $GLOBALS['capsule'];
                $insertRes = $capsule::transaction(function () use ($data){
                    // 教师表
                    $insertId = TeacherService::insertData($data);

                    // 用户表
                    $role_id = RoleService::customRole('teacher');  // 角色ID值
                    $pass = __PASS__ == false ? trim($data['number']) : __PASS__;
                    $pass_hash = $data['number'].$pass;
                    $insertUserId = UserService::insertData([
                        'username'  => $data['number'],
                        'passwd'    => pass_hash($pass_hash),
                        'role_id'   => $role_id,
                        'email'     => $data['email']
                    ]);

                    // 关系表
                    $relation = UserRelationService::insertData([
                        'role_id'  => $role_id,
                        'user_id'  => $insertUserId,
                        'relation_id'=>$insertId
                    ]);  // 入关系表
                    
                    return $relation;
                    
                }, 3);  // 3 第二个参数，该参数定义在发生死锁时，应该重新尝试事务的次数
            } catch ( \Illuminate\Database\QueryException $e){
                $insertRes = false;
            }
            //处理结果
            $code = 400400;   // 失败
            if($insertRes){$code = 400200; } //成功
            echo $this->returnData($code); 
        }
    }
    
    
   //删除操作
   public function delData($id){
        try{
            $capsule = $GLOBALS['capsule'];
            $data = $capsule::transaction(function () use ($id){
                
                 // 删除教师信息
                TeacherService::DelOne($id);  
                
                // 删除关系表
                $role_id = RoleService::customRole('teacher');
                $relation = UserRelationService::delData([
                    'role_id'       => $role_id,
                    'relation_id'   =>  $id
                ]);
   
                // 删除用户信息
                $delRes = UserService::delData($relation['user_id']);
                
                return $delRes;
                
            }, 3);  // 3 第二个参数，该参数定义在发生死锁时，应该重新尝试事务的次数
        } catch ( \Illuminate\Database\QueryException $e){
            $data = false;
        }
        if($data){
           return $this->returnData(400500); 
        }
   }
   
   
    // 更新操作
    public function updateData(){
         if(IsPost()){
            // 数据验证
            $id = $this->getPostDatas('POST','id',false,'htmlspecialchars');
            $data = $this->getPostDatas('POST');
            unset($data['id']); //销毁
            $res = Validate::make([
                [ 'name','required|isnull', '学生姓名必填', Validate::MUST_VALIDATE ],
                [ 'email', 'email', '邮箱格式错误', Validate::MUST_VALIDATE ],
                [ 'tutor_class_id', 'required|isnull', '导师职称必选', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            $dataUpdate = [
                'name'=>$data['name'],
                'email'=>$data['email'],
                'tutor_class_id'=>$data['tutor_class_id']
            ];
            try{
                $updateRes =  TeacherService::updateData($dataUpdate,['id'=>$id]);
            } catch ( \Illuminate\Database\QueryException $e){
                $updateRes = false;
            }
            
            //处理结果
            $code = 400600;   // 失败
            if($updateRes){
                $code = 400300; //成功
            }
            echo $this->returnData($code); 
        }  
    }
    
    
    // 注销和启用
    public function cancelStart($id,$status){
        try{
            $dataStatus = [
                2=>1,
                1=>2
            ];
            $dataUpdate=[
                'status'=>$dataStatus[$status]
            ];
            $updateRes =  TeacherService::updateData($dataUpdate,['id'=>$id]);
        } catch ( \Illuminate\Database\QueryException $e){
            $updateRes = false;
        }
        //处理结果
        $code = 400600;   // 失败
        if($updateRes){
            $code = 400300; //成功
        }
        echo $this->returnData($code); 
    }
    
    
   
}
