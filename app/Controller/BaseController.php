<?php

/**
 * 2017年12月4日11:20:40
 * Angela
 * 后台基础控制器
 */
namespace App\Controller;
use App\Service\LoginService;
use App\Service\MenuService;
use App\Service\AuthService;
use App\Service\UserRelationService;
use App\Service\ElectiveService;
use App\Service\StudentService;
use App\Library\Session;

class BaseController extends CommonController{
    
   

    /**
     * 构造方式
     */
    public function __construct() {
        parent::__construct();
        $this->init();
    }
    
    /**
     * 2017年12月26日20:55:00
     * 检测是否登录
     * Angela
     */
    private function init(){
           
        $isLogin = LoginService::checkLogin();
        if (!$isLogin){
            if(isMethod('AJAX')){
                return $this->returnData(888,siteURL('/login/index.html'));
            }else{
                return redirect('/login/index.html');
            }
        }else{
            // 登录成功
            $key_nav = __AUTH__.'_navs_menu';
            $nav = LoginService::setGet($key_nav,function(){
               return MenuService::leftMenu();
            }); 
            
            // 设置权限数据
            //$AuthData = $this->makeAuthData();
            //dd($AuthData);
            LoginService::setGet(__AUTH__,function(){
                return  $this->makeAuthData();
            }); 
            
            // 分配变量
            $this->assign([
                'nav'       =>  $nav,
                'login'     =>  $isLogin
            ]);
            if(!AuthService::checkUserRule()){  // 检查权限
                if(isMethod('AJAX')){
                    return $this->returnData(999,[]);
                }else{
                    return redirect('/login/error.html');
                }
            }
        }
    }
    
    
    
    //权限数据设置  （方法很关键）
    private function makeAuthData(){
        
        $Login = LoginService::getLoginInfo();
        $role_id = $Login['role_id'];
        $user_id = $Login['uid'];
        $authData = [];
        $relation_id = 0;
        $whereData = ['user_id'=>$user_id,'role_id'=>$role_id];
        switch ($role_id){
            case '1':  // 管理员
                $authData = [];
                break;
            case '2':  // 教师
                $relation = UserRelationService::getOneData($whereData);
                $relation_id = $relation['relation_id'];
                $Course = ElectiveService::getSelecteds(['teacher_id'=>$relation_id]);//获取选择该教师的学生ID
                
                $student_ids = array_column($Course, 'student_id');
                $elective_ids = array_column($Course, 'id');  // 使用在成绩表中

                //$authData = ['teacher_id'=>[$relation_id],'student_id'=>$student_ids];

                $authData = ['teacher_id'=>[$relation_id],'student_id'=>$student_ids,'elective_id'=>$elective_ids];

                break;
            case '3':  // 学生
                $relation = UserRelationService::getOneData($whereData);
                $relation_id = $relation['relation_id'];
                $authData = ['student_id'=>[$relation_id]];
                break;
            case '4':  // 辅导员
                $relation = UserRelationService::getOneData($whereData);
                $relation_id = $relation['relation_id'];
                $studentsData = StudentService::getWhereStudentsData([['tutor_id','=',$relation_id]]);
                $student_ids = array_column($studentsData, 'id');
                $authData = ['tutor_id'=>[$relation_id],'student_id'=>$student_ids];
                break;
        }
        
        Session::set(__REID__,$relation_id);    // 设置关系ID
        
        return $authData;
    }
    
}
