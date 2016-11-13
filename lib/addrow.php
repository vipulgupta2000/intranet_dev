<?php

require_once("auth.php");

function addrow($tbl)
{
	$tg_top="<table width=auto border=1 cellpadding=2 cellspacing=2>";
	$tg_hdr="<th>";
	$tg_hdr_cl="</th>";
	$tg_ro="<tr>";
	$tg_ro_cl="</tr>";
	$tg_td="<td>";
	$tg_td_cl="</td>";
	$tg_top_cl="</table>";
	$tg_ip="<input";
	$tg_ip_type=" type=\"";
	$tg_ip_name="\" name=\"";
	$tg_ip_value="\" value=\"";
	$tg_ip_size="\" size=\"";
	$tg_ip_id="\" id=\"";
	$tg_class="\" class=\"ro";
	$tg_ip_cl="\" >";
	$tg_chk="<input type=\"checkbox\" name=\"chb";
	$tg_chk_val="";
	$tg_dat="<a href=\"javascript:NewCal('";
	$tg_dat_cl="','ddmmyyyy')\"><img src=datetimepick/cal.gif width=16 height=16 border=0 alt=Pick a date></a>";
	$tg_sel="<select id=\"";
	$tg_cl="\" >";
	$tg_sel_cl="</select>";
	$tg_opt="<option value=\"";
	$tg_opt_cl="</option>";

	//DB SQLs
	$sql="select tblid from config where name='$tbl'";
	$result=mysql_query($sql);
	$myvar=mysql_result($result,0);
	$sql1="select * from field where tblid=$myvar";
	$result1=mysql_query($sql1);
$fname="";
$fval="";
	$a="";
	$opt=array(1 => "k");
	$mode=1;
	$j=0;

	/*if(isset($_POST['sqli']))
	{
	echo $_POST['sqli'];
	if (!mysql_query($_POST['sqli']))
			{
			die('Error: ' . mysql_error());
			}
	}
	*/
	if(isset($_POST['fname']))
	{
	echo $fname;
	$array=array($_POST['fname']);
	print_r($array);
	}
//Open Table
		echo $tg_top;

//Print Header row
		$a= $tg_ro;
		//blank cell for checkbox
		//$a=$a.$tg_hdr.$tg_hdr_cl;
		while($row = mysql_fetch_array($result1))
		{
		if(!$row['dbindex']=='primary')
		$a=$a.$tg_hdr.$row['alias'].$tg_hdr_cl;
		}
		$a=$a.$tg_ro_cl;

//Print Data rows
		$cnt=0;
		{
		$j=1;

		$a=$a.$tg_ro;

		//Input for checkbox
		//$a=$a.$tg_td.$tg_chk.$cnt.$tg_chk_val;
		//$a=$a.$tg_ip_cl.$tg_td_cl;

		$result1=mysql_query($sql1);
		while($row = mysql_fetch_array($result1))
		{
		if($row['type']=="option")
				{
				$a=$a.$tg_td.$tg_sel.$row['name'].$cnt.$tg_ip_name.$row['name'].$cnt.$tg_cl;
				if($cnt==0)
				{
				$sql_opt="select * from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'];
				$result_opt=mysql_query($sql_opt);
				$opt[$j]="";
				while($row_opt = mysql_fetch_array($result_opt))
					{
					$opt[$j]=$opt[$j].$tg_opt.$row_opt['value'].$tg_cl.$row_opt['alias'].$tg_opt_cl;
					}

				}

				$a=$a.$opt[$j].$tg_sel_cl.$tg_td_cl;
				$j++;
				}
				elseif(!$row['dbindex']=='primary')
				{
				$a=$a.$tg_td.$tg_ip.$tg_ip_type.$row['type'].$tg_ip_name.$row['name'].$cnt.$tg_ip_size.$row['size'].$tg_ip_value;
					if($row['type']=="date")
					{
					$a=$a.$tg_ip_id.$row['name'].$cnt.$tg_class;
					$a=$a.$tg_ip_cl.$tg_dat.$row['name'].$cnt.$tg_dat_cl.$tg_td_cl;
					}
					else {
					$a=$a.$tg_ip_cl.$tg_td_cl;
					}
				}
		}
				//Store variables for insert query
				if(!$row['dbindex']=='primary')
							{$fname=$fname.$row['name'].",";
							if($row['type']=='date')
							$fval=$fval."'\"".setmydate($_POST[$row['name']])."\"',";
							else
							$fval=$fval."'\".$"."_"."POST['".$row['name']."'].\"',";
					}
		$cnt++;
		$a=$a.$tg_ro_cl."\n";
		}
//Close Table
		echo $a.$tg_top_cl;

$fname=chop($fname,",");
$fval=chop($fval,",");
$sqli = "INSERT INTO ".$tbl." ( ".$fname." ) VALUES (".$fval.")";
echo "<input class=\"btn btn-primary\" id=\"btn\" type=\"submit\" name=\"addrow\" value=\"Add Row\">";
//echo "<input class=\"btn btn-warning\" id=\"btn\" type=\"submit\" name=\"modify\" value=\"modify\">";
return $sqli;
}

function insert($tbl)
{
$sql1=dbsql($tbl);
$result1=mysql_query($sql1);
$fname="";	$fval="";	$i=0;
	while($row = mysql_fetch_array($result1))
	{
		if(!$row['dbindex']=='primary')
		{
		$fname=$fname.$row['name'].",";
		if($row['type']=='date')
		$fval=$fval."".setmydate($_POST[$row['name'].$i]).",";
		elseif($row['type']=='password')
		$fval=$fval."'".sha1($_POST[$row['name'].$i])."',";
		else
		{$fval=$fval."'".$_POST[$row['name'].$i]."',";
		//echo "fval is".$row['name'];
		}
		}
	}
$fname=chop($fname,",");
$fval=chop($fval,",");
$sqli="INSERT INTO ".$tbl." ( ".$fname." ) VALUES (".$fval.")";
return $sqli;
}

function insert_data($tbl)
{
$sql=dbsql($tbl);
	$result1=mysql_query($sql);
$fname="";	$fval="";	$i=0;
	while($row = mysql_fetch_array($result1))
	{
		if(!$row['dbindex']=='primary')
		{
		$fname=$fname.$row['name'].",";
		if($row['type']=='date')
		$fval=$fval."".setmydate($_POST[$row['name'].$i]).",";
		elseif($row['type']=='password')
		$fval=$fval."'".sha1($_POST[$row['name'].$i])."',";
		else
		{$fval=$fval."'".$_POST[$row['name']]."',";
		//echo "fval is".$row['name'];
		}
		}
	}
$fname=chop($fname,",");
$fval=chop($fval,",");
$sqli="INSERT INTO ".$tbl." ( ".$fname." ) VALUES (".$fval.")";
if(!mysql_query($sqli))
		{
		die.mysql_error();
	}
}

function create($tbl)
{
$sql1=dbsql($tbl);
$result1=mysql_query($sql1);
$fname="";	$fval="";	$i=0;
	while($row = mysql_fetch_array($result1))
	{
		if('primary' == $row['dbindex'])
		{
		$fname=$fname.$row['name']." ".$row['dbtype']."(".$row['size'].") NOT NULL AUTO_INCREMENT PRIMARY KEY,";
		}else
		{$fname=$fname.$row['name']." ".$row['dbtype']."(".$row['size']."),";}
	}
$fname=chop($fname,",");
$fval=chop($fval,",");
$sqli="CREATE Table ".$tbl." ( ".$fname." ) ";
return $sqli;
}

function update($tbl)
{
$sqlu=dbsql($tbl);
$fname="";	$fval="";	$i=0;
//$data_sql="select * from ".$tbl;
//$result_data=mysql_query($data_sql);
$cnt=$_POST['icnt'];
$field_list=explode(",",$_POST['field_edit']);

while($i<$cnt)
{

if(isset($_POST['chb'.$i]))
{
	$result1=mysql_query($sqlu);
	while($row = mysql_fetch_array($result1))
	{
		if($row['dbindex']=="primary" ||in_array($row['name'], $field_list))
		{if($row['dbindex']=='primary')
		{
		$fval=$fval.$row['name']."=";
			if($row['dbtype']=='int')
			{
			$fval=$fval.$_POST[$row['name'].$i].",";
			}else
			{
			$fval=$fval."'".$_POST[$row['name'].$i]."',";
			}
		}
		else{
		$fname=$fname.$row['name']."=";
		if($row['type']=="date")
		{
		$fname=$fname."'".setmydate($_POST[$row['name'].$i])."',";
		}elseif($row['type']=='password')
		{$fname=$fname."'".sha1($_POST[$row['name'].$i])."',";}
		else{
		$fname=$fname."'".$_POST[$row['name'].$i]."',";
		}

		}
		}
	}


$fname=chop($fname,",");
$fval=chop($fval,",");
$sqli="UPDATE ".$tbl." set ".$fname." Where ".$fval;
//	echo $sqli."<br/>";
$fname="";	$fval="";
if(isset($sqli) && !mysql_query($sqli))
	{
	die.mysql_error();
	}
}
$i++;
}

}

function delete($tbl)
{
$sqlu=dbsql($tbl);
$fname="";	$fval="";	$i=0;
//$data_sql="select * from ".$tbl;
//$result_data=mysql_query($data_sql);
$cnt=$_POST['icnt'];
$field_list=explode(",",$_POST['field_edit']);

while($i<$cnt)
{

if(isset($_POST['chb'.$i]))
{
	$result1=mysql_query($sqlu);
	while($row = mysql_fetch_array($result1))
	{
		if($row['dbindex']=="primary" ||in_array($row['name'], $field_list))
		{echo $row['name'];if($row['dbindex']=='primary')
		{
		$fval=$fval.$row['name']."=";
			if($row['dbtype']=='int')
			{
			$fval=$fval.$_POST[$row['name'].$i].",";
			}else
			{
			$fval=$fval."'".$_POST[$row['name'].$i]."',";
			}
		}
		else{
		$fname=$fname.$row['name']."=";
		if($row['type']=="date")
		{
		$fname=$fname."'".setmydate($_POST[$row['name'].$i])."',";
		}else{
		$fname=$fname."'".$_POST[$row['name'].$i]."',";
		}

		}
		}
	}


$fname=chop($fname,",");
$fval=chop($fval,",");
$sqli="DELETE from ".$tbl." Where ".$fval;
echo $sqli."<br/>";
$fname="";	$fval="";
if(isset($sqli) && !mysql_query($sqli))
	{
	die.mysql_error();
	}
}
$i++;
}
}
?>