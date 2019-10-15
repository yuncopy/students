<?php





if(php_sapi_name() == 'cli'){
    // Create Router instance  路由启动
    $router = new \Bramus\Router\Router();

    // 设置命名空间----从这里开始定义你的路由
    $router->setNamespace('App\Console\command');

    $router->cmd('index/show',"index@show");

    $router->run();



}
return false;