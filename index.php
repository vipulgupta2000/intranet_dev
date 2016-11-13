<html>
<head>
<title>Welcome to Intranet</title>
<link rel="stylesheet" type="text/css" href="css/templateblue.css" />
</head>

<body>
<center>
<div id="box">

	<div id="top">
	<div id="top_left">
	<img id="img" src="images/logo.png" alt="Input Zero" />
	</div>
	<div id="top_middle">
	<font face="times new roman">Welcome To Intranet</font>
	</div>
	</div>

	<div id="middle">
	<br /><br /><br /><br /><br />
	<table  width="250" border="1" cellpadding="0" cellspacing="1">
	<tr>
	<form method="post" action="check_login.php">
	<td>
	<table width="100%" border="1" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
	<tr>
	<td colspan="3"><strong>Member Login </strong></td>
	</tr>

	<tr>
	<td width="80">Username:</td>
	<td width="250"><input name="myusername" type="text" id="myusername" size="30"></td>
	</tr>

	<tr>
	<td width="80">Password:</td>
	<td width="250"><input name="mypassword" type="password" id="mypassword" size="30"></td>
	</tr>

	<tr>
	<td>&nbsp;</td>
	<td><input type="submit" name="submit" value="Login"></td>
	</tr>
	</table>
	</td>
	</form>
	</tr>
	</table>
	<br/><input id='btn' type='submit' name='forgot_pass' value='Forgot/Reset Password' onclick="location.href='forgot_pass/index.html'"/><br/><br/>
	</div>

	<div id="footer">
	&copy Input Zero Technologies Pvt. Ltd.
	</div>

</div>
</center>
</body>
</html>
