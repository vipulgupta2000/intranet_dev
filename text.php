<?php

$tbl_name="users";


$sql="select catname, id from category";
if(!$result=mysql_query($sql))
{die(mysql_error());
}else
{
echo "<div class=\"panel-group\" id=\"accordion\">";
while($row = mysql_fetch_array($result))
{
 echo "<div class=\"panel panel-default\">";
  echo   "<div class=\"panel-heading\">";
   echo   "<h4 class=\"panel-title\">";
   $adr='#collapse'.$row['id'];
	echo "<a data-toggle=\"collapse\" data-parent=\"#accordion\" href=$adr>".$row['catname']." </a>";
	$adr1 = substr($adr, 1);
 echo "</h4>    </div>";
    echo "<div id=$adr1 class=\"panel-collapse collapse\">";	
	echo "<div class=\"panel-body\">";
      $tbl="pages";
     $sql1="select id,title,page_filter,status from $tbl where status='published' and catid='$row[id]' ORDER BY time DESC LIMIT 3";
     if($result1=mysql_query($sql1))
     {
     while($row1=mysql_fetch_array($result1))
     {
	echo "<p>";
	$title=strtoupper($row1['title']);
     echo "<b><a href=\"home.php?page=update&id=".$row1['id']."\">".$title."</a></b>";
     $string=substr($row1['page_filter'],0,100);
     echo "<br />".$string;
     echo"</p>";
     }
	 }else{
     die(mysql_error());
     }
	 echo "</div></div></div>";}
	 }
     echo "</div>";
	 ?>