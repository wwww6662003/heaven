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
     * ���캯��,���ڳ�ʼ�����л���.
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
     * ���ڶ�confilg�ļ����ݽ������ݷ���,��ʼ�����л���.
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
	//	|			��һ���֣� ���ݱ���Ϣ����
	//	+---------------------------------------------------
	
	//����$this->table_name
	protected function parse_table_name(){		
		if(!$this->table_name){				
			$this->get_table_name();
		}
		return $this->table_name;
	}

	//����$this->tabl_info
	protected function parse_table_info(){		
		if(!$this->table_info){				
			$this->get_table_info();
		}
		return $this->table_info;
	}

	//����$this->primary_key
	protected function parse_table_primarykey(){		
		if(!$this->primary_key){				
			$this->get_table_primarykey();
		}
		return $this->primary_key;
	}

	//����$this->table_field
	protected function parse_table_field(){		
		if(!$this->table_field){			
			$this->get_table_field();
		}
		return $this->table_field;
	}

	//��ȡ����
	protected function get_class_name(){
		if(!$this->class_name){		
			$class_name = get_class($this);
			//echo $class_name;die();
			$class_name= strtolower($class_name);	
			$this->class_name = $class_name;
		}
		return $this->class_name;
	}

	//��ȡ���ݱ�ǰ׺
	protected function get_table_prefix(){
		if(!$this->table_prefix){				
			$this->table_prefix = (!empty($this->params['prefix'])) ? $this->params['prefix'] : '';
		}
		return $this->table_prefix;
	}

	//��ȡ���ݱ���Ϣ
	protected function get_table_info(){
		
		$this->parse_table_name();
		$sql="SHOW FIELDS FROM `{$this->table_name}`";		
		$this->table_info = $this->db->get_array($sql);
		return $this->table_info;
	}

	//��ȡ���ݱ���
	protected function get_table_name(){		
		$this->get_class_name();
		$this->get_table_prefix();
		$this->table_name = (!empty($this->table_prefix)) ? $this->table_prefix.substr($this->class_name,0,-5) : substr($this->class_name,0,-5);
		return $this->table_name;
	}

	//cache_file�ļ�����
	protected function parse_cache_file($name){
		if(!$this->cache_dir){				
			$this->cache_dir = APP_PATH.'/Cache/Model/';
		}
		$this->parse_table_name();
		return $this->cache_dir.$this->table_name.'_'.$name.'.data.php';
	}
	
	//���ɻ����ļ�
	protected function create_cache($name, $data){
		$cache_file = $this->parse_cache_file($name);	
		$content = "<?php \r\n";
		$content .= "return ";
		$content .= var_export($data,true).";";
		$content .= "\r\n?>";
		//�ж�cache_dir�Ƿ���ڣ�����������Ŀ¼
		if(!is_dir($this->cache_dir)){				
			mkdir($this->cache_dir,0777);
		}
		file_put_contents($cache_file,$content,LOCK_EX);
		return true;
	}

	//���ػ����ļ�
	protected function load_cache($name){		
		return include($this->parse_cache_file($name));
	}

	//��������ļ�
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

	//��ȡ���ݱ������
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

	//��ȡ���ݱ��ֶ���Ϣ
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
	//	|			�ڶ����֣� Select SQL ��䴦��
	//	+---------------------------------------------------
	
	//����from(),where(),order()�Ⱥ����������ر��ǶԲ���Ϊ����Ĵ���
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
	
	//���ڴ���d.demoΪ:`d`.`demo`
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
	
	//���ڴ���FROM()����optings���⺯��
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
	
	//���ڴ������
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
	
	//���ڴ���FROM()����columns���⺯��
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
	
	
	//from('���ݱ�','��ѯ�ֶ�')���ڴ��� SELECT fields FROM table֮���SQL��䲿��
	public function from($name, $item=false){
		if(empty($name)){			
			return false;
		}		
		$table_str = $this->parse_from_options($name);				
		$item_str = ($item==true) ? $this->parse_from_columns($item) : '*';				
		$this->option['from'] = 'SELECT '.$item_str.' FROM '.$table_str;		 	
		return $this;
	}
	
	//where('��ѯ����')���ڴ��� WHERE id=0537 ��������SQL��䲿��,ע���������к��ַ���ʱӦ����quote_into()����ת��
	public function where($string){
		if(empty($string)){				
			return false;
		}
		$where_str = $this->parse_options($string,true);		
		$where_str = $this->parse_where($where_str);
		$this->option['where'] .= ($this->option['where']) ? ' AND '.$where_str : ' WHERE '.$where_str;
		return $this;
	}
	
	//or_where('��ѯ����')���ڴ��� OR WHERE id=0531 ��������SQL��䲿��,ע���������к��ַ���ʱӦ����quote_into()����ת��
	public function orwhere($string){
		if(empty($string)){			
			return false;
		}		
		$or_where_str = $this->parse_options($string,true);
		$or_where_str = $this->parse_where($or_where_str);		
		$this->option['or_where'] .= ($this->option['or_where']) ? ' AND '.$or_where_str : ' OR '.$or_where_str;				
		return $this;
	}
	
	//���ڴ���where,orwhere�������⴦��.
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
	
	//order('��������')���ڴ��� ORDER BY post_id ASC ֮���SQL��䲿��,ע���������к��ַ���ʱӦ����quote_into()����ת��
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
	
	//limit(10,20)���ڴ���LIMIT 10, 20֮���SQL��䲿�� 
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
	
	//jion('����2', '��ϵ���')�൱��SQL��LEFT JOIN ��2 ON ��ϵSQL���
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
	
	//�������GROUP BY���Ĵ���
	public function group($params){		
		if(empty($params)){			
			return false;
		}		
		$group_str = $this->parse_options($params);
		$this->option['group'] .= ($this->option['group']) ? ', '.$group_str : ' GROUP BY '.$group_str;
		return $this;
	}
	
	//having('��ѯ����')���ڴ��� having id=0537 ��������SQL��䲿��,ע���������к��ַ���ʱӦ����quote_into()����ת��
	public function having($string){
		if(empty($string)){				
			return false;
		}		
		$having_str = $this->parse_options($string,true);		
		$having_str = $this->parse_where($having_str);		
		$this->option['having'] .= ($this->option['having']) ? ' AND '.$having_str : ' HAVING '.$having_str;
		return $this;
	}
	
	//SQLָ�ȫ����,�����ַ�ת�塣
	public function quoteInto($value){
		//�жϲ����Ƿ�Ϊ����.
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
	
	//��װSQL��䲢��ɲ�ѯ�������ز�ѯ���,�÷�$this->select();
	public function select($all_data=true){
		if(!$this->option['from']){
			return false;
		}
		$sql = $this->option['from'].$this->option['join'].$this->option['where'].$this->option['or_where'].$this->option['group'].$this->option['having'].$this->option['order'].$this->option['limit'];
		//��ղ���Ҫ���ڴ�ռ��.
		$clear_array = array('from','join','where', 'or_where', 'group', 'having', 'order', 'limit');
		foreach($clear_array as $item){	
			if($this->option[$item]){
				unset($this->option[$item]);
			}
		}
		return $all_data ? $this->db->get_array($sql) : $this->db->fetch_row($sql);
	}
	
	//	+---------------------------------------------------
	//	|			�������֣� Insert, Update, Delete, Find
	//	+---------------------------------------------------
	
	//������������ȡĳ��������һ����Ϣ,����������������
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


	//����������Ϣ����ȡ���ݱ�ȫ����Ϣ
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
	
	//����ĳһ��������ȡһ����Ϣ���ֶ��ͣ���ע��ֻ��һ����Ϣ
	public function fetchRow($where){
		if(empty($where)){			
			return false;
		}
		$this->parse_table_name();		
		$sql = 'SELECT * FROM `'.$this->table_name.'`';				
		//����where SQL���
		$this->option['where'] = '';				
		$this->where($where);				
		$sql .= $this->option['where'];		
		unset($this->option['where']);		
		$this->myrow = (Object)$this->db->fetch_row($sql);				
		return $this;
	}
	
	//�½�һ�����ݣ������͵�
	public function createRow(){
		$this->myrow = '';
		$this->myrow = (object)$this->myrow;
		return $this;
	}
	
	//�����ݱ�д��һ����Ϣ
	public function insert($content){
		if(!is_array($content)||empty($content)){			
			return false;
		}
		$this->parse_table_name();
		$this->parse_table_field();				
		$field_str = '';
		$content_str = '';		
		//������Ҫд�����ݵ������ values �����ݱ��ֶζ�Ӧ˳��
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

	//ɾ������һ��������������,ע�����$where�к����ַ�����Ӧ��$this->qutote_into()����ת��
	public function delete($where){
		$this->parse_table_name();
		$this->option['where'] = '';		
		$this->where($where);		
		$sql = 'DELETE FROM '.$this->table_name.$this->option['where'];		
		unset($this->option['where']);
		$this->db->query($sql);
		return true;
	}
	
	//����һ����Ϣ
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

	//�������ݣ�ʵ�ʺ���Ϊ��update��û��IDʱ��insett,ע������Ϊ������
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
	
	//��ȡһ�����ݣ������ͣ�����Ϊ�ֶΡ�
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
	
	//��ȡ�������ݣ������ͣ�����Ϊ�ֶΡ�
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
	//	|	���Ĳ��֣� __SET(), __DESTRUCT(), __CALL(), ��DB_MYSQLI��ԭ������
	//	+---------------------------------------------------------------------
	
	//����һ��SQL����ȡִ�к��ȫ�����ݿ⣨�ֶ��ͣ�
	public function execute ($sql, $all_data=true){
		if(empty($sql)){
			return false;
		}				
		return $all_data ? $this->db->get_array($sql) : $this->db->fetch_row($sql);
	}

	//QUERY,����ִ��SQL���
	public function query($sql){
		return $this->db->query($sql);
	}

	//��ȡINSERT_ID
	public function insert_id(){
		return $this->db->insert_id();
	}

	//����������
	public function startTrans(){		
		return $this->db->startTrans();
	}
	
	//�������ύ
	public function commit(){		
		return $this->db->commit();
	}
	
	//����ع�
	public function rollback(){		
		return $this->db->rollback();
	}
	
	//����һ��sql���飬����ȫ��������
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
	
	//���ϲ�ѯ,ʵ���������ݱ�����ݲ�ѯ.
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

	//�������ݱ�Ĺ�ϵ����
	protected function relations(){
		return array();
	}
	
	//�������,$string����Ϊ$this->string������
	protected function clear($string){
		if($string){				
			unset($string);
		}
	}
	
	//�������ܱ���������и�ֵ
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
	
	//ֱ�ӵ��ú������������
	public function __toString(){
		if($this->option){				
			$sql=$this->option['from'].$this->option['join'].$this->option['where'].$this->option['or_where'].$this->option['group'].$this->option['having'].$this->option['order'].$this->option['limit'];				
			return (string)$sql;
		}
		else {				
			return (string)'This is Model Class!';
		}
	}
	
	//�����������������ڳ������н����󣬴�ɨս��
	public function __destruct(){
		$unset_array = array($this->params, $this->option, $this->myrow);
		foreach($unset_array as $name){
			$this->clear($name);
		}
	}
}
?>