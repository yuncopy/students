<?php

/**
 * Angela
 * 课程管理控制器
 * 2017年11月21日17:54:32
 * 
 */

namespace App\Controller;
use App\Service\CategoryService;
use App\Service\SubjectsService;
use App\Library\Validate;


class CategoryController extends BaseController {
    
    private $rooms;
    /**
     * 2017年11月25日11:11:30
     * Angela
     * 加载页面
     */
    public function index() {
        
        //获取星期数据
        $cates = CategoryService::getCates();
        $rooms = (CategoryService::roomData($cates['rooms']));
        $this->assign([
            'weeks'     =>  $cates['weeks'],
            'intervals' =>  $cates['intervals'],
            'rooms'     =>  $rooms,
            'grades'    =>  $cates['grades'],
            'credits'   =>  $cates['credits']
        ]);
        $this->display('category.html');
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
       $data =[
           $this->getPostDatas('POST','name',false,'htmlspecialchars'),
           $this->getPostDatas('POST','value',false,'htmlspecialchars'),
           $this->getPostDatas('POST','cate',false,'htmlspecialchars')
       ];
       $res = CategoryService::uniqueData($data);
       if($res){
            echo json_encode(false); //json_encode(false) 表示有重复的号码;
       }else{
            echo json_encode(true); //必须是json_encode(true)
       }
    }
    
    
    // 更新操作
    public function updateData(){
         if(IsPost()){
            // 数据验证
            $id = $this->getPostDatas('POST','id',false,'htmlspecialchars');
            $cate = $this->getPostDatas('POST','cate',false,'htmlspecialchars');
            $data = $this->getPostDatas('POST');
            $interval = ($cate == 'interval') ? true : false;
            $name =  trim($data['name']);
            $data['name'] =  $interval ? str_replace(':', '@', $name) : $name; // 处理进行正则匹配
            $rule = $interval 
                    ? [ 'name', 'regexp:/^[0-2][0-9]\@[0-5][0-9]\-[0-2][0-9]\@[0-5][0-9]$/', '时间段格式错误', Validate::MUST_VALIDATE ] 
                    : [ 'name','required|isnull', '名称必填', Validate::MUST_VALIDATE ];
            
            // 规则数组
            $validate = [
                [ 'id', 'regexp:/[0-9]+$/', '编号格式错误', Validate::MUST_VALIDATE ],
                $rule,
                [ 'cate','required|isnull', '类型未选择', Validate::MUST_VALIDATE ]
            ];
            
            $grade = ($cate == 'grade') ? true : false;
            if($grade){
                $validate[] = [ 'value', 'regexp:/^[0-9]{1,2}\-[0-9]{1,3}$/', '分数格式错误', Validate::MUST_VALIDATE ];
                $agrade = explode('-', $data['value']);
                sort($agrade);  // 排序
                $grade_value = implode('-', $agrade);
            }
            
            //p($validate);
            
            $res = Validate::make( $validate ,$data);  // 验证
            
            //p($dataUpdate);
            try{
                // 处理数据入库
                $dataUpdate = [
                    'name'=>$name
                ];
                
                $grade ? $dataUpdate['value'] = $grade_value : false;  // 添加数组
                
                $updateRes =  CategoryService::updateData($cate,$dataUpdate,['id'=>$id]);
            } catch ( \Illuminate\Database\QueryException $e){
                $updateRes = false;
            }
            
            //处理结果
            $code = 105400;   // 失败
            if($updateRes){
                $code = 105200; //成功
            }
            return $this->returnData($code); 
        }  
    }
    
    
    //删除操作
    public function delData($id){
        $cate = $this->getPostDatas('GET','cate',false,'htmlspecialchars');
        return  CategoryService::delData($cate,$id) && $this->returnData(105800);
    }
   
    
    
    /**
     * 2018年1月4日16:10:45
     * Angela
     * 添加课程
     */
    public function  addData(){
        if(IsPost()){
            // 数据验证
            $data = $this->getPostDatas('POST'); //[ 'id', 'regexp:/[0-9]+$/', '编号格式错误', Validate::MUST_VALIDATE ],
            $cate = $this->getPostDatas('POST','cate',false,'htmlspecialchars');
            $pid = $this->getPostDatas('POST','pid',false,'htmlspecialchars');
            $interval = ($cate == 'interval') ? true : false;
            $name = trim($data['name']);
            $data['name'] = $interval ?  str_replace(':', '@', $name) : $name; // 处理进行正则匹配
            $rule = $interval
                    ? [ 'name','regexp:/^[0-2][0-9]\@[0-5][0-9]\-[0-2][0-9]\@[0-5][0-9]$/', '时间段格式错误', Validate::MUST_VALIDATE ] 
                    : [ 'name','required|isnull', '名称必填', Validate::MUST_VALIDATE ];
            //p($rule);
            
            $validate=[
                $rule,
                [ 'cate','required|isnull', '类型未选择', Validate::MUST_VALIDATE ]
            ];
            
            // 分数处理
            $grade = ($cate == 'grade') ? true : false;
            if($grade){
                $validate[] = [ 'value', 'regexp:/^[0-9]{1,2}\-[0-9]{1,3}$/', '分数格式错误', Validate::MUST_VALIDATE ];
                $agrade = explode('-', $data['value']);
                sort($agrade);  // 排序
                $grade_value = implode('-', $agrade);
            }
            
            $res = Validate::make($validate,$data);
            
            // 处理数据入库
            try{
                $dataInster['name'] = $name;  // 添加数据
                
                (!empty($pid)) && ($cate=='room') ? $dataInster['pid'] = $pid : 0;    // 添加教室父类ID
                
                $grade ? $dataInster['value'] = $grade_value : false;  // 添加数组
                //p($dataInster);
                $insertId = CategoryService::insertData($cate,$dataInster);
            } catch ( \Illuminate\Database\QueryException $e){
                $insertId = false;
            }
            //处理结果
            $code = 105400;   // 失败
            if($insertId){$code = 105200; } //成功
            return $this->returnData($code); 
        }
    }
    
    // 获取父类信息
    public function parentData(){
        $pid = $this->getPostDatas('POST','id',false,'htmlspecialchars');
        $data=['id',$pid,'room'];
        $res = CategoryService::uniqueData($data);
        return $this->returnData(200,$res); 
    }

   
    
    
   
}
