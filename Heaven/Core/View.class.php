<?php
/**
 * @package HeavenMVC
 * @version 1.0 View.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */
if (! defined ( 'IN_HEAVEN' )) {
	exit ();
}
class Core_View extends Core_Base {
	private static $instance;
	public $template_dir;
	public $compile_dir;
	public $cache_dir;
	
	protected $tpl_vars;
	protected $cache_flag;
	protected $tpl_filename;
	
	protected static $file;
	protected static $cache;
	protected static $template;
	/**
	 * 构造函数,用于初始化运行环境.
	 * @access public
	 * @return mixed
	 */
	public function __construct() {
		self::$file = Lib_File::getInstance ();
		self::$cache = Core_Cache::getInstance ();
		self::$template = Core_Template::getInstance ();
		
		$this->tpl_vars = array ();
		$this->template_dir = str_replace ( '\\', '/', APP_PATH . '/Tpl/' . TPL_NAME . '/' . APP_NAME . '/' );
		$this->compile_dir = str_replace ( '\\', '/', APP_PATH . '/Cache/template_c/' . TPL_NAME . '/' . APP_NAME . '/' );
		$this->cache_dir = str_replace ( '\\', '/', APP_PATH . '/Cache/template/' . TPL_NAME . '/' . APP_NAME . '/' );
		$this->compile_dir = is_dir ( $this->compile_dir ) ? $this->compile_dir : (self::$file->mkdirs ( $this->compile_dir ));
		$this->cache_dir = is_dir ( $this->cache_dir ) ? $this->cache_dir : (self::$file->mkdirs ( $this->cache_dir ));
	}
	
	/**
	 * 对模板变量进行赋值
	 * @access public
	 * @return void
	 * @param string or array $handle
	 * @param unknown_type $value
	 */
	public function assign($handle, $value = false) {
		if (is_array ( $handle )) {
			foreach ( $handle as $key => $val ) {
				$this->tpl_vars [$key] = $val;
			}
		} else {
			$this->tpl_vars [$handle] = $value;
		}
	}
	
	/**
	 * 输出模板内容.
	 * @access public
	 * @return void
	 * @param string $filename
	 */
	public function display($filename = false,$opt='') {
		$this->tpl_filename=$filename;
		if ($filename == false) {
			$filename = md5 ( common_config ( 'controller' ).$opt ) . common_config ( 'cache_extname' );
		} else {
			$filename = md5 ($filename.$opt) . common_config ( 'cache_extname' );
		}
		if ($this->cache_flag == 0) {
			ob_start ();
			$this->create_template_c ( $this->tpl_filename );
			$content_html = ob_get_contents ();
			ob_end_clean ();
			self::$cache->create_cache ( $content_html, $filename );
		} else if ($this->cache_flag == 1) {
			$this->create_template_c ( $this->tpl_filename );
		} else if ($this->cache_flag == - 1) {
			require ($this->cache_dir . $filename);
		} else if ($this->cache_flag > 1) {
			$flag = self::$cache->check_cache_file ( $filename );
			if ($flag == true) {
				ob_start ();
				$this->create_template_c ( $this->tpl_filename );
				$content_html = ob_get_contents ();
				ob_end_clean ();
				self::$cache->create_cache ( $content_html, $filename );
			} else {
				require ($this->cache_dir . $filename);
			}
		}
	}
	
	/**
	 * 处理布局,模板标签include的部分.
	 * @access public
	 * @return void
	 * @param string $filename
	 */
	public function layout($filename) {
		if (empty ( $filename ))
			return false;
		$content = file_get_contents ( common_config ( 'app_web_dir' ) . 'Tpl/' . TPL_NAME . '/Public/' . $filename );
		$content_c = self::$template->handle_template_content ( $content );
		file_put_contents ( common_config ( 'app_real_dir' ) . 'Cache/Template_c/' . TPL_NAME . '/Public/' . $filename, $content_c, LOCK_EX );
		ob_start ();
		require (common_config ( 'app_real_dir' ) . 'Cache/Template_c/' . TPL_NAME . '/Public/' . $filename);
		$content_html = ob_get_contents ();
		ob_end_clean ();
		file_put_contents ( common_config ( 'app_real_dir' ) . 'Cache/Template/' . TPL_NAME . '/Public/' . md5 ( $filename ) . '.php', $content_html, LOCK_EX );
		require (common_config ( 'app_real_dir' ) . 'Cache/Template/' . TPL_NAME . '/Public/' . md5 ( $filename ) . '.php');
	}
	/**
	 * 建立模板编译后的文件
	 * @access public
	 * @return void
	 * $param void
	 */
	public function create_template_c($filename = false) {
		if ($filename == false) {
			$tpl_filename = $this->template_dir . common_config ( 'controller' ) . common_config ( 'tpl_extname' );
		} else {
			$tpl_filename = $this->template_dir . $filename.common_config ( 'tpl_extname' );
		}
		$content = file_get_contents ( $tpl_filename );
		$content_c = self::$template->handle_template_content ( $content );
		file_put_contents ( $this->compile_dir . common_config ( 'controller' ) . common_config ( 'cache_extname' ), $content_c, LOCK_EX );
		require ($this->compile_dir . common_config ( 'controller' ) . common_config ( 'cache_extname' ));
	}
	
	/**
	 * 设置缓存时间,0不缓存但建立缓存文件，1不缓存也不建立缓存文件,-1永久缓存,
	 * @access public
	 * @return void
	 * @param int $time
	 */
	public function set_cache_lifetime($time = 0) {
		$lifetime = self::$cache->set_cache_lifetime ( $time );
		if ($lifetime == 0) {
			$this->cache_flag = 0;
		} else if ($lifetime == - 1) {
			$this->cache_flag = - 1;
		} else {
			$this->cache_flag = $lifetime;
		}
	}
	/**
	 * 析构函数，清空所有的临时变量
	 * @access public 
	 * @return void
	 */
	public function __destruct() {
		$this->tpl_vars = array ();
	}
	
	/**
	 * 用于本类的静态调用,子类需要重载才能正常使用.
	 * @access public
	 * @param string $params 类的名称
	 * @return void
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new self ( );
		}
		return self::$instance;
	}
}
?>