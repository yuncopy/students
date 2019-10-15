<?php

/**
 * Angela
 * 配置管理控制器
 * 2017年11月21日17:54:32
 * 
 */

namespace App\Controller;
use App\Service\WebsiteService;
use App\Library\Validate;


class WebsiteController extends BaseController {
    

    /**
     * 2017年11月25日11:11:30
     * Angela
     * 加载页面
     */
    public function index() {
        
        $this->display('website.html');
    }
    
    /**
     * 2018年1月4日14:19:56
     * Angela
     * 获取配置数据
     */
    public function  getdata(){
        $where = [];
        $data =  WebsiteService::getList($where);
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
       $res = WebsiteService::uniqueData($name,$value);
       if($res){
            echo json_encode(false); //json_encode(false) 表示有重复的号码;
       }else{
            echo json_encode(true); //必须是json_encode(true)
       }
    }
    
    
    /**
     * 2018年1月4日16:10:45
     * Angela
     * 添加配置
     */
    public function  addData(){
        if(IsPost()){
            $data = $this->getPostDatas('POST');   // 数据验证
            $res = Validate::make([
                [ 'name','regexp:/^[a-z]{2,64}$/', '配置名称必填', Validate::MUST_VALIDATE ],
                [ 'value','required|isnull', '配置值必填', Validate::MUST_VALIDATE ],
                [ 'remark','required|isnull', '配置备注必填', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            try{
               $insertId = WebsiteService::insertData([
                    'name'  =>  $data['name'],
                    'value' =>  $data['value'],
                    'remark'=>  $data['remark']
                ]);
            } catch ( \Illuminate\Database\QueryException $e){
                $insertId = false;
            }
            //处理结果
            $code = 103400;   // 失败
            if($insertId){$code = 103200; } //成功
            return $this->returnData($code); 
        }
    }
    
    
   //删除操作
   public function delData($id){
        try{
            $data = WebsiteService::DelOne($id);  
        } catch ( \Illuminate\Database\QueryException $e){
            $data = false;
        }
        if($data){
           return $this->returnData(103800); 
        }
   }
   
   
    // 更新操作
    public function updateData(){
         if(IsPost()){
            // 数据验证
            $id = $this->getPostDatas('POST','id',false,'htmlspecialchars');
            $data = $this->getPostDatas('POST');
            $res = Validate::make([
                [ 'name','regexp:/^[a-z]{2,64}$/', '配置名称必填', Validate::MUST_VALIDATE ],
                [ 'value','required|isnull', '配置值必填', Validate::MUST_VALIDATE ],
                [ 'remark','required|isnull', '配置备注必填', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            $dataUpdate = [
                'name'  =>  $data['name'],
                'value' =>  $data['value'],
                'remark'=>  $data['remark']
            ];
            //p($dataUpdate);
            try{
                $updateRes =  WebsiteService::updateData($dataUpdate,['id'=>$id]);
            } catch ( \Illuminate\Database\QueryException $e){
                $updateRes = false;
            }
            
            //处理结果
            $code = 103500;   // 失败
            if($updateRes){
                $code = 103600; //成功
            }
            return $this->returnData($code); 
        }  
    }

   
}
