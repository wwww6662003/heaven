<?php
class TopAction extends Core_Action {
	public function index() {
		$session = Lib_Session::getInstance ();
		if ($session->is_set ( 'admin_id' ) && $session->is_set ( 'admin_name' )) {
			$this->assign('app_web_dir',$GLOBALS['path']['app_web_dir']);
			$this->assign('app_web_dir_public',$GLOBALS['path']['app_web_dir_public']);
			$this->assign('index_url',$GLOBALS['site_info']['url']);
			$this->assign ( 'charset', $GLOBALS ['site_info'] ['charset'] );
			$this->assign('top_url',$GLOBALS['path']['app_web_dir'].'login/logout');
			$this->assign('admin_name',$session->get('admin_name'));
			$this->display ( 'top' );
		} else {
			$this->js_alert ( 'µÇÂ½Ê§°Ü!', $GLOBALS['path']['app_web_dir'] . 'login', '' );
		}
	}
}
?>