<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$empid=isset($_POST['user'])?$_POST['user']:NULL;
$pass=isset($_POST['pass'])?sha1($_POST['pass']):NULL;

$sql_sync_eds="update intranet_db.login l set l.pass='$pass' where l.emp_id=$empid";
//echo $sql_sync_eds;
$sql_sync_tms="update tms_lms.usertable u set u.password='$pass' where u.empid='$empid'";
$sql_sync_ams="update ams.usertable u u.password='$pass' where u.empid='$empid'";
if(is_null($empid))
{echo "EMPID did not come so passwords not synced";
}else
{
 if($result=mysql_query($sql_sync_eds))
echo "CMS Password done<br />";
else
   die.mysql_error();   


if($result=mysql_query($sql_sync_tms))
{}
else
   die.mysql_error();   

if($result=mysql_query($sql_sync_ams))
{}
else
   die.mysql_error();   

}

?>
