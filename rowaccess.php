<?php

function rowaccess($id,$tbl,$key='access')
{


//$sql1="select groupid from groups where name='".$_SESSION['SESS_access']."'";
//$result1=mysql_query($sql1);
//$gpid=mysql_result($result1,0);
$j=0;
$k=0;
$arr_acc=explode(';',$_SESSION['SESS_access']);
if(in_array(1,$arr_acc))
{$j=1;
}else
{
$sql="select $key,id from $tbl where id=$id";
//echo $sql;
$result=mysql_query($sql);


while($row = mysql_fetch_array($result))
{

	$arr=explode(';',$row[$key]);
	if(in_array(0,$arr))
	{$j=1; break;}
	//print_r($arr);

	foreach($arr_acc as $gpid)
	{
			//echo "j is ".$gpid;
			if(in_array($gpid,$arr))
			{
			$j=1;
			$k=1;
			
			}


	}
/*if($k==1){
echo "hiii".$row['id'];
$k=0;
}*/

}
}
//echo "j is ".$j;
return $j;
}


function qualaccess($tbl,$key='access')
{
//$sql1="select groupid from groups where name='".$_SESSION['SESS_access']."'";
//$result1=mysql_query($sql1);
//$gpid=mysql_result($result1,0);
$j=0;
$k=0;
$catid=NULL;

$arr_acc=explode(';',$_SESSION['SESS_access']);
if(in_array(1,$arr_acc))
{$j=1;$catid="admin";
}else
{
$sql="select $key,id from $tbl ";
//echo $sql;
$result=mysql_query($sql);
while($row = mysql_fetch_array($result))
{

	$arr=explode(';',$row[$key]);
	if(in_array(0,$arr))
	{$j=1;$k=1;}
	else
	{
	foreach($arr_acc as $gpid)
	{
			//echo "j is ".$gpid;
			if(in_array($gpid,$arr))
			{
			$j=1;
			$k=1;
			
			}


	}
	}
if($k==1){
//echo "hiii".$row['id'];
$k=0;
$catid=$catid.$row['id'].',';
}
}
}
$catid=chop($catid,",");
if($catid=="")
{
//echo "no access";
return NULL;
}
//$catid = substr($catid, 0, -1);
//echo "j is ".$j;
//echo $catid;
//echo $catid;
return $catid;
}







