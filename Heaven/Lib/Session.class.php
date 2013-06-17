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
	
	//�������
	protected static $instance; //���ڹ������singletonģʽ����

	//���캯��.
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
	
	//�ر�session.
	public function destory(){
		if (session_id()){
			unset($_SESSION);
        	session_destroy();		
		}
	}
	
	//���û��ر������ʱ,session��ֹͣ.
	public function close(){
		if (session_id())
			session_write_close();
	}
	
	//��ȡsession_name.
	public function get_name(){
		return session_name();
	}
	
	//��ȡsession_id.
	public function get_id(){
		return session_id();
	}
	
	//����session_name.
	public function set_name($value){
		session_name($value);
	}
	
	//����session_id.
	public function set_id($id){
		session_id($id);
	}
	
	//����session�ļ��Ĵ��·��.
	public function set_save_path($value){
		if(is_dir($value))
			session_save_path($value);
		else
			Core_Action::halt($value.'is not a valid directory');
	}
	
	//��ȡsession�ļ����·��.
	public function get_session_path(){
		return session_save_path();
	}
	
	//����session_start�Ƿ���.
	public function is_start(){
		return session_id() ? true : false;
	}
	
	//����session���и�sessionֵ.
	public function is_set($key){
		if (session_id()){
			return isset($_SESSION[$key]);
		}
		else {
			return false;
		}
	}
	
	//����session��Чʱ��.
	public function get_timeout()
	{
		return (int)ini_get('session.gc_maxlifetime');
	}
	
	//����session�������ʱ��.
	public function set_timeout($value)
	{
		ini_set('session.gc_maxlifetime',$value);
	}

	//��������
	public function __destruct(){
		
	}

	//���ڱ���ľ�̬����,������Ҫ���ز�������ʹ��.
    public static function getInstance(){		
		if(self::$instance == null){		
			self::$instance = new self;
		}		
		return self::$instance;
	}
}