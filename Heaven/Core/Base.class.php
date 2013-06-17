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
	 * 自动变量设置
	 * @access public
	 * @param $name 属性名称
	 * @param $value  属性值
	 */
	public function __set($name, $value) {
		if (property_exists ( $this, $name )) {
			$this->$name = $value;
		}
	}
	
	/**
	 * 自动变量获取
	 * @access public
	 * @param $name 属性名称
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
	 * 处理当类外调用不存在的方法
	 * @access public
	 * @param $method 方法名称
	 * @param $args   参数名称
	 * @return string
	 */
	public function __call($method, array $args) {
		echo 'Method:' . $method . '() is not exists in Class:' . get_class ( $this ) . '!<br>The args is:<br>';
		foreach ( $args as $val ) {
			echo $val . '<br>';
		}
	}
	
	/**
	 * 直接调用函数，输出内容
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return ( string ) 'This is ' . get_class ( $this ) . ' Class!';
	}
}
?>