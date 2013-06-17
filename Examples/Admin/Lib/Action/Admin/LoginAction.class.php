<?php
class LoginAction extends Core_Action {
	private $login_model;
	public function index() {
		$this->assign ( 'app_web_dir', $GLOBALS ['path'] ['app_web_dir'] );
		$this->assign ( 'title', $GLOBALS ['site_info'] ['title'] );
		$this->assign ( 'keywords', $GLOBALS ['site_info'] ['keywords'] );
		$this->assign ( 'description', $GLOBALS ['site_info'] ['description'] );
		$this->assign ( 'charset', $GLOBALS ['site_info'] ['charset'] );
		$this->assign ( 'version', $GLOBALS ['site_info'] ['version'] );
		$this->display ( 'login' );
	}
	public function login() {
		$this->assign ( 'app_web_dir', $GLOBALS ['path'] ['app_web_dir'] );
		$this->assign ( 'title', $GLOBALS ['site_info'] ['title'] );
		$this->assign ( 'keywords', $GLOBALS ['site_info'] ['keywords'] );
		$this->assign ( 'description', $GLOBALS ['site_info'] ['description'] );
		$this->assign ( 'charset', $GLOBALS ['site_info'] ['charset'] );
		$this->assign ( 'version', $GLOBALS ['site_info'] ['version'] );
		$this->display ( 'login' );
	}
	//验证登陆
	public function login_ok() {
		$admin_name = $this->str_encode_parse ( $_POST ['username'] );
		$password = $this->check_str ( $_POST ['password'] );
		$this->login_model = model_init ( 'login' );
		$encrypt = Core_Encrypt::getInstance ();
		$en_password = $encrypt->encrypt ( $password );
		$sql = "select id,adminname,password from  hello_login where adminname='{$admin_name}'";
		$data = $this->login_model->execute ( $sql, false );
		if ($data) {
			if ($data ['password'] == $en_password) {
				$session = Lib_Session::getInstance ();
				$session->set_name ( 'heaven_session' );
				$session->set ( 'admin_id', $data ['id'] );
				$session->set ( 'admin_name', $data ['adminname'] );
				$this->js_alert ( '登陆成功!', $GLOBALS ['path'] ['app_web_dir'] . 'index', '' );
			} else {
				$this->js_alert ( '密码错误!', $GLOBALS ['path'] ['app_web_dir'] . 'login', '' );
			}
		} else {
			$this->js_alert ( '用户名不存在!', $GLOBALS ['path'] ['app_web_dir'] . 'login', '' );
		}
	}
	public function logout() {
		$session = Lib_Session::getInstance ();
		$session->delete ( 'admin_id' );
		$session->delete ( 'admin_name' );
		$session->destory ();
		$this->js_alert ( '成功退出', $GLOBALS ['path'] ['app_web_dir'] . 'login', '' );
	}
}
?>