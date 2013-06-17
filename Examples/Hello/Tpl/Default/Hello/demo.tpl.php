<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=gbk">
  <link  href="{$app_web_dir}Public/Css/demo.css" rel="stylesheet" type="text/css" />
  <title>Hello,HeavenMVC</title>
 </head>
 <body>
 <div class="main">
 <!--{include top.php}-->
 <table>
 <!--{loop $data $da}-->    
 <tr><td><!--{$da['username']}--></td><td><!--{$da['pass']}--></td></tr>
 <!--{/loop}-->
 </table>
  <!--{include footer.php}-->
</div>
 </body>
</html>
 