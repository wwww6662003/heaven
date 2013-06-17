<?php
/**
 * @package HeavenMVC
 * @version 1.0 Session.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */
if(!defined('IN_HEAVEN')){
	exit;
}
class Lib_Session extends Core_Base {
	
	//定义变量
	protected static $instance; //用于构建类的singleton模式参数

	//构造函数.
	public function __construct(){
		$this->start();
		register_shutdown_function(array($this,'close'));
	}
	
	//start.
	public function start(){
		session_start();
	}
	
	public function set($key, $value){
		$_SESSION[$key]=$value;
	}
	
	public function get($key){
		if (isset($_SESSION[$key]))
			return $_SESSION[$key];
		else 
			return false;
	}
	
	public function delete($key){
		if (isset($_SESSION[$key])){
			unset($_SESSION[$key]);
			return true;
		}
		else {
			return false;
		}
	}
	
	public function clear(){
		$_SESSION = array();
	}
	
	//关闭session.
	public function destory(){
		if (session_id()){
			unset($_SESSION);
        	session_destroy();		
		}
	}
	
	//当用户关闭浏览器时,session将停止.
	public function close(){
		if (session_id())
			session_write_close();
	}
	
	//获取session_name.
	public function get_name(){
		return session_name();
	}
	
	//获取session_id.
	public function get_id(){
		return session_id();
	}
	
	//设置session_name.
	public function set_name($value){
		session_name($value);
	}
	
	//设置session_id.
	public function set_id($id){
		session_id($id);
	}
	
	//设置session文件的存放路径.
	public function set_save_path($value){
		if(is_dir($value))
			session_save_path($value);
		else
			Core_Action::halt($value.'is not a valid directory');
	}
	
	//获取session文件存放路径.
	public function get_session_path(){
		return session_save_path();
	}
	
	//检验session_start是否开启.
	public function is_start(){
		return session_id() ? true : false;
	}
	
	//检验session里有该session值.
	public function is_set($key){
		if (session_id()){
			return isset($_SESSION[$key]);
		}
		else {
			return false;
		}
	}
	
	//检验session有效时间.
	public function get_timeout()
	{
		return (int)ini_get('session.gc_maxlifetime');
	}
	
	//设置session有最大存活时间.
	public function set_timeout($value)
	{
		ini_set('session.gc_maxlifetime',$value);
	}

	//构晰函数
	public function __destruct(){
		
	}

	//用于本类的静态调用,子类需要重载才能正常使用.
    public static function getInstance(){		
		if(self::$instance == null){		
			self::$instance = new self;
		}		
		return self::$instance;
	}
}