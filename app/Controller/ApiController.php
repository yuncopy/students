<?php

/**
 * 基础控制器
 */
namespace App\Controller;
use App\Service\ReturnCodeService;
use App\Service\StudentService;
use App\Service\CategoryService;
class ApiController{
   
    
    
    public function process($type){
        switch ($type){
            case 'process':
                //StudentService::studentImportData();
                StudentService::studentImportDataTest();
             break;
        }
    }

    /**
     * 2017年12月27日20:38:14
     * Angela
     * 处理返回结果
     */
    final protected function returnData($status,$data=null){
        $message = ReturnCodeService::getMessage($status);
        $out_data = [
            'status'    =>  $status,
            'message'   =>  $message
        ];
        $data !== null ? $out_data['data'] = $data : false;
        return die(json_encode($out_data));
    }


    /**
     *
     * 特殊处理课程添加
     *
     * @api /api/add-class-time.html?time=另行通知
     * @author  jackin.chen
     *
     *
    */
    public function addClassTime(){

        $time = isset($_GET['time']) ?  htmlspecialchars($_GET['time']) : false;
        if($time){
            try{
                $dataInster['name'] = $time;  // 添加数据
                $cate = 'interval'; //表类型
                $insertId = CategoryService::insertData($cate,$dataInster);
            } catch ( \Illuminate\Database\QueryException $e){
                $insertId = false;
            }

            var_dump($insertId);
        }

    }

    /**
     *
     * DROP INDEX student_course ON mpa_electives;
     * create unique index idx_student_course on mpa_electives(student_id,course_id,semester_id);
    */
    
}
