<?php

/**
 * 2017年11月21日14:58:02
 * Angela 
 * 功能:定义项目的路由文件
 */
/**
 * 项目自动加载使用composer
 * 1、文件夹名称和命名空间保持一致
 * 2、使用  method_exists($object, $method_name) 进行验证测试
 * 3、使用 mixed call_user_func_array ( callable $callback , array $param_arr ) 进行验证测试
 */
// In case one is using PHP 5.4's built-in server
$filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}
// Create Router instance  路由启动
$router = new \Bramus\Router\Router();
// $router->get('/(\d+)', 'App\Controller\HomeController@index');  // 测试成功

// Custom 404 自定义路由错误提示
$router->set404(function(){
    
    //return redirect('/login/error.html');  // 设置响应
    //throw new \Exception("Router 404 Not Found");
});
// Before Router Middleware 路由之前调用
$router->before('GET|POST', '/file/.*', function () {
    
    header('X-Powered-By: Angela/celcom');  // 设置响应
});

// 设置命名空间----从这里开始定义你的路由
$router->setNamespace('App\Controller');
$router->get('/(\d+)?(.html)?', 'HomeController@index');                  // 首页路由
$router->get('/category/(\d+).html', 'HomeController@category');        // 分类列表
$router->get('/contact.html', 'HomeController@contact');                // 联系列表
$router->get('/singlepage/(\d+).html', 'HomeController@singlepage');   // 查看单条详情
$router->get('/sorts/(\d+)[.html]?(/\d+)?.html', 'HomeController@sorts');

//$router->match('GET|POST', '/singlepage/(\d+).html', 'HomeController@singlepage'); //查看单条详情



// =====游戏应用======start=====
$router->get('/categorys(/\w+)?.html', 'GameController@category');        // 分类
$router->get('/top(/\w+)?.html', 'GameController@top');                // 排行
$router->get('/album.html', 'GameController@album');            // 专辑
$router->get('/detail/(\w+).html', 'GameController@detail');        // 查询
$router->get('/news(/\w+)?.html', 'GameController@news');
$router->get('/download/(\w+).html', 'GameController@download');        // 查询 
$router->get('/logout.html', 'HomeController@logout');   // 退出
$router->post('/celcom/msisdn.html', 'GameController@msisdn');   // 检查用户是否存在


// =====游戏应用======end=======

//=========后台文件管理====相关=======start======

$router->get('/file/index.html', 'FileController@index');               // 管理列表
$router->match('GET|POST','login/login.html', 'LoginController@login'); // 管理列表
$router->get('/login/logout.html', 'LoginController@logout');           // 管理列表
$router->get('/file/cate.html', 'FileController@cate');                 // 分类列表
$router->get('/file/list.html', 'FileController@lists');                // 游戏列表
$router->post('/file/getgame.html', 'FileController@getajaxlist');       // 游戏列表
$router->get('/file/add.html', 'FileController@add');                   // 游戏分类
$router->get('/file/addcate(/\d+)?.html', 'FileController@addcate');     // 游戏分类添加界面/编辑
$router->post('/file/docate.html', 'FileController@docate');     // 游戏分类添加界面/编辑
$router->get('/file/delcate(/\d+)?.html', 'FileController@delcate');     // 游戏分类删除
$router->get('/file/image.html', 'FileController@image');               // 文件管理
$router->post('/file/addgame.html', 'FileController@addgame');       // 添加游戏
$router->get('/file/delgame/(\d+).html', 'FileController@delgame'); // 删除游戏
$router->match('GET|POST', '/file/editgame/(\d+).html', 'FileController@editgame');  // 编辑游戏
$router->get('/file/system.html', 'FileController@system'); // 系统配置

$router->match('GET|POST','/file/sysuser.html', 'FileController@sysuser'); // 系统用户
$router->match('GET|POST','/file/addsysuser.html', 'FileController@addsysuser'); // 系统用户
$router->get('/file/deluser/(\d+).html', 'FileController@deluser'); // 系统用户删除
$router->get('/file/edituser/(\d+).html', 'FileController@addsysuser'); // 系统用户编辑

$router->get('/file/authority.html', 'FileController@authority'); // 权限管理
$router->post('/file/getauthority.html', 'FileController@getauthority'); // 获取权限数据
$router->match('GET|POST','/file/addauthority.html', 'FileController@addauthority'); // 添加权限
$router->get('/file/editauthority/(\d+).html', 'FileController@addauthority'); // 权限管理列表
$router->get('/file/delauthority/(\d+).html', 'FileController@delauthority'); // 权限管理列表
$router->match('GET|POST','/file/subdata.html', 'FileController@subdata');  


$router->get('/login/error.html', 'LoginController@error'); // 权限处理
//=========订阅====相关=======start======
//  /celcom/svid(/sessionid)  sessionid可选     celcom 订阅服务器  
$router->get('/celcom(/\w+)?(/\d+)?(/\w+)?','HomeController@celcom');
//            /callback/charged/1.00/+60132816305/895983
//            /callback/{status}/{amount}/{msisdn}/{$pwd}/{$spTransID}
$router->get('/callback/(\w+)/([a-z0-9.]+)/([a-z0-9+]+)/(\w+)?/(\w+)','HomeController@callback');  
//$router->get('/callback/(\w+)/([a-z0-9.]+)/([a-z0-9+]+)(/\w+)?','HomeController@callback');  // ip:port/callback/{status}/{amount}/{msisdn}/{$pwd}
$router->post('/celcom/login.html','HomeController@login');     // 登录账户
$router->post('/celcom/device.html','HomeController@setDevice'); // 设置设备标识
$router->post('/celcom/getpass.html','HomeController@getPass');  // 获取密码
$router->post('/celcom/unsub.html', 'HomeController@unsub');   // 检查用户是否存在


// Run it!
$router->run();  // 路由执行

