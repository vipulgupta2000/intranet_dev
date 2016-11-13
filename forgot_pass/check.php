<html>
<head>
<title>Welcome to Intranet</title>
</head>
<body>

<?php
require_once('../mail_function.php');
require_once('db.php');

if(isset($_POST['sent'])){
	$empid = $_POST['for_empid'];
	$email = $_POST['for_email'];
	$empname = $_POST['for_empname'];
	$rs ='';
	$sql = mysql_query("select empid from $tbl_name where empid = '".$empid."' and email_id = '".$email."' and Status = 'active'");
	while($row = mysql_fetch_array($sql)){
		$rs = $row['empid'];
	}
	$str = 'j57412462462rvf2ve45be45be4gb4e8b74';
	if($rs == $empid){
		$to[] = $_POST['for_email'];
		$to_name[] = $_POST['for_empname'];
		
		$shuffled = str_shuffle($str);
		$mytemp = sha1($shuffled);
		
		$subject[] = "Reset Your Password";
		
		$message[] = "Please Click On this Link to Reset Your Password<br><br> http://".$_SERVER['SERVER_NAME']."/intranet/forgot_pass/change.php?temp=".$shuffled."<br><br>This Link is Valid for One Hour.<br>This is the System Generated Mail.<br>Thanks<br>IZ001 Server";
		
		/* $current_date = date('Y-m-d H:I:s');
		$date = new DateTime($current_date);
		$date->modify('+1 hour');
		$validity =  $date->format('U'); */
	
		$date=date_create();
		$j=$date->modify("+1 hours");
		$validity= date_format($j,'U');
		
		/* Code Added for V3 to store single row in temp_table for each emp */
		$query = mysql_query("select count(empid) from temp_table where empid = '$empid'");
		$query_result = mysql_result($query,0);
		
		if($query_result == 0){
			$sql = mysql_query("insert into temp_table (empid, email, temp_string, validity) values('$empid','$email','$mytemp','$validity')");
		}
		
		else{
			$sql = mysql_query("update temp_table set temp_string = '$mytemp' , validity = '$validity' where empid = '$empid'");
		}
		
		//$sql = mysql_query("insert into temp_table (empid, email, temp_string, validity) values('$_POST[for_empid]','$empid','$mytemp','$validity')");
		
		
		send_mail($to, $to_name, $subject, $message, 10);
		echo "Please go and check your Email and Follow the Instruction to Reset Password...";
		echo "<br/><br/><a href='../index.php'>Back to Login Again</a>";
	}
	else{
		echo "<center>Error: Wrong attempt, Try Again Later...!!!";
		echo "<br/><br/><a href='../index.php'>Back to Login Again</a></center>";
	}
}
else{
	echo "<center><h3>Sorry you don't have Access to this Page...!!!</h3></center>";
}
?>
