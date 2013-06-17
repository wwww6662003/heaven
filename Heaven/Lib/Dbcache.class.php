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
	
	//�������
	private static $instance; //���ڹ������singletonģʽ����
	public $cache_dir;		//����Ŀ¼
	public $cache_file; 	//�����ļ�
	
	protected $cache_toggle; //���濪�أ���д���ء�������ʱ�������ļ�����д��
	protected $data;		//�������ݣ������ݱ��ȡ��������ϵ�����
	protected $filter;		//���ݱ��ֶεĹ��˺���Ч�ֶ�
	protected $mode;		//������������
	protected $keys;		//������������:�����Ͳ���
	protected $values;	//������������:�����Ͳ���
	public $lifetime;	//������������
	
	//���캯��,��ʼ��������
	public function __construct(){
		$this->cache_dir = APP_PATH.'/Cache/data/';	//Ĭ�ϻ���Ŀ¼
		$this->lifetime = 86400;			//Ĭ�ϻ�������Ϊ24Сʱ
		$this->cahce_toggle = 0;				//Ĭ�ϻ��濪�عر�
		return true;
	}
	
	//���������ļ���·��.
	protected function parse_cache_file($file_name){
		if(!$this->cache_file[$file_name]){				
			$this->cache_file[$file_name] = $this->cache_dir.$file_name.'_cache.php';
		}
		return $this->cache_file[$file_name];
	}
	
	//��ȡ�������ݡ�
	protected function get_cache_data($file_name){
		//��ȡ���ݱ�����.
		$model = Controller::model($file_name);
		$data = $model->findAll();
		//����mode.
		if($this->mode){			
			$this->mode = 1;
		}		
		//��ʱ����.
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
		//��ʱ������������ڴ��ռ��.
		unset($this->mode);
		unset($data);
		$this->data[$file_name] = $data_temp;		
		//��ʱ������������ڴ��ռ��.
		unset($data_temp);
		return $this->data[$file_name];
	}

	//���ɻ����ļ���
	protected function create_cache($file_name){
		$this->parse_cache_file($file_name);
		$contents = "<?php\r\n";
		$contents .= "return ";
		$contents .= var_export($this->data[$file_name], true);
		$contents .= "\r\n?>";
		//������Ŀ¼������ʱ�����д���Ŀ¼��
		if(!file_exists($this->cache_dir)){				
			mkdir($this->cache_dir, 0777);
		}
		file_put_contents($this->cache_file[$file_name], $contents);
		return true;
	}

	//����������Ӷ��ж�TOGGLE���صĿ�����رա�
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

	//���ػ��棬�����е���������
	public function load($file_name){		
		//�����ж�.
		if(empty($file_name)){			
			return false;
		}
		//���������ļ��Ƿ���д.
		$this->parse_cache($file_name);
		if($this->cache_toggle){				
			$this->get_cache_data($file_name);				
			$this->create_cache($file_name);
		}
		return include($this->cache_file[$file_name]);
	}
	
	//���û�������.
	public function lifetime($life_time){		
		if(empty($life_time)){
			return false;
		}
		$this->lifetime = $life_time;
		return $this;
	}

	//���û�����������,������.
	public function limit($field){
		if(empty($field)||!is_array($field)){			
			return false;
		}
		$this->mode = 2;
		$this->filter = $field;		
		return $this;
	}

	//���û�����������,������.
	public function config($key, $value){
		if(empty($key)||empty($value)){
			return false;
		}		
		$this->mode = 3;
		$this->keys = $key;
		$this->values = $value;
		return $this;
	}

	//�����ļ������������ɻ����ļ�.
	public function set($file_name, $data){
		//��������.
		if(empty($file_name)||!is_array($data)){			
			return false;
		}
		//��ȡ�����ļ�·��.
		$this->parse_cache_file($file_name);
		$this->data[$file_name] = $data;
		//���������ļ�.
		$this->create_cache($file_name);
		return true;
	}
	
	//��ȡ$file_name�Ļ����ļ�����.
	public function get($file_name){
		//��������.
		if(empty($file_name)){			
			return false;
		}
		//��ȡ�����ļ�·��.
		$this->parse_cache_file($file_name);		
		//�����жϻ����ļ�.
		if(!file_exists($this->cache_file[$file_name])){			
			Core_Action::halt('The cache file of '.$file_name.' is not exists!');
		}
		return include($this->cache_file[$file_name]);
	}

	//��ջ����ļ�
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
	
	//������еĻ����ļ�
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