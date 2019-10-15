<?php

/**
 * 基础控制器
 */
namespace App\Controller;
use App\Service\ReturnCodeService;
class CommonController{
    
    private $assign = [];

    public function __construct()
    {
        
    }
    
    /**
     *Angela
     * 分配数据到前台
     * @param array $data 分配页面数据
     */
    final public function assign($data)
    {
        if(is_array($data)){
            $this->assign = array_merge($this->assign,$data); // 合并数组
        }else{
            throw new \Exception('Not Key Or Value');
        }
    }
    
   /**
    * 
    * 渲染一个视图模板, 并直接输出给请求端
    * @param unknown_type $file 视图文件
    */
    final public function display($file){
        
        /* *
         * 原始加载模版方式
         $file_path = __VIEW__'.$file;
         if(is_file($file_path) && file_exists($file_path)){
             extract($this->assign);
             include_once $file_path;
         }else{
             throw new \Exception('Not Find View File : '.$file_path);
         }
         */
        
        $file_path = __VIEW__.$file;
        if(is_file($file_path) && file_exists($file_path)){
            $view_cache_dir= __CACHE__.'template';  //试图文件缓存目录
            $loader = new \Twig_Loader_Filesystem(__VIEW__);
            $twig = new \Twig_Environment($loader, array(
                'cache' => $view_cache_dir,
                'debug'=> true
            ));
            //https://stackoverflow.com/questions/3595727/twig-pass-function-into-template
            $function = new \Twig_SimpleFunction('url', function ($url) {
                return siteURL($url);  // 处理全局路径
            });
            $twig->addFunction($function);  // 添加自定义函数
            
            //权限函数页面是否隐藏
            $wfunc = new \Twig_SimpleFunction('W', function ($permission_id) {
                return W($permission_id);  // 处理全局路径
            });
            $twig->addFunction('W',$wfunc);  //   W is our girlfriend's surname
            
            $template = $twig->loadTemplate($file);
            $template->display($this->assign? $this->assign : array());
        }else{
            throw new \Exception('Not Find View File : '.$file_path);
        }
   }
   
   
   /**
     * 获取提交数据
     * @param string $method 请求方式
     * @param string $name 键名
     * @param string $default 默认值
     * @param string $func 处理值的回调函数
     * @return array
     */
    final protected function getPostDatas($method = '',$name='', $default = '', $func = ''){

        $fromData = array();
        $methods = !empty($method) ? strtoupper($method):$method;
        switch ($methods){
            case "GET":
                $fromData = $_GET; //$_GET;
                break;
            case "POST":
                $fromData = $_POST; //$_POST;
                break;
            default:
                parse_str(file_get_contents('php://input'), $fromData);
                break;
        }
    
        if (!empty($name)) {
            $fromData = array_key_exists($name, $fromData) && !empty($fromData[$name]) ? $fromData[$name] : $default;
            return !empty($func) && $fromData ? call_user_func($func, $fromData) : $fromData ;
        }
        return $fromData;
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
        return die(json_encode($out_data,JSON_UNESCAPED_UNICODE));
    }
    
}
