<?php
/**
 * @package HeavenMVC
 * @version 1.0 Dbcache.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */

if(!defined('IN_HEAVEN')){
	exit;
}
class Lib_Dbcache extends Core_Base{
	
	//定义变量
	private static $instance; //用于构建类的singleton模式参数
	public $cache_dir;		//缓存目录
	public $cache_file; 	//缓存文件
	
	protected $cache_toggle; //缓存开关，重写开关。当开启时，缓存文件重新写入
	protected $data;		//缓存数据，从数据表读取并处理完毕的数组
	protected $filter;		//数据表字段的过滤后有效字段
	protected $mode;		//缓存数据类型
	protected $keys;		//缓存数据类型:设置型参数
	protected $values;	//缓存数据类型:设置型参数
	public $lifetime;	//缓存生存周期
	
	//构造函数,初始化变量。
	public function __construct(){
		$this->cache_dir = APP_PATH.'/Cache/data/';	//默认缓存目录
		$this->lifetime = 86400;			//默认缓存周期为24小时
		$this->cahce_toggle = 0;				//默认缓存开关关闭
		return true;
	}
	
	//分析缓存文件的路径.
	protected function parse_cache_file($file_name){
		if(!$this->cache_file[$file_name]){				
			$this->cache_file[$file_name] = $this->cache_dir.$file_name.'_cache.php';
		}
		return $this->cache_file[$file_name];
	}
	
	//获取缓存数据。
	protected function get_cache_data($file_name){
		//获取数据表数据.
		$model = Controller::model($file_name);
		$data = $model->findAll();
		//分析mode.
		if($this->mode){			
			$this->mode = 1;
		}		
		//临时数据.
		$data_temp = array();		
		switch ($this->mode){			
			case 1:
				$data_temp = $data;
				break;				
			case 2:
				foreach ($data as $k=>$v){					
					foreach ($this->filter as $lines){						
						$data_temp[$k][$lines] = $v[$lines];
					}
				}				
				unset($this->filter);				
				break;			
			case 3:
				foreach($data as $lines){				
					$data_temp[$lines[$this->keys]] = $lines[$this->values];
				}
				unset($this->keys);
				unset($this->values);
			    break;
		}
		//及时清空无用数据内存的占用.
		unset($this->mode);
		unset($data);
		$this->data[$file_name] = $data_temp;		
		//及时清空无用数据内存的占用.
		unset($data_temp);
		return $this->data[$file_name];
	}

	//生成缓存文件。
	protected function create_cache($file_name){
		$this->parse_cache_file($file_name);
		$contents = "<?php\r\n";
		$contents .= "return ";
		$contents .= var_export($this->data[$file_name], true);
		$contents .= "\r\n?>";
		//当缓存目录不存在时，自行创建目录。
		if(!file_exists($this->cache_dir)){				
			mkdir($this->cache_dir, 0777);
		}
		file_put_contents($this->cache_file[$file_name], $contents);
		return true;
	}

	//缓存分析，从而判断TOGGLE开关的开启与关闭。
	protected function parse_cache($file_name){		
		$this->parse_cache_file($file_name);
		if(file_exists($this->cache_file[$file_name])){				
			$time_now = $_SERVER['REQUEST_TIME'];				
			$time_built = filemtime($this->cache_file[$file_name]);				
			if($time_now-$time_built>$this->lifetime){
				$this->cache_toggle = 1;
			}
			else{
				$this->cache_toggle = 0;
			}
		}
		else{				
			$this->cache_toggle = 1;
		}
		return $this->cache_toggle;
	}

	//加载缓存，本类中的主函数。
	public function load($file_name){		
		//参数判断.
		if(empty($file_name)){			
			return false;
		}
		//分析缓存文件是否重写.
		$this->parse_cache($file_name);
		if($this->cache_toggle){				
			$this->get_cache_data($file_name);				
			$this->create_cache($file_name);
		}
		return include($this->cache_file[$file_name]);
	}
	
	//设置缓存周期.
	public function lifetime($life_time){		
		if(empty($life_time)){
			return false;
		}
		$this->lifetime = $life_time;
		return $this;
	}

	//设置缓存数据类型,过滤型.
	public function limit($field){
		if(empty($field)||!is_array($field)){			
			return false;
		}
		$this->mode = 2;
		$this->filter = $field;		
		return $this;
	}

	//设置缓存数据类型,设置型.
	public function config($key, $value){
		if(empty($key)||empty($value)){
			return false;
		}		
		$this->mode = 3;
		$this->keys = $key;
		$this->values = $value;
		return $this;
	}

	//根据文件名及数据生成缓存文件.
	public function set($file_name, $data){
		//参数分析.
		if(empty($file_name)||!is_array($data)){			
			return false;
		}
		//获取缓存文件路径.
		$this->parse_cache_file($file_name);
		$this->data[$file_name] = $data;
		//创建缓存文件.
		$this->create_cache($file_name);
		return true;
	}
	
	//获取$file_name的缓存文件内容.
	public function get($file_name){
		//参数分析.
		if(empty($file_name)){			
			return false;
		}
		//获取缓存文件路径.
		$this->parse_cache_file($file_name);		
		//分析判断缓存文件.
		if(!file_exists($this->cache_file[$file_name])){			
			Core_Action::halt('The cache file of '.$file_name.' is not exists!');
		}
		return include($this->cache_file[$file_name]);
	}

	//清空缓存文件
	public function cache_clear($file_name){
		if(empty($file_name)){
			return false;
		}
		$this->parse_cache_file($file_name);		
		if(file_exists($this->cache_file[$file_name])){			
			unlink($this->cache_file[$file_name]);
		}
		return true;
	}
	
	//清空所有的缓存文件
	public function cache_clear_all(){		
		$content_cache_dir = opendir($this->cache_dir);		
		while(false !== ($file = readdir($content_cache_dir))){			
			if($file == '.'||$file =='..'||$file == 'index.html'){					
				continue;
			}				
			unlink($this->cache_dir.$file);
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