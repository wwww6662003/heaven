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
class adodb extends Base{
	
	//定义变量
	private static $instance; //用于构建类的singleton模式参数
	public $adodb;	//adodb实例化对象.
	protected $params;      //config数据库信息

	public function __construct($dbdriver='mysql'){

		//加载adodb inc 文件.
		$adodb_inc = APP_ROOT.'/extensions/adodb/adodb/adodb.inc.php';		
		if(!file_exists($adodb_inc)){			
			Controller::halt('The file : '.$adodb_inc. ' is not exists!');
		}
		include($adodb_inc);
		$this->adodb = ADONewConnection($dbdriver);
		$params = $this->init();
		$this->adodb->Connect($params['host'], $params['username'], $params['password'], $params['dbname']);
		return true;
	}

	//用于对confilg文件数据进行数据分析,初始化运行环境.
	function init(){
		//加载config文件
		if(file_exists(APP_ROOT.'/config/config.php')){
			$config = include(APP_ROOT.'/config/config.php');
		}
		else{
			trigger_error('The file config is not exists', E_USER_ERROR);
		}
		//分析confilg内容
		if(!is_array($config)){
			trigger_error('The config file content is error', E_USER_ERROR);
		}
		
		if($config['host']&&$config['username']&&$config['password']&&$config['dbname']){			
			$config['host'] = trim($config['host']);
			$config['username'] = trim($config['username']);
			$config['password'] = trim($config['password']);
			$config['dbname'] = trim($config['dbname']);
		}
		else{			
			trigger_error('Mysql Server HostName or UserName or Password or DatabaseName is error in the config file', E_USER_ERROR);
		}
		return $config;
	}

	//用于本类的静态调用,子类需要重载才能正常使用.
    public static function getInstance($dbdriver='mysql'){		
		if(self::$instance == null){		
			self::$instance = new adodb($dbdriver);
		}		
		return self::$instance;
	}
}
?>