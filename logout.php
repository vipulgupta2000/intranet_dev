<?php
	//Start session
	//session_start();
	//require_once('auth.php');
	//Unset the variables stored in session
	unset($_SESSION['SESS_uname']);
    unset($_SESSION['SESS_pwd']);
    session_destroy();
mysql_close($con);
?>

<p align="center">&nbsp;</p>
<h4 align="center" class="err">You have been successfully logged out.</h4>
<p align="center">Click here to <a href="index.php">Login</a></p>

