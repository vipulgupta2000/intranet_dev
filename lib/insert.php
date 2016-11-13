<?php
//Declaration Section

require_once('auth.php');
include('display.php');
include('addrow.php');
include('update.php');
include('utils.php');
include('rowaccess.php');

//DB SQLs
$tbl=$_GET['page'];
if(isset($_POST['delete']))
{
delete($tbl);
}
if(isset($_POST['update']))
{
update($tbl);
//echo "<p> records updated </p>";
}
if(isset($_POST['addrow']))
{
$sqli=insert($tbl);
//echo $sqli;
if(!mysql_query($sqli))
	{
	die.mysql_error();
	}
//echo "<p> records inserted </p>";
}
if(isset($_POST['create']))
{
$sqli=create($tbl);
echo $sqli;
if(!mysql_query($sqli))
	{
	die.mysql_error();
	}
}

if(isset($_POST['drop']))
{
$sqli="drop table $tbl";
echo $sqli;
if(!mysql_query($sqli))
	{
	die.mysql_error();
	}
}


include($_GET['page']);

//echo "<input id=\"btn\" type=\"submit\" name=\"addrow\" value=\"Add Row\">";
//echo "<input id=\"btn\" type=\"submit\" name=\"drop\" value=\"drop\">";

//if(!mysql_query("select * from ".$_GET['page']))
//{
//echo " Please click button to create table in the database";
//echo "<input id=\"btn\" type=\"submit\" name=\"create\" value=\"create\">";
//}
?>