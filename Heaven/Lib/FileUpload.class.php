<?php
/**
 * @package HeavenMVC
 * @version 1.0 FileUpload.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */

if(!defined('IN_HEAVEN')){
	exit;
}
class Lib_FileUpload extends Core_Base{
	
	//定义变量
	private static $instance; //用于构建类的singleton模式参数
	protected $limit_size;	//文件大小
	protected $file_name;	//文件名字
	protected $limit_type;	//文件类型
	
	//构造函数
	public function  __construct(){		
		$this->limit_size = 10485760;	//默认文件大小 10M		
		return true;
	}
	
	//初始化
	protected function parse_init($file){		
		$this->file_name = $file;		
		if($this->file_name['size'] > $this->limit_size){			
			Core_Action::halt('The File Size of the file:'.$this->file_name['name'].' is too big!');
		}		
		if($this->limit_type){			
			$this->parse_mimetype($file);
		}		
		return true;
	}
	
	//设置上传文件的最大大小.
	public function set_limit_size($size){
		if($size){
			$this->limit_size = $size;
		}
		return $this;
	}

	//设置上传文件允许的格式
	public function set_limit_type($type){
		if(empty($type)||!is_array($type)){
			return false;
		}
		$this->limit_type = $type;
		return $this;
	}

	//验证上传文件的格式
	protected function parse_mimetype(){		
		//上传文件允许的格式
		$mime_type = array(
		'jpg'=>'image/jpeg',
		'gif'=>'image/gif',
		'png'=>'image/png',
		'bmp'=>'image/bmp',
		'html'=>'text/html',
		'css'=>'text/css',
		'wbmp'=>'image/vnd.wap.wbmp',
		'js'=>'application/x-javascript', 
		'swf'=>'application/x-shockwave-flash',
		'xml'=>'application/xhtml+xml',
		'php'=>'application/x-httpd-php',
		'txt'=>'text/plain',
		'wma'=>'audio/x-ms-wma',
		'mp3'=>'audio/mpeg',
		'zip'=>'application/zip',
		'rar'=>'application/x-rar-compressed',
		'flv'=>'flv-application/octet-stream',
		);
		//判断limit_type是否在允许上传文件格式列表之内
		$mime_type_key = array_keys($mime_type);
		foreach($this->limit_type as $type){
			if(!in_array($type, $mime_type_key)){				
				Core_Action::halt('设置的LIMIT TYPE不在允许上传格式的列表之内!');
			}
		}	
		$allow_type_array = array();
		foreach($this->limit_type as $type){
			$allow_type_array[] = $mime_type[$type];
		}
		if(!in_array($this->file_name['type'], $allow_type_array)){
			Core_Action::halt('上传失败:你上传的文件格式不正确!');
		}		
		return true;
	}
	
	//上传文件
	public function upload($file_upload, $file_name){		
		if(is_array($file_upload) && !empty($file_name)){
			$this->parse_init($file_upload);			
			if(!move_uploaded_file($this->file_name['tmp_name'], $file_name)){				
				return flase;
			}			
			return true;
		}
		else{			
			return false;
		}
	}

	/**
     * 用于本类的静态调用,子类需要重载才能正常使用.
     * @access public
     * @param string $params 类的名称
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