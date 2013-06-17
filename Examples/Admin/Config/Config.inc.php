<?php
//database config
$GLOBALS['db_config']=array (
  'host' => 'localhost',
  'username' => 'root',
  'password' => 'root',
  'dbname' => 'heaven',
  'charset' => 'gbk',
  'driver' => 'mysql',
  'port' => '3306',
  'prefix'=>'hello_'//need add _
);
//sitename config
$GLOBALS['site_info']=array(
'title'=>'天堂后台管理系统',
'keywords'=>'天堂后台管理系统',
'description'=>'天堂后台管理系统',
'url'=>'http://www.hello.com/',
'charset'=>'gb2312',
'version'=>'版本2010v1.0',
'default_controller_method'=>'index'
);
//error config
$GLOBALS['error_config']=array(
'error_rewrite_url'=>'The current pattern is  rewrite.the url error,please delete index.php of url.',
'error_not_rewrite_url'=>'The current pattern is not rewrite.the url error,please add index.php of url.',
'error_url_letter'=>'Please distinguish module are case sensitive.'
);
//template extname config
$GLOBALS['ext_name']=array(
'cache_extname'=>'.php',
'tpl_extname'=>'.tpl.php'
);

//path config
$GLOBALS['path']=array(
'app_web_dir'=>'http://admin.hello.com/',
'app_web_dir_public'=>'http://admin.hello.com/Public/',
'app_web_dir_tpl_public'=>'http://admin.hello.com/Tpl/Default/Public/',
);

?>