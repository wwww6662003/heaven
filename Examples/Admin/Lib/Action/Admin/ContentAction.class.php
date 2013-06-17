<?php
class ContentAction extends Core_Action {
	public function index() {
		$session = Lib_Session::getInstance ();
		if ($session->is_set ( 'admin_id' ) && $session->is_set ( 'admin_name' )) {
			$this->assign('app_web_dir',$GLOBALS['path']['app_web_dir']);
			$this->assign('app_web_dir_public',$GLOBALS['path']['app_web_dir_public']);
			$this->assign ( 'charset', $GLOBALS ['site_info'] ['charset'] );
			$this->display ( 'content' );
		} else {
			$this->js_alert ( 'µÇÂ½Ê§°Ü!', $GLOBALS['path']['app_web_dir'] . 'login', '' );
		}
	}
}
?>