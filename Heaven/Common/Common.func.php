<?php
/**
 * ���������ò���,��ȫ������
 * @return void
 * @param string or array $key,string value
 */
function common_config($key, $value = null) {
	static $_config = array ();
	if (is_string ( $key )) {
		if (isset ( $_config [$key] )) {
			return $_config [$key];
		} else {
			if (! is_null ( $value )) {
				$_config [$key] = $value;
			} else {
				return false;
			}
		}
	}
	if (is_array ( $key )) {
		return $_config = array_merge ( $_config, array_change_key_case ( $key ) );
	}
	return false;
}

/**
 * ��ʼ��������
 * @return void
 * @param string $classname,string $flag
 */
function controller_init($name, $flag = 'Heaven_') {
	static $_action = array ();
	if ($_action [$flag . $name] == null) {
		$_action [$flag . $name] = new $name ( );
	}
	return $_action [$flag . $name];
}
/**
 * ��ʼ��ģ��
 * @return void
 * @param string $classname,string $flag
 */
function model_init($table_name) {
	static $model = array ();
	$model_name = ucfirst ( strtolower ( $table_name ) ) . 'Model';
	if ($model [$model_name] == null) {
		$model [$model_name] = new $model_name ( );
	}
	return $model [$model_name];
}
/**
 * ��ʼģ��
 * @return void
 * @param string $classname,string $flag
 */
function lib_init($name, $flag = 'Heaven_') {
	static $_Lib = array ();
	if ($_Lib [$flag . $name] == null) {
		$_Lib [$flag . $name] = new $name ( );
	}
	return $_Lib [$flag . $name];
}
/**
 * ��ʽ�������Ϣ
 * @return void
 * @param string $message,string format
 */
function format_out($message, $format = '%s') {
	printf ( $format, $message );
	exit ();
}
/**
 * �������
 * @return void
 * @param string message,string flag
 */
function error_out($message, $flag=E_USER_ERROR) {
	trigger_error ( $message, $flag);
}
?>