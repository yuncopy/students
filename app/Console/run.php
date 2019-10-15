<?php

// 检测PHP环境
if(version_compare(PHP_VERSION,'7.1.0','<'))  die('require PHP > 7.1.0 !');

date_default_timezone_set('PRC');
ini_set('display_errors', 'On');
define("APP_PATH",  realpath(dirname(__FILE__) . '/../../')); /* 指向public的上一级 */
require APP_PATH.'/vendor/autoload.php'; // Autoload 自动载入
require APP_PATH.'/app/bootstrap.php'; // 启动器
require APP_PATH.'/config/console.php'; // 路由配置、开始处理
