<?php
/* 
 * Edition:	ET080708
 * Desc:	ET Template
 * File:	template.ease.php
 * Author:	David Meng
 * Site:	http://www.systn.com
 * Email:	mdchinese@gmail.com
 * 
 */

//��������ļ�
if (is_file(dirname(__FILE__).'/template.core.php')){
	include dirname(__FILE__).'/template.core.php';
}else {
	die('Sorry. Not load core file.');
}

Class template extends ETCore{
	
	/**
	*	����ģ���÷�
	*/
	function template(
		$set = array(
				'ID'		 =>'1',					//����ID
				'TplType'	 =>'htm',				//ģ���ʽ
				'CacheDir'	 =>'cache',				//����Ŀ¼
				'TemplateDir'=>'template' ,			//ģ����Ŀ¼
				'AutoImage'	 =>'on' ,				//�Զ�����ͼƬĿ¼���� on��ʾ���� off��ʾ�ر�
				'LangDir'	 =>'language' ,			//�����ļ���ŵ�Ŀ¼
				'Language'	 =>'default' ,			//���Ե�Ĭ���ļ�
				'Copyright'	 =>'off' ,				//��Ȩ����
				'MemCache'	 =>'' ,					//Memcache��������ַ����:127.0.0.1:11211
			)
		){
		
		parent::ETCoreStart($set);
	}

}
?>