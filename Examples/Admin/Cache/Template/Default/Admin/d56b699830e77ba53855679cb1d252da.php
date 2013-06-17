<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>天堂后台管理系统</title>
<meta name="keywords" content="天堂后台管理系统" />
<meta name="description" content="天堂后台管理系统" />
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	overflow: hidden;
}

form {
	margin: 0px;
}

.STYLE3 {
	color: #528311;
	font-size: 12px;
}

.STYLE4 {
	color: #42870a;
	font-size: 12px;
}
-->
</style>
<script type="text/javascript">
function check_form() 
{ 
if (login_form.username.value == "") 
{ 
alert("请输入用户名"); 
login_form.username.focus(); 
return false; 
} 
if (login_form.password.value == "") 
{ 
alert("请输入口令"); 
login_form.password.focus(); 
return false; 
} 
login_form.submit();
} 
</script>

</head>

<body>
<table width="100%" height="100%" border="0" cellpadding="0"
	cellspacing="0">
	<tr>
		<td bgcolor="#e5f6cf">&nbsp;</td>
	</tr>
	<tr>
		<td height="608" background="http://admin.hello.com/Public/images/login_03.gif">

		<table width="862" border="0" align="center" cellpadding="0"
			cellspacing="0">
			<tr>
				<td height="266"
					background="http://admin.hello.com/Public/images/login_04.gif">&nbsp;</td>
			</tr>
			<tr>
				<td height="95">
				<form method="post" action="http://admin.hello.com/login/login_ok/" name="login_form">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="424" height="95"
							background="http://admin.hello.com/Public/images/login_06.gif">&nbsp;</td>
						<td width="183"
							background="http://admin.hello.com/Public/images/login_07.gif">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="21%" height="30">
								<div align="center"><span class="STYLE3">用户</span></div>
								</td>
								<td width="79%" height="30"><input type="text" name="username"
									style="height: 18px; width: 130px; border: solid 1px #cadcb2; font-size: 12px; color: #81b432;"></td>
							</tr>
							<tr>
								<td height="30">
								<div align="center"><span class="STYLE3">密码</span></div>
								</td>
								<td height="30"><input type="password" name="password"
									style="height: 18px; width: 130px; border: solid 1px #cadcb2; font-size: 12px; color: #81b432;"></td>
							</tr>
							<tr>
								<td height="30">&nbsp;</td>
								<td height="30"><img src="http://admin.hello.com/Public/images/dl.gif"
									width="81" height="22" border="0" usemap="#Map"
									onclick="check_form()"></td>
							</tr>
						</table>
						</td>
						<td width="255"
							background="http://admin.hello.com/Public/images/login_08.gif">&nbsp;</td>
					</tr>
				</table>
				</form>
				</td>
			</tr>
			<tr>
				<td height="247" valign="top"
					background="http://admin.hello.com/Public/images/login_09.gif">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="22%" height="30">&nbsp;</td>
						<td width="56%">&nbsp;</td>
						<td width="22%">&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td height="30">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="44%" height="20">&nbsp;</td>
								<td width="56%" class="STYLE4">版本2010v1.0</td>
							</tr>
						</table>
						</td>
						<td>&nbsp;</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#a2d962">&nbsp;</td>
	</tr>
</table>

<map name="Map">
	<area shape="rect" coords="3,3,36,19" href="#">
	<area shape="rect" coords="40,3,78,18" href="#">
</map>
</body>
</html>
