<?php
/**
 * @package HeavenMVC
 * @version 1.0 Heaven.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */
if (! defined ( 'IN_HEAVEN' )) {
	exit ();
}
class Core_Base {
	
	/**
	 * �Զ���������
	 * @access public
	 * @param $name ��������
	 * @param $value  ����ֵ
	 */
	public function __set($name, $value) {
		if (property_exists ( $this, $name )) {
			$this->$name = $value;
		}
	}
	
	/**
	 * �Զ�������ȡ
	 * @access public
	 * @param $name ��������
	 * @return mixed
	 */
	public function __get($name) {
		if (isset ( $this->$name )) {
			return $this->$name;
		} else {
			return false;
		}
	}
	
	/**
	 * ����������ò����ڵķ���
	 * @access public
	 * @param $method ��������
	 * @param $args   ��������
	 * @return string
	 */
	public function __call($method, array $args) {
		echo 'Method:' . $method . '() is not exists in Class:' . get_class ( $this ) . '!<br>The args is:<br>';
		foreach ( $args as $val ) {
			echo $val . '<br>';
		}
	}
	
	/**
	 * ֱ�ӵ��ú������������
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return ( string ) 'This is ' . get_class ( $this ) . ' Class!';
	}
}
?>