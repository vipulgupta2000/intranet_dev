<html>
<head>
<title>Time Management System</title>
</head>
<body>
<form action="" method="POST">
<?php
session_start();
$host="localhost";
$username="lmsuser";
$password="v89bXBBYNQCfDDw5";
$db_name="tms_lms";
$tbl_name="usertable";

// Connect to server and select database.
mysql_connect("$host", "$username", "$password") or die("cannot connect");

mysql_select_db ("$db_name") or die ("cannot select DB");

// username and password sent from form

$emp_id=$_REQUEST["user"];
$pass=$_REQUEST["pass"];
//$myusername=$_POST['myusername'];
//$mypassword=$_POST['mypassword'];

$tmp = mysql_query("select username,password from usertable where empid = '$emp_id'");
$tmp1 = mysql_fetch_array($tmp);
$myusername = $tmp1['username'];
$mypassword = $tmp1["password"]; 
//echo "the user name is".$myusername;
//echo "the password is".$mypassword;

// To protect MySQL injection (more detail about MySQL injection)
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);
$myusername = mysql_real_escape_string($myusername);
$mypassword = mysql_real_escape_string($mypassword);

$sql="SELECT * FROM usertable WHERE username='$myusername' and password='$mypassword'";

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
$_SESSION['SESS_access'] = $member['permission'];
$_SESSION['SESS_mgrid'] = $member['mgrid'];
session_write_close();
header("location: home.php?page=text.php");
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
