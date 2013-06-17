<?php
require('Config.php');
header("HTTP/1.1 301 Moved Permanently");
header("Location: {$GLOBALS['domain_name']}");
exit(); //注意：虽然客户端转向了，但程序还会向下执行，所以要exit
?>