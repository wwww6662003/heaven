<?php
// +---------------------------------------------------------------
// | Heaven Framework
// +---------------------------------------------------------------
// | Copyright (c) 2012 http://code.google.com/p/heavenmvc/ All rights reserved.
// +---------------------------------------------------------------
// | Email: wangwei(wwww6662003@163.com) QQ:86610497
// +---------------------------------------------------------------

if(!defined('IN_HEAVEN')){
	exit;
}
//加载smarty类文件
include(APP_ROOT.'/extensions/heaven_smarty/smarty/Smarty.class.php');

class heaven_smarty extends Smarty {
	
	//定义变量
	private static $instance; //用于构建类的singleton模式参数

	//构造函数，用于初始化运行环境
	public function __construct(){
		
		$this->template_dir = APP_ROOT.'/views/';		
		$this->compile_dir =  APP_ROOT.'/cache/template_c/';
		$this->cache_dir =  APP_ROOT.'/cache/template/';
		$this->config_dir =  APP_ROOT.'/config/';
		return true;
	}

	//用于本类的静态调用,子类需要重载才能正常使用.
    public static function getInstance(){		
		if(self::$instance == null){		
			self::$instance = new self;
		}		
		return self::$instance;
	}
}
?>