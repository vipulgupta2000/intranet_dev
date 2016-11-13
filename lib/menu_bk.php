
<?php


echo "<li><a class=\"menu_left\" href=\"home.php?page=dashboard \">Dashboard</a></li>";
echo "<li class=\"dropdown\">";
echo "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Administration<b class=\"caret\"></b></a>";
echo "<ul class=\"dropdown-menu\">";
$a='';
$sql="select tblid,name,alias from config";
if(!$result=mysql_query($sql))
{die(mysql_error());
}else
{
while($row = mysql_fetch_array($result))
{
if(($row['tblid']<15))
{
echo "<li><a class=\"menu_left\" href=\"home.php?page=".$row['name']."\">".$row['alias']."</a></li>";
}
elseif(($row['name']=="pages"))
{
$a = $a."<li><a class=\"menu_left\" href=\"home.php?page=".$row['name']."\">".$row['alias']."</a></li>";
}
elseif($_SESSION['SESS_perm']=='admin')
$a = $a."<li><a class=\"menu_left\" href=\"home.php?page=".$row['name']."\">".$row['alias']."</a></li>";
}
}
echo "</ul></li>";

echo "<li class=\"dropdown\">";
echo "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Wiki<b class=\"caret\"></b></a>";
echo "<ul class=\"dropdown-menu\">";
echo $a;
echo "</ul></li>";
?>

