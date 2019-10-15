<?php

/**
 * Angela
 * 课程管理控制器
 * 2017年11月21日17:54:32
 * 
 */

namespace App\Controller;
use App\Service\SemesterService;
use App\Library\Validate;


class SemesterController extends BaseController {
    

    /**
     * 2017年11月25日11:11:30
     * Angela
     * 加载页面
     */
    public function index() {
        
        $this->assign(['id'=>SemesterService::autoNumber()]);
        $this->display('semester.html');
    }
    
    
    /**
     * 2018年1月4日14:19:56
     * Angela
     * 获取课程数据
     */
    public function  getdata(){
        $where = [];
        $data =  SemesterService::getList($where);
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
       $res = SemesterService::uniqueData($name,$value);
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
            
            $nameRegexp = '/^20[0-9]{2}\-20[0-9]{2}学年第[0-9]{1}学期$/';
            $res = Validate::make([
                [ 'id', 'regexp:/[0-9]+$/', '学期编号格式错误', Validate::MUST_VALIDATE ],
                [ 'name','regexp:'.$nameRegexp, '学期名称不正确', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            try{
               $insertId = SemesterService::insertData([
                    'name'=>$data['name'],
                    'sort'=>SemesterService::autoNumber('sort')
                ]);
            } catch ( \Illuminate\Database\QueryException $e){
                $insertId = false;
            }
            //处理结果
            $code = 104400;   // 失败
            if($insertId){$code = 104200; } //成功
            echo $this->returnData($code); 
        }
    }
    
    
   //删除操作
   public function delData($id){
        try{
            $data = SemesterService::DelOne($id);  
        } catch ( \Illuminate\Database\QueryException $e){
            $data = false;
        }
        if($data){
           return $this->returnData(104800); 
        }
   }
   
   
    // 更新操作
    public function updateData(){
         if(IsPost()){
            // 数据验证
            $id = $this->getPostDatas('POST','id',false,'htmlspecialchars');
            $data = $this->getPostDatas('POST');
            unset($data['id']); //销毁
            $nameRegexp = '/^20[0-9]{2}\-20[0-9]{2}学年第[0-9]{1}学期$/';
            $res = Validate::make([
                [ 'name','regexp:'.$nameRegexp, '学期名称不正确', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            $dataUpdate = [
                'name'=>$data['name']
            ];
            //p($dataUpdate);
            try{
                $updateRes =  SemesterService::updateData($dataUpdate,['id'=>$id]);
            } catch ( \Illuminate\Database\QueryException $e){
                $updateRes = false;
            }
            
            //处理结果
            $code = 104500;   // 失败
            if($updateRes){
                $code = 104600; //成功
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
            $updateRes =  SemesterService::updateData($dataUpdate,['id'=>$id]);
        } catch ( \Illuminate\Database\QueryException $e){
            $updateRes = false;
        }
        //处理结果
        $code = 104500;   // 失败
        if($updateRes){
            $code = 104600; //成功
        }
        echo $this->returnData($code); 
    }
    
    
    // 降序
    public  function descData($id){
        try{
            $res = SemesterService::descData($id);
        } catch ( \Illuminate\Database\QueryException $e){
            $res = false;
        }
        return $this->returnData(200,$res);
    }
    
    
    //升序
    public  function ascData($id){
        try{
            $res = SemesterService::ascData($id);
        } catch ( \Illuminate\Database\QueryException $e){
            $res = false;
        }
        $this->returnData(200,$res);
    }
   
}
