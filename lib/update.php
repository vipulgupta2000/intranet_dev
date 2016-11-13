<?php

function input($tbl,$qual,$arr,$arr_show)
{
	$sql1=dbsql($tbl);
	$tg_top="<div class=\"table-responsive\"><table class=\"table table-striped\" id=\"".$tbl."\" width=auto border=1 cellpadding=2 cellspacing=2>";
	$tg_hdr="<th>";
	$tg_hdr_cl="</th>";
	$tg_ro="<tr>";
	$tg_ro_cl="</tr>";
	$tg_td="<td>";
	$tg_td_cl="</td>";
	$tg_top_cl="</table></div>";
	$tg_ip="<input";
	$tg_ip_type=" type=\"";
	$tg_ip_name="\" name=\"";
	$tg_ip_value="\" value=\"";
	$tg_ip_size="\" size=\"";
	$tg_ip_id="\" id=\"";
	$tg_class="\" class=\"ro";
	$tg_ip_cl="\" />";
	$tg_chk="<input type=\"checkbox\" name=\"chb";
	$tg_chk_val="";
	$tg_dat="<a href=\"javascript:NewCal('";
	$tg_dat_cl="','ddmmyyyy')\"><img src=datetimepick/cal.gif width=16 height=16 border=0 alt=Pick a date></a>";
	$tg_sel="<select id=\"";
	$tg_cl="\" >";
	$tg_sel_cl="</select>";
	$tg_opt="<option value=\"";
	$tg_opt_cl="</option>";
	$tg_hidden="hidden";
	$tg_readonly="\" readonly ";
	$tg_text="<textarea rows=\"4\" cols=\"15\" ";
	$tg_text_cl="</textarea>";

	$result1=mysql_query($sql1);
	$data_sql="select * from ".$tbl;
	if(isset($qual))
		{
		$qual=" where ".$qual;
		$data_sql=$data_sql.$qual;
	}
	$data_sql=getPagesql($data_sql,7);
	//echo $data_sql;
//mode 0 is columnar and mode 1 for row-wise printing
	$a="";
	$opt=array(1 => "k");
	$mode=1;
	$j=0;

	if($mode==0)
	{
	//Open Table
	echo $tg_top;
	//Print Header column
		while($row = mysql_fetch_array($result1))
		{

		echo $tg_ro;

		echo $tg_hdr.$row['alias'].$tg_hdr_cl;

	//Print data columns
		$result_data=mysql_query($data_sql);
		while($datarow=mysql_fetch_array($result_data))
		{
		if($row['dbindex']=='primary')
		{
		echo $tg_td.$datarow[$row['name']].$tg_td_cl;
		}else
		{
		echo $tg_td.$tg_ip.$tg_ip_type.$row['type'].$tg_ip_name.$row['name'].$tg_ip_size.$row['size'].$tg_ip_value;
						echo $datarow[$row['name']];
				echo $tg_ip_cl.$tg_td_cl;
		}
		}

		echo $tg_ro_cl;
		}
	//Close Table
		echo $tg_top_cl;
	}
	if($mode==1)
	{
//Open Table
		echo $tg_top;

//Print Header row
		$a= $tg_ro;
		//blank cell for checkbox
		$a=$a.$tg_hdr.$tg_hdr_cl;
		while($row = mysql_fetch_array($result1))
		{
		if(empty($arr_show) || in_array($row['name'],$arr_show))
		$a=$a.$tg_hdr.$row['alias'].$tg_hdr_cl;
		}
		$a=$a.$tg_ro_cl;

//Print Data rows
		$result_data=mysql_query($data_sql);
		$cnt=0;
		while($datarow=mysql_fetch_array($result_data))
		{
		$j=1;

		$a=$a.$tg_ro;

		//Input for checkbox
		$a=$a.$tg_td.$tg_chk.$cnt.$tg_chk_val;
		$a=$a.$tg_ip_cl.$tg_td_cl;

		$result1=mysql_query($sql1);
		while($row = mysql_fetch_array($result1))
		{
			if(empty($arr_show) || in_array($row['name'],$arr_show))
				{
					if($row['dbindex']=="primary" || !in_array($row['name'], $arr))
					//if($row['dbindex']=="primary")
					{
						//$a=$a.$tg_td.$tg_ip.$tg_ip_type.$row['type'].$tg_ip_name.$row['name'].$cnt.$tg_readonly.$tg_ip_size.$row['size'].$tg_ip_value;
						$a=$a.$tg_td.$tg_ip.$tg_ip_type.$tg_hidden.$tg_ip_name.$row['name'].$cnt.$tg_ip_size.$row['size'].$tg_ip_value;
						//$a=$a.$datarow[$row['name']].$tg_ip_cl.$tg_td_cl;

						if($row['type']=="date")
						{
						$a=$a.getmydate($datarow[$row['name']]).$tg_ip_cl;
						$a=$a.getmydate($datarow[$row['name']]).$tg_td_cl;
						}else
						{
						$a=$a.$datarow[$row['name']].$tg_ip_cl;
						$a=$a.$datarow[$row['name']].$tg_td_cl;
						}
					}elseif($row['type']=="textarea")
					{
						$a=$a.$tg_td.$tg_text.$tg_ip_name.$row['name'].$cnt.$tg_ip_cl;
						$a=$a.$datarow[$row['name']].$tg_text_cl.$tg_td_cl;
					}
					elseif($row['type']=="option")
					{
								$a=$a.$tg_td.$tg_sel.$row['name'].$cnt.$tg_ip_name.$row['name'].$cnt.$tg_cl;
										$a=$a.$tg_opt.$datarow[$row['name']].$tg_cl.$datarow[$row['name']].$tg_opt_cl;
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
					else
					{
						$a=$a.$tg_td.$tg_ip.$tg_ip_type.$row['type'].$tg_ip_name.$row['name'].$cnt.$tg_ip_size.$row['size'].$tg_ip_value;
							if($row['type']=="date")
							{
							$a=$a.getmydate($datarow[$row['name']]);
							$a=$a.$tg_ip_id.$row['name'].$cnt.$tg_class;
							$a=$a.$tg_ip_cl.$tg_dat.$row['name'].$cnt.$tg_dat_cl.$tg_td_cl;
							}
							else
							{$a=$a.$datarow[$row['name']].$tg_ip_cl.$tg_td_cl;}
					}
//close if condition
				}
//close inner while
		}
		$cnt++;
		$a=$a.$tg_ro_cl."\n";
		}
//Close Table
		$a=$a.$tg_top_cl;
		$a=$a."<input type=\"number\" name=\"icnt\" value=$cnt style=\"display:none\">";
		$a=$a."<input type=\"hidden\" name=\"field_edit\" value=\"".implode(",",$arr)."\" >";
//Close Mode
	}

	//only get pages if this is main table
	if($_GET['page']==$tbl)
$a=$a."&nbsp<input class=\"btn btn-danger\" id=\"btn\" type=\"submit\" name=\"update\" value=\"Update\">";
if($_SESSION['SESS_perm']=='admin')
$a=$a."&nbsp<input class=\"btn btn-danger\" id=\"btn_del\" type=\"submit\" name=\"delete\" value=\"Delete\">";
return $a;
}


?>