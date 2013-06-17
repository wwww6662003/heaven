<?php
/**
 * @package HeavenMVC
 * @version 1.0 Widget.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */
if(!defined('IN_HEAVEN')){
	exit;
}
abstract class Widget extends Controller{
	//提交函数.
	public function render($widget_name){
		if(empty($widget_name)){
			return false;
		}
		static $_instance = array();
		$widget_name = ucfirst(strtolower($widget_name)).'Widget';
		if(!isset($_instance[$widget_name])){
			$_instance[$widget_name] = new $widget_name();
		}
		$_instance[$widget_name]->render();
	}
	//重载cache函数,防止cache函数与controller中的cache相冲突.
	public function cache(){
		return true;
	}
	public function set_cache(){
		return false;
	}
	//模板输出.
	public function display($file_name=false){
		if(empty($file_name))
			$file_name = strtolower(substr(get_class($this),0, -6));
		return self::$view->widget($file_name);
	}
}
?>