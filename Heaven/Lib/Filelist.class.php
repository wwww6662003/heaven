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
	
	//定义变量
	private static $instance; //用于构建类的singleton模式参数

	//构造函数
	public function __construct(){
		
	}
	
	//初始化
	protected function parse_dir($dir_name){		
		if(!is_dir($dir_name)){		
			trigger_error('The dir '.$dir_name. ' is not exists!', E_USER_ERROR);
		}		
		return $dir_name;
	}

	//分晰目标目录.
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
	
	//获取目录内文件
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

	//COPY文件夹
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

	//移动文件夹
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
	
	//删除文件夹
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