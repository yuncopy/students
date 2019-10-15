<?php

/**
 * Angela
 * 课程管理控制器
 * 2017年11月21日17:54:32
 * 
 */

namespace App\Controller;
use App\Service\SubjectsService;
use App\Service\CategoryService;
use App\Library\Validate;


class SubjectController extends BaseController {
    

    /**
     * 2017年11月25日11:11:30
     * Angela
     * 加载页面
     */
    public function index() {
        
        
        $credits = CategoryService::getCates('credit');  //学分
        $credits = arraySort($credits,'name');
        $this->assign(['id'=>SubjectsService::autoNumber(),'credits'=>$credits]);
        $this->display('subject.html');
    }
    
    /**
     * 2018年1月4日14:19:56
     * Angela
     * 获取课程数据
     */
    public function  getdata(){
        $where = [];
        $data =  SubjectsService::getList($where);
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
       $res = SubjectsService::uniqueData($name,$value);
       if($res){
            echo json_encode(false); //json_encode(false) 表示有重复的号码;
       }else{
            echo json_encode(true); //必须是json_encode(true)
       }
    }
    
    
    /**
     * 2018年1月4日16:10:45
     * Angela
     * 添加课程
     */
    public function  addData(){
        if(IsPost()){
            // 数据验证
            $data = $this->getPostDatas('POST');
            $res = Validate::make([
                [ 'id', 'regexp:/[0-9]+$/', '课程编号格式错误', Validate::MUST_VALIDATE ],
                [ 'name','required|isnull', '课程姓名必填', Validate::MUST_VALIDATE ],
                [ 'credit','required|isnull', '课程学分必填', Validate::MUST_VALIDATE ],
                [ 'start_time','required|isnull', '课程选课开始时间', Validate::MUST_VALIDATE ],
                [ 'end_time','required|isnull', '课程选课截至时间', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            try{
               $insertId = SubjectsService::insertData([
                    'name'=>$data['name'],
                    'start_time'=>$data['start_time'],
                    'end_time'=>$data['end_time'],
                    'credit' => $data['credit']
                ]);
            } catch ( \Illuminate\Database\QueryException $e){
                $insertId = false;
            }
            //处理结果
            $code = 500400;   // 失败
            if($insertId){$code = 500200; } //成功
            return $this->returnData($code); 
        }
    }
    
    
   //删除操作
   public function delData($id){
        try{
            $data = SubjectsService::DelOne($id);  
        } catch ( \Illuminate\Database\QueryException $e){
            $data = false;
        }
        if($data){
           return $this->returnData(500500); 
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
                [ 'start_time','required|isnull', '课程选课开始时间', Validate::MUST_VALIDATE ],
                [ 'credit','required|isnull', '课程学分必填', Validate::MUST_VALIDATE ],
                [ 'end_time','required|isnull', '课程选课截至时间', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            $dataUpdate = [
                'name'=>$data['name'],
                'credit'=>$data['credit'],
                'start_time'=>$data['start_time'],
                'end_time'=>$data['end_time']
            ];
            //p($dataUpdate);
            try{
                $updateRes =  SubjectsService::updateData($dataUpdate,['id'=>$id]);
            } catch ( \Illuminate\Database\QueryException $e){
                $updateRes = false;
            }
            
            //处理结果
            $code = 500600;   // 失败
            if($updateRes){
                $code = 500300; //成功
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
            $updateRes =  SubjectsService::updateData($dataUpdate,['id'=>$id]);
        } catch ( \Illuminate\Database\QueryException $e){
            $updateRes = false;
        }
        //处理结果
        $code = 500600;   // 失败
        if($updateRes){
            $code = 500300; //成功
        }
        return $this->returnData($code); 
    }
    
    
   
}
