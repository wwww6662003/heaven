<?php
/**
 * @package HeavenMVC
 * @version 1.0 DbMysql.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */
if (! defined ( 'IN_HEAVEN' )) {
	exit ();
}
class Core_DatabaseDriver_DbMysql extends Core_Base {
	
	//�������
	private static $instance; //���ڹ������singletonģʽ����
	private $db_persist = false;
	
	protected $result = null;
	protected $quote = '\'';
	protected $in_transaction = false;
	private $sql = array ();
	private $link = null;
	
	/**
	 * ���캯��,���ڳ�ʼ�����л���.
	 * @access public
	 * @param string $name mysql���Ӳ���
	 * @return mixed
	 */
	public function __construct($params) {
		//ʵ����mysql����.
		if (! $this->db_persist) {
			$this->link = $params ['port'] ? mysql_connect ( $params ['host'] . ':' . $params ['port'], $params ['username'], $params ['password'] ) : mysql_connect ( $params ['host'], $params ['username'], $params ['password'] );
		} else {
			$this->link = $params ['port'] ? mysql_pconnect ( $params ['host'] . ':' . $params ['port'], $params ['username'], $params ['password'] ) : mysql_pconnect ( $params ['host'], $params ['username'], $params ['password'] );
		}
		if (! $this->link) {
			if (HEAVEN_DEBUG == true) {
				trigger_error ( 'Mysql Server connect fail <br>Error Message: ' . mysql_errno (), E_USER_ERROR );
			} else {
				Core_Log::write ( 'Mysql Server connect fail. Error Code:' . mysql_errno (), 'Warning' );
				Core_Action::show_message ( 'Mysql Server connect fail', - 1 );
			}
		} else {
			$this->select_db ( $params ['dbname'], $params ['charset'] );
			$sql_version = mysql_get_server_info ();
			if (version_compare ( $sql_version, '5.0.2', '>=' )) {
				$this->_query ( "SET SESSION SQL_MODE=''" );
			}
		}
		return true;
	}
	/**
	 * ѡ�����ݿ�
	 * @param string $database
	 * @param string $charset
	 */
	function select_db($database, $charset = 'gbk') {
		mysql_select_db ( $database, $this->link );
		$this->_query ( 'set names ' . $charset );
	}
	/**
	 * ִ��sql���
	 * @param unknown_type $sql
	 */
	protected function _query($sql) {
		$this->result = mysql_query ( $sql, $this->link );
		if($this->result){
			return true;
		}
	}
	/**
	 * ��ѯ�����
	 * @param string $sql
	 * @return unknown
	 */
	function query($sql) {
		$this->sql [] = $sql;
		$this->_query ( $sql );
		if (empty ( $this->result ))
			throw new dbo_exception ( $this->error (), $sql );
		return true;
	}
	
	/**
	 * ��ȡһ�����
	 * @return unknown
	 */
	function fetch() {
		if (! is_resource ( $this->result ))
			return false;
		return mysql_fetch_assoc ( $this->result );
	}
	/**
     * ͨ��һ��SQL����ȡһ����Ϣ���ֶ��ͣ�
     * @access public
	 * @param string $sql SQL���
     * @return string
     */
	public function fetch_row($sql){
		$this->query($sql);
		$this->result=$this->fetch();
		if($this->result){
			$rows = $this->result;
			$this->free_result();
		}
		return $rows;
	}
	/**
	 * ͨ��һ��SQL����ȡȫ����Ϣ���ֶ��ͣ�
	 * @access public
	 * @param string $sql SQL���
	 * @return string
	 */
	public function get_array($sql) {
		$this->_query ( $sql );
		$myrow = array ();
		if ($this->result) {
			while ( $row = mysql_fetch_assoc ( $this->result ) ) {
				$myrow [] = $row;
			}
			$this->free_result ();
		}
		return $myrow;
	}
	/**
	 * ��ȡ���н����
	 * @return unknown
	 */
	function fetch_all() {
		$ret = array ();
		while ( $ret [] = $this->fetch () )
			;
		$this->free_result ();
		array_pop ( $ret );
		return $ret;
	}
	function insert($table, array $array, $execute = true) {
		$multi = false;
		reset ( $array );
		if (is_array ( current ( $array ) )) {
			$multi = true;
			$keys = array_keys ( current ( $array ) );
		} else {
			$keys = array_keys ( $array );
		}
		$sql = 'INSERT INTO ' . $this->quote . $table . $this->quote . ' (' . $this->quote . implode ( $this->quote . ', ' . $this->quote, $keys ) . $this->quote . ') VALUES ';
		$values = array ();
		if ($multi) {
			$i = 0;
			foreach ( $array as $row ) {
				$values [$i] = array ();
				foreach ( $row as $value )
					$values [$i] [] = $this->quote ( $value );
				$values [$i] = '(' . implode ( ', ', $values [$i] ) . ')';
				$i ++;
			}
			$values = implode ( ', ', $values );
		} else {
			foreach ( $array as $value )
				$values [] = $this->quote ( $value );
			$values = '(' . implode ( ', ', $values ) . ')';
		}
		$sql .= $values;
		if ($execute) {
			$result = $this->query ( $sql );
			$this->result = null;
			return $result;
		}
		return $sql;
	}
	/**
	 * ��������
	 * @param string $table
	 * @param array $data
	 * @param array $where
	 */
	function update($table, array $data, array $where = array()) {
		$sql = 'UPDATE ' . $this->quote . $table . $this->quote . ' SET ';
		$update_data = array ();
		foreach ( $data as $field => $value )
			$update_data [] = $this->quote . $field . $this->quote . ' = ' . $this->quote ( $value );
		
		$where_data = array ();
		foreach ( $where as $field => $value )
			$where_data [] = $this->quote . $field . $this->quote . ' = ' . $this->quote ( $value );
		
		$sql .= implode ( ', ', $update_data ) . ' WHERE ';
		if (empty ( $where_data ))
			$sql .= '1;';
		else
			$sql .= implode ( ' AND ', $where_data );
		$this->query ( $sql );
		$this->result = null;
	}
	
	/**
	 * ��ȡӰ�������
	 * @return unknown
	 */
	function affected_rows() {
		return mysql_affected_rows ( $this->link );
	}
	/**
	 * ��ȡ�²��������id
	 * @return unknown
	 */
	function insert_id() {
		return mysql_insert_id ( $this->link );
	}
	/**
	 * ��ȡ��¼������
	 * @return unknown
	 */
	function num_rows() {
		return mysql_num_rows ( $this->result );
	}
	/**
	 * ���ֵ����Ų�ת��
	 * @param string $data
	 * @return unknown
	 */
	function quote($data) {
		if (is_numeric ( $data ))
			return $data;
		if ($this->magic_quotes_active) {
			$data = stripslashes ( $data );
		}
		return '\'' . mysql_real_escape_string ( $data, $this->link ) . '\'';
	}
	/**
     * SQLָ�ȫ����
     * @access public
     * @param string $str 
     * @return string
     */
    public function escape_string($str) {
        return mysql_real_escape_string($str);
    }
	/**
	 * �ͷŽ����
	 *
	 */
	function free_result() {
		if (is_resource ( $this->result ))
			mysql_free_result ( $this->result );
		$this->result = null;
	}
	/**
	 * �ر����ݿ�����
	 *
	 */
	function close() {
		mysql_close ( $this->link );
	}
	/**
	 * �����������
	 * @return unknown
	 */
	function error() {
		return 'MySQL(mysql) Error [' . mysql_errno ( $this->link ) . ']: ' . mysql_error ( $this->link );
	}
	/**
	 * ����ʼ
	 * @return unknown
	 */
	function begin() {
		if (! $this->in_transaction) {
			$this->in_transaction = true;
			return $this->_begin ();
		}
		return false;
	}
	/**
	 * �����ύ
	 * @return unknown
	 */
	function commit() {
		if ($this->in_transaction) {
			$this->in_transaction = false;
			return $this->_commit ();
		}
		return false;
	}
	/**
	 * ���񲻳ɹ�
	 * @return unknown
	 */
	function rollback() {
		if ($this->in_transaction) {
			$this->in_transaction = false;
			return $this->_rollback ();
		}
		return false;
	}
	/**
	 * ��ʼ����
	 * @return unknown
	 */
	protected function _begin() {
		return $this->query ( 'BEGIN' );
	}
	/**
	 * �ύ����
	 * @return unknown
	 */
	protected function _commit() {
		return $this->query ( 'COMMIT' );
	}
	/**
	 * ����ع�
	 * @return unknown
	 */
	protected function _rollback() {
		return $this->query ( 'ROLLBACK' );
	}
	/**
	 * ��������
	 */
	function __destruct() {
		$this->rollback ();
	}
	/**
	 * ���ڱ���ľ�̬����,������Ҫ���ز�������ʹ��.
	 * @access public
	 * @param string $params �������
	 * @return void
	 */
	public static function getInstance($params) {
		if (self::$instance == null) {
			self::$instance = new Core_DatabaseDriver_Dbmysql ( $params );
		}
		return self::$instance;
	}
}
class dbo_exception extends Exception {
	
	function __construct($error, $sql) {
		$errormsg = '<fieldset style="border: 1px solid;"><legend>An error has occoured.</legend>' . "\n$sql\n$error</fieldset>";
		parent::__construct ( $errormsg );
	}
}
?>