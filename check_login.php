<html>
<head>
<title>Time Management System</title>
</head>
<body>
<form action="" method="POST">
<?php

$host="localhost";
$username="iztwiki";
$password="QarhBM7JGXEYXY3t";
$db_name="iztwiki";
$tbl_name="users";

// Connect to server and select database.
@mysql_connect("$host", "$username", "$password") or die("cannot connect");

mysql_select_db ("$db_name") or die ("cannot select DB");

session_start();

// username and password sent from form
if(isset($_POST['myusername']))
{$pass=$_POST['mypassword'];
$myusername=$_POST['myusername'];
$mypassword=sha1($_POST['mypassword']);
}elseif(isset($_REQUEST["user"]))
{
$myusername=$_REQUEST["user"];
$mypassword=sha1($_REQUEST["pass"]);
//echo $myusername;
}else
{
echo "Wrong Username or Password";
header("location:index.php");
}

// To protect MySQL injection (more detail about MySQL injection)
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);
$myusername = mysql_real_escape_string($myusername);
$mypassword = mysql_real_escape_string($mypassword);

$sql="SELECT * FROM $tbl_name WHERE empid='$myusername' and password='$mypassword'";

$result=mysql_query($sql);

if($result)
{
if(mysql_num_rows($result)==1)
{
session_regenerate_id();
$member = mysql_fetch_assoc($result);
$_SESSION['SESS_ename'] = $member['empname'];
$_SESSION['SESS_uname'] = $member['username'];
$_SESSION['SESS_pwd'] = $member['password'];
$_SESSION['SESS_empid'] = $member['empid'];
$_SESSION['SESS_perm'] = $member['permission'];
$_SESSION['SESS_access'] = $member['access'];
$_SESSION['SESS_mgrid'] = $member['mgrid'];
$_SESSION['pass'] = $pass;
$_SESSION['SESS_tmp']=NULL;
session_write_close();
header("location: home.php?page=intranet.php");
exit();
}
else
{
echo "Wrong Username or Password";
header("location:index.php");
}
}
?>
</form>
</body>
</html>
