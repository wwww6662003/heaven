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
	
	//�������
	private static $instance; //���ڹ������singletonģʽ����
	protected $limit_size;	//�ļ���С
	protected $file_name;	//�ļ�����
	protected $limit_type;	//�ļ�����
	
	//���캯��
	public function  __construct(){		
		$this->limit_size = 10485760;	//Ĭ���ļ���С 10M		
		return true;
	}
	
	//��ʼ��
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
	
	//�����ϴ��ļ�������С.
	public function set_limit_size($size){
		if($size){
			$this->limit_size = $size;
		}
		return $this;
	}

	//�����ϴ��ļ�����ĸ�ʽ
	public function set_limit_type($type){
		if(empty($type)||!is_array($type)){
			return false;
		}
		$this->limit_type = $type;
		return $this;
	}

	//��֤�ϴ��ļ��ĸ�ʽ
	protected function parse_mimetype(){		
		//�ϴ��ļ�����ĸ�ʽ
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
		//�ж�limit_type�Ƿ��������ϴ��ļ���ʽ�б�֮��
		$mime_type_key = array_keys($mime_type);
		foreach($this->limit_type as $type){
			if(!in_array($type, $mime_type_key)){				
				Core_Action::halt('���õ�LIMIT TYPE���������ϴ���ʽ���б�֮��!');
			}
		}	
		$allow_type_array = array();
		foreach($this->limit_type as $type){
			$allow_type_array[] = $mime_type[$type];
		}
		if(!in_array($this->file_name['type'], $allow_type_array)){
			Core_Action::halt('�ϴ�ʧ��:���ϴ����ļ���ʽ����ȷ!');
		}		
		return true;
	}
	
	//�ϴ��ļ�
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