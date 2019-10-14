<?php

/**
 * @author jackin.chen
 * @email yuncopy@sina.com
 * @time 2019.10.13
 *
 * 安装向导
 *
 */

// 编码
header('Content-type:text/html;charset=utf-8');
date_default_timezone_set("PRC");
require_once './behavior.class.php';

class Install{

    public $assign = []; //分配变量

    CONST INSTALL_HTML = [
        'agree'=>'./agree.html',//同意协议页面
        'env'=>'./env.html',//同意协议页面
        'create'=>'./create.html',//创建数据库页面
        'complete'=>'./success.html',//完成页面
        'error'=>'./error.html',//创建数据库页面
    ];
    CONST MESSAGE = [
        'agree'=>['msg'=>'协议阅读'],
        'env'=>['msg'=>'环境检测'],
        'create'=>['msg'=>'数据信息填写'],
        'complete'=>['msg'=>'安装成功'],
        'delete'=>['msg'=>'重新安装'],
    ];

    public $_ac = '';

    public function __construct( $_param = 'c')
    {
        $this->_ac = $_param;
    }

    /**
     * 分配变量
     *
     * @author jackin.chen
     * @time 2019-10-13 13:37
     *
     * @para $data
     * @return  bool
     */
    public function assign($data)
    {
        if(is_array($data)){
            $this->assign = array_merge($this->assign,$data); // 合并数组
        }
        return false;
    }

    /**
     *加载模版
     *
     * @author jackin.chen
     * @time 2019-10-13 13:40
     *
     * @param $file_path
     * @return  bool
     */
    public function display($file_path){
        if(is_file($file_path) && file_exists($file_path)){
            extract($this->assign);
            include_once $file_path;
        }
        return false;
    }

    /**
     * 检测是否安装过
     *
     * @author jackin.chen
     * @time 2019-10-13 12:47
     *
     * @return $this
     */
    public function installed(){
        if(file_exists('./install.lock'))
        {
            $this->assign([
                'title'=>'您已经安装过该系统',
                'content'=>'重新安装需要先删除./install/install.lock 文件',
                'host'=>'../'
            ]);
            exit($this->display(self::INSTALL_HTML['error']));
        }
        return $this;
    }




    /**
     * 同意协议页面
     *
     * @author jackin.chen
     * @time 2019-10-13 13:02
     *
     * @param $_a
     * @param $_v
     * @return $this
     */
    public function agreement($_a,$_v){

        if($_a == $_v || !$_a){
            new behavior(self::MESSAGE[$_v]);
            $this->assign([
                'agree'=>'./index.php?'.$this->_ac.'=env',
                'active'=>'1'
            ]);
            exit($this->display(self::INSTALL_HTML[$_v]));
        }
        return $this;
    }

    /**
     * 环境检测页面
     *
     * @author jackin.chen
     * @time 2019-10-13 13:13
     *
     * @param $_a
     * @param $_v
     * @return $this
     */
    public function environment($_a,$_v){
        if($_a == $_v){
            new behavior(self::MESSAGE[$_v]);
            $php_version=explode('.', PHP_VERSION);
            $php_cla = '';
            if(($php_version['0']>=7) || ($php_version['0']>=5 && $php_version['1']>=4)){
                 $php_cla = 'yes';
            }
            $php_can = 'N';
            if (($php_version['0']>=7) || ($php_version['0']>=5 && $php_version['1']>=4)){
                $php_can = 'Y';
            }


            $writable = function ($path){
                return is_writable(realpath($path));
            };

            $realpath = function ($path){
                $APP = realpath('../../').'/';
                $APP_PATH = realpath($path);
                return str_replace($APP,'',$APP_PATH);
            };

            $php_env = [
                ['name'=>'操作系统','low'=>'不限','cur'=>php_uname('s'),'cla'=>'yes','can'=>'Y'],
                ['name'=>'PHP版本','low'=>'7.1','cur'=>PHP_VERSION,'cla'=>$php_cla,'can'=>$php_can],
            ];

            $writableData =[
                ['name'=>$realpath('./'),'low'=>'可写',
                    'cur'=>$writable('../') ? '可写' : '不可写' ,
                    'cla'=>$writable('../') ? 'yes' : '' ,
                    'can'=>$writable('../') ? 'Y' : 'N'
                ],
                ['name'=>$realpath('../../config'),'low'=>'可写',
                    'cur'=>$writable('../../config') ? '可写' : '不可写' ,
                    'cla'=>$writable('../../config') ? 'yes' : '' ,
                    'can'=>$writable('../../config') ? 'Y' : 'N'
                ],
                ['name'=>$realpath('../../storage'),'low'=>'可写',
                    'cur'=>$writable('../../storage') ? '可写' : '不可写' ,
                    'cla'=>$writable('../../storage') ? 'yes' : '' ,
                    'can'=>$writable('../../storage') ? 'Y' : 'N'
                ],
                ['name'=>$realpath('../../storage/cache'),'low'=>'可写',
                    'cur'=>$writable('../../storage/cache') ? '可写' : '不可写' ,
                    'cla'=>$writable('../../storage/cache') ? 'yes' : '' ,
                    'can'=>$writable('../../storage/cache') ? 'Y' : 'N'
                ],
                ['name'=>$realpath('../../storage/log'),'low'=>'可写',
                    'cur'=>$writable('../../storage/log') ? '可写' : '不可写' ,
                    'cla'=>$writable('../../storage/log') ? 'yes' : '' ,
                    'can'=>$writable('../../storage/log') ? 'Y' : 'N'
                ],
                ['name'=>$realpath('../../storage/upload'),'low'=>'可写',
                    'cur'=>$writable('../../storage/upload') ? '可写' : '不可写' ,
                    'cla'=>$writable('../../storage/upload') ? 'yes' : '' ,
                    'can'=>$writable('../../storage/upload') ? 'Y' : 'N'
                ],
            ];

            $this->assign([
                'create'=>'./index.php?'.$this->_ac.'=create',
                'prev'=>'./index.php?'.$this->_ac.'=agree',
                'active'=>'2',
                'len'=>count($writableData ) + count($php_env),
                'php_env'=>$php_env,
                'writable'=>$writableData,

            ]);
            exit($this->display(self::INSTALL_HTML[$_v]));
        }
        return $this;
    }

    /**
     * 创建数据库页面
     *
     * @author jackin.chen
     * @time 2019-10-14 10:43
     *
     * @param $_a
     * @param $_v
     * @return $this
     */
    public function create($_a,$_v){

        if($_a == $_v){
            new behavior(self::MESSAGE[$_v]);
            $this->assign([
                'active'=>'3',
                'prev'=>'./index.php?'.$this->_ac.'=env',
                'action'=>'./index.php?'.$this->_ac.'=success',
            ]);
            exit($this->display(self::INSTALL_HTML[$_v]));
        }
        return $this;
    }


    /**
     * 执行导入SQL文件
     *
     * @author jackin.chen
     * @time 2019-10-14 14:12
     *
     * @param $_a
     * @param $_v
     * @return $this
     */
    public function success($_a,$_v){

        if($_a == $_v ){

            // 判断是否为post
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                $data = [];
                foreach ($_POST as $name=> $item){
                    $data[$name] = stripslashes(trim($item));
                }


                $JSON_DIE = function ($data){
                    die(json_encode($data,JSON_UNESCAPED_UNICODE));
                };

                // 连接数据库
                $link=@new mysqli("{$data['DB_HOST']}:{$data['DB_PORT']}",$data['DB_USER'],$data['DB_PWD']);

                // 获取错误信息
                $error=$link->connect_error;
                if (!is_null($error)) {
                    $error=addslashes($error); // 转义防止和alert中的引号冲突
                    new behavior(array('msg'=>'数据库连接失败['.$error.']'));    // 数据库连接失败上报
                    $JSON_DIE(['msg'=>'数据库链接失败:'.stripslashes($error),'status'=>400]);
                }

                // 设置字符集
                $link->query("SET NAMES 'utf8'");

                // 数据库版本校验
                if($link->server_info < 5.0)
                {
                    // 数据库版本过低上报
                    new behavior(array('msg'=>'数据库版本过低['.$link->server_info.']', 'mysql_version'=>$link->server_info));

                    $JSON_DIE(['msg'=>'请将您的MySQL升级到5.0以上:'.stripslashes($error),'status'=>400]);

                }
                $mysql_ver = $link->server_info;

                // 创建数据库并选中
                if(!$link->select_db($data['DB_NAME'])){
                    $create_sql='CREATE DATABASE IF NOT EXISTS '.$data['DB_NAME'].' DEFAULT CHARACTER SET utf8;';
                    if(!$link->query($create_sql))
                    {
                        // 数据库创建失败上报
                        new behavior(array('msg'=>'创建数据库失败', 'mysql_version'=>$mysql_ver));
                        $JSON_DIE(['msg'=>'创建数据库失败','status'=>400]);
                    }
                    $link->select_db($data['DB_NAME']);
                }
                // 导入sql数据并创建表
                $sql_str=file_get_contents('./balecms.sql');
                $sql_array=preg_split("/;[\r\n]+/", str_replace('sc_',$data['DB_PREFIX'],$sql_str));
                $success = 0;
                $failure = 0;
                if($sql_array){
                    foreach ($sql_array as $k => $v) {
                        if (!empty($v)) {
                            if($link->query($v))
                            {
                                $success++;
                            } else {
                                $failure++;
                            }
                        }
                    }
                    $link->close();
                }

                // 数据表创建上报
                new behavior(array('msg'=>'运行sql[成功'.$success.', 失败'.$failure.']', 'mysql_version'=>$mysql_ver));

                $date = date('Y-m-d m:i:s');
                // 配置文件信息处理
                $db_str=<<<php
<?php

/**
 * 数据库配置信息-自动安装生成
 * @author   jackin.chen
 * @version  0.0.1
 * @datetime  {$date}
 */
return array(
    // 数据库配置信息
    'default'=>[
        'driver'    => 'mysql',
        'host'      => '{$data['DB_HOST']}',  // 服务器地址
        'database'  => '{$data['DB_NAME']}',  // 数据库名
        'username'  => '{$data['DB_USER']}',  // 用户名
        'password'  => '{$data['DB_PWD']}',   // 密码
        'port'      => {$data['DB_PORT']},    // 端口
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '{$data['DB_PREFIX']}', // 数据库表前缀 
    ]
);
?>
php;
                // 创建数据库链接配置文件,master,develop,test,debug 分别都更新，core模式没更改

                $config = realpath('../../config/database.php');

                @file_put_contents($config, $db_str);

                // 安装完成上报
                new behavior(array('msg'=>'安装完成', 'mysql_version'=>$mysql_ver));

                $JSON_DIE(['msg'=>'./index.php?'.$this->_ac.'=complete','status'=>200]);
            }


            //显示创建界面
            new behavior(self::MESSAGE[$_v]);
            $this->assign([
                'active'=>'3',
                'prev'=>'./index.php?'.$this->_ac.'=env',
                'action'=>'./index.php?'.$this->_ac.'=success',
            ]);
            exit($this->display(self::INSTALL_HTML[$_v]));
        }
        return $this;
    }


    /**
     * 显示安装成功信息
     *
     * @author jackin.chen
     * @time 2019-10-14 14:51
     *
     * @param $_a
     * @param $_v
     * @return $this
     */
    public function complete($_a,$_v){

        if($_a == $_v){

            new behavior(self::MESSAGE[$_v]);

            @touch('./install.lock');

            $this->assign([
                'active'=>'4',
                'name'=>'admin',
                'pass'=>'1111111',
                'admin'=>'/login/index.html',
                'home'=>'/'
            ]);
            exit($this->display(self::INSTALL_HTML[$_v]));
        }
        return $this;
    }



}

// 参数
$_param = '_a';
$_a = isset($_GET[$_param]) ? trim($_GET[$_param]) : '';
(new Install($_param))
    ->installed()
    ->agreement($_a,'agree')
    ->environment($_a,'env')
    ->create($_a,'create')
    ->success($_a,'success')
    ->complete($_a,'complete');


