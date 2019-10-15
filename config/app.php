<?php
/**
 * 2017年11月21日15:02:28
 * Angela 
 * 功能： 定义项目全局配置
 */
defined("__DEBUG__")    or  define("__DEBUG__", true );                         // 调试是否开启
defined("__CONFIG__")   or  define("__CONFIG__",APP_PATH."/config/");           // 配置路径
defined("__VIEW__")     or  define("__VIEW__",APP_PATH."/app/View/");           // 视图目录
defined("__STROAGE__")  or  define("__STROAGE__",APP_PATH."/storage/");         // 业务存储路径
defined("__LOG__")      or  define("__LOG__",APP_PATH."/storage/log/");         // 日志存储路径
defined("__CACHE__")    or  define("__CACHE__",APP_PATH."/storage/cache/");     // 视图缓存存储路径 
defined("__UPLOAD__")   or  define("__UPLOAD__",APP_PATH."/storage/upload/");   // 上传文件存储路径

//系统配置
defined("__AUTH__")     or  define("__AUTH__", "auth");                     // 权限设置键
defined("__REID__")     or  define("__REID__", "relation_id_key");              // 关系ID键
defined("__RPID__")     or  define("__RPID__", "current_permission_key");       // 当前角色权限

//管理员配置

defined("__PASS__")     or  define("__PASS__", '111111');                // 新增用户统一密码，如果设置了，使用该密码，设置为false 则使用用户编号
defined("__SALT__")     or  define("__SALT__", date('Ymd'));                    // MD5 加盐
defined("__TEMPLATE__") or  define("__TEMPLATE__","/");    // 前台模板

defined("__STUDENT_STATUS__") or  define("__STUDENT_STATUS__",[
    ['id'=>1,'name'=>'在读'],
    ['id'=>2,'name'=>'开题'],
    ['id'=>3,'name'=>'结业'],
    ['id'=>4,'name'=>'毕业'],
    ['id'=>5,'name'=>'肄业'],
    ['id'=>6,'name'=>'退学']
]); //学生状态

defined("__STUDENT_GENDER__") or  define("__STUDENT_GENDER__",[
    ['id'=>1,'name'=>'女'],
    ['id'=>2,'name'=>'男'],
    ['id'=>3,'name'=>'保密']
]); //学生状态










//创建目录
$need_mkdir = [
    __STROAGE__,
    __LOG__,
    __CACHE__,
    __UPLOAD__
];
if($need_mkdir){
    foreach ($need_mkdir as $val){
        if(!file_exists($val)){
            @mkdir($val,0777,true);
        }
    } 
}
                 

