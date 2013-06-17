<?php
/**
 * @package HeavenMVC
 * @version 1.0 Log.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */

if(!defined('IN_HEAVEN')){
	exit;
}
class Core_Log extends Core_Base{
	
	//定义变量
	private static $instance; //用于构建类的singleton模式参数
	
	/**
     * 日志写入
     * @param string $message 日志内容
     * @param string $leverl 日志类型
     * @param string $log_file_name 日志文件
     * @return mixed
     */
	public function write($message, $leverl='Error', $log_file_name=false){		
		$time_now = date('[Y-m-d H:i:s]', time());
		if(empty($log_file_name)){
			$log_file_name = APP_PATH.'/Logs/'.date('Y_m_d', time()).'.log';
		}
		//检查日志文件是否超过最大允许大小
		if(file_exists($log_file_name) && (filesize($log_file_name) >= 2097152)){
			rename($log_file_name, APP_PATH.'/Logs/'.time().'-'.basename($log_file_name));
		}
		error_log("{$time_now} {$leverl}: {$message}\r\n", 3, $log_file_name);
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