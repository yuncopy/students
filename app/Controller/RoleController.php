<?php

/**
 * Angela
 * 课程管理控制器
 * 2017年11月21日17:54:32
 * 
 */

namespace App\Controller;
use App\Service\RoleService;
use App\Library\Validate;


class RoleController extends BaseController {
    

    /**
     * 2017年11月25日11:11:30
     * Angela
     * 加载页面
     */
    public function index() {
        
        $this->display('role.html');
    }
    
    /**
     * 2018年1月4日14:19:56
     * Angela
     * 获取课程数据
     */
    public function  getdata(){
        $where = [];
        $data =  RoleService::getList($where);
        echo json_encode($data);
    }
    
    
    //授权操作(修改操作)
    public function authData($id){
        if(IsPost()){
            $permission_id = $this->getPostDatas('POST','permission_id',false);
            $id = $this->getPostDatas('POST','role_id',false,'htmlspecialchars');
            $dataUpdate = [];
            if($permission_id && $id){
                
                // 获取原来数据
                /*
                $role = RoleService::getOneData($id);
                $original_permission_id = $role['permission_id'];
               */
                asort($permission_id,SORT_NUMERIC);
                $dataUpdate =[
                    'permission_id' => implode(',', $permission_id)
                ];
            }
            //p($dataUpdate);
            $code = 900902;
            try{
                $updateRes =  RoleService::updateData($dataUpdate,['id'=>$id]);
                RoleService::delPermission(); //删除角色缓存权限
            } catch ( \Illuminate\Database\QueryException $e){
                $updateRes = false;
            }
            if($updateRes){$code = 900901; } //成功
            
            return $this->returnData($code); 
        }else{
            $role = RoleService::uniqueData($id);
            $this->assign([
                'role_id'       =>  $id,
                'role_name'     =>  $role['show_name'],
                'permission_id' =>  $role['permission_id']
            ]);
            $this->display('roleauth.html');
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
       $res = RoleService::uniqueData($name,$value);
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

            Validate::make([
                [ 'name','regexp:/^[a-z]{2,32}$/', '键名使用英文小写', Validate::MUST_VALIDATE ],
                [ 'show_name','required|isnull', '角色展示名称必填', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            $code = 101500;   // 失败
            $cont =[];
            try{
                
                $dataInsert=[
                    "name"      => $data['name'],
                    "show_name" => $data['show_name']
                ];

                $insertId = RoleService::insertData($dataInsert);
               
            } catch ( \Illuminate\Database\QueryException $e){
               
                $insertId = false;
            }
            
            if($insertId){$code = 101400; } //成功
            return $this->returnData($code,$cont); 
        }
    }
    
    
   //删除操作
   public function delData($id){
        try{
            $data = RoleService::delData($id);  
        } catch ( \Illuminate\Database\QueryException $e){
            $data = false;
        }
        if($data){
           return $this->returnData(101800); 
        }
   }
   
   
    // 更新操作
    public function updateData(){
         if(IsPost()){
            // 数据验证
            $id = $this->getPostDatas('POST','id',false,'htmlspecialchars');
            $data = $this->getPostDatas('POST');
            $res = Validate::make([
                [ 'show_name','required|isnull', '管理员必选', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            $dataUpdate = [
                "show_name"  => $data['show_name']
            ];
            //p($dataUpdate);
            $code = 101500;   // 失败
            try{            //处理结果
                $updateRes =  RoleService::updateData($dataUpdate,['id'=>$id]);
                if($updateRes){$code = 101700;}
            } catch ( \Illuminate\Database\QueryException $e){
                $updateRes = false;
            }
            
            if($updateRes){
                $code = 101600; //成功
            }
            return  $this->returnData($code); 
        }  
    }
    
    
   
    
   
}
