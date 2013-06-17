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
     * ���ù���ģʽ�����ݿ��������г�ʼ��.
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