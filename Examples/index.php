<?php
require('Config.php');
header("HTTP/1.1 301 Moved Permanently");
header("Location: {$GLOBALS['domain_name']}");
exit(); //ע�⣺��Ȼ�ͻ���ת���ˣ������򻹻�����ִ�У�����Ҫexit
?>