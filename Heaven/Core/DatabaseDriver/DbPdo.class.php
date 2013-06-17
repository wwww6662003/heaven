<?php
/**
 * @package HeavenMVC
 * @version 1.0 DbPdo.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */

if(!defined('IN_HEAVEN')){
	exit;
}
class Core_DatabaseDriver_DbPdo extends Core_Base{
	
	//定义变量
	private static $instance; //用于构建类的singleton模式参数
	public $db_link;
	protected $transtimes;
	
	/**
     * 构造函数,用于初始化运行环境.
     * @access public
     * @param string $name mysql连接参数
     * @return mixed
     */
	public function __construct($params){
		//分析dsn.
		$dsn = array();
		$dsn['host']=$params['host'];
		$dsn['dbname']=$params['dbname'];
		if($dsn['port']=$params['port']){
			$dsn['port']=$params['port'];
		}
		$dsn_string = sprintf('%s:%s', $params['driver'], http_build_query($dsn, '', ';'));
		//实例化mysql连接.
		$this->db_link = $params['port'] ? new PDO($dsn_string,$params['username'],$params['password'], $params['port']) : new PDO($dsn_string,$params['username'],$params['password']);
		if(!$this->db_link){
			if(HEAVEN_DEBUG == true){
				trigger_error('DATEBASE Server connect fail <br>Error Message: '.$this->error().'<br>Error Code: '.$this->errno(),E_USER_ERROR);
			}
			else{
				Core_Log::write('DATEBASE Server connect fail. Error Code:'.$this->errno().' Error Message:'.$this->error(), 'Warning');
				Core_Action::show_message('Mysql Server connect fail', -1);
			}
		}
		return true;
	}

	/**
     * 执行SQL语句
     * @param string $sql SQL语句
     * @return array
     */
	public function query($sql){
		$result = $this->db_link->query($sql);
		if($result==false){
			if(HEAVEN_DEBUG==true){
				trigger_error('SQL execute failed: '.$sql.'<br>Error Message: '.$this->error().'<br>Error Code: '.$this->errno().'<br>Error SQL: '.$sql,E_USER_ERROR);
			}
			else{
				Core_Log::write('SQL execute failed. SQL code:'.$sql.' Error Code:'.$this->errno().'Error Message: '.$this->error());
				Core_Action::show_message('Database SQL execute failed!', -1);
			}
		}
		return $result;
	}		
	
	/**
     * 获取MYSQL 错误描述信息
     * @access public
     * @return string
     */
	public function error(){
		return $this->db_link->errorInfo();
	}

	/**
     * 获取MYSQL 错误信息代码
     * @access public
     * @return int
     */
	public function errno(){
		return $this->db_link->errorCode();
	}

	/**
     * 通过一个SQL语句获取一行信息（字段型）
     * @access public
	 * @param string $sql SQL语句
     * @return string
     */
	public function fetch_row($sql){
		$result = $this->query($sql);
		if($result){
			$rows = $result->fetch(PDO::FETCH_ASSOC);
			$result->free();
		}
		return $rows;
	}

	/**
     * 通过一个SQL语句获取全部信息（字段型）
     * @access public
	 * @param string $sql SQL语句
     * @return string
     */
	public function get_array($sql){
		$result = $this->query($sql);		
		if($result){			
			$myrow=$result->fetchAll(PDO::FETCH_ASSOC);;			
		}
		return $myrow;
	}
	
	/**
     * 获取insert_id
     * @access public
     * @return int
     */
	public function insert_id(){
		return $this->db_link->lastInsertId();
	}

	/**
     * 开启事务处理
     * @access public
     * @return mixed
     */
	public function startTrans(){		
		if($this->transtimes == false){	
			$this->db_link->beginTransaction();
		}		
		$this->transtimes = true;
		return true;
	}
	
	/**
     * 提交事务处理
     * @access public
     * @return mixed
     */
	public function commit(){		
		if($this->transtimes == true){
			$result = $this->db_link->commit();
			if($result){
				$this->transtimes = false;
			}
			else {
				if(HEAVEN_DEBUG == true){
					trigger_error('SQL Commit failed <br>Error Message: '.$this->error().'<br>Error Code: '.$this->errno(),E_USER_ERROR);
				}
				else{
					Core_Log::write('SQL Commit failed. Error Code:'.$this->errno().' Error Message: '.$this->error());
					Core_Action::show_message('Database SQL execute failed!', -1);
				}
			}
		}		
		return true;
	}
	
	/**
     * 事务回滚
     * @access public
     * @return mixed
     */
	public function rollback(){		
		if($this->transtimes == true){
			$result = $this->db_link->rollBack();			
			if($result){				
				$this->transtimes = false;
			}
			else {
				if(HEAVEN_DEBUG == true){
					trigger_error('SQL RollBack failed <br>Error Message: '.$this->error().'<br>Error Code: '.$this->errno(),E_USER_ERROR);
				}
				else{
					Core_Log::write('SQL RollBack failed. Error Code: '.$this->errno().' Error Message: '.$this->error());
					Core_Action::show_message('Database SQL execute failed!', -1);
				}
			}
		}		
		return true;
	}
	
	/**
     * SQL指令安全过滤
     * @access public
     * @param string $str  SQL字符串
     * @return string
     */
    public function escape_string($str) {
        return $this->db_link->real_escape_string($str);
    }

	 /**
     * 关闭MYSQL SERVER
     * @access public
     * @return mixed
     */
	public function close($db_link){
		if($db_link==false){
			return false;
		}
		return $db_link=false;
	}

	/**
     * 析构函数，用于类程序运行后，打扫战场的作用
     * @access public
     * @return void
     */
	public function __destruct(){
		if($this->db_link){
			$this->close($this->db_link);
		}
	}
	
	/**
     * 用于本类的静态调用,子类需要重载才能正常使用.
     * @access public
     * @param string $params 类的名称
     * @return void
     */
    public static function getInstance($params){		
		if(self::$instance == null){
			self::$instance = new Core_DatabaseDriver_DbPdo($params);
		}		
		return self::$instance;
	}
}
?>