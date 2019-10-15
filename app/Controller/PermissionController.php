<?php

/**
 * Angela
 * 课程管理控制器
 * 2017年11月21日17:54:32
 * 
 */

namespace App\Controller;
use App\Service\PermissionService;
use App\Library\Validate;


class PermissionController extends BaseController {
    

    /**
     * 2017年11月25日11:11:30
     * Angela
     * 加载页面
     */
    public function index() {
        $this->display('permission.html');
    }
    
    /**
     * 2018年1月4日14:19:56
     * Angela
     * 获取课程数据
     */
    public function  getdata(){
        $where = [];
        $permissions =  PermissionService::getList($where);
        $data = $permissions['data'];
        $treePermission = make_tree_with_namepre($data);
        $dataPermissions = make_tree2list($treePermission);
        $permissions['data'] = $dataPermissions;

        //处理数据权限
        $permissions['filter'] = [
            ['name'=>'学期管理'],
            ['name'=>'导师管理'],
        ];

        echo json_encode($permissions);
    }
    
    
    /**
     * 2018年1月4日16:10:45
     * Angela
     * 检测唯一性
     */
    public function uniqueName(){
       $name = $this->getPostDatas('POST','name',false,'htmlspecialchars');
       $value = $this->getPostDatas('POST','value',false,'htmlspecialchars');
       $res = DefensesService::uniqueData($name,$value);
       if($res){
            echo json_encode(false); //json_encode(false) 表示有重复的号码;
       }else{
            echo json_encode(true); //必须是json_encode(true)
       }
    }
    
    
    /**
     * 2018年1月4日16:10:45
     * Angela
     * 添加
     */
    public function  addData(){
        if(IsPost()){
            // 数据验证
            $data = $this->getPostDatas('POST');
            //p($data);
            $res = Validate::make([
                [ 'name','required|isnull', '名称必选', Validate::MUST_VALIDATE ],
                [ 'method','required|isnull', '方法必填', Validate::MUST_VALIDATE ],
                [ 'route','regexp:/^\/[a-z]+\/[a-z]+(\/[\@])?(\/\@)?\.html$/', '必填正确路由格式', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            $code = 900400;   // 失败
            $cont =[];
            try{
                
               $insertId = PermissionService::insertData($data);
               
            } catch ( \Illuminate\Database\QueryException $e){
                $message = $e->getMessage(); 
                //$cont['message'] = $message;
                $unique_course = 'idx_method_route_name';  // 索引名称， 保证同一学生只能选一门课程 
                $pos_course = strpos($message, $unique_course);
                if ($pos_course !== false)  $code = 900900;
                $cont['code'] = $e->getCode();
                
                $insertId = false;
            }
            
            if($insertId){$code = 900200; } //成功
            return $this->returnData($code,$cont); 
        }
    }
    
    
   //删除操作
   public function delData($id){
        try{
            $data = PermissionService::DelOne($id);  
        } catch ( \Illuminate\Database\QueryException $e){
            $data = false;
        }
        if($data){
           return $this->returnData(900500); 
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
                [ 'name','required|isnull', '名称必选', Validate::MUST_VALIDATE ],
                [ 'method','required|isnull', '方法必填', Validate::MUST_VALIDATE ],
                [ 'route','regexp:/^\/[a-z]+\/[a-z]+(\/[\@])?(\/\@)?\.html$/', '必填正确路由格式', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            $data_name = trim($data['name']);
            $name = substr($data_name, strrpos($data_name," "));
            $dataUpdate = [
                'name'      =>  trim($name),
                'method'    =>  $data['method'],
                'route'     =>  $data['route']
            ];
            try{
                $updateRes =  PermissionService::updateData($dataUpdate,['id'=>$id]);
            } catch ( \Illuminate\Database\QueryException $e){

                $updateRes = false;
                
            }
            
            //处理结果
            $code = 900600;   // 失败
            if($updateRes){
                $code = 900300; //成功
            }
            return $this->returnData($code); 
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
            $updateRes =  PermissionService::updateData($dataUpdate,['id'=>$id]);
        } catch ( \Illuminate\Database\QueryException $e){
           
            $updateRes = false;
        }
        //处理结果
        $code = 900600;   // 失败
        if($updateRes){
            $code = 900700; //成功
        }
        return  $this->returnData($code); 
    }
    
    
   
}
