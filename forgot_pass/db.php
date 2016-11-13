<?php
error_reporting(E_ALL ^ E_DEPRECATED);
$ini_array = parse_ini_file("../../conf.ini",true);
$host=$ini_array['intranet']['host'];
$username=$ini_array['intranet']['username'];
$password=$ini_array['intranet']['password'];
$db_name=$ini_array['intranet']['db_name'];
$tbl_name = "users";
// Connect to server and select database.
mysql_connect($host,$username,$password) or die("cannot connect");
mysql_select_db ($db_name) or die ("cannot select DB");

?>


