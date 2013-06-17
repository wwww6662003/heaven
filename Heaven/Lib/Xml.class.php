<?php
/**
 * @package HeavenMVC
 * @version 1.0 Xml.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */

if(!defined('IN_HEAVEN')){
	exit;
}
class Lib_Xml extends Core_Base {
	
	//定义变量
	private static $instance; //用于构建类的singleton模式参数
	
	/**
     * 构造函数,用于初始化运行环境.
     * @access public
     * @return mixed
     */
	public function __construct(){
		
	}

	//加载xml文件.支持文件名及xml代码.
	public function load_xml($file_name){		
		if(empty($file_name)){			
			return false;
		}		
		if(strpos($file_name, '<?xml')===false){			
			return simplexml_load_file($file_name);
		}
		else{			
			return simplexml_load_string($file_name);
		}
	}

	//数据转化为xml代码.
	protected function data2xml($data){		
		if(empty($data)){			
			return false;
		}
		if(is_object($data)){			
			$data = get_object_vars($data);
		}		
		$xml = "";
		foreach ($data as $key=>$value){			
			if(is_array($value)){				
				foreach ($value as $k=>$v){					
					$xml .= is_numeric($k) ? $this->add_child($key, $v) : "<".$key.">\r".$this->add_child($k,$v)."</".$key.">\r";
				}
			}
			else{				
				$xml .= "<".$key.">".$value."</".$key.">\r";
			}
		}		
		return $xml;
	}
	
	//进行对xml编码.
	public function xml_encode($data, $root=false){		
		if(empty($data)){			
			return false;
		}
		$root = empty($root) ? 'root' : trim($root);		
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r";
		$xml .= "<".$root.">\r";
		$xml .= $this->data2xml($data);
		$xml .= "</".$root.">";		
		return $xml;
	}
	
	//处理XML代码.
	protected function add_child($tag, $value){		
		if(empty($tag)||empty($value)){			
			return false;
		}		
		if(is_array($value)){			
			$xml = "";
			foreach ($value as $key=>$lines){				
				if(is_array($lines)){					
					$xml .="<".$tag.">\r";
					foreach ($lines as $k=>$v){						
						$xml .= is_numeric($k) ? $this->add_child($key,$v) : "<".$key.">\r".$this->add_child($k,$v)."</".$key.">\r";
					}
					$xml .= "</".$tag.">\r";
				}
				else{					
					$xml .= is_numeric($key) ? "<".$tag.">".$lines."</".$tag.">\r" : "<".$key.">".$lines."</".$key.">\r";
				}
			}
		}
		else{			
			$xml .= "<".$tag.">".$value."</".$tag.">\r";
		}		
		return $xml;
	}

	//构晰函数
	public function __destruct(){
		
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