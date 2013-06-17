<?php
class IndexAction extends Core_Action {
	public function index() {
		$session = Lib_Session::getInstance ();
		if ($session->is_set ( 'admin_id' ) && $session->is_set ( 'admin_name' )) {
			$this->assign ( 'title', $GLOBALS ['site_info'] ['title'] );
			$this->assign ( 'keywords', $GLOBALS ['site_info'] ['keywords'] );
			$this->assign ( 'description', $GLOBALS ['site_info'] ['description'] );
			$this->assign ( 'charset', $GLOBALS ['site_info'] ['charset'] );
			$this->assign ( 'top_url', $GLOBALS ['path'] ['app_web_dir']. 'top' );
			$this->assign ( 'menu_url', $GLOBALS ['path'] ['app_web_dir']. 'menu' );
			$this->assign ( 'main_url', $GLOBALS ['path'] ['app_web_dir']. 'main' );
			$this->display ( 'index' );
		} else {
			$this->js_alert ( 'гКох╣гб╫!', $GLOBALS['path']['app_web_dir'] . 'login', '' );
		}
	}
}
?>