<?php
/**
 * @package HeavenMVC
 * @version 1.0 Action.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */
if (! defined ( 'IN_HEAVEN' )) {
	exit ();
}
class Core_Action extends Core_Base {
	
	protected static $view;
	/**
	 * ���캯��,���ڳ�ʼ�����л���.
	 * @access public
	 * @return mixed
	 */
	public function __construct() {
		$this->init ();
		self::$view = Core_View::getInstance ();
	}
	/**
	 * Ӧ�ó����ʼ��
	 * @access public
	 * @return void
	 */
	private function init() {
		error_reporting ( E_ALL ^ E_NOTICE );
		date_default_timezone_set ( HEAVEN_TIMEZONE );
		if (file_exists ( APP_PATH . '/Cache/Session' )) {
			session_save_path ( APP_PATH . '/Cache/Session' );
		}
		if (get_magic_quotes_runtime ()) {
			set_magic_quotes_runtime ( 0 );
		}
		if (get_magic_quotes_gpc ()) {
			$_COOKIE = $this->strip_slashes ( $_COOKIE );
		} else {
			$_POST = $this->add_slashes ( $_POST );
			$_GET = $this->add_slashes ( $_GET );
			$_SESSION = $this->add_slashes ( $_SESSION );
		}
		return true;
	}
	/**
	 * ģ�������ֵ
	 * @access public
	 * @param string or array $name
	 * @param string $value
	 */
	public function assign($key, $value = false) {
		return self::$view->assign ( $key, $value );
	}
	
	/**
	 * ���ģ������.
	 * @param string $filename ģ������
	 * @return string
	 */
	public function display($filename = false) {
		if (empty ( $filename )) {
			$filename = $GLOBALS ['site_info'] ['default_controller_method'];
		}
		return self::$view->display ( $filename );
	}
	
	/**
	 * ���û���ʱ��,0�����棬-1���û���,Ϊ��Ĭ��10��
	 * @access public
	 * @return void
	 * @param int $time
	 */
	public function set_cache_lifetime($time = 0) {
		self::$view->set_cache_lifetime ( $time );
	}
	
	/**
	 * �������൱��addslashes.
	 * @access public
	 * @param string $string 
	 * @return string
	 */
	protected function add_slashes($string) {
		if (is_array ( $string )) {
			foreach ( $string as $key => $value ) {
				$string [$key] = $this->add_slashes ( $value );
			}
			return $string;
		} else {
			return addslashes ( $string );
		}
	}
	
	/**
	 * �൱��stripslashes()
	 * @access public
	 * @param string $string 
	 * @return string
	 */
	protected function strip_slashes($string) {
		if (is_array ( $string )) {
			foreach ( $string as $key => $value ) {
				$string [$key] = $this->strip_slashes ( $value );
			}
			return $string;
		} else {
			return stripslashes ( $string );
		}
	}
	
	/**
	 * ��������������Ϣ
	 * @access public
	 * @return void
	 * @param string $class,string $method 
	 */
	public function error_write($class, $method) {
		echo $class . '.class.php�в����ڷ���' . $method . '<br/>���ȶ���';
	}
	
	/**
	 * ����û�������Ϣ
	 * @access public 
	 * @param unknown_type $string
	 * @return unknown
	 */
	public function halt($string) {
		return trigger_error ( $string, E_USER_ERROR );
	}
	
	/**
	 * ��ʾҳ����Ϣ,$go_url·��ӦΪ��վ��Ŀ¼.
	 * @param string $message 
	 * @param string $go_url  
	 * @param int $limit_time 
	 * @return string
	 */
	public function show_message($message, $go_operate = '', $go_url = false, $limit_time = false) {
		if (empty ( $message )) {
			return false;
		}
		if ($go_url) {
			if ($go_operate == 'back') {
				$limit_time = empty ( $limit_time ) ? 5000 : 1000 * $limit_time;
				$go_url = "javascript:history.go(-1);";
				$message .= "<a href=\"{$go_url}\" target=\"_self\">���������{$limit_time}�����ת,���������û��Ӧ,��������...</a>\n";
			} else {
				if ($limit_time) {
					$limit_time = empty ( $limit_time ) ? 1000 : 1000 * $limit_time;
					$go_url = str_replace ( array ("\n", "\r" ), '', $go_url );
					$message .= "<br/>\n<a href=\"{$go_url}\" target=\"_self\">���������{$limit_time}�����ת��{$go_url},���������û��Ӧ,��������...</a>\n";
				}
			}
			$message .= "<script type=\"text/javascript\">\nfunction heaven_redirect_url(url){location.href=url;}setTimeout(\"heaven_redirect_url('{$go_url}')\", {$limit_time});\n</script>\n";
		}
		echo $message;
		exit ();
	}
	
	/**
	 * ϵͳ��������������ݺ��� print_r().
	 * @param array $data  ����
	 * @param string $option  �Ƿ���var_dump()
	 * @return array
	 */
	public function debug_write($data, $option = false) {
		if ($option) {
			ob_start ();
			var_dump ( $data );
			$output = ob_get_clean ();
			$output = str_replace ( '"', '', $output );
			$output = preg_replace ( "/\]\=\>\n(\s+)/m", "] => ", $output );
			echo '<pre>', $output, '</pre>';
		} else {
			echo '<pre>';
			print_r ( $data );
			echo '</pre>';
		}
		
		exit ();
	}
	/**
     +----------------------------------------------------------
	 * ҳ����ת������������ҳ����ת����������ַ��:URL�ض���.
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @param string $url ��Ҫ��ת��URL
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function redirect($url) {
		//��������.
		if (empty ( $url )) {
			return false;
		}
		if (! headers_sent ()) {
			header ( "Location:" . $url );
			exit ();
		} else {
			echo '<script type="text/javascript">location.href="' . $url . '";</script>';
			exit ();
		}
	}
	/**
	 * �����Ի�����ת��Ϣ
	 */
	function js_alert($message = '', $url = '', $after = '') {
		$t2js = str_replace ( array ("\r", "\n" ), array ('', '\n' ), addslashes ( $message ) );
		$out = "<script language=\"javascript\" type=\"text/javascript\">\n";
		if (! empty ( $message )) {
			$out .= "alert(\"";
			$out .= str_replace ( "\\\\n", "\\n", $t2js );
			$out .= "\");\n";
		}
		if (! empty ( $after )) {
			$out .= $after . "\n";
		}
		if (! empty ( $url )) {
			$out .= "document.location.href=\"";
			$out .= $url;
			$out .= "\";\n";
		}
		$out .= "</script>";
		echo $out;
		exit ();
	}
	/**
	 * ��ȡ�ַ�����֧������
	 *
	 * @param string $str ��Ҫת�����ַ���
	 * @param int $start ��ʼλ��
	 * @param int $length ��ȡ����
	 * @param string $charset �����ʽ
	 * @param bool $suffix �ض���ʾ�ַ�
	 * @return string
	 */
	function msubstr_utf($str, $start = 0, $length, $charset = "utf-8", $suffix = false) {
		if (function_exists ( "mb_substr" ))
			return mb_substr ( $str, $start, $length, $charset );
		elseif (function_exists ( 'iconv_substr' )) {
			return iconv_substr ( $str, $start, $length, $charset );
		}
		$re ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all ( $re [$charset], $str, $match );
		$slice = join ( "", array_slice ( $match [0], $start, $length ) );
		if ($suffix)
			return $slice . "...";
		return $slice;
	}
	
	/**
	 * ��������ַ���
	 *
	 * @param int $len �ַ�������
	 * @param int $type 0��Ĭ�ϣ�->��ĸ���ֻ�ϣ�1->��Сд��ĸ��2->���֣�3->��д��ĸ�� 4->Сд��ĸ
	 * @param string $addChars �Զ����ַ���
	 * @return string 
	 */
	function rand_string($len = 6, $type = 0, $addChars = '') {
		$str = '';
		switch ($type) {
			case 1 :
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
				break;
			case 2 :
				$chars = str_repeat ( '0123456789', 3 );
				break;
			case 3 :
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
				break;
			case 4 :
				$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
				break;
			default :
				// Ĭ��ȥ�������׻������ַ�oOLl������01��Ҫ�����ʹ��addChars����
				$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
				break;
		}
		if ($len > 10) { //λ�������ظ��ַ���һ������
			$chars = $type == 2 ? str_repeat ( $chars, $len ) : str_repeat ( $chars, 5 );
		}
		$chars = str_shuffle ( $chars );
		$str = substr ( $chars, 0, $len );
		return $str;
	}
	/*
	�ύ�Ĳ�������
	$way==2�Ǳ�ʾ�ύ�������ַ�,���$str�������ַ���0;
	*/
	function check_str($str, $way = 1) {
		$str = trim ( $str );
		if (strlen ( $str ) == 0) {
			if ($way == 2) {
				$str = 0;
			}
			return $str;
		} else {
			$str = str_replace ( "'", "", $str );
			if ($way == 2) {
				if (preg_match ( "/^([0-9.,-]+)$/", $str )) {
					return $str;
				} else {
					return 0;
				}
			}
			return $str;
		}
	}
	/**
	 * sqlע����˺���
	 */
	function str_decode_parse($str) {
		if (empty ( $str ))
			return;
		if ($str == "")
			return $str;
		$str = str_replace ( "&amp;", "&", $str );
		$str = str_replace ( "&gt;", ">", $str );
		$str = str_replace ( "&lt;", "<", $str );
		$str = str_replace ( "&nbsp;", chr ( 32 ), $str );
		$str = str_replace ( "&nbsp;", chr ( 9 ), $str );
		$str = str_replace ( "&\"", chr ( 34 ), $str );
		$str = str_replace ( " & #39;", chr ( 39 ), $str );
		$str = str_replace ( "<br />", chr ( 13 ), $str );
		$str = str_replace ( "''", "'", $str );
		$str = str_replace ( "sel&#101;ct", "select", $str );
		$str = str_replace ( "jo&#105;n", "join", $str );
		$str = str_replace ( "un&#105;on", "union", $str );
		$str = str_replace ( "wh&#101;re", "where", $str );
		$str = str_replace ( "ins&#101;rt", "insert", $str );
		$str = str_replace ( "del&#101;te", "delete", $str );
		$str = str_replace ( "up&#100;ate", "update", $str );
		$str = str_replace ( "lik&#101;", "like", $str );
		$str = str_replace ( "dro&#112;", "drop", $str );
		$str = str_replace ( "cr&#101;ate", "create", $str );
		$str = str_replace ( "mod&#105;fy", "modify", $str );
		$str = str_replace ( "ren&#097;me", "rename", $str );
		$str = str_replace ( "alt&#101;r", "alter", $str );
		$str = str_replace ( "ca&#115;", "cast", $str );
		return $str;
	}
	
	function str_encode_parse($str) {
		if (empty ( $str ))
			return;
		if ($str == "")
			return $str;
		$str = trim ( $str );
		$str = str_replace ( "&", "&amp;", $str );
		$str = str_replace ( ">", "&gt;", $str );
		$str = str_replace ( "<", "&lt;", $str );
		$str = str_replace ( chr ( 32 ), "&nbsp;", $str );
		$str = str_replace ( chr ( 9 ), "&nbsp;", $str );
		$str = str_replace ( chr ( 34 ), "&\"", $str );
		$str = str_replace ( chr ( 39 ), " & #39;", $str );
		$str = str_replace ( chr ( 13 ), "<br />", $str );
		$str = str_replace ( "'", "''", $str );
		$str = str_replace ( "select", "sel&#101;ct", $str );
		$str = str_replace ( "join", "jo&#105;n", $str );
		$str = str_replace ( "union", "un&#105;on", $str );
		$str = str_replace ( "where", "wh&#101;re", $str );
		$str = str_replace ( "insert", "ins&#101;rt", $str );
		$str = str_replace ( "delete", "del&#101;te", $str );
		$str = str_replace ( "update", "up&#100;ate", $str );
		$str = str_replace ( "like", "lik&#101;", $str );
		$str = str_replace ( "drop", "dro&#112;", $str );
		$str = str_replace ( "create", "cr&#101;ate", $str );
		$str = str_replace ( "modify", "mod&#105;fy", $str );
		$str = str_replace ( "rename", "ren&#097;me", $str );
		$str = str_replace ( "alter", "alt&#101;r", $str );
		$str = str_replace ( "cast", "ca&#115;", $str );
		return $str;
	}
	
	/**
	 * ������������ַ���
	 *
	 * @param mixed $value Ҫ���˵ı���
	 * @return mixed
	 */
	function stripslashes_deep($value) {
		if (get_magic_quotes_gpc ()) {
			$value = is_array ( $value ) ? array_map ( 'stripslashes_deep', $value ) : stripslashes ( $value );
			return $value;
		} else {
			return $value;
		}
	}
	
	function html2str($value) {
		if (is_array ( $value )) {
			$value = array_map ( 'html2str', $value );
		} else {
			$value = htmlspecialchars ( $value );
		}
		return $value;
	}
	
	/**
	 * ��ȡ�ͻ���ip��ַ
	 */
	function get_client_ip() {
		if (getenv ( "HTTP_CLIENT_IP" ) && strcasecmp ( getenv ( "HTTP_CLIENT_IP" ), "unknown" ))
			$ip = getenv ( "HTTP_CLIENT_IP" );
		else if (getenv ( "HTTP_X_FORWARDED_FOR" ) && strcasecmp ( getenv ( "HTTP_X_FORWARDED_FOR" ), "unknown" ))
			$ip = getenv ( "HTTP_X_FORWARDED_FOR" );
		else if (getenv ( "REMOTE_ADDR" ) && strcasecmp ( getenv ( "REMOTE_ADDR" ), "unknown" ))
			$ip = getenv ( "REMOTE_ADDR" );
		else if (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], "unknown" ))
			$ip = $_SERVER ['REMOTE_ADDR'];
		else
			$ip = "unknown";
		return ($ip);
	}
	
	//ȡ�ַ���
	function msubstr($str, $start, $len) {
		$tmpstr = "";
		$strlen = $start + $len;
		for($i = 0; $i < $strlen; $i ++) {
			if (ord ( substr ( $str, $i, 1 ) ) > 0xa0) {
				$tmpstr .= substr ( $str, $i, 2 );
				$i ++;
			} else
				$tmpstr .= substr ( $str, $i, 1 );
		}
		return $tmpstr;
	}
	/*
	����������Ľ��б���ת��
	*/
	
	function encodearr($arr) {
		if (is_array ( $arr )) {
			foreach ( $arr as $key => $value ) {
				$arr [$key] = encodearr ( $value );
			}
			return $arr;
		}
		//echo iconv("gb2312","utf-8",$arr);
		return strtolower ( iconv ( "gb2312", "utf-8", $arr ) );
	}
	
	function length($str) { //����ͳ�������ַ�
		$len = strlen ( $str );
		$i = 0;
		while ( $i < $len ) {
			if (preg_match ( "/^[" . chr ( 0xa1 ) . "-" . chr ( 0xff ) . "]+$/", $str [$i] )) {
				$i += 2;
			} else {
				$i += 1;
			}
			$n += 1;
		}
		return $n;
	}
	/*
	explode����ת��
	*/
	
	function arraytostr($array, $s) {
		$str = "";
		//print_r($array);
		for($i = 0; $i < count ( $array ); $i ++) {
			$str .= $array [$i] . $s;
			//echo $array[$i];
		}
		if ($str != '') {
			$t = strlen ( $s ) * (- 1);
			$str = substr ( $str, 0, $t );
		}
		//echo $str;
		return $str;
	}
	/*
	ȡ�õ�ǰ��url������ַ
	*/
	
	function pageUrl($way = "") {
		if ($way == 1) {
			if ($_SERVER ['HTTP_REFERER'] == "") {
				$REFERERURL = $_SERVER ['REQUEST_URI'];
			} else {
				$REFERERURL = $_SERVER ['HTTP_REFERER'];
			}
		} else {
			if ($_SERVER ['SERVER_PORT'] != 80) {
				$REFERERURL = "http://" . $_SERVER ['SERVER_NAME'] . ":" . $_SERVER ['SERVER_PORT'] . $_SERVER ['REQUEST_URI'];
			} else {
				$REFERERURL = "http://" . $_SERVER ['SERVER_NAME'] . $_SERVER ['REQUEST_URI'];
			}
		}
		return $REFERERURL;
	}
	
	/**
	 * ��ȡ���������ĵ�ַ
	 * @param unknown_type $d1
	 * @param unknown_type $d2
	 * @return unknown
	 */
	function get_host_url($isport = false) {
		$scheme = (isset ( $_SERVER ['HTTPS'] ) && (strtolower ( $_SERVER ['HTTPS'] ) != 'off')) ? 'https://' : 'http://';
		if (! empty ( $_SERVER ['HTTP_X_FORWARDED_HOST'] )) {
			$t = strpos ( $_SERVER ['HTTP_X_FORWARDED_HOST'], ':' );
			if ($t > 0) {
				$host = substr ( $_SERVER ['HTTP_X_FORWARDED_HOST'], 0, $t );
				$port = substr ( $_SERVER ['HTTP_X_FORWARDED_HOST'], $t + 1 );
			} else {
				$host = $_SERVER ['HTTP_X_FORWARDED_HOST'];
			}
		} else if (! empty ( $_SERVER ['HTTP_HOST'] )) {
			$t = strpos ( $_SERVER ['HTTP_HOST'], ':' );
			if ($t > 0) {
				$host = substr ( $_SERVER ['HTTP_HOST'], 0, $t );
				$port = substr ( $_SERVER ['HTTP_HOST'], $t + 1 );
			} else {
				$host = $_SERVER ['HTTP_HOST'];
			}
		} else if (! empty ( $_SERVER ['SERVER_NAME'] )) {
			$host = $_SERVER ['SERVER_NAME'];
		}
		if (empty ( $port )) {
			$port = $_SERVER ['SERVER_PORT'];
		}
		if ($isport) {
			return $scheme . $host . ':' . $port . '/';
		} else {
			return $scheme . $host . '/';
		}
	}
	
	//���ڱȽϺ��� ��������ʱ����������
	function DateDiff($d1, $d2 = "") {
		if (is_string ( $d1 ))
			$d1 = strtotime ( $d1 );
		if (is_string ( $d2 ))
			$d2 = strtotime ( $d2 );
		return ($d2 - $d1) / 86400;
	}
	
	/*
 	���������ļ���
 	�ļ��д���ʱ������,�Զ�����һ��
 */
	
	function cascademddir($dir) {
		if (! $dir) {
			return;
		}
		$arr = explode ( "/", $dir );
		$t = "";
		for($i = 0; $i < count ( $arr ); $i ++) {
			$t .= "{$arr[$i]}/";
			if (! file_exists ( $t )) {
				mkdir ( $t );
			}
		}
	}
	function deldir($dir) {
		if (! file_exists ( $dir ))
			return;
		$dh = opendir ( $dir );
		while ( $file = readdir ( $dh ) ) {
			if ($file != "." && $file != "..") {
				$fullpath = $dir . "/" . $file;
				if (! is_dir ( $fullpath )) {
					unlink ( $fullpath );
				} else {
					deldir ( $fullpath );
				}
			}
		}
		closedir ( $dh );
		if (rmdir ( $dir )) {
			return true;
		} else {
			return false;
		}
	}
	/**
     +----------------------------------------------------------
	 * ϵͳ��������������ݺ��� print_r().
     +----------------------------------------------------------
	 * @param array $data  ����
	 * @param string $option  �Ƿ���var_dump()
     +----------------------------------------------------------
	 * @return array
     +----------------------------------------------------------
	 */
	public function dump($data, $option = false) {
		if ($option) {
			ob_start ();
			var_dump ( $data );
			$output = ob_get_clean ();
			$output = str_replace ( '"', '', $output );
			$output = preg_replace ( "/\]\=\>\n(\s+)/m", "] => ", $output );
			echo '<pre>', $output, '</pre>';
		} else {
			echo '<pre>';
			print_r ( $data );
			echo '</pre>';
		}
		
		exit ();
	}
	/**
	 * ��$_POST��ȡ��ȫ�ֱ�������ת�廯�����������밲ȫ.
	 * @access public
	 * @param string $string 
	 * @return string
	 */
	public function post($string) {
		if (is_array ( $string )) {
			foreach ( $string as $key => $value ) {
				$string [$key] = $this->post ( $value );
			}
			return $string;
		} else {
			return htmlspecialchars ( trim ( $_POST [$string] ) );
		}
	}
	
	/**
	 * ��$_GET��ȡ��ȫ�ֱ�������ת�廯�����������밲ȫ.
	 * @access public
	 * @param string $string 
	 * @return string
	 */
	public function get($string) {
		if (is_array ( $string )) {
			foreach ( $string as $key => $value ) {
				$string [$key] = $this->get ( $value );
			}
			return $string;
		} else {
			return htmlspecialchars ( trim ( $_GET [$string] ) );
		}
	}

}

?>