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
    try{  
        throw new \Exception("404 Not Found <small>Router</small>");  //抛出异常
    } catch (\Exception $e) {
        //获取异常信息
        if(isMethod('AJAX')){
            die(json_encode(['status'=>8899,'message'=>$e->getMessage()]));
        }else{
            //404 路由错误提示页面
            die('<iframe id="iframe" src="'. siteURL('/login/found.html').'" frameborder="0" height="100%" width="100%"></iframe>');  
        }
    }
});


// Before Router Middleware 路由之前调用
//$router->before('GET|POST', '/.*', function () {
    
//});

// 设置命名空间----从这里开始定义你的路由
$router->setNamespace('App\Controller');


// 域名访问页面
$router->get('/','LoginController@home');



$router->get('/api(/\w+)?.html', 'ApiController@process'); // 排除权限控制

$router->get('/api/add-class-time.html', 'ApiController@addClassTime'); // 排除权限控制


// 后台管理
$router->get('/home/index.html', 'HomeController@index');    //后台结构页面
$router->get('/home/admin.html', 'HomeController@admin');   // 后台首页页面

// 登录管理  （不涉及权限）
$router->get('/login/index.html', 'LoginController@index');  // 用户登录界面
$router->post('/login/login.html', 'LoginController@login');  // 执行用户登录操作
$router->get('/login/logout.html', 'LoginController@logout');  // 执行用户登录操作

$router->get('/login/error.html', 'LoginController@error');  // 错误提示页面
$router->get('/login/found.html', 'LoginController@found');   // 404 路由错误提示页面


// 学生管理
$router->get('/student/index.html', 'StudentController@index');   // 加载页面
$router->post('/student/getdata.html', 'StudentController@getData');   // 获取数据
$router->match('GET|POST','/student/adddata.html', 'StudentController@adddata'); // 添加操作
$router->post('/student/unique.html', 'StudentController@uniqueName'); // 检测唯一性
$router->get('/student/export.html', 'StudentController@studentExport'); // 导出学生信息
$router->post('/student/upload.html', 'StudentController@studentUpload'); // 上传EXCEL学生信息
$router->post('/student/import.html', 'StudentController@studentImport'); // 执行批量导入学生信息
$router->get('/student/repeat.html', 'StudentController@studentRepeat'); // 执行批量导出重复学生信息
$router->get('/student/del/(\d+).html', 'StudentController@stuendtDel'); //删除学生操作
$router->match('GET|POST','/student/update(/\d+)?.html', 'StudentController@stuendtUpdate'); //编辑学生操作
$router->post('/student/where.html', 'StudentController@whereData'); // 修改数据
$router->post('/student/process.html', 'StudentController@studentProcess'); // 执行批量导入学生信息进度


// 成绩管理
$router->get('/fraction/index.html', 'FractionController@index');   // 学生管理
$router->get('/fraction/student.html', 'FractionController@student');   // 学生成绩
$router->post('/fraction/getdata.html', 'FractionController@getData');   //获取数据
$router->match('GET|POST','/fraction/adddata(/\d+)?.html', 'FractionController@addData');   // 加载页面添加
$router->get('/fraction/del/(\d+).html', 'FractionController@delData'); //删除操作
$router->post('/fraction/update.html', 'FractionController@updateData'); // 修改数据



// 类别管理
//---学期管理
$router->get('/semester/index.html', 'SemesterController@index');   //加载页面
$router->post('/semester/getdata.html', 'SemesterController@getData');   //获取数据
$router->post('/semester/unique.html', 'SemesterController@uniqueName');   //检测唯一性
$router->post('/semester/adddata.html', 'SemesterController@addData'); // 新增数据
$router->get('/semester/del(/\d+)?.html', 'SemesterController@delData'); // 删除数据
$router->post('/semester/update.html', 'SemesterController@updateData'); // 修改数据
$router->get('/semester/cancelstart/(\d+)/(\d+).html', 'SemesterController@cancelStart'); // 注销或者开启
$router->get('/semester/desc(/\d+)?.html', 'SemesterController@descData'); // 降序数据
$router->get('/semester/asc(/\d+)?.html', 'SemesterController@ascData'); // 升序数据

//---课程相关  (星期，时间段，教室)
$router->get('/category/index.html', 'CategoryController@index');   //加载页面
$router->post('/category/unique.html', 'CategoryController@uniqueName');   //检测唯一性
$router->post('/category/update.html', 'CategoryController@updateData'); // 修改数据
$router->get('/category/del(/\d+)?.html', 'CategoryController@delData'); // 删除数据
$router->post('/category/adddata.html', 'CategoryController@addData'); // 新增数据
$router->post('/category/getparent.html', 'CategoryController@parentData'); // 新增数据


// 导师管理
$router->get('/tutor/index.html', 'TutorController@index');   //加载页面
$router->post('/tutor/getdata.html', 'TutorController@getData');   //获取数据
$router->post('/tutor/unique.html', 'TutorController@uniqueName');   //检测唯一性
$router->post('/tutor/adddata.html', 'TutorController@addData'); // 新增数据
$router->get('/tutor/del(/\d+)?.html', 'TutorController@tutorDel'); // 删除数据
$router->post('/tutor/update.html', 'TutorController@tutorUpdate'); // 修改数据
$router->get('/tutor/cancelstart/(\d+)/(\d+).html', 'TutorController@tutorCancelStart'); // 注销或者开启


//导师类型
$router->get('/tutor/class.html', 'TutorController@pageClass'); //页面加载
$router->post('/tutor/getclass.html', 'TutorController@getClassData'); //获取数据
$router->post('/tutor/classunique.html', 'TutorController@uniqueClassName'); //唯一性
$router->post('/tutor/addclassdata.html', 'TutorController@addClassData'); //唯一性
$router->post('/tutor/updateclassdata.html', 'TutorController@updateClassData'); //唯一性
$router->get('/tutor/delclassdata(/\d+)?.html', 'TutorController@delClassData'); // 删除数据



// 教师管理
$router->get('/teacher/index.html', 'TeacherController@index');   //加载页面
$router->post('/teacher/getdata.html', 'TeacherController@getData');   //获取数据
$router->post('/teacher/unique.html', 'TeacherController@uniqueName');   //检测唯一性
$router->post('/teacher/adddata.html', 'TeacherController@addData'); // 新增数据
$router->get('/teacher/del(/\d+)?.html', 'TeacherController@delData'); // 删除数据
$router->post('/teacher/update.html', 'TeacherController@updateData'); // 修改数据
$router->get('/teacher/cancelstart/(\d+)/(\d+).html', 'TeacherController@cancelStart'); // 注销或者开启


// 课程管理
$router->get('/subject/index.html', 'SubjectController@index');   //加载页面
$router->post('/subject/getdata.html', 'SubjectController@getData');   //获取数据
$router->post('/subject/unique.html', 'SubjectController@uniqueName');   //检测唯一性
$router->post('/subject/adddata.html', 'SubjectController@addData'); // 新增数据
$router->get('/subject/del(/\d+)?.html', 'SubjectController@delData'); // 删除数据
$router->post('/subject/update.html', 'SubjectController@updateData'); // 修改数据
$router->get('/subject/cancelstart/(\d+)/(\d+).html', 'SubjectController@cancelStart'); // 注销或者开启


//课程安排
$router->get('/course/index.html', 'CourseController@index');   //加载页面
$router->match('GET|POST','/course/adddata(/\d+)?.html', 'CourseController@addData'); //课程安排页面/操作
$router->post('/course/getdata.html','CourseController@getData');   //获取数据
$router->match('GET|POST','/course/update(/\d+)?.html', 'CourseController@updateData'); //课程安排编辑
$router->get('/course/cancelstart/(\d+)/(\d+).html', 'CourseController@cancelStart'); // 注销或者开启
$router->get('/course/del(/\d+)?.html', 'CourseController@delData'); // 删除数据


//选课管理
$router->get('/elective/index(/\d+)?.html', 'ElectiveController@index');   //加载页面
$router->post('/elective/getdata.html','ElectiveController@getData');   //获取数据
$router->get('/elective/select.html','ElectiveController@select');   //已选列表
$router->post('/elective/selectdata.html','ElectiveController@selectData');   //已选数据
$router->post('/elective/adddata.html','ElectiveController@addData');   //进行选课
$router->get('/elective/del(/\d+)?.html','ElectiveController@delData');   //取消选课
$router->post('/elective/notselected.html','ElectiveController@notSelectedData'); 



//开题管理
$router->get('/proposal/index(/\d+)?.html', 'ProposalController@index');   //加载页面
$router->post('/proposal/getdata.html', 'ProposalController@getData');   //获取数据
$router->post('/proposal/unique.html', 'ProposalController@uniqueName');   //检测唯一性
$router->post('/proposal/adddata.html', 'ProposalController@addData'); // 新增数据
$router->get('/proposal/del(/\d+)?.html', 'ProposalController@delData'); // 删除数据
$router->post('/proposal/update.html', 'ProposalController@updateData'); // 修改数据
$router->get('/proposal/cancelstart/(\d+)/(\d+).html', 'ProposalController@cancelStart'); // 注销或者开启



//答辩管理

$router->get('/defense/index(/\d+)?.html', 'DefenseController@index');   //加载页面
$router->post('/defense/getdata.html', 'DefenseController@getData');   //获取数据
$router->post('/defense/unique.html', 'DefenseController@uniqueName');   //检测唯一性
$router->post('/defense/adddata.html', 'DefenseController@addData'); // 新增数据
$router->get('/defense/del(/\d+)?.html', 'DefenseController@delData'); // 删除数据
$router->post('/defense/update.html', 'DefenseController@updateData'); // 修改数据
$router->get('/defense/cancelstart/(\d+)/(\d+).html', 'DefenseController@cancelStart'); // 注销或者开启


//用户管理
$router->get('/user/index.html', 'UserController@index');   //加载页面
$router->post('/user/getdata.html', 'UserController@getData');   //获取数据
$router->post('/user/unique.html', 'UserController@uniqueName');   //检测唯一性
$router->post('/user/adddata.html', 'UserController@addData'); // 新增数据
$router->get('/user/del(/\d+)?.html', 'UserController@delData'); // 删除数据
$router->get('/user/repass(/\d+)?.html', 'UserController@repassData'); // 删除数据
$router->post('/user/update.html', 'UserController@updateData'); // 修改数据
$router->get('/user/cancelstart/(\d+)/(\d+).html', 'UserController@cancelStart'); // 注销或者开启
$router->post('/user/pass.html', 'UserController@passData'); // 修改数据



// 角色管理
$router->get('/role/index.html', 'RoleController@index');   //加载页面
$router->post('/role/getdata.html', 'RoleController@getData');   //获取数据
$router->post('/role/unique.html', 'RoleController@uniqueName');   //检测唯一性
$router->post('/role/adddata.html', 'RoleController@addData'); // 新增数据
$router->get('/role/del(/\d+)?.html', 'RoleController@delData'); // 删除数据
$router->post('/role/update.html', 'RoleController@updateData'); // 修改数据
$router->get('/role/cancelstart/(\d+)/(\d+).html', 'RoleController@cancelStart'); // 注销或者开启
$router->match('GET|POST','/role/auth(/\d+)?.html', 'RoleController@authData'); //授权操作

// 菜单管理
$router->get('/menu/index.html', 'MenuController@index');   //加载页面
$router->post('/menu/getdata.html', 'MenuController@getData');   //获取数据
$router->post('/menu/unique.html', 'MenuController@uniqueName');   //检测唯一性
$router->post('/menu/adddata.html', 'MenuController@addData'); // 新增数据
$router->get('/menu/del(/\d+)?.html', 'MenuController@delData'); // 删除数据
$router->post('/menu/update.html', 'MenuController@updateData'); // 修改数据
$router->get('/menu/cancelstart/(\d+)/(\d+).html', 'MenuController@cancelStart'); // 注销或者开启
$router->get('/menu/fontawesome.html', 'MenuController@fontawesome');   //加载图标界面



// 权限管理
$router->get('/permission/index.html', 'PermissionController@index');   //加载页面
$router->post('/permission/getdata.html', 'PermissionController@getData');   //获取数据
$router->post('/permission/unique.html', 'PermissionController@uniqueName');   //检测唯一性
$router->post('/permission/adddata.html', 'PermissionController@addData'); // 新增数据
$router->get('/permission/del(/\d+)?.html', 'PermissionController@delData'); // 删除数据
$router->post('/permission/update.html', 'PermissionController@updateData'); // 修改数据
$router->get('/permission/cancelstart/(\d+)/(\d+).html', 'PermissionController@cancelStart'); // 注销或者开启



//网站管理
$router->get('/website/index.html', 'WebsiteController@index');   //加载页面
$router->post('/website/getdata.html', 'WebsiteController@getData');   //获取数据
$router->post('/website/adddata.html', 'WebsiteController@addData');   // 加载页面添加
$router->post('/website/unique.html', 'WebsiteController@uniqueName');   //检测唯一性
$router->get('/website/del/(\d+).html', 'WebsiteController@delData'); //删除操作
$router->post('/website/update.html', 'WebsiteController@updateData'); // 修改数据




//前台路由定义=====start=====不建议分开管理====在一个文件方便管理，防止重名，遵守所见必所得原则





// Run it!
$router->run();  // 路由执行

