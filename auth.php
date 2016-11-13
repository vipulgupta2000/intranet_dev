<?php
	//Start session
	session_start();

	if(!isset($_GET['page']))
	{echo "incorrect way to access page";exit();
	}

	if(isset($_SESSION['SESS_uname']))
	{
	//echo "Welcome ". $_SESSION['SESS_uname']." ";
    //echo "Your Employee ID is=" . $_SESSION['SESS_empid'];
       $con = @mysql_connect("localhost","iztwiki","QarhBM7JGXEYXY3t");
	    if(!$con)
	    {
	    die('Could not connect: ' . mysql_error());
    	}
    mysql_select_db("iztwiki",$con);
    $page_array=array('logout.php','text.php','search');
    if(!in_array($_GET['page'],$page_array))
    {pageaccess();}

    }else
    {
    header("location:accessdenied.php");
	exit();
	}

function pageaccess()
{
    $sql="select * from access where groupname='$_SESSION[SESS_perm]' and page_name='$_GET[page]'";
	$result=mysql_query($sql)or die ("cannot execute");
	$cnt = mysql_num_rows($result);
	if(!($cnt==1 || ($_SESSION['SESS_perm']=='admin')))
	{
	header("location:accessdenied.php");
	}
}



?>