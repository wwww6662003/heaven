<?php 
/**
 * @package HeavenMVC
 * @version 1.0 Model.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */
if(!defined('IN_HEAVEN')){
	exit;
}
abstract class Core_Model extends Core_Base{	
	protected $table_info;  
	protected $primary_key; 
	protected $table_name;  
	protected $table_prefix; 
	protected $table_field;
	protected $class_name;  

	protected $db;			
	protected $params;      
	protected $option;      
	protected $myrow;      
	protected $order;      
	protected $cache_dir;	
	
	/**
     * 构造函数,用于初始化运行环境.
     * @access public
     * @return mixed
     */
	public function __construct(){
		if(!$this->db){				
			$this->params = $this->init();			
			$this->db = Core_Database::factory($this->params);
		}
		return true;
	}

	 /**
     * 用于对confilg文件数据进行数据分析,初始化运行环境.
     * @access protected
     * @return mixed
     */
	protected function init(){		
		if($this->params){
			return $this->params;
		}
		if(file_exists(APP_PATH.'/Config/Config.inc.php')){
			include(APP_PATH.'/Config/Config.inc.php');
		}
		else {
			trigger_error('The file config is not exists', E_USER_ERROR);
		}
		if(!is_array($GLOBALS['db_config'])){
			trigger_error('The config file content is error', E_USER_ERROR);
		}
		if($GLOBALS['db_config']['host']&&$GLOBALS['db_config']['username']&&$GLOBALS['db_config']['password']&&$GLOBALS['db_config']['dbname']){			
			$GLOBALS['db_config']['host'] = trim($GLOBALS['db_config']['host']);
			$GLOBALS['db_config']['username'] = trim($GLOBALS['db_config']['username']);
			$GLOBALS['db_config']['password'] = trim($GLOBALS['db_config']['password']);
			$GLOBALS['db_config']['dbname'] = trim($GLOBALS['db_config']['dbname']);
		}
		else {			
			trigger_error('Mysql Server HostName or UserName or Password or DatabaseName is error in the config file', E_USER_ERROR);
		}
		$GLOBALS['db_config']['charset'] = ($GLOBALS['db_config']['charset']) ? trim($GLOBALS['db_config']['charset']) : 'gbk';
		$GLOBALS['db_config']['prefix'] = ($GLOBALS['db_config']['prefix']) ? trim($GLOBALS['db_config']['prefix']) : '';
		return $GLOBALS['db_config'];
	}
	
	//	+---------------------------------------------------
	//	|			第一部分： 数据表信息处理
	//	+---------------------------------------------------
	
	//加载$this->table_name
	protected function parse_table_name(){		
		if(!$this->table_name){				
			$this->get_table_name();
		}
		return $this->table_name;
	}

	//加载$this->tabl_info
	protected function parse_table_info(){		
		if(!$this->table_info){				
			$this->get_table_info();
		}
		return $this->table_info;
	}

	//加载$this->primary_key
	protected function parse_table_primarykey(){		
		if(!$this->primary_key){				
			$this->get_table_primarykey();
		}
		return $this->primary_key;
	}

	//加载$this->table_field
	protected function parse_table_field(){		
		if(!$this->table_field){			
			$this->get_table_field();
		}
		return $this->table_field;
	}

	//获取类名
	protected function get_class_name(){
		if(!$this->class_name){		
			$class_name = get_class($this);
			//echo $class_name;die();
			$class_name= strtolower($class_name);	
			$this->class_name = $class_name;
		}
		return $this->class_name;
	}

	//获取数据表前缀
	protected function get_table_prefix(){
		if(!$this->table_prefix){				
			$this->table_prefix = (!empty($this->params['prefix'])) ? $this->params['prefix'] : '';
		}
		return $this->table_prefix;
	}

	//获取数据表信息
	protected function get_table_info(){
		
		$this->parse_table_name();
		$sql="SHOW FIELDS FROM `{$this->table_name}`";		
		$this->table_info = $this->db->get_array($sql);
		return $this->table_info;
	}

	//获取数据表名
	protected function get_table_name(){		
		$this->get_class_name();
		$this->get_table_prefix();
		$this->table_name = (!empty($this->table_prefix)) ? $this->table_prefix.substr($this->class_name,0,-5) : substr($this->class_name,0,-5);
		return $this->table_name;
	}

	//cache_file文件生成
	protected function parse_cache_file($name){
		if(!$this->cache_dir){				
			$this->cache_dir = APP_PATH.'/Cache/Model/';
		}
		$this->parse_table_name();
		return $this->cache_dir.$this->table_name.'_'.$name.'.data.php';
	}
	
	//生成缓存文件
	protected function create_cache($name, $data){
		$cache_file = $this->parse_cache_file($name);	
		$content = "<?php \r\n";
		$content .= "return ";
		$content .= var_export($data,true).";";
		$content .= "\r\n?>";
		//判断cache_dir是否存在，不存在则建立目录
		if(!is_dir($this->cache_dir)){				
			mkdir($this->cache_dir,0777);
		}
		file_put_contents($cache_file,$content,LOCK_EX);
		return true;
	}

	//加载缓存文件
	protected function load_cache($name){		
		return include($this->parse_cache_file($name));
	}

	//清除缓存文件
	public function cache_clear(){
		$option_clear = array('primarykey', 'field');
		foreach ($option_clear as $lines){			
			$file_clear = $this->parse_cache_file($lines);			
			if(file_exists($file_clear)){			
				unlink($file_clear);
			}
		}		
		return true;
	}

	//获取数据表的主键
	protected function get_table_primarykey(){
		if(file_exists($this->parse_cache_file('primarykey'))){				
			$this->primary_key = $this->load_cache('primarykey');
		}
		else {				
			$this->parse_table_info();
			foreach ($this->table_info as $val){					
				if($val['Key']=='PRI'){						
					$this->primary_key = $val['Field'];
				}
			}				
			$this->create_cache('primarykey',$this->primary_key);
		}
		return $this->primary_key;
	}

	//获取数据表字段信息
	protected function get_table_field(){
		if(file_exists($this->parse_cache_file('field'))){				
			$this->table_field = $this->load_cache('field');
		}
		else {				
			$this->parse_table_info();				
			$fields = array();				
			foreach ($this->table_info as $val){
				$fields[] = $val['Field'];
			}				
			$this->table_field = $fields;				
			$this->create_cache('field',$this->table_field);
		}
		return $this->table_field;
	}
	
	//	+---------------------------------------------------
	//	|			第二部分： Select SQL 语句处理
	//	+---------------------------------------------------
	
	//处理from(),where(),order()等函数参数，特别是对参数为数组的处理
	protected function parse_options($string, $option=false){
		if(is_array($string)){				
			$option_str = '';			
			if($option){
				foreach ($string as $val){					
					$option_str .= ' '.trim($val).' AND';
				}
				$option_str = substr($option_str,0,-3);
			}
			else {
				foreach ($string as $val){
					$str = $this->parse_options_init($val);					
					$option_str .= ' '.trim($str).',';
				}
				$option_str = substr($option_str,0,-1);
			}
		}
		else {				
			$option_str = ($option) ? trim($string) : $this->parse_options_init($string);
		}
		return $option_str;
	}
	
	//用于处理d.demo为:`d`.`demo`
	protected function parse_options_init($string){				
		if(strpos($string, '.')){
			preg_match('/(.+)\.(.+)/', $string, $params);
			$option_str = ' `'.trim($params[1]).'`.`'.trim($params[2]).'`';
		}
		else{
			$option_str = ' `'.$string.'`';
		}
		return $option_str;
	}
	
	//用于处理FROM()函数optings特殊函数
	protected function parse_from_options($string){
		$this->get_table_prefix();
		if(is_array($string)){				
			$option_str = '';				
			foreach($string as $key=>$value){
				if(!empty($this->table_prefix)){
					$option_str .= is_int($key) ? ' `'.$this->table_prefix.trim($value).'`,' : ' `'.$this->table_prefix.trim($value).'` AS `'.$key.'`,';
				}
				else {
					$option_str .= is_int($key) ? ' `'.trim($value).'`,' : ' `'.trim($value).'` AS `'.$key.'`,';
				}
			}				
			$option_str = substr($option_str, 0, -1);  
		}
		else {				
			$option_str = !empty($this->table_prefix) ? '`'.$this->table_prefix.trim($string).'`' : '`'.trim($string).'`';
		}		
		return $option_str;
	}
	
	//用于处理参数
	protected function parse_columns($string){				
		if(preg_match('/COUNT\((.+)\)|count\((.+)\)|AVG\((.+)\)|avg\((.+)\)|SUM\((.+)\)|sum\((.+)\)|MAX\((.+)\)|max\((.+)\)|MIN\((.+)\)|min\((.+)\)|DISTINCT(.+)|distinct(.+)/', $string)){
			$option_str = trim($string);
		}
		else{			
			if(strpos($string, '.')){
				preg_match('/(.+)\.(.+)/', $string, $params);
				$option_str = ' `'.trim($params[1]).'`.`'.trim($params[2]).'`';			
				$option_str = str_replace('`*`', '*', $option_str);
			}
			else{
				$option_str = ' `'.$string.'`';
			}
		}		
		return $option_str;
	}
	
	//用于处理FROM()函数columns特殊函数
	protected function parse_from_columns($columns){		
		if(is_array($columns)){			
			$opting_str = '';			
			foreach ($columns as $key=>$value){				
				$str = $this->parse_columns($value);				
				$opting_str .= is_int($key) ? $str.',' : $str.' AS `'.$key.'`,'; 
			}			
			$opting_str = substr($opting_str, 0, -1);
		}
		else {			
			$opting_str = $this->parse_columns($columns);
		}		
		return $opting_str;
	}
	
	
	//from('数据表','查询字段')用于处理 SELECT fields FROM table之类的SQL语句部分
	public function from($name, $item=false){
		if(empty($name)){			
			return false;
		}		
		$table_str = $this->parse_from_options($name);				
		$item_str = ($item==true) ? $this->parse_from_columns($item) : '*';				
		$this->option['from'] = 'SELECT '.$item_str.' FROM '.$table_str;		 	
		return $this;
	}
	
	//where('查询条件')用于处理 WHERE id=0537 诸如此类的SQL语句部分,注：当参数中含字符串时应先用quote_into()进行转义
	public function where($string){
		if(empty($string)){				
			return false;
		}
		$where_str = $this->parse_options($string,true);		
		$where_str = $this->parse_where($where_str);
		$this->option['where'] .= ($this->option['where']) ? ' AND '.$where_str : ' WHERE '.$where_str;
		return $this;
	}
	
	//or_where('查询条件')用于处理 OR WHERE id=0531 诸如此类的SQL语句部分,注：当参数中含字符串时应先用quote_into()进行转义
	public function orwhere($string){
		if(empty($string)){			
			return false;
		}		
		$or_where_str = $this->parse_options($string,true);
		$or_where_str = $this->parse_where($or_where_str);		
		$this->option['or_where'] .= ($this->option['or_where']) ? ' AND '.$or_where_str : ' OR '.$or_where_str;				
		return $this;
	}
	
	//用于处理where,orwhere参数特殊处理.
	protected function parse_where($string){		
		$Regexp_array = array(
		'#(\w+?)\s*=#i',
		'#(\w+?)\s*([>|<])#i',
		'#(\w+?)\s*([>|<|!|=]=)#i',
		'#(\w+?)\s+like#i',
		'#(\w+?)\s+between\s+(\w+?)\s+and#i',
		'#(\w+?)\s+in\((.+?)\)#i',
		'#match\((.+?)\)\s+against\((.+?)\)#i',
		);
		$Replace_array = array(
		'`\\1`=',
		'`\\1`\\2',
		'`\\1`\\2',
		'`\\1` LIKE',
		'`\\1` BETWEEN \\2 AND',
		'`\\1` IN(\\2)',
		'MATCH (\\1) AGAINST (\\2)',
		);		
		$Regexp_array02 = array(
		'#(\w+?)\.(\w+?)\s*=#i',
		'#(\w+?)\.(\w+?)\s*([>|<])#i',
		'#(\w+?)\.(\w+?)\s*([>|<|!|=]=)#i',
		'#(\w+?)\.(\w+?)\s+like#i',
		'#(\w+?)\.(\w+?)\s+between\s+(\w+?)\s+and#i',
		'#(\w+?)\.(\w+?)\s+in\((.+?)\)#i',
		);		
		$Replace_array02 = array(
		'`\\1`.`\\2`=',
		'`\\1`.`\\2`\\3',
		'`\\1`.`\\2`\\3',
		'`\\1`.`\\2` LIKE',
		'`\\1`.`\\2` BETWEEN \\3 AND',
		'`\\1`.`\\2` IN(\\3)',
		);		
		if(strpos($string, '.')){			
			$option_string =  preg_replace($Regexp_array02, $Replace_array02, $string);
			
		}
		else {			
			$option_string =  preg_replace($Regexp_array, $Replace_array, $string);
		}		
		return $option_string;
	}
	
	//order('排列条件')用于处理 ORDER BY post_id ASC 之类的SQL语句部分,注：当参数中含字符串时应先用quote_into()进行转义
	public function order($string){
		if(empty($string)){
			return false;
		}		
		if(is_array($string)){			
			$order_str = '';			
			foreach ($string as $lines){				
				$order_str .= ' '.trim($lines).',';
			}			
			$order_str = substr($order_str, 0, -1);
		}
		else {			
			$order_str = trim($string);
		}		
		if(strpos($order_str, '.')){			
			$order_str = preg_replace(array('#(\w+?)\.(\w+?)\s+asc#i','#(\w+?)\.(\w+?)\s+desc#i'), array('`\\1`.`\\2` ASC','`\\1`.`\\2` DESC'), $order_str);
		}
		else {			
			$order_str = preg_replace(array('#(\w+?)\s+asc#i','#(\w+?)\s+desc#i'), array('`\\1` ASC','`\\1` DESC'), $order_str);
		}		
		$this->option['order'] .= ($this->option['order']) ? ' AND '.$order_str : ' ORDER BY '.$order_str;
		return $this;
	}
	
	//limit(10,20)用于处理LIMIT 10, 20之类的SQL语句部分 
	public function limit($num1, $num2=false){
		if(is_int($num1)){				
			$num1 = trim($num1);				
			$num2 = (is_int($num2)) ? trim($num2) : '';				
			$limit_str = $num2 ? $num1.', '.$num2 : $num1;				
			$this->option['limit'] = ' LIMIT '.$limit_str;				
			return $this;
		}
		else {				
			return false;
		}
	}
	
	//jion('表名2', '关系语句')相当于SQL中LEFT JOIN 表2 ON 关系SQL语句
	public function join($name, $where){		
		if(empty($name)||empty($where)){			
			return false;
		}		
		$table_str = $this->parse_from_options($name);
		$join_str = $this->parse_options($where,true);		
		$join_str = $this->parse_where($join_str);			
		$this->option['join'] .= ' LEFT JOIN '.$table_str.' ON '.$join_str;			
		return $this;
	}
	
	//用于完成GROUP BY语句的处理
	public function group($params){		
		if(empty($params)){			
			return false;
		}		
		$group_str = $this->parse_options($params);
		$this->option['group'] .= ($this->option['group']) ? ', '.$group_str : ' GROUP BY '.$group_str;
		return $this;
	}
	
	//having('查询条件')用于处理 having id=0537 诸如此类的SQL语句部分,注：当参数中含字符串时应先用quote_into()进行转义
	public function having($string){
		if(empty($string)){				
			return false;
		}		
		$having_str = $this->parse_options($string,true);		
		$having_str = $this->parse_where($having_str);		
		$this->option['having'] .= ($this->option['having']) ? ' AND '.$having_str : ' HAVING '.$having_str;
		return $this;
	}
	
	//SQL指令安全过滤,用于字符转义。
	public function quoteInto($value){
		//判断参数是否为数组.
		if(is_array($value)){
			foreach($value as $k=>$v){
				$value[$k] = $this->quoteInto($v);
			}
			return $value;
		}else{
			if (is_string($value)){
				return '\''.$this->db->escape_string($value).'\'';
			}
			return $value;
		}
	}
	
	//组装SQL语句并完成查询，并返回查询结果,用法$this->select();
	public function select($all_data=true){
		if(!$this->option['from']){
			return false;
		}
		$sql = $this->option['from'].$this->option['join'].$this->option['where'].$this->option['or_where'].$this->option['group'].$this->option['having'].$this->option['order'].$this->option['limit'];
		//清空不必要的内存占用.
		$clear_array = array('from','join','where', 'or_where', 'group', 'having', 'order', 'limit');
		foreach($clear_array as $item){	
			if($this->option[$item]){
				unset($this->option[$item]);
			}
		}
		return $all_data ? $this->db->get_array($sql) : $this->db->fetch_row($sql);
	}
	
	//	+---------------------------------------------------
	//	|			第三部分： Insert, Update, Delete, Find
	//	+---------------------------------------------------
	
	//根据主键，获取某个主键的一行信息,主键可以类内设置
	public function find($id){
		if(empty($id)){
			return false;
		}
		$this->parse_table_primarykey();				
		$this->parse_table_name();				
		$sql = 'SELECT * FROM `'.$this->table_name.'` WHERE `'.$this->primary_key.'`';
		
		if(is_array($id)){	
			$values = '';
			foreach ($id as $lines){				
				$values .= ' '.trim($lines).',';
			}			
			$values = substr($values, 0, -1);			
			$sql .= ' IN ('.$values.')';
			$myrow = $this->db->get_array($sql);
		}
		else {
			$sql .= ' = '.trim($id);
			$myrow = $this->db->fetch_row($sql);
		}				
		return $myrow;
	}


	//根据主键信息，获取数据表全部信息
	public function findAll(){
		$this->parse_table_primarykey();
		$this->parse_table_name();
		if(!$this->order){		
			$this->order = 'ASC';
		}else {		
			$this->order = (in_array(strtoupper($this->order), array('ASC','DESC'))) ? strtoupper($this->order) : 'ASC';
		}
		$sql = 'SELECT * FROM `'.$this->table_name.'` ORDER BY `'.$this->primary_key.'` '.$this->order;
		return $this->db->get_array($sql);
	}
	
	//根据某一条件，获取一行信息（字段型），注：只是一行信息
	public function fetchRow($where){
		if(empty($where)){			
			return false;
		}
		$this->parse_table_name();		
		$sql = 'SELECT * FROM `'.$this->table_name.'`';				
		//处理where SQL语句
		$this->option['where'] = '';				
		$this->where($where);				
		$sql .= $this->option['where'];		
		unset($this->option['where']);		
		$this->myrow = (Object)$this->db->fetch_row($sql);				
		return $this;
	}
	
	//新建一行数据，对象型的
	public function createRow(){
		$this->myrow = '';
		$this->myrow = (object)$this->myrow;
		return $this;
	}
	
	//向数据表写入一行信息
	public function insert($content){
		if(!is_array($content)||empty($content)){			
			return false;
		}
		$this->parse_table_name();
		$this->parse_table_field();				
		$field_str = '';
		$content_str = '';		
		//处理所要写入内容的数组的 values 与数据表字段对应顺序
		foreach ($content as $key=>$val){
			if(in_array($key, $this->table_field)){				
				$field_str .= ' `'.trim($key).'`,';
				$content_str .= ' \''.$this->db->escape_string(trim($val)).'\',';
			}
		}				
		$field_str = substr($field_str, 0, -1);
		$content_str = substr($content_str, 0, -1);				
		$sql = 'INSERT INTO `'.$this->table_name.'` ('.$field_str.' )'.' VALUES ('.$content_str.')';
		$this->db->query($sql);				
		return true;
	}

	//删除符合一定条件的行数据,注：如果$where中含有字符串，应用$this->qutote_into()进行转义
	public function delete($where){
		$this->parse_table_name();
		$this->option['where'] = '';		
		$this->where($where);		
		$sql = 'DELETE FROM '.$this->table_name.$this->option['where'];		
		unset($this->option['where']);
		$this->db->query($sql);
		return true;
	}
	
	//更新一行信息
	public function update($content,$where){
		if(!is_array($content) || empty($where)){				
			return false;
		}		
		$this->parse_table_name();				
		$this->parse_table_field();				
		$content_str = '';		
		foreach ($content as $key=>$val){
			if(in_array($key, $this->table_field)){						
				$content_str .= '`'.$key.'` = \''.$this->db->escape_string(trim($val)).'\',';
			}
		}				
		$content_str = substr($content_str, 0, -1);				
		$sql = 'UPDATE `'.$this->table_name.'` SET '.$content_str;				
		$this->option['where'] = '';
		$this->where($where);				
		$sql .= $this->option['where'];
		unset($this->option['where']);
		
		$this->db->query($sql);				
		return true;
	}

	//保存数据，实质函数为：update，没有ID时则insett,注：对数为对象型
	public function save(){
		if(is_object($this->myrow)){				
			$myrow = (array)$this->myrow;				
			$key_arr = array_keys($myrow);				
			$this->parse_table_primarykey();
			
			if(in_array($this->primary_key, $key_arr)){
				$where = $this->primary_key.'=\''.$myrow[$this->primary_key].'\'';
				unset($myrow[$this->primary_key]);
				$this->update($myrow,$where);
			}
			else {
				$this->insert($myrow);
			}				
			return true;
		}
		else {				
			return false;
		}
	}
	
	//获取一行数据，数组型，索引为字段。
	function getOne($where){
		if(empty($where)){
			return false;
		}
		$this->parse_table_name();				
		$sql = 'SELECT * FROM `'.$this->table_name.'`';				
		$this->option['where'] = '';				
		$this->where($where);				
		$sql .= $this->option['where'];
		unset($this->option['where']);
		return $this->db->fetch_row($sql);
	}
	
	//获取多行数据，数组型，索引为字段。
	function getAll($where, $order=false){
		if(empty($where)){
			return false;
		}
		$this->parse_table_name();				
		$sql = 'SELECT * FROM `'.$this->table_name.'`';				
		$this->option['where'] = '';				
		$this->where($where);				
		$sql .= $this->option['where'];
		unset($this->option['where']);			
		if(empty($order)){
			$this->parse_table_primarykey();
			if($this->order){						
				$this->order = (in_array(strtoupper($this->order), array('ASC','DESC'))) ? strtoupper($this->order) : 'ASC';
			}
			else {						
				$this->order = 'ASC';
			}
			$sql .= ' ORDER BY `'.$this->primary_key.'` '.$this->order;
		}
		else {
			$this->option['order'] = '';
			$sql .= $this->option['order'];
		}
		unset($this->option['order']);				
		return $this->db->get_array($sql);
	}
	
	//	+----------------------------------------------------------------------
	//	|	第四部分： __SET(), __DESTRUCT(), __CALL(), 及DB_MYSQLI的原生函数
	//	+---------------------------------------------------------------------
	
	//根据一个SQL语句获取执行后的全部数据库（字段型）
	public function execute ($sql, $all_data=true){
		if(empty($sql)){
			return false;
		}				
		return $all_data ? $this->db->get_array($sql) : $this->db->fetch_row($sql);
	}

	//QUERY,用于执行SQL语句
	public function query($sql){
		return $this->db->query($sql);
	}

	//获取INSERT_ID
	public function insert_id(){
		return $this->db->insert_id();
	}

	//开启事务处理
	public function startTrans(){		
		return $this->db->startTrans();
	}
	
	//事务处理，提交
	public function commit(){		
		return $this->db->commit();
	}
	
	//事务回滚
	public function rollback(){		
		return $this->db->rollback();
	}
	
	//根据一个sql数组，进行全程事务处理
	public function handle_trans($sql=array()){		
		if(!is_array($sql)){			
			return false;
		}		
		$this->startTrans();		
		foreach ($sql as $lines){			
			$result = $this->query($lines);			
			if(false == $result){				
				$this->rollback();				
				return false;
			}
		}		
		$this->commit();		
		return true;
	}
	
	//联合查询,实现两个数据表的数据查询.
	public function getRelation($params){		
		if(!$relation_array){
			$relation_array = $this->relations();
		}		
		if(is_array($relation_array[$params][0])&&!empty($relation_array[$params][1])){
			$item_select = array();
			foreach($relation_array[$params][0] as $key=>$value){
				$item_select[] = is_numeric($key) ? '`'.$value.'`.*' : '`'.$key.'`.*';
			}			
			$this->from($relation_array[$params][0], $item_select)->where($relation_array[$params][1]);

			if($relation_array[$params][2]){
				$this->order($relation_array[$params][2]);
			}
			return $this->select();
		}
		else{
			return false;
		}
	}

	//两个数据表的关系函数
	protected function relations(){
		return array();
	}
	
	//清除变量,$string参数为$this->string对象型
	protected function clear($string){
		if($string){				
			unset($string);
		}
	}
	
	//对类内受保护对象进行赋值
	public function __set($key, $val){
		if(is_object($this->myrow)){				
			return $this->myrow->$key = $val;
		}
		else {				
			if(in_array($key, array('order','table_name','primary_key','cache_dir'))){
				return $this->$key = $val;
			}
			else {
				return false;
			}
		}
	}
	
	//直接调用函数，输出内容
	public function __toString(){
		if($this->option){				
			$sql=$this->option['from'].$this->option['join'].$this->option['where'].$this->option['or_where'].$this->option['group'].$this->option['having'].$this->option['order'].$this->option['limit'];				
			return (string)$sql;
		}
		else {				
			return (string)'This is Model Class!';
		}
	}
	
	//析构函数，用于类内程序运行结束后，打扫战场
	public function __destruct(){
		$unset_array = array($this->params, $this->option, $this->myrow);
		foreach($unset_array as $name){
			$this->clear($name);
		}
	}
}
?>