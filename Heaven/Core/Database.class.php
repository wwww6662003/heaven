<?php
/**
 * @package HeavenMVC
 * @version 1.0 Database.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */
if(!defined('IN_HEAVEN')){
	exit;
}
class Core_Database extends Core_Base{
	
	/**
     * 利用工厂模式对数据库操作类进行初始化.
     * @access public
     * @return object
     */
	public function factory($params){
		if($params['driver']){
			if($params['driver']=='mysql'){
				$link_id = Core_DatabaseDriver_DbMysql::getInstance($params);
			}else if($params['driver']=='mysqli'){
				$link_id = Core_DatabaseDriver_DbMysqli::getInstance($params);
			}else{
				$link_id = Core_DatabaseDriver_DbPdo::getInstance($params);
			}	
		}else{
			$link_id = Core_DatabaseDriver_DbMysql::getInstance($params);
		}		
		return $link_id;
	}		
}
?>