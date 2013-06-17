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
	
	//�������
	private static $instance; //���ڹ������singletonģʽ����
	
	/**
     * ��־д��
     * @param string $message ��־����
     * @param string $leverl ��־����
     * @param string $log_file_name ��־�ļ�
     * @return mixed
     */
	public function write($message, $leverl='Error', $log_file_name=false){		
		$time_now = date('[Y-m-d H:i:s]', time());
		if(empty($log_file_name)){
			$log_file_name = APP_PATH.'/Logs/'.date('Y_m_d', time()).'.log';
		}
		//�����־�ļ��Ƿ񳬹���������С
		if(file_exists($log_file_name) && (filesize($log_file_name) >= 2097152)){
			rename($log_file_name, APP_PATH.'/Logs/'.time().'-'.basename($log_file_name));
		}
		error_log("{$time_now} {$leverl}: {$message}\r\n", 3, $log_file_name);
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