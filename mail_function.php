<?php

require_once ('mail/class.phpmailer.php');
require_once ('mail/class.smtp.php');

error_reporting(E_ALL ^ E_DEPRECATED);
$ini_array = parse_ini_file("../../conf.ini",true);
$host=$ini_array['intranet']['host'];
$username=$ini_array['intranet']['username'];
$password=$ini_array['intranet']['password'];
$db_name=$ini_array['intranet']['db_name'];

mysql_connect("$host","$username","$password") or die("cannot connect");
mysql_select_db ("$db_name") or die ("cannot select DB");


function send_mail($to, $to_name, $subject, $message, $seconds){
	set_time_limit($seconds);
	
	$sql = mysql_query("select * from configmail where status = 'Active'");
	$num_row = mysql_num_rows($sql);
	
	if($num_row >=1){
		while($row = mysql_fetch_array($sql)){
			$host = $row['host'];
			$fromID = $row['fromid'];
			$fromName = $row['fromname'];
			$login = $row['login'];
			$password = $row['password'];
			$port = $row['port'];
			$smtpAuth = $row['smtpAuth'];
			$smtpSecure = $row['smtpSecure'];
			$smtpDebug = $row['smtpDebug'];
		}
			
			$mail = new PHPMailer();
			$mail->IsSMTP();							// set mailer to use SMTP
			$mail->IsHTML(true);						// set HTML Is true to Execute HTML
			$mail->Host = $host;						// specify main and backup server
			$mail->SMTPAuth = $smtpAuth;				// turn on SMTP authentication
			$mail->Username = $login;					// SMTP username
			$mail->Password = $password;				// SMTP password
			$mail->Port = $port;
			$mail->SMTPSecure = $smtpSecure;			// SMTP Secure
			$mail->SetFrom($fromID, $fromName);			// SMTP From
			$count = count($to);
						
			for($i = 0; $i < $count; $i++){
				
				if($to[$i] != ''){
					$mail->AddAddress($to[$i], $to_name[$i]);		// To Whom Mail Will be Send
					$mail->Subject = $subject[$i];					// Subject of the Mail
					$mail->Body  = $message[$i];					// Body of the Mail Content
			
					if(!$mail->Send()){
					echo "Mailer Error: " . $mail->ErrorInfo;
					exit;
					}
					else{
						echo "Notification Mail Sent<br>";	
												
					}
				}
			}		
	}
	else{
		echo "Error : No Active Config Mail";
	}
}
?>
