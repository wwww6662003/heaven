<?php
/**
 * @package HeavenMVC
 * @version 1.0 Cookie.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */
if(!defined('IN_HEAVEN')){
	exit;
}
class Lib_Cookie extends Core_Base{
	//定义变量.
	private static $instance; //用于构建类的singleton模式参数

	//判断cookie是否存在.
	public static function is_set($name){
		return isset($_COOKIE[$name]);
	}
	
	// 获取某个Cookie值.
	public static function get($name){
		$value = $_COOKIE[$name];
		$value = unserialize(base64_decode($value));
		return $value;
	}
	
	//设置某个Cookie值.
	public static function set($name,$value,$expire='',$path='',$domain=''){
		//参数分析.
		$expire = empty($expire) ? time()+3600 : time()+$expire;
		if (empty($path))
			$path = '/';
		//数据加密处理.	
		$value = base64_encode(serialize($value));
		setcookie($name, $value,$expire,$path,$domain);
		$_COOKIE[$name] = $value;		
	}
	
	//删除某个Cookie值
	public static function delete($name){
		self::set($name, '', '-3600');
		unset($_COOKIE[$name]);
	}
	
	// 清空Cookie值
	public static function clear(){
		unset($_COOKIE);
	}

	/**
     * 用于本类的静态调用,子类需要重载才能正常使用.
     * @access public
     * @param string $params 类的名称
     * @return void
     */
    public static function getInstance(){		
		if(self::$instance == null){		
			self::$instance = new self;
		}		
		return self::$instance;
	}
}
?>