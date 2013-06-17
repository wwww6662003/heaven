<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->tpl_vars['charset']; ?>" />
<title><?php echo $this->tpl_vars['title']; ?></title>
<meta name="keywords" content="<?php echo $this->tpl_vars['keywords']; ?>" />
<meta name="description" content="<?php echo $this->tpl_vars['description']; ?>" />
<style>
body
{
  scrollbar-base-color:#C0D586;
  scrollbar-arrow-color:#FFFFFF;
  scrollbar-shadow-color:DEEFC6;
}
</style>
</head>
<frameset rows="60,*" cols="*" frameborder="no" border="0" framespacing="0">
  <frame src="<?php echo $this->tpl_vars['top_url']; ?>" name="topFrame" scrolling="no">
  <frameset cols="180,*" name="btFrame" frameborder="NO" border="0" framespacing="0">
    <frame src="<?php echo $this->tpl_vars['menu_url']; ?>" noresize name="menu" scrolling="yes">
    <frame src="<?php echo $this->tpl_vars['main_url']; ?>" noresize name="main" scrolling="yes">
  </frameset>
</frameset>
<noframes>
	<body>您的浏览器不支持框架！</body>
</noframes>
</html>