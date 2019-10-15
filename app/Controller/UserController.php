<?php

/**
 * Angela
 * 课程管理控制器
 * 2017年11月21日17:54:32
 * 
 */

namespace App\Controller;
use App\Service\UserService;
use App\Library\Validate;
use App\Service\LoginService;

class UserController extends BaseController {
    

    /**
     * 2017年11月25日11:11:30
     * Angela
     * 加载页面
     */
    public function index() {
        $this->display('user.html');
    }
    
    /**
     * 2018年1月4日14:19:56
     * Angela
     * 获取课程数据
     */
    public function  getdata(){
        $where = [];
        $data =  UserService::getList($where);
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
       $res = UserService::uniqueData($name,$value);
       if($res){
            echo json_encode(false); //json_encode(false) 表示有重复的号码;
       }else{
            echo json_encode(true); //必须是json_encode(true)
       }
    }
    
    
    /**
     * 2018年1月4日16:10:45
     * Angela
     */
    public function  addData(){
        if(IsPost()){
            // 数据验证
            $data = $this->getPostDatas('POST');

            $res = Validate::make([
                [ 'username','required|isnull', '管理员必选', Validate::MUST_VALIDATE ],
                [ 'passwd','required|isnull', '密码必填', Validate::MUST_VALIDATE ],
                [ 'email','required|email', '邮箱格式必填', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            $code = 100500;   // 失败
            $cont =[];
            try{
                
                $dataInsert=[
                    "username"  => trim($data['username']),
                    "passwd"    => pass_hash(trim($data['username']).trim($data['passwd'])),
                    "email"     => $data['email'],
                    "role_id"   => 1  // 管理员
                ];
                
                $insertId = UserService::insertData($dataInsert);
               
            } catch ( \Illuminate\Database\QueryException $e){
               
                $insertId = false;
            }
            
            if($insertId){$code = 100900; } //成功
            return $this->returnData($code,$cont); 
        }
    }
    
    
   //删除操作
   public function delData($id){
        try{
            $data = UserService::delData($id);  
        } catch ( \Illuminate\Database\QueryException $e){
            $data = false;
        }
        if($data){
           return $this->returnData(100800); 
        }
   }
    // 重置密码
    public function repassData($id){
        try{
            UserService::repassDataAll();
            $data = UserService::repassData($id);  
            if($data || $data === 0){
                $status = 100810;
            }
        } catch ( \Illuminate\Database\QueryException $e){
            $status = 100100;
        }
        return $this->returnData($status); 
    }

   
    // 更新操作
    public function updateData(){
         if(IsPost()){
            // 数据验证
            $id = $this->getPostDatas('POST','id',false,'htmlspecialchars');
            $passwd = $this->getPostDatas('POST','passwd',false,'htmlspecialchars');
            $data = $this->getPostDatas('POST');
            $res = Validate::make([
                [ 'username','required|isnull', '管理员必选', Validate::MUST_VALIDATE ],
                [ 'email','required|email', '邮箱格式必填', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            $dataUpdate = [
                "username"  => $data['username'],
                "email"     => $data['email']
            ];
            //p($dataUpdate);
            //处理结果
            $code = 100100;   // 失败
            try{
                if( $passwd ){  // 存在密码则进行修改
                    $dataUpdate['passwd'] = pass_hash(trim($data['username']).trim($passwd));  
                }
                $updateRes =  UserService::updateData($dataUpdate,['id'=>$id]);
                if($updateRes){
                   $code = 100700; //成功
                }
            } catch ( \Illuminate\Database\QueryException $e){
                $updateRes = false;
                
            }
            
            if($updateRes){
                $code = 100600; //成功
            }
            return  $this->returnData($code); 
        }  
    }
    
    
   
    

    // 修改密码
    public function passData(){
        
        $data = $this->getPostDatas('POST');
        $res = Validate::make([
            [ 'pass1','required|isnull', '原始密码必选', Validate::MUST_VALIDATE ],
            [ 'pass2','regexp:/^[a-zA-Z\d_@#]{6,16}$/', '设置密码格式错误', Validate::MUST_VALIDATE ],
            [ 'pass3','regexp:/^[a-zA-Z\d_@#]{6,16}$/', '确认密码格式错误', Validate::MUST_VALIDATE ],
        ] ,$data);
        
        $code = 100100;   // 失败
        $Login = LoginService::getLoginInfo();
        if(pass_verify($data['pass1'], $Login)){// 当前密码是否正确
            if($data['pass2'] == $data['pass3']){
                try{
                    $pass = pass_hash($Login['username'].$data['pass3']);
                    $dataUpdate = ['passwd'=>$pass];
                    $updateRes =  UserService::updateData($dataUpdate,['id'=>$Login['uid']]);
                    LoginService::changeedPass();  // 检测是否已经修改密码
                    LoginService::updateCachePass($pass);  // 更新缓存密码
                    $code = 100600; //成功
                } catch ( \Illuminate\Database\QueryException $e){
                    $updateRes = false;
                }
            }else{
                $code = 100602;
            }
        }else{
            $code = 100601;
        }
        return  $this->returnData($code); 
    }
    
   
    
   
}
