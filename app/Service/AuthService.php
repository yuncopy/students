<?php
/**
 * 2017年11月24日12:03:25
 * Angela 
 * 功能:AOC界面处理
 * 
 * 参考资料
 * http://code.mixmedia.com/resource-permission-rule/
 * http://php.net/manual/zh/language.operators.bitwise.php
 */
namespace App\Service;
use \Bramus\Router\Router;
use App\Service\PermissionService;
use App\Service\RoleService;
use App\Service\LoginService;
use App\Library\Cookie;

class AuthService extends CommonService{
    
     //忽略检查URL
    private static $IgnoreURLPattern = [
        '/home/index.html',  // 后台首页
        '/user/pass.html'  // 设置密码
    ];

   
    /**
     * 2017年12月12日14:23:37
     * Angela
     * 是否有权限访问
     */
    static public function checkUserRule(){
        
        $URLInfo = self::getCurrentURL();
        $URL = $URLInfo['url'];
        $pattern = implode('|', self::$IgnoreURLPattern);
        $pattern_str = str_replace('/', '\/', $pattern);
        if(preg_match("/^{$pattern_str}$/i", $URL)){
            return true;
        }else{
            //缓存
            $RolePermission = LoginService::setGet(__RPID__,function(){
                $dataRole =  RoleService::getCurrentRolePermission(); // 当前角色的权限（登录进来就保持不变）
                return $dataRole;
            });
            
            $RolePermissionArray = explode(',', $RolePermission);
            $CurrentPermission = self::getCurrentPermission();    // 当前访问权限(根据访问权限时刻变化)
            if(in_array($CurrentPermission, $RolePermissionArray)){
                $checkResult = true;
            }else{
                $checkResult = false;
            }
            return $checkResult;
        }
    }


   
    /**
     * 2017年12月11日18:26:43
     * Angela 
     * @return string 获取当前URL十进制转二进制值
     */
    static public function getCurrentPermission() {
        $router = new Router();
        $method = $router->getRequestMethod(); // 获取当前访问方法
        $uri = self::call_private_method( $router ,'getCurrentUri');  // 访问当前URL
        $re_uri = preg_replace("/[0-9]+/", '@', $uri);  // 入库时约定     
        // 查询数据库中对应十进制
        $where=[
            'route' =>  $re_uri,
            'method'=>  $method 
        ];
        $permission = PermissionService::getWhereData($where);  // 当前访问权限ID
        
        //所有添加操作都需要清除角色过滤缓存
        if ( ($method == 'POST') && (strpos ( $re_uri ,  'add' )) ) {
            LoginService::checkDelAuths();  // 检测是否存在学生ID的键值
        }
        return $permission['id'];
    }
    
    // 当前URL
    static public function getCurrentURL() {
        $router = new Router();
        $method = $router->getRequestMethod(); // 获取当前访问方法
        $uri = self::call_private_method( $router ,'getCurrentUri');  // 访问当前URL
        $where=[
            'url' =>  $uri,
            'method'=>  $method 
        ];
        return $where;
    }
    
      /**
     * 2017年12月11日18:21:23
     * Angela
     * 利用反射机制，访问私有方法
     */
    // http://php.net/manual/zh/reflectionmethod.getclosure.php  学习使用
    static public function call_private_method($object, $method, $args = array()) {
        $reflection = new \ReflectionClass(get_class($object));    //利用反射api构造一个 类对应的反射类  
        $closure = $reflection->getMethod($method)->getClosure($object);   //您可以使用getClosure调用私有方法 
       // $reflection->getMethod($method)  //获取该类 $method参数所指向的方法对象  
       // $reflection->getMethod($method)->getClosure($object);  
        return call_user_func_array($closure, $args);   // $closure(...$args);  等效
    }
    
   
}