<?php
/**
 * @package HeavenMVC
 * @version 1.0 Encrypt.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */
if (! defined ( 'IN_HEAVEN' )) {
	exit ();
}
class Core_Encrypt extends Core_Base {
	private static $instance;
	private $encrypt_key = '';
	/**
	 * 构造函数,用于初始化运行环境.
	 * @access public
	 * @return mixed
	 */
	public function __construct($encrypt_key = 'heaven') {
		$this->encrypt_key = $encrypt_key;
	}
	/**
	 * 获取数据的动态key或者静态key
	 * @return void
	 */
	public function get_encrypt_key() {
		return $this->encrypt_key;
	}
	/**
	 * 设定字符串的加密key
	 * @return void
	 * @param int $flag,string $encrypt_key
	 */
	public function set_encrypt_key($flag = 1, $encrypt_key = 'heaven') {
		if ($flag == 0) {
			$this->encrypt_key = $encrypt_key;
		} else {
			srand ( mktime () );
			$this->encrypt_key = md5 ( rand ( 0, 10000 ) );
		}
	}
	/**
	 * 对字符串的第二次加密
	 * @param string $txt
	 * @param string $key
	 * @return void
	 */
	private function encode_again($txt, $key) {
		$key = md5 ( $key );
		$ctr = 0;
		$tmp = '';
		for($i = 0; $i < strlen ( $txt ); $i ++) {
			if ($ctr == strlen ( $key ))
				$ctr = 0;
			$tmp .= substr ( $txt, $i, 1 ) ^ substr ( $key, $ctr, 1 );
			$ctr ++;
		}
		return urlencode($tmp);
	}
	
	/**
	 * 加密字符串函数
	 * @param string $txt
	 * @return void
	 */
	public function encrypt($txt) {
		$this->encrypt_key = $this->get_encrypt_key ();
		$ctr = 0;
		$tmp = '';
		for($i = 0; $i < strlen ( $txt ); $i ++) {
			if ($ctr == strlen ( $this->encrypt_key ))
				$ctr = 0;
			$tmp .= substr ( $this->encrypt_key, $ctr, 1 ) . (substr ( $txt, $i, 1 ) ^ substr ( $this->encrypt_key, $ctr, 1 ));
			$ctr ++;
		}
		return $this->encode_again ( base64_encode ( $this->encrypt_key . $tmp ), $this->encrypt_key );
	}
	
	/**
	 * 解密字符串函数
	 * @param string $txt
	 * @return unknown
	 */  
	public function decrypt($txt) {
		$txt=urldecode($txt);
		$txt = $this->encode_again ( $txt, $this->encrypt_key );
		$txt = substr ( base64_decode ( $txt ), strlen ( $this->encrypt_key ) );
		$tmp = '';
		for($i = 0; $i < strlen ( $txt ); $i ++) {
			$md5 = substr ( $txt, $i, 1 );
			$i ++;
			$tmp .= (substr ( $txt, $i, 1 ) ^ $md5);
		}
		return $tmp;
	}
	
	/**
	 * 用于本类的静态调用,子类需要重载才能正常使用.
	 * @access public
	 * @param string $params 类的名称
	 * @return void
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new self ( );
		}
		return self::$instance;
	}
}
?>
