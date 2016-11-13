<html>
<head>
<title>Welcome to Intranet</title>
<link rel="stylesheet" type="text/css" href="../css/templateblue.css" />
</head>
<body>


<?php
require_once('db.php');
if(isset($_POST['change'])){
$empid = $_POST['for_empid'];
$empname = $_POST['for_empname'];
$new_pass = $_POST['for_pass'];


$mynewpass = sha1($new_pass);

$sql = "update users set password = '$mynewpass' where empid=$empid";

if(!mysql_query($sql))
	{
	die.mysql_error();
	}
	else{
		echo "<center>Password Successfully Changed";
		echo "<br/><br/><a href='../index.php'>Back to Login Again</a></center>";
	}
}
else{
	echo "<center><h3>Sorry you don't have Access to this Page...!!!</h3></center>";
}

?>


