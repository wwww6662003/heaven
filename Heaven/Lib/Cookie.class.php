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
	//�������.
	private static $instance; //���ڹ������singletonģʽ����

	//�ж�cookie�Ƿ����.
	public static function is_set($name){
		return isset($_COOKIE[$name]);
	}
	
	// ��ȡĳ��Cookieֵ.
	public static function get($name){
		$value = $_COOKIE[$name];
		$value = unserialize(base64_decode($value));
		return $value;
	}
	
	//����ĳ��Cookieֵ.
	public static function set($name,$value,$expire='',$path='',$domain=''){
		//��������.
		$expire = empty($expire) ? time()+3600 : time()+$expire;
		if (empty($path))
			$path = '/';
		//���ݼ��ܴ���.	
		$value = base64_encode(serialize($value));
		setcookie($name, $value,$expire,$path,$domain);
		$_COOKIE[$name] = $value;		
	}
	
	//ɾ��ĳ��Cookieֵ
	public static function delete($name){
		self::set($name, '', '-3600');
		unset($_COOKIE[$name]);
	}
	
	// ���Cookieֵ
	public static function clear(){
		unset($_COOKIE);
	}

	/**
     * ���ڱ���ľ�̬����,������Ҫ���ز�������ʹ��.
     * @access public
     * @param string $params �������
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