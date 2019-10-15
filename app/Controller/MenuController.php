<?php

/**
 * Angela
 * 课程管理控制器
 * 2017年11月21日17:54:32
 * 
 */

namespace App\Controller;
use App\Service\PermissionService;
use App\Service\MenuService;
use App\Library\Validate;


class MenuController extends BaseController {
    

    /**
     * 2017年11月25日11:11:30
     * Angela
     * 加载页面
     */
    public function index() {
        
        $permission = PermissionService::getList([]);
        $permission_data = $permission['data'];
        $select_option = make_option_tree_for_select($permission_data,0);
        $this->assign(['option'=>$select_option]);
        $this->display('menu.html');
    }
    
    
    //查看图标
    public function fontawesome() {
        
        $this->display('fontawesome.html');
    }
    
    
    
    /**
     * 2018年1月4日14:19:56
     * Angela
     * 获取课程数据
     */
    public function  getdata(){
        $where = [];
        $permissions =  MenuService::getList($where);
        $data = $permissions['data'];
        $treePermission = make_tree_with_namepre($data);
        $dataPermissions = make_tree2list($treePermission);
        $permissions['data'] = $dataPermissions;
        echo json_encode($permissions);
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
            $pid = $this->getPostDatas('POST','pid',false,'htmlspecialchars');
            //p($data);
            $res = Validate::make([
                [ 'name','required|isnull', '菜单名称必选', Validate::MUST_VALIDATE ],
                [ 'permission_id','required|isnull', '必填选择权限位', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            $code = 102400;   // 失败
            try{
                $dataInsert = [
                    'name'          => trim($data['name']),
                    'permission_id' => $data['permission_id']
                ];
                
                if($pid){ $dataInsert['pid'] = $pid;}
                
                $insertId = MenuService::insertData($dataInsert);
            } catch ( \Illuminate\Database\QueryException $e){
                $insertId = false;
            }
            
            if($insertId){$code = 102200; } //成功
            return $this->returnData($code); 
        }
    }
    
    
   //删除操作
   public function delData($id){
        try{
            $data = MenuService::DelOne($id);  
        } catch ( \Illuminate\Database\QueryException $e){
            $data = false;
        }
        if($data){
           return $this->returnData(102800); 
        }
   }
   
   
    // 更新操作
    public function updateData(){
         if(IsPost()){
            // 数据验证
          
            $id = $this->getPostDatas('POST','id',false,'htmlspecialchars');
            $data = $this->getPostDatas('POST');
            $res = Validate::make([
                [ 'name','required|isnull', '菜单名称必选', Validate::MUST_VALIDATE ],
                [ 'permission_id','required|isnull', '必填选择权限位', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            $data_name = trim($data['name']);
            $dataUpdate = [
                'name'          =>  $data_name,
                'permission_id' =>  trim($data['permission_id'])
            ];
            try{
                $updateRes =  MenuService::updateData($dataUpdate,['id'=>$id]);
            } catch ( \Illuminate\Database\QueryException $e){
                $updateRes = false;
            }
            
            //处理结果
            $code = 102500;   // 失败
            if($updateRes){
                $code = 102600; //成功
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
            $updateRes =  MenuService::updateData($dataUpdate,['id'=>$id]);
        } catch ( \Illuminate\Database\QueryException $e){
           
            $updateRes = false;
        }
        //处理结果
        $code = 102500;   // 失败
        if($updateRes){
            $code = 102600; //成功
        }
        return  $this->returnData($code); 
    }
    
    
   
}
