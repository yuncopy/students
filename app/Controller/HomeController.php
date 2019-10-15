<?php

/**
 * 首页控制
 * 2017年11月21日17:54:32
 * 
 */

namespace App\Controller;
use App\Library\Session;
use App\Service\DefensesService;
use App\Service\StudentService;
use App\Service\LoginService;

class HomeController extends BaseController {

    /**
     * 2017年11月25日11:11:30
     * Angela
     * 显示后台结构页面
     */
    public function index() {
        $website = website();
        $this->assign(['website'=>$website]);
        $this->display('index.html');
    }
    
    /**
     * 后台主页
     */
    public function admin() {
         
        
        $AUTH= Session::get(__AUTH__);
        if(isset($AUTH['student_id'])){  // 学生信息
            $student_id = Session::get(__REID__);  // 获取关系ID，由角色决定
            $student=DefensesService::uniqueData('student_id',$student_id);  // 最新一条纪录
            if($student){
                $result = $student['result'];
                switch ( $result ){
                    case '1':
                        $this->assign(['resultText'=> '顺利毕业','result'=>$result]);
                        break;
                    case '2':
                        $this->assign(['resultText'=> '再次答辩','result'=>$result]);
                        break;
                    case '3':
                        $this->assign(['resultText'=> '答辩评审中...','result'=>$result]);
                        break;
                    default :
                        $this->assign(['resultText'=> '在校就读','result'=>4]);
                        break;
                }
                $this->assign(['fraction_url'=> siteURL("/fraction/student.html")]);
            }else{
                $this->assign(['elective_url'=> siteURL("/elective/index/{$student_id}.html")]);
            }
            $studentInfo = StudentService::getOneData($student_id);
            $this->assign(['student'=> $studentInfo]);
            $this->assign(['fraction_url'=> siteURL("/fraction/student.html")]);
        }
       
        $initPass = LoginService::checkInitPass();
        $this->assign(['initPass'=> $initPass]);
        $this->display('admin.html');
    }

}
