<?php
/**
 * @package HeavenMVC
 * @version 1.0 Heaven.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */
if (! defined ( 'IN_HEAVEN' )) {
	exit ();
}
class Core_Application {
	
	//private static $module = ''; //模块
	private static $controller = ''; //控制器
	private static $action = ''; //动作
	private static $params = array (); //参数数组
	/**
	 * 解析url地址
	 * @access public
	 * @return void
	 */
	public static function parse_url() {
		$path = intval ( strpos ( $_SERVER ['REQUEST_URI'], '?' ) );
		if (HEAVEN_REWRITE == true) {
			$url_all = $_SERVER ['REQUEST_URI'];
			if (strrpos ( $url_all, 'index.php' )) {
				error_out ( $GLOBALS ['error_config'] ['error_rewrite_url'] );
			}
			/*if (intval ( strpos ( $url_all, APP_NAME ) ) > 0) {
				$url_all = substr ( $url_all, strpos ( $url_all, APP_NAME ) );
			} else {
				error_out ( $GLOBALS ['error_config'] ['error_url_letter']);
			}*/
			//http://admin.hello.com/login/login/id/1
			$url_arr = explode ( '/', $url_all );
			array_shift ( $url_arr );
			if (! empty ( $url_arr )) {
				/*$m = array_shift ( $url_arr );
				self::$module = ucfirst ( $m ) ? ucfirst ( $m ) : 'Home';*/
				$c = array_shift ( $url_arr );
				self::$controller = $c ? $c : 'Index';
				$a = array_shift ( $url_arr );
				self::$action = strtolower ( $a ) ? strtolower ( $a ) : 'index';
				if (! empty ( $url_arr )) {
					for($i = 0; $i < count ( $url_arr ) / 2 + 1; $i += 2) {
						$arr3 [$url_arr [$i]] = $url_arr [$i + 1];
					}
				}
				self::$params = $arr3;
			}
		} else {
			if (! strrpos ( $url_all, 'index.php' )) {
				error_out ( $GLOBALS ['error_config'] ['error_not_rewrite_url'] );
			}
			if ($path) {
				//http://admin.hello.com/index.php?controller=login&action=login&id=1
				$arr = parse_url ( $_SERVER ['REQUEST_URI'] );
				parse_str ( $arr ['query'], $arr2 );
				if (! empty ( $arr2 )) {
					/*$m = array_shift ( $arr2 );
					self::$module =  ucfirst ( $m )  ?  ucfirst ( $m )  : 'Home';*/
					$c = array_shift ( $arr2 );
					self::$controller = $c ? $c : 'Index';
					$a = array_shift ( $arr2 );
					self::$action = $a ? $a : 'index';
					if (! empty ( $arr2 )) {
						self::$params = $arr2;
					}
				}
			} else {
				//http://admin.hello.com/index.php/login/login/id/1
				$arr = trim ( $_SERVER ['PATH_INFO'], '/' );
				$arr2 = explode ( '/', $arr );
				if (! empty ( $arr2 )) {
					/*$m = array_shift ( $arr2 );
					self::$module =  ucfirst ( $m )  ?  ucfirst ( $m )  : 'Home';*/
					$c = array_shift ( $arr2 );
					self::$controller = $c ? $c : 'Index';
					$a = array_shift ( $arr2 );
					self::$action = $a ? $a : 'index';
					if (! empty ( $arr2 )) {
						for($i = 0; $i < count ( $arr2 ) / 2 + 1; $i += 2) {
							$arr3 [$arr2 [$i]] = $arr2 [$i + 1];
						}
					}
					self::$params = $arr3;
				}
			}
		}
		//echo self::$module.'<br/>'.self::$controller.'<br/>'.self::$action.'<br/>'.$GLOBALS['ext_name']['tpl_extname'];print_r($arr3);
		//global info
		//common_config ( 'module', self::$module );
		common_config ( 'controller', self::$controller ? self::$controller : $GLOBALS ['site_info'] ['default_controller_method'] );
		common_config ( 'action', self::$action ? self::$action : $GLOBALS ['site_info'] ['default_controller_method'] ); //action's method name
		common_config ( 'params', self::$params ); //params is array
		common_config ( 'cache_extname', $GLOBALS ['ext_name'] ['cache_extname'] );
		common_config ( 'tpl_extname', $GLOBALS ['ext_name'] ['tpl_extname'] );
		common_config ( 'app_web_dir', 'http://' . $_SERVER ['SERVER_NAME'] . substr ( $_SERVER ['SCRIPT_NAME'], 0, strrpos ( $_SERVER ['SCRIPT_NAME'], '/' ) ) . '/' );
		common_config ( 'app_web_dir_public', 'http://' . $_SERVER ['SERVER_NAME'] . substr ( $_SERVER ['SCRIPT_NAME'], 0, strrpos ( $_SERVER ['SCRIPT_NAME'], '/' ) ) . '/Public/' );
		common_config ( 'app_web_dir_tpl_public', 'http://' . $_SERVER ['SERVER_NAME'] . substr ( $_SERVER ['SCRIPT_NAME'], 0, strrpos ( $_SERVER ['SCRIPT_NAME'], '/' ) ) . '/Public/Tpl/Public/' );
		common_config ( 'app_real_dir', $_SERVER ['DOCUMENT_ROOT'] . substr ( $_SERVER ['SCRIPT_NAME'], 0, strrpos ( $_SERVER ['SCRIPT_NAME'], '/' ) ) . '/' );
		//sitename info
		common_config ( 'title', $GLOBALS ['site_info'] ['title'] );
		common_config ( 'keywords', $GLOBALS ['site_info'] ['keywords'] );
		common_config ( 'description', $GLOBALS ['site_info'] ['description'] );
		common_config ( 'charset', $GLOBALS ['site_info'] ['charset'] );
		common_config ( 'version', $GLOBALS ['site_info'] ['version'] );
	}
	
	/**
	 * 系统运行函数
	 * @access public
	 * @return void
	 */
	public static function Run() {
		self::parse_url ();
		self::$controller = ucfirst ( self::$controller ) . 'Action';
		$controller = controller_init ( self::$controller );
		$method = self::$action;
		if (method_exists ( $controller, self::$action )) {
			$controller->$method ();
		} else {
			$controller->error_write ( self::$controller, $method );
		}
	
	}
	
	/**
	 * 自动加载相应的类库
	 * @access public 
	 * @return void
	 * @param unknown_type $class_name
	 */
	public static function autoload($classname) {
		//core class and extensiton class autoload
		if (strpos ( $classname, '_' )) {
			$path = '/';
			$arr = explode ( '_', $classname );
			foreach ( $arr as $k => $v ) {
				$path .= $arr [$k] . '/';
			}
			require_once str_replace ( '\\', '/', HEAVEN_PATH . rtrim ( $path, '/' ) . '.class.php' );
		} else {
			//user class autoload
			if (substr ( $classname, - 6 ) == 'Action') {
				$action_class_path = str_replace ( '\\', '/', APP_PATH . '/Lib/Action/' . APP_NAME . '/' . $classname . '.class.php' );
				if (file_exists ( $action_class_path )) {
					require ($action_class_path);
				} else {
					error_out ( $action_class_path );
				}
			} else if (substr ( $classname, - 5 ) == 'Model') {
				$model_class_path = str_replace ( '\\', '/', APP_PATH . '/Lib/Model/' . APP_NAME . '/' . $classname . '.class.php' );
				if (file_exists ( $model_class_path )) {
					require ($model_class_path);
				} else {
					error_out ( $model_class_path );
				}
			}
		}
	}
}
//注册__autoload()函数.


spl_autoload_register ( array ('Core_Application', 'autoload' ) );
?>