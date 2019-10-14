<?php

/**
 * 行为记录
 */
class behavior
{
    // 上报数据 URL
    private $report = ''; //http://www.bale.me/report/innstall.php

    /**
     * 开始收集数据
     *
     * behavior constructor.
     * @param array $param
     */
	public function __construct($param = array())
	{
		// 数据列表
		$data = array(
				'user'			=>	$this->GetUserCookie(),
				'host'			=>	$this->GetUrl('host'),
				'server_port'	=>	$this->GetServerPort(),
				'server_ip'		=>	$this->GetServerIP(),
				'url'			=>	$this->GetUrl('url'),
				'request_url'	=>	$this->GetUrl('request'),
				'source_url'	=>	$this->GetSourceUrl(),
				'client_ip'		=>	$this->GetClientIP(),
				'os'			=>	$this->GetOs(),
				'browser'		=>	$this->GetBrowser(),
				'method'		=>	$this->GetMethod(),
				'scheme'		=>	$this->GetScheme(),
				'version'		=>	$this->GetHttpVersion(),
				'client'		=>	$this->GetClinet(),
				'php_os'		=>	PHP_OS,
				'php_version'	=>	PHP_VERSION,
				'php_sapi_name'	=>	php_sapi_name(),
				'client_date'	=>	date('Y-m-d H:i:s'),
				'ymd'			=>	date('Ymd'),
				'ver'			=>	'2.3.1',
			);

		// 描述信息
		if(!empty($param['msg']))
		{
			$data['msg'] = $param['msg'];
		}

		// mysql版本
		if(!empty($param['mysql_version']))
		{
			$data['mysql_version'] = $param['mysql_version'];
		}


		if($this->report){
            if(function_exists('curl_init'))
            {
                $this->CurlPost($this->report, $data);
            } else {
                $this->Fsockopen_Post($this->report, $data);
            }
        }
	}

    /**
     * 发送的post数据
     *
     * @author jackin.chen
     * @time 2019-10-14 15:00
     *
     * @param $url
     * @param $post
     * @return bool|string
     */
	private function CurlPost($url, $post)
	{
		$options = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER         => false,
				CURLOPT_POST           => true,
				CURLOPT_POSTFIELDS     => $post,
			);

		$ch = curl_init($url);
		curl_setopt_array($ch, $options);

		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

    /**
     * Post fsockopen方式
     *
     * @author jackin.chen
     * @time 2019-10-14 15:00
     *
     * @param $url
     * @param string $data
     * @return string
     */
	private function Fsockopen_Post($url, $data = '')
	{
	    $row = parse_url($url);
	    $host = $row['host'];
	    $port = isset($row['port']) ? $row['port'] : 80;
	    $file = $row['path'];
	    $post = '';
	    while (list($k,$v) = each($data)) 
	    {
	        if(isset($k) && isset($v)) $post .= rawurlencode($k)."=".rawurlencode($v)."&"; //转URL标准码
	    }
	    $post = substr( $post , 0 , -1 );
	    $len = strlen($post);
	    $fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
	    if (!$fp) {
	        return "$errstr ($errno)\n";
	    } else {
	        $receive = '';
	        $out = "POST $file HTTP/1.0\r\n";
	        $out .= "Host: $host\r\n";
	        $out .= "Content-type: application/x-www-form-urlencoded\r\n";
	        $out .= "Connection: Close\r\n";
	        $out .= "Content-Length: $len\r\n\r\n";
	        $out .= $post;    
	        fwrite($fp, $out);
	        while (!feof($fp)) {
	          $receive .= fgets($fp, 128);
	        }
	        fclose($fp);
	        $receive = explode("\r\n\r\n",$receive);
	        unset($receive[0]);
	        return implode("",$receive);
	    }
	}

    /**
     * http类型
     *
     * @author jackin.chen
     * @time 2019-10-14 15:01
     *
     * @return string
     */
	private function GetScheme()
	{
		return empty($_SERVER['HTTPS']) ? 'HTTP' : 'HTTPS';
	}

    /**
     * 客户端
     *
     * @author jackin.chen
     * @time 2019-10-14 15:01
     *
     * @return string
     */
	private function GetClinet()
	{
		return empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'];
	}

    /**
     * http版本
     *
     * @author jackin.chen
     * @time 2019-10-14 15:01
     *
     * @return mixed|string
     */
	private function GetHttpVersion()
	{
		return empty($_SERVER['SERVER_PROTOCOL']) ? '' : str_replace(array('HTTP/', 'HTTPS/'), '', $_SERVER['SERVER_PROTOCOL']);
	}

    /**
     * 请求类型
     *
     * @author jackin.chen
     * @time 2019-10-14 15:01
     *
     * @return string
     */
	private function GetMethod()
	{
		return empty($_SERVER['REQUEST_METHOD']) ? '' : $_SERVER['REQUEST_METHOD'];
	}

    /**
     * 用户操作系统
     *
     * @author jackin.chen
     * @time 2019-10-14 15:01
     *
     * @return string
     */
	private function GetOs()
	{  
		if(!empty($_SERVER['HTTP_USER_AGENT']))
		{  
			$os = $_SERVER['HTTP_USER_AGENT'];  
			if(preg_match('/win/i', $os))
			{  
				$os = 'Windows';
			} elseif (preg_match('/mac/i',$os))
			{
				$os = 'MAC';
			} elseif (preg_match('/linux/i', $os))
			{
				$os = 'Linux';
			} elseif (preg_match('/unix/i', $os))
			{
				$os = 'Unix';
			} elseif (preg_match('/bsd/i', $os))
			{  
				$os = 'BSD';
			} elseif (preg_match('/iphone/i', $os))
			{  
				$os = 'iPhone';
			} elseif (preg_match('/android/i', $os))
			{  
				$os = 'Android';
			} else {
				$os = 'Other';
			}
			return $os;
		}
		return 'unknown';
	}

    /**
     * 用户浏览器
     *
     * @author jackin.chen
     * @time 2019-10-14 15:01
     *
     * @return string
     */
	private function GetBrowser()
	{
		if(!empty($_SERVER['HTTP_USER_AGENT']))
		{
			$br = $_SERVER['HTTP_USER_AGENT'];
			if(preg_match('/MSIE/i', $br))
			{
				$br = 'MSIE';  
			} elseif(preg_match('/Firefox/i', $br))
			{  
				$br = 'Firefox';  
			} elseif(preg_match('/Chrome/i', $br))
			{  
				$br = 'Chrome';  
			} elseif(preg_match('/Safari/i', $br))
			{  
				$br = 'Safari';  
			} elseif (preg_match('/Opera/i', $br))
			{  
				$br = 'Opera';  
			} else {  
				$br = 'Other';  
			}  
				return $br;  
		}
		return 'unknown';
	}

    /**
     * 获取url地址
     *
     * @author jackin.chen
     * @time 2019-10-14 15:01
     *
     * @param string $type
     * @return string
     */
	private function GetUrl($type = 'url')
	{
		// 当前host
		$host = empty($_SERVER['HTTP_HOST']) ? '' : $_SERVER['HTTP_HOST'];
        $root = '';
		// 是否获取host
		if($type == 'host')
		{
			return $host;
		}

		// http类型
		$http = empty($_SERVER['HTTPS']) ? 'http' : 'https';

		// 根目录
		if(!empty($_SERVER['SCRIPT_NAME']))
		{
			$root = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/')+1);
		} else {
			if(!empty($_SERVER['PHP_SELF']))
			{
				$root = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')+1);
			}
		}

		// url 或 request
		if($type == 'url')
		{
			return $http.'://'.$host.$root;
		} else {
			if(!empty($_SERVER['REQUEST_URI']))
			{
				return $http.'://'.$host.$_SERVER['REQUEST_URI'];
			}
		}
	}

    /**
     * 获取服务器ip
     *
     * @author jackin.chen
     * @time 2019-10-14 15:02
     *
     * @return string
     */
	private function GetServerIP()
	{
		return empty($_SERVER['SERVER_ADDR']) ? '' : $_SERVER['SERVER_ADDR'];
	}

    /**
     * 获取当前web端口
     *
     * @author jackin.chen
     * @time 2019-10-14 15:02
     *
     * @return int
     */
	private function GetServerPort()
	{
		return empty($_SERVER['SERVER_PORT']) ? 80 : $_SERVER['SERVER_PORT'];
	}

    /**
     * 获取用户ip
     *
     * @author jackin.chen
     * @time 2019-10-14 15:02
     *
     * @param bool $long
     * @return array|false|string
     */
	function GetClientIP($long = false)
	{
		$onlineip = ''; 
		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown'))
		{ 
			$onlineip = getenv('HTTP_CLIENT_IP'); 
		} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown'))
		{ 
			$onlineip = getenv('HTTP_X_FORWARDED_FOR'); 
		} elseif(getenv('REMOTE_ADDR' ) && strcasecmp(getenv('REMOTE_ADDR'),'unknown'))
		{ 
			$onlineip = getenv('REMOTE_ADDR'); 
		} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'],'unknown'))
		{ 
			$onlineip = $_SERVER['REMOTE_ADDR']; 
		} 
		if($long)
		{
			$onlineip = sprintf("%u", ip2long($realip));
		}
		return $onlineip;
	}

    /**
     * 获取来源url地址
     *
     * @author jackin.chen
     * @time 2019-10-14 15:02
     *
     * @return string
     */
	private function GetSourceUrl()
	{
		return empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
	}

    /**
     * 获取用户cookieid
     *
     * @author jackin.chen
     * @time 2019-10-14 15:02
     *
     * @return false|string
     */
	private function GetUserCookie()
	{
		if(!empty($_COOKIE['behavior_user_cookie'])) return $_COOKIE['behavior_user_cookie'];

		$user_cookie = $this->GetUserNumberRand();
		setcookie('behavior_user_cookie', $user_cookie);
		return $user_cookie;
	}

    /**
     * 生成用户cookie编号
     *
     * @author jackin.chen
     * @time 2019-10-14 15:02
     *
     * @return false|string
     */
	private function GetUserNumberRand()
	{
		$str = date('YmdHis');
		for($i=0; $i<6; $i++) $str .= rand(0, 9);
		return $str;
	}
}
?>