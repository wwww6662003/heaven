<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=gbk">
  <link  href="<?php echo common_config('app_web_dir');?>Public/Css/demo.css" rel="stylesheet" type="text/css" />
  <title>Hello,HeavenMVC</title>
 </head>
 <body>
 <div class="main">
 <?php $this->layout('top.php'); ?>
 <table>
 <?php
if(is_array($this->tpl_vars['data'])){
	foreach($this->tpl_vars['data'] as $this->tpl_vars['da']){
?>    
 <tr><td><?php echo $this->tpl_vars['da']['username']; ?></td><td><?php echo $this->tpl_vars['da']['pass']; ?></td></tr>
 <?php
	}
}
?>
 </table>
  <?php $this->layout('footer.php'); ?>
</div>
 </body>
</html>
 