<?php

use App\Library\Session;
use App\Service\WebsiteService;
/**
 * 2017年11月21日14:22:43
 * Angela 
 * 功能 ： 定义公共函数
 */


/**
 * 创建密码
 * @pass 需要加密原始密码
 */
if(!function_exists('pass_hash')){
    function pass_hash($pass){
        return md5(trim($pass));
    }
}

/**
 * 校验密码
 * @pass  需要校验的密码  string 
 * @res   用户信息   array
 */
if(!function_exists('pass_verify')){
    function pass_verify($pass,$res){
        $username = trim($res['username']);
        $user_pass = md5($username.$pass);
        $passwd = trim($res['passwd']);

        //var_dump($user_pass,$passwd);

        if ($passwd == $user_pass){
            return true;
        }
        return false;
    }
}


if (!function_exists('Config')) {

    /**
     * 2017年11月21日16:49:40
     * Angela 
     * 获取配置文件信息
     * @param null $name  Config('system.NetType');
     * @return string
     */
    function Config($name) {
        $array_conf = explode('.', $name);
        $file_name = array_shift($array_conf);
        $config_path = __CONFIG__ . "{$file_name}.php";
        if (is_file($config_path) && file_exists($config_path)) {
            $conf = include ($config_path);  // 包含文件
            $info = $conf;
            foreach ($array_conf as $v) {
                if (isset($info[$v])) {
                    $info = $info[$v];  // 重新更新变量值，因为循环程序没有结束，可以操作变量值
                }
            }
            return $info;
        }
    }
}


if (!function_exists('p')) {

    /**
     * 调试打印操作
     */
    function p($data, $exit = true) {
        if (is_string($data)) {
            die($data);
        } else if (is_array($data)) {
            echo '<pre>';
            print_r($data);
        } else {
            var_dump($data);
        }
        $exit === true ? exit() : false;
    }

}


if (!function_exists('redirect')) {

    /**
     * 重定向浏览器到指定的 URL   
     * www.jbxue.com
     * @param string $url 要重定向的 url   
     * @param int $delay 等待多少秒以后跳转   
     * @param bool $js 指示是否返回用于跳转的 JavaScript 代码   
     * @param bool $jsWrapped 指示返回 JavaScript 代码时是否使用 <script type="text/javascript"></script>
      标签进行包装
     * @param bool $return 指示是否返回生成的 JavaScript 代码   
     */
    function redirect($url, $delay = 0, $js = false, $jsWrapped = true, $return = false) {
        $delay = (int) $delay;
        $url = siteURL($url);  // 处理全路径
        if (!$js) {
            if (headers_sent() || $delay > 0) {
                echo '<html><head><meta http-equiv = "refresh" content = "' . $delay . ";URL=" . $url . '" /></head></html >';
                exit;
            } else {
                header("Location: {$url}");
                exit;
            }
        }
        $out = '';
        if ($jsWrapped) {
            $out .= '<script  type="text/javascript">';
        }
        $url = rawurlencode($url);
        if ($delay > 0) {
            $out .= "window.setTimeOut(function () { document.location='{$url}'; }, {$delay});";
        } else {
            $out .= "document.location='{$url}';";
        }
        if ($jsWrapped) {
            $out .= '</script>';
        }

        if ($return) {
            return $out;
        }
        echo $out;
        exit;
    }

}
if (!function_exists('siteURL')) {

    /**
     * 处理全路径
     */
    function siteURL($url) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'] . '/';
        return $protocol . $domainName . ltrim($url, '/');
    }

}

if (!function_exists('ScienceNumToString')) {

    /**
     * [ScienceNumToString 科学数字转换成原始的数字]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-04-06T17:21:51+0800
     * @param    [int]   $num [科学数字]
     * @return   [string]     [数据原始的值]
     */
    function ScienceNumToString($num) {
        if (stripos($num, 'e') === false)
            return $num;

        // 出现科学计数法，还原成字符串 
        $num = trim(preg_replace('/[=\'"]/', '', $num, 1), '"');
        $result = '';
        while ($num > 0) {
            $v = $num - floor($num / 10) * 10;
            $num = floor($num / 10);
            $result = $v . $result;
        }
        return $result;
    }

}

if (!function_exists('downLoadChunked')) {

    function downLoadChunked($filename, $retbytes = TRUE) {
        $buffer = '';
        $cnt = 0;
        // $handle = fopen($filename, 'rb');
        $handle = fopen($filename, 'rb');
        if ($handle === false) {
            return false;
        }
        while (!feof($handle)) {
            $buffer = fread($handle, 1024 * 1024); // Size (in bytes) of tiles chunk
            echo $buffer;
            ob_flush();
            flush();
            if ($retbytes) {
                $cnt += strlen($buffer);
            }
        }
        $status = fclose($handle);
        if ($retbytes && $status) {
            return $cnt; // return num. bytes delivered like readfile() does.
        }
        return $status;
    }

}

if (!function_exists('rand_string')) {
    /**
     * [GetNumberCode 随机数生成生成]
     * @param  	 [int] $len [生成位数]
     * @return 	 [int]         [生成的随机数]
     */
    function rand_string($len, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') {
        $string = '';
        for ($i = 0; $i < $len; $i++) {
            $pos = rand(0, strlen($chars) - 1);
            $string .= $chars{$pos};
        }
        return $string;
    }
}


if (!function_exists('arraySort')) {
/**
     * @desc arraySort php二维数组排序 按照指定的key 对数组进行排序
     * @param array $arr 将要排序的数组
     * @param string $keys 指定排序的key
     * @param string $type 排序类型 asc | desc
     * @return array
     */
    function arraySort($arr, $keys, $type = 'asc') {
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v){
            $keysvalue[$k] = $v[$keys];
        }
        $type == 'asc' ? asort($keysvalue) : arsort($keysvalue);
        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
           $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }
}   
    
    
if (!function_exists('make_to_tree')) {
     /*
     * 二数组形成树状结构
     * @param $arr 操作数组
     * @param $parent_id 顶级父ID
     * @param $parent_name 顶级父字段名
     * @param $primary_key 主键
     * return void
     */
    function make_to_tree($arr, $parent_id = 0, $parent_name = "pid", $primary_key = "id") {
        $new_arr = array();
        foreach ($arr as $k => $v) {
            if ($v[$parent_name] == $parent_id) {
                $new_arr[] = $v;
                unset($arr[$k]);
            }
        }
        foreach ($new_arr as &$a) {
            $a['children'] = make_to_tree($arr, $a[$primary_key]);
        }
        return $new_arr;
    }
}


if (!function_exists('make_tree_with_namepre')) {
    function make_tree_with_namepre($arrs)
    {
        $arr = make_to_tree($arrs);
        if (!function_exists('add_namepre1')) {
            function add_namepre1($arr, $prestr='') {
                $new_arr = array();
                foreach ($arr as $v) {
                    if ($prestr) {
                        if ($v == end($arr)) {
                            $v['name'] = $prestr.'└─ '.$v['name'];
                        } else {
                            $v['name'] = $prestr.'├─ '.$v['name'];
                        }
                    }

                    if ($prestr == '') {
                        $prestr_for_children = '　 ';
                    } else {
                        if ($v == end($arr)) {
                            $prestr_for_children = $prestr.'　　 ';
                        } else {
                            $prestr_for_children = $prestr.'│　 ';
                        }
                    }
                    $v['children'] = add_namepre1($v['children'], $prestr_for_children);

                    $new_arr[] = $v;
                }
                return $new_arr;
            }
        }
        return add_namepre1($arr);
    }
}
 

if (!function_exists('make_option_tree_for_select')) {
    /**
     * @param $arr
     * @param int $depth，当$depth为0的时候表示不限制深度
     * @return string
     */
    function make_option_tree_for_select($arrs, $depth=0)
    {
        $arr = make_tree_with_namepre($arrs);
        if (!function_exists('make_options1')) {
            function make_options1($arr, $depth, $recursion_count=0, $ancestor_ids='') {
                $recursion_count++;
                $str = '';
                foreach ($arr as $v) {
                    $disabled='';
                    if ($v['pid'] == 0) {
                        $recursion_count = 1;
                        //$disabled = !empty($v['children']) ? 'disabled = disabled' : '';
                    }
                    $str .= "<option value='".$v['id']."' {$disabled}  data-depth='{$recursion_count}' data-ancestor_ids='".ltrim($ancestor_ids,',')."'>{$v['name']}</option>";
                    
                    if ($depth==0 || $recursion_count<$depth) {
                        $str .= make_options1($v['children'], $depth, $recursion_count, $ancestor_ids.','.$v['id']);
                    }
                }
                return $str;
            }
        }
        return make_options1($arr, $depth);
    }
}

// 将树状转化为列表       
if(!function_exists('make_tree2list')){
    function make_tree2list($arr){
        $new_arr = array();
        foreach($arr as $k => $v){
            $new_arr[] = $v; 
            if(is_array($v['children']) && !empty($v['children'])){
               $bb = make_tree2list($v['children']);
               $new_arr = array_merge($new_arr,  $bb);
            }
        }
        return $new_arr;
    }
}


/**
 * 获取数据表字段
 */
if(!function_exists('getTableColumns')){
    // 获取表字段
    function getTableColumns(string $tableName){
        if($tableName){
            $capsule = $GLOBALS['capsule'];
            $schema = $capsule::schema();
            $columns = $schema->getColumnListing($tableName); 
            return $columns;
        }
    } 
}


/**
 * 权限控制前端是否展示
 */
if(!function_exists('W')){
    // 是否隐藏页面中数据
    // 页面中调用  <h5  {{W(1)}} >登录信息</h5>
    function W($permission_id){
        if($permission_id){
            $permission_str = Session::get(__RPID__);
            $permission_array = explode(",", $permission_str);
            if(!in_array($permission_id, $permission_array)){
               return  "style=display:none;";  // 隐藏数据
            }
            //return  "style=display:none;{$permission_str}||{$permission_id}";  // 调试代码
        }
    } 
}

// 获取网站配置信息
if (!function_exists('website')) {
    function  website($key=null){
        try{
            $website = WebsiteService::getWebsite($key);
            $data  = array_column($website, 'value','name');
            return is_null($key) ? $data : $website['value'];
        } catch ( \Illuminate\Database\QueryException $e){   
            return false;
        }
    }
}

//===============请求方法================
if (!function_exists('IsPost')) {

    /**
     * php判断当前请求是post还是get
     */
    function IsPost() {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'POST');
    }

}

if (!function_exists('isMethod')) {

    /**
     * 获取请求方法名
     */
    function isMethod($action) {
        switch (strtoupper($action)) {
            case 'GET':
                return $_SERVER['REQUEST_METHOD'] == 'GET';
            case 'POST':
                return $_SERVER['REQUEST_METHOD'] == 'POST' || !empty($this->items['POST']);
            case 'DELETE':
                return $_SERVER['REQUEST_METHOD'] == 'DELETE' ?: (isset($_POST['_method']) && $_POST['_method'] == 'DELETE');
            case 'PUT':
                return $_SERVER['REQUEST_METHOD'] == 'PUT' ?: (isset($_POST['_method']) && $_POST['_method'] == 'PUT');
            case 'AJAX':
                return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            case 'wechat':
                return isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
            case 'mobile':
                return isMobile();
        }
    }

}

if (!function_exists('isMobile')) {

    /**
     * 判断是否为手机端
     */
    function isMobile() {
        //微信客户端检测
        if ($this->isWeChat()) {
            return true;
        }
        if (!empty($_GET['_mobile'])) {
            return true;
        }
        if (!isset($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }
        $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
        $mobile_browser = '0';
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $mobile_browser++;
        }
        if ((isset($_SERVER['HTTP_ACCEPT'])) and ( strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false)) {
            $mobile_browser++;
        }
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            $mobile_browser++;
        }
        if (isset($_SERVER['HTTP_PROFILE'])) {
            $mobile_browser++;
        }
        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = [
            'w3c ',
            'acs-',
            'alav',
            'alca',
            'amoi',
            'audi',
            'avan',
            'benq',
            'bird',
            'blac',
            'blaz',
            'brew',
            'cell',
            'cldc',
            'cmd-',
            'dang',
            'doco',
            'eric',
            'hipt',
            'inno',
            'ipaq',
            'java',
            'jigs',
            'kddi',
            'keji',
            'leno',
            'lg-c',
            'lg-d',
            'lg-g',
            'lge-',
            'maui',
            'maxo',
            'midp',
            'mits',
            'mmef',
            'mobi',
            'mot-',
            'moto',
            'mwbp',
            'nec-',
            'newt',
            'noki',
            'oper',
            'palm',
            'pana',
            'pant',
            'phil',
            'play',
            'port',
            'prox',
            'qwap',
            'sage',
            'sams',
            'sany',
            'sch-',
            'sec-',
            'send',
            'seri',
            'sgh-',
            'shar',
            'sie-',
            'siem',
            'smal',
            'smar',
            'sony',
            'sph-',
            'symb',
            't-mo',
            'teli',
            'tim-',
            'tosh',
            'tsm-',
            'upg1',
            'upsi',
            'vk-v',
            'voda',
            'wap-',
            'wapa',
            'wapi',
            'wapp',
            'wapr',
            'webc',
            'winw',
            'winw',
            'xda',
            'xda-',
        ];
        if (in_array($mobile_ua, $mobile_agents)) {
            $mobile_browser++;
        }
        if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false) {
            $mobile_browser++;
        }
        // Pre-final check to reset everything if the user is on Windows
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false) {
            $mobile_browser = 0;
        }
        // But WP7 is also Windows, with a slightly different characteristic
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false) {
            $mobile_browser++;
        }
        if ($mobile_browser > 0) {
            return true;
        } else {
            return false;
        }
    }

}



