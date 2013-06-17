<?php
class IndexAction extends Core_Action{
    public function index() {
    	$this->set_cache_lifetime(10);
        $this->assign('hello','ÄúºÃHeavenMVC!');
        //$h=Lib_init('Lib_Html');
       	$h=Lib_Html::getInstance();//Html class autoload
		$option=array('style'=>'width:800px;','target'=>'_blank');
		$link=$h->link('ÐÂÁ´½Ó','http://www.heaven.com',$option);
		$this->assign('link',$link);
		$op1=array(array('www1','www2'),array('www1','www2'));
		$table=$h->table($op1,$option);
        $this->assign('table',$table);
        $this->display();
    }
}
?>