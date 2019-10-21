<?php
/**
 * 首页控制
 * 2017年11月21日17:54:32
 */
namespace App\Controller;
use App\Service\LoginService;
class LoginController extends CommonController{
    
    /**
     * 2017年12月26日20:57:20
     * Angela
     * 加载登录界面
     */
    public function index()
    {
        $isLogin = LoginService::checkLogin();
        if($isLogin){
            return redirect('/home/index.html');
        }
        $website = website();
        $this->assign(['website'=>$website]);
        $this->display('login.html'); 
    }
    
    
    /**
     * 2017年12月1日17:27:32
     * Angela
     * 后台用户登录
     */
    public function login(){
        
        $username = $this->getPostDatas('POST','username',false,'htmlspecialchars');
        $pass = $this->getPostDatas('POST','passwd',false,'htmlspecialchars');

        //echo pass_hash($username.$pass);exit;
        //echo password_hash($pass,PASSWORD_DEFAULT);exit; // password_verify

        if($username && $pass){
            $rs = LoginService::checkLoginName(
               ['username'=>$username]  // 一维数组
            );  //查询查询
            if(pass_verify($pass,$rs)) {  // 检测密码
                LoginService::login($rs);  // 设置用户登录标识
                $this->returnData(100200,'/home/index.html');// 成功跳转
            }
            $this->returnData(100400); // 用户登录失败
        }
    }
    
    /**
     * 2017年12月1日17:27:32
     * Angela
     * 后台用户登录
     */
    
    public function logout(){
        LoginService::logout();  // 清除数据
        return  redirect('/login/index.html'); // 直接跳转
    }




    /**
     * 2017年12月1日17:46:10
     * Angela 
     * 添加用户
     */
    public function insertUser(){
        
        $data=[
            'name'=>'bluepay',
            'pass'=> md5('bluepayqwer@123')
        ];
        $rs = (new UsersModel)->insertOrUpdate($data);

    }
    
    
    
    /**
    * 2017年12月12日15:11:09
    * Angela
    * 访问权限,提示错误
    */
    public function error(){
        $errorMessage = [
            'draw'=>1,'data'=>[], // 处理表格数据展示
            'recordsFiltered'=>1,
            'recordsTotal'=>1
        ];
        if(isMethod('AJAX')){
            return die(json_encode($errorMessage));
        }else{
            return $this->display('500.html'); 
        }
    }
    
    /**
     * 2018年1月24日16:49:22
     * Angela 
     * 路由查询找不到  (ERROR ,路由找不到)
     */
    public function found(){
        return $this->display('404.html'); 
    }
    
    /**
     * 2018年1月24日16:57:09
     * Angela
     * 前台首页
     */
    public function home(){
        $website = website();
        $this->assign(['website'=>$website]);
        return $this->display(__TEMPLATE__.'index.html'); 
    }
    
}
