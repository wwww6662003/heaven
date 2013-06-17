<?php
/**
 * @package HeavenMVC
 * @version 1.0 File.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */
if (! defined ( 'IN_HEAVEN' )) {
	exit ();
}
class Lib_File extends Core_Base{
	
	private static $instance;//用于构建类的singleton模式参数
	
	/**
	 * @access public
	 * @return void
	 * @param unknown_type $dirs
	 */
	public function checkdir($dirs) {
		$arrfile = glob ( $dirs );
		foreach ( $arrfile as $key => $val ) {
			if (is_dir ( $val )) {
				$this->checkdir ( $val . '/*' );
			} else {
				include(realpath ( $val ));
			}
		}
	}
	/**
	 * 创建多级目录
	 * @access public
	 * @param unknown_type $dir
	 * @return unknown
	 */
	public function mkdirs($dir) {
		if (! is_dir ( $dir )) {
			if (! $this->mkdirs ( dirname ( $dir ) )) {
				return false;
			}
			if (! mkdir ( $dir, 0777 )) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * 删除多级目录
	 * @access public
	 * @return void
	 * @param unknown_type $dir
	 */
	public function rmdirs($dir) {
		$d = dir ( $dir );
		while ( false !== ($child = $d->read ()) ) {
			if ($child != '.' && $child != '..') {
				if (is_dir ( $dir . '/' . $child ))
					$this->rmdirs ( $dir . '/' . $child );
				else
					unlink ( $dir . '/' . $child );
			}
		}
		$d->close ();
		rmdir ( $dir );
	}
	
	/**
	 * 实例化本类
	 * @access public 
	 * @return unknown
	 */
 public static function getInstance(){		
		if(self::$instance == null){		
			self::$instance = new self;
		}		
		return self::$instance;
	}

}

/*$f=new Lib_File();
//$f->checkdir('./*'); 
$f->mkdirs('div/css/layout');
$f->rmdirs('./div');*/
?>