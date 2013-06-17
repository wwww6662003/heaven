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
//加载fpdf.php文件.
include(APP_ROOT.'/extensions/pdf/fpdf/fpdf.php');

class pdf extends FPDF{

	//定义变量
	private static $instance; //用于构建类的singleton模式参数

	//构晰函数
	public function __destruct(){
		exit;
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