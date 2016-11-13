<html>
<head>
<title>Welcome to Intranet</title>
<link rel="stylesheet" type="text/css" href="../css/templateblue.css" />
</head>
<body>

<?php
require_once('db.php');

$link = $_GET['temp'];
$mylink = sha1($link);

$sql = mysql_query("select * from temp_table where temp_string = '".$mylink."'");

while($row = mysql_fetch_array($sql)){
	$validity = $row['validity'];
	$empid = $row['empid'];
	$email = $row['email'];
}
$current_date = date('U');

if($current_date > $validity){
	echo "<center>Error:- Sorry, Your Link Expired..Please Generate Password Again...!!!";
	echo "<br/><a href='../index.php'>Back to HomePage</a></center>";
}
else{
?>	
	<center>
	<div id="box">

	<div id="top">
	<div id="top_left">
	<img id="img" src="../images/logo.png" alt="Input Zero" />
	</div>
	<div id="top_middle">
	<font face="times new roman">Welcome To Salary Management</font>
	</div>
	</div>

	<div id="middle">
	<br /><br /><br /><br /><br />
	<table   border="1" cellpadding="0" cellspacing="1">
	<tr>	
	<form method="POST" action="">
	<td>
	<table width="100%" border="1" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
	<tr>
	<td colspan="3"><center><strong>Reset Password</strong></center></td>
	</tr>

	<tr>
	<td width="100%">New Password:</td>
	<td width="250"><input name="for_pass" type="password" id="for_pass" size="40" required></td>
	</tr>
	
	<tr>
	<td width="100%">Confirm Password:</td>
	<td width="250"><input name="for_confirm_pass" type="password" id="for_confirm_pass" size="40" required></td>
	</tr>

	<tr>
	<td colspan=2><center><input id='btn' type="submit" name="change" value="Change Password"></center></td>
	</tr>
	</table>
	</td>
	</form>
	</tr>
	</table>
	</div>

	<div id="footer">
	&copy Input Zero Technologies Pvt. Ltd.
	</div>

</div>
</center>
</body>
</html>

<?php
	
}

/* Code Updated in v3 for using same page to change password */
if(isset($_POST['change'])){
	//echo $empid;
	$fval = $_POST['for_pass'];
	$fval_con = $_POST['for_confirm_pass'];

	if($fval == ""){
		echo "<center><h3>Please Enter New Password...!!!</h3></center>";
	}
	else if($fval == $fval_con){
		$sha_pass = sha1($fval);
		$s = mysql_query("update users set password = '$sha_pass' where empid = ".$empid);
		
		echo "<center><h3>Password Successfully Changed...!!!</h3>";
		echo "<br/><a href='../index.php'>Back to Login Again</a></center>";
	}
	else{
		echo "<center><h3>New and Confirm Password are not same...!!!</h3></center>";
	}
}
/* end Code Updated in v3 for using same page to change password */
?>


