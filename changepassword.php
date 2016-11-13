<html>
<head>
<title>IZTWIKI</title>
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
	<font face="times new roman">Welcome To IZTWIKI</font>
	</div>
	</div>
<p></p>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<?php
echo"<form action=\"#\"method=\"post\">";
echo "Employee id:<input type=\"text\" name=\"empid1\">";
echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<br/>Old Password:<input type=\"password\" name=\"old\">";
echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<br/>New Password:<input type=\"password\" name=\"new\">";
echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<br/><input type=\"submit\" name=\"submit\" value=\"submit\">";
echo"</form>";


$host="localhost";
$username="iztwiki";
$password="QarhBM7JGXEYXY3t";
$db_name="iztwiki";
$tbl_name="users";


mysql_connect("$host", "$username", "$password") or die("cannot connect");
mysql_select_db ("$db_name") or die ("cannot select DB");

$sql="select password from users where empid='$_POST[empid1]'";

$result=mysql_query($sql);


while($row = mysql_fetch_array($result))
{
if(isset($_POST['submit']))
{
 if(($row['password'])==sha1($_POST['old']))
{
$a=sha1($_POST['new']);
$sql="update users set password='$a' where empid='$_POST[empid1]'";
mysql_query($sql) or die ("Query Error :".mysql_error());
echo " password changed";
header("location:index.php");
}
else
{
echo " Enter correct Old Password";
}
}
}

$host1="localhost";
$username1="lmsuser";
$password1="v89bXBBYNQCfDDw5";
$db_name1="tms_lms";
$tbl_name1="usertable";


mysql_connect("$host1", "$username1", "$password1") or die("cannot connect");
mysql_select_db ("$db_name1") or die ("cannot select DB");


$sql1="select password from usertable where empid='$_POST[empid1]'";

$result1=mysql_query($sql1);


while($row = mysql_fetch_array($result1))
{
if(isset($_POST['submit']))
{
 if(($row['password'])==sha1($_POST['old']))
{
$b=sha1($_POST['new']);
$sql2="update usertable set password='$b' where empid='$_POST[empid1]'";
mysql_query($sql2) or die ("Query Error :".mysql_error());
echo " password changed";
header("location:index.php");
}
else
{
echo " Enter correct Old Password";
}
}
}
?>

<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>



	

</div>

	
<div id="footer">
	&copy Input Zero Technologies Pvt. Ltd.
	</div>

</div>
</center>

</body>
</html>
