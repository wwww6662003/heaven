<?php
class CategoryAction extends Core_Action {
	public function index() {
		$session = Lib_Session::getInstance ();
		if ($session->is_set ( 'admin_id' ) && $session->is_set ( 'admin_name' )) {
			$this->assign ( 'app_web_dir', $GLOBALS ['path'] ['app_web_dir'] );
			$this->assign ( 'app_web_dir_public', $GLOBALS ['path'] ['app_web_dir_public'] );
			$this->assign ( 'charset', $GLOBALS ['site_info'] ['charset'] );
			echo $this->create_category_tree ();die();
			$this->display ( 'category' );
		} else {
			$this->js_alert ( 'µÇÂ½Ê§°Ü!', $GLOBALS ['path'] ['app_web_dir'] . 'login', '' );
		}
	}
	public function create_category_tree() {
		$html_select='';
		$tree = new Lib_Category('¸ùÄ¿Â¼');
		$category_model = CategoryModel::getInstance ();
		$category_data_arr = $category_model->get_all_category ();
		print_r($category_data_arr);
		for($i = 0; $i < count ( $category_data_arr ); $i ++) {
			$tree->setNode ( $category_data_arr [$i] ['id'], $category_data_arr [$i] ['parent_id'], $category_data_arr [$i] ['category_name'] );
		}
		$category = $tree->getChilds ();
		foreach ( $category as $key=>$id ) {
			$html_select.=$tree->getLayer ( $id, '|-' ) .$tree->getValue ( $id ) . "<br />\n";
		}
		return $html_select;
	}
}
?>