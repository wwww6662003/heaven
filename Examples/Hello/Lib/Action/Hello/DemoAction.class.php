<?php
class DemoAction extends Core_Action {

	public function demo(){
		$model=model_init('demo');
		$data=$model->findAll();
		//$this->debug_write($data);
		$this->set_cache_lifetime(0);
		$this->assign('hello','top��');
		$this->assign('footer','footer��');
		$this->assign(array('data'=>$data));
		$this->display();
	}
}
?>