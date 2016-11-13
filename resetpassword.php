<?php


$host="localhost";
$username="editor";
$password="YqvjywySafVDSDej";
$db_name="editor";
$tbl_name="users";


mysql_connect("$host", "$username", "$password") or die("cannot connect");
mysql_select_db ("$db_name") or die ("cannot select DB");

echo"<form action=\"resetpassword.php\" method=\"post\">";
echo "NEW PASSWORD:<input type=\"text\" name=\"new\">";
echo "EMPLOYEE ID:<input type=\"text\" name=\"empid2\">";
echo "Activation Code:<input type=\"text\" name=\"code\">";
echo"<input type=\"submit\" name=\"submit\" value=\"submit\">";
echo"</form>";


$sql="select * from users";

$result=mysql_query($sql);


while($row = mysql_fetch_array($result))
{

if(isset($_POST['submit']))
{
$a=sha1($_POST['new']);

$sql1="update users set password='$a' WHERE empid='$_POST[empid2]' and activation_code='$_POST[code]'";
mysql_query($sql1) or die ("Query Error :".mysql_error());
}
}
?>