<?php
$host="localhost";
$username="iztwiki";
$password="QarhBM7JGXEYXY3t";
$db_name="iztwiki";
$tbl_name="users";


mysql_connect("$host", "$username", "$password") or die("cannot connect");
mysql_select_db ("$db_name") or die ("cannot select DB");


echo"<form action=\"forget.php\" method=\"post\">";
echo "Employee id:<input type=\"text\" name=\"empid1\">";
echo"<input type=\"submit\" name=\"submit\" value=\"Send\">";
echo"</form>";


$sql="select email_id from users where empid='$_POST[empid1]'";

$result=mysql_query($sql);


while($row = mysql_fetch_array($result))
{
$email=$row['email_id'];
if($_POST['submit']=='send')
{

if(mysql_num_rows($result))
{
echo "User exist";
}
else
{
echo "No user exist with this Employee id";
}
}

if(mysql_num_rows($result))
{
$code=rand(100,999);

$message="You activation link is:http://192.168.1.4/intranet/resetpassword.php?email=$email&code=$code";
$message="$code";
mail($row['email_id'], "Password Reset Code", $message);
echo "Email sent";
$sql1="update users set activation_code='$code' where empid='$_POST[empid1]'";
mysql_query($sql1) or die ("Query Error :".mysql_error());
}
else
{
echo "No user exist with this email id";
}
}

?>