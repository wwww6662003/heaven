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
class editor extends Base{
	
	//定义变量.
	private static $instance; //用于构建类的singleton模式参数
	public $editor_html;	//编辑器HTML代码
	
	
	protected function parse_init(){
		if(file_exists(APP_ROOT.'/extensions/editor/ckeditor/ckeditor.php')){			
			include(APP_ROOT.'/extensions/editor/ckeditor/ckeditor.php');
		}     
		return true;
	}

	public function get_editor($name, $width=false, $height=false){
		$this->parse_init();
		$CKEditor = new CKEditor();
		$CKEditor->returnOutput = true;		
		$dirname = Controller::get_base_url(); 		
		$CKEditor->basePath = $dirname.'/extensions/editor/ckeditor/'; //编译器的根目录
		$CKEditor->config['width'] = !empty($width) ? intval($width) : '760';
		$CKEditor->config['height'] = !empty($height) ? intval($height) : '300';
		$CKEditor->config['filebrowserBrowseUrl'] = $dirname.'/extensions/editor/ckeditor/ckfinder/ckfinder.html';
		$CKEditor->config['filebrowserImageBrowseUrl'] = $dirname.'/extensions/editor/ckeditor/ckfinder/ckfinder.html?Type=Images';
		$CKEditor->config['filebrowserFlashBrowseUrl'] = $dirname.'/extensions/editor/ckeditor/ckfinder/ckfinder.html?Type=Flash';
		$CKEditor->config['filebrowserUploadUrl'] = $dirname.'/extensions/editor/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
		$CKEditor->config['filebrowserImageUploadUrl'] = $dirname.'/extensions/editor/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';
		$CKEditor->config['filebrowserFlashUploadUrl'] = $dirname.'/extensions/editor/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash';
		session_start();
		$_SESSION['IsAuthorized']= true;
		$this->editor_html = $CKEditor->editor($name);
		return $this;
	}

	//模板赋值
	public function assign($tag_name){		
		if(empty($tag_name)){
			return false;
		}
		return Controller::assign($tag_name, $this->editor_html);
	}

	//输出本类信息 方便程序直接对实例化对象进行调用
	public function __toString(){		
		if($this->editor_html){			
			return (string)$this->editor_html;
		}
		else{			
			return (string)'This is Editor Class';
		}
	}

	//构晰函数
	public function __destruct(){		
		if($this->editor_html){			
			unset($this->editor_html);
		}
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