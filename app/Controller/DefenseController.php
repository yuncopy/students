<?php

/**
 * Angela
 * 课程管理控制器
 * 2017年11月21日17:54:32
 * 
 */

namespace App\Controller;
use App\Service\SemesterService;
use App\Service\DefensesService;
use App\Service\StudentService;
use App\Library\Validate;


class DefenseController extends BaseController {
    

    /**
     * 2017年11月25日11:11:30
     * Angela
     * 加载页面
     */
    public function index($id) {
        $semesters = SemesterService::getAllsData();
        $student = StudentService::getOneData($id);
        $results = [
            ['name'=>'通过','id'=>1],
            ['name'=>'不通过','id'=>2]
        ];
        $this->assign([
            'results'   => $results,
            'semesters' => $semesters,
            'student'   => !empty($student) ? $student : null
        ]);
        $this->display('defense.html');
    }
    
    /**
     * 2018年1月4日14:19:56
     * Angela
     * 获取课程数据
     */
    public function  getdata(){
        $where = [];
        $data =  DefensesService::getList($where);
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
     * 添加课程
     */
    public function  addData(){
        if(IsPost()){
            // 数据验证
            $data = $this->getPostDatas('POST');
            $res = Validate::make([
                [ 'student_id', 'regexp:/[0-9]+$/', '学生编号格式错误', Validate::MUST_VALIDATE ],
                [ 'semester_id','regexp:/[0-9]+$/', '学期编号格式错误', Validate::MUST_VALIDATE ],
                [ 'start_time','required|isnull', '开题时间必选', Validate::MUST_VALIDATE ],
                [ 'name','required|isnull', '开题题目必填', Validate::MUST_VALIDATE ],
                [ 'member','required|isnull', '开题成员必填', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            $code = 800400;   // 失败
            $cont =[];
            try{
                
               $insertId = DefensesService::insertData($data);
               
            } catch ( \Illuminate\Database\QueryException $e){
                $message = $e->getMessage(); 
                //$cont['message'] = $message;
                $unique_course = 'idx_name_student_id';  // 索引名称， 保证同一学生只能选一门课程 
                $pos_course = strpos($message, $unique_course);
                if ($pos_course !== false)  $code = 800900;
                $cont['code'] = $e->getCode();
                
                $insertId = false;
            }
            
            if($insertId){$code = 800200; } //成功
            return $this->returnData($code,$cont); 
        }
    }
    
    
   //删除操作
   public function delData($id){
        try{
            $data = DefensesService::DelOne($id);  
        } catch ( \Illuminate\Database\QueryException $e){
            $data = false;
        }
        if($data){
           return $this->returnData(800500); 
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
                [ 'start_time','required|isnull', '开题时间必选', Validate::MUST_VALIDATE ],
                [ 'name','required|isnull', '开题题目必填', Validate::MUST_VALIDATE ],
                [ 'member','required|isnull', '开题成员必填', Validate::MUST_VALIDATE ]
            ] ,$data);
            
            // 处理数据入库
            $dataUpdate = [
                'name'      =>  $data['name'],
                'start_time'=>  $data['start_time'],
                'member'    =>  $data['member'],
                'remark'    =>  $data['remark']
            ];
            //p($dataUpdate);
            try{
                $updateRes =  DefensesService::updateData($dataUpdate,['id'=>$id]);
            } catch ( \Illuminate\Database\QueryException $e){

                $updateRes = false;
                
            }
            
            //处理结果
            $code = 800600;   // 失败
            if($updateRes){
                $code = 800300; //成功
            }
            echo $this->returnData($code); 
        }  
    }
    
    
    // 注销和启用
    public function cancelStart($id,$status){
        try{
            $dataStatus = [
                2=>1,
                1=>2,
                3=>1
            ];
            $dataUpdate=[
                'result'=>$dataStatus[$status]
            ];
            $updateRes =  DefensesService::updateData($dataUpdate,['id'=>$id]);
        } catch ( \Illuminate\Database\QueryException $e){
           
            $updateRes = false;
        }
        //处理结果
        $code = 800600;   // 失败
        if($updateRes){
            $code = 800700; //成功
        }
        return  $this->returnData($code); 
    }
    
    
   
}
