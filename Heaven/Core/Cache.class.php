<?php
/**
 * @package HeavenMVC
 * @version 1.0 Cache.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */
if (! defined ( 'IN_HEAVEN' )) {
	exit ();
}
class Core_Cache extends Core_Base {
	private static $instance;
	protected static $file;
	
	public $cache_dir;
	public $lifetime;
	public $cache_on;
	protected $filters;
	protected $cache_file;
	
	/**
	 * 构造函数,用于初始化运行环境.
	 * @access public
	 * @return mixed
	 */
	public function __construct() {
		self::$file = Lib_File::getInstance ();
		$this->cache_dir = str_replace ( '\\', '/', APP_PATH . '/Cache/template/' . TPL_NAME . '/' . APP_NAME . '/' );
		$this->cache_dir = is_dir ( $this->cache_dir ) ? $this->cache_dir : (self::$file->mkdirs ( $this->cache_dir ));
		$this->cache_on = false;
		$this->lifetime = 10;
		return true;
	}
	/**
	 * 设置缓存周期时间.
	 * @access public
	 * @return class
	 */
	public function set_cache_lifetime($expire = 0) {
		$this->lifetime = $expire;
		return $this->lifetime;
	}
	
	/**
	 * 建立缓存文件
	 * @access public
	 * @return void
	 * @param string $filename
	 */
	public function create_cache($content, $filename) {
		$fp = fopen ( $this->cache_dir . $filename, 'w' );
		flock ( $fp, LOCK_EX );
		fwrite ( $fp, $content ) or die ( '写文件错误' );
		echo $content;
	}
	
	/**
	 * 判断缓存文件是否需要重新写入.
	 * @access public
	 * @return mixed
	 */
	public function check_cache_file($filename) {
		if (file_exists ( $this->cache_dir . $filename )) {
			$this->cache_on = ($_SERVER ['REQUEST_TIME'] - filemtime ( $this->cache_dir . $filename ) > $this->lifetime) ? true : false;
		} else {
			$this->cache_on = true;
		}
		return $this->cache_on;
	}
	
	/**
	 * 清除缓存文件(template下面是静态的缓存文件)
	 * @access public 
	 * @return void
	 */
	public function clear_cache_file() {
		if (glob ( $this->cache_dir . '*' )) {
			$files = glob ( $this->cache_dir . '*' );
			foreach ( $files as $f ) {
				@unlink($f);
			}
		}
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