<?php
/**
 * @package HeavenMVC
 * @version 1.0 
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */
define('IN_HEAVEN', true);
define('HEAVEN_REWRITE', true);
define('HEAVEN_DEBUG', false);
define('APP_NAME', 'Admin');
define('APP_PATH', dirname(__FILE__));
define('HEAVEN_PATH', '../../Heaven');
define('TPL_NAME', 'Default');
define('HEAVEN_TIMEZONE', 'Asia/ShangHai');
include HEAVEN_PATH.'/Heaven.class.php';
include 'Config/Config.inc.php';
Heaven::Run();
?>