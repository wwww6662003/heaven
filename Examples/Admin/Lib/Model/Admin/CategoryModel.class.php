<?php
class CategoryModel extends Core_Model {
	private static $instance;
	public function get_all_category() {
		$sql = "select id,parent_id,category_name from hello_category order by id asc";
		$category_data_arr = $this->execute ( $sql );
		return $category_data_arr;
	}
	/**
	 * ���ڱ���ľ�̬����,������Ҫ���ز�������ʹ��.
	 * @access public
	 * @param string $params �������
	 * @return void
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new self ( );
		}
		return self::$instance;
	}
}
?>