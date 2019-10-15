<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:AOC界面处理
 * 
 */
namespace App\Service;
use App\Library\Session;
use App\Model\UsersModel;
use App\Model\ChangePassModel;

class LoginService extends CommonService{
    
    // 用户登录相关服务
    private static $login_key = 'login_key';

    /**、2017年12月4日10:36:04
     * Angela
     * 用户登录使用
     */
    public static function login($data){
        if(is_array($data) && !empty($data)){
            $saveData = [
                'username'  =>  $data['username'],
                'uid'       =>  $data['id'],
                'email'     =>  $data['email'],
                'role_id'   =>  $data['role_id'],
                'passwd'      =>  $data['passwd']
            ];
            Session::set(self::$login_key ,$saveData); // 保存数据
        }
    }
    
    /**
     * 2018年1月2日22:11:54
     * Angela
     * 检测用户是否登录
     */
    public static function checkLogin(){
        $loginData = Session::get(self::$login_key ,false);
        if($loginData){
            return $loginData;
        }else{
            return false;
        }
    }

    /**
     * Angela
     * 获取用户信息
     */
    public static function checkLoginName($username){
        return  (new UsersModel)->checkLogin($username);  // 查询用户信息
    }
    
    
    /**
     * Angela
     * 获取用户信息
     */
    public static function getLoginInfo($key=null){
        $data = Session::get(self::$login_key ,false);
        if(!is_null($key)){
            $login = array_key_exists($key, $data) ? $data[$key] : false;
        }
        return !empty($login) ? $login : $data;
    }  

    /**
     * 更新缓存密码
     */
    public static function updateCachePass($pass){
        $data = Session::get(self::$login_key ,false);
        $data['pass'] = $pass;
        Session::set(self::$login_key ,$data); // 保存数据
        return true;
    }  
    
    /**
     * 用户退出
     */
    public static function logout(){
        Session::del(self::$login_key);  // 删除用户信息
        return Session::flush();  // 删除所有
    }  
    
    
    /**
     * 设置和获取缓存
     */
    public static function setGet($key,$value=''){
        $data = Session::get($key,false);  //获取数据
        if($data){
            return $data;  //获取数据
        }else{
            if($value instanceof \Closure){
                $data = $value();  // 执行匿名函数
                Session::set($key ,$data); // 保存数据
                return $data;
            }else{
                Session::set($key ,$value); // 保存数据
                return $value;
            }
        }
    }
    
    /*
     * 删除权限缓存
     * __RPID__
     */
    public static function delPermission(){
        return Session::del(__RPID__);  // 删除权限缓存
    }
    
     /*
     * 删除过滤缓存
     * __AUTH__
     */
    public static function checkDelAuths(){
        $key_auth = __AUTH__;
        $data =  Session::get($key_auth);  
        if(isset($data['student_id'])){
           return Session::get($key_auth); // 删除权限缓存
        }
    }
    
    
    /**
     * 提示更换密码
     */
    public static function checkInitPass(){
        return (new ChangePassModel)->checkInitPass();
    }
    
    /**
     * 标记已修改密码的用户
     */
    public static function changeedPass(){
        $uid = self::checkInitPass();  // 检测是否已经修改
        if(!$uid){
            $uid = LoginService::getLoginInfo('uid');
            return (new ChangePassModel)->insertOrUpdate(['user_id'=>$uid]);  // 添加
        }
    }
    


         
    
    
}