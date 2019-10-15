<?php

date_default_timezone_set('PRC');
// 检测PHP环境
if(version_compare(PHP_VERSION,'7.1.0','<'))  die('require PHP > 7.1.0 !');


define("APP_PATH",  realpath(dirname(__FILE__) . '/../')); /* 指向public的上一级 */

// 检测是否是新安装
if(is_dir("./install") && !file_exists("./install/install.lock"))
{
    $url = '../install/index.php';
    exit(header('location:'.$url));
}

require APP_PATH.'/vendor/autoload.php'; // Autoload 自动载入
require APP_PATH.'/app/bootstrap.php'; // 启动器
require APP_PATH.'/config/routes.php'; // 路由配置、开始处理

