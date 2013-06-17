<?php
/**
 * @package HeavenMVC
 * @version 1.0 Filelist.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */

if(!defined('IN_HEAVEN')){
	exit;
}
class Lib_Filelist extends Core_Base{
	
	//�������
	private static $instance; //���ڹ������singletonģʽ����

	//���캯��
	public function __construct(){
		
	}
	
	//��ʼ��
	protected function parse_dir($dir_name){		
		if(!is_dir($dir_name)){		
			trigger_error('The dir '.$dir_name. ' is not exists!', E_USER_ERROR);
		}		
		return $dir_name;
	}

	//����Ŀ��Ŀ¼.
	protected function dest_dir($dir_name){		
		if(!is_dir($dir_name)){			
			mkdir($dir_name, 0777);
		}
		else{
			if(!is_writable($dir_name)){
				chmod($dir_name, 0777);
			}
		}		
		return $dir_name;
	}
	
	//��ȡĿ¼���ļ�
	public function get_file_list($dir_name){		
		$dir = $this->parse_dir($dir_name);		
		$handle = opendir($dir);
		$files = array();
		while (false !== ($file = readdir($handle))){			
			if($file == '.' || $file == '..'){				
				continue;
			}			
			$files[] = $file;
		}	
		closedir($handle);		
		return $files;
	}

	//COPY�ļ���
	public function copy_dir($source, $dest){		
		$parse_dir = $this->parse_dir($source);
		$dest_dir = $this->dest_dir($dest);		
		$file_list = $this->get_file_list($parse_dir);		
		foreach ($file_list as $file){			
			if(is_dir($parse_dir.'/'.$file)){				
				$this->copy_dir($parse_dir.'/'.$file, $dest_dir.'/'.$file);
			}
			else{				
				copy($parse_dir.'/'.$file, $dest_dir.'/'.$file);
			}
		}		
		return true;
	}

	//�ƶ��ļ���
	public function move_dir($source, $dest){		
		$parse_dir = $this->parse_dir($source);
		$dest_dir = $this->dest_dir($dest);		
		$file_list = $this->get_file_list($parse_dir);		
		foreach ($file_list as $file){			
			if(is_dir($parse_dir.'/'.$file)){				
				$this->move_dir($parse_dir.'/'.$file, $dest_dir.'/'.$file);
			}
			else{				
				if(copy($parse_dir.'/'.$file, $dest_dir.'/'.$file)){					
					unlink($parse_dir.'/'.$file);
				}	
			}
		}		
		rmdir($parse_dir);		
		return true;
	}
	
	//ɾ���ļ���
	public function delete_dir($file_dir){		
		$parse_dir = $this->parse_dir($file_dir);		
		$file_list = $this->get_file_list($parse_dir);		
		foreach ($file_list as $file){			
			if(is_dir($parse_dir.'/'.$file)){				
				$this->delete_dir($parse_dir.'/'.$file);
			}
			else{			
				unlink($parse_dir.'/'.$file);
			}
		}		
		return true;
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