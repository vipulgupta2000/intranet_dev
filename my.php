

<?php


// Connect to server and select database.

$sql="select catname from category";
if(!$result=mysql_query($sql))
{die(mysql_error());
}else
{
$i=1;
while($row = mysql_fetch_array($result))
{
echo "<div class=\"panel-group\" id=\"accordion\">";
 echo "<div class=\"panel panel-default\">";
  echo   "<div class=\"panel-heading\">";
   echo   "<h4 class=\"panel-title\">";
echo "<a data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#collapse\".$i>".$row['catname']." </a>";

//echo "<li><a class=\"menu_left\" href=\"home.php?page=".$row['name']."\">".$row['alias']."</a></li>";
$i++;

 echo "</h4>    </div>";
    echo "<div id=\"collapse\" class=\"panel-collapse collapse\">";
      echo "<div class=\"panel-body\">";
      echo "<div id=\"collapse\" class=\"panel-collapse collapse\">";
	      echo  "<div class=\"panel-body\">";

	  		

	  		//$pageid=isset($_GET['id'])?$_GET['id'] : 3;
	  		$sql1="select id,title,page_filter,status from pages where catid='1' && status='published' ORDER BY id DESC LIMIT 3";
	  		if($result1=mysql_query($sql1))
	  		{
	  		while($row1=mysql_fetch_array($result1))
	  		{
	  		echo "<p>";
	  		echo "<a href=\"home.php?page=update&id=".$row1['id']."\">".$row1['title']."</a>";
	  		$string=substr($row1['page_filter'],0,100);
	  		echo "<br />".$string;
	  		echo"</p>";
	  		}
	  		}else{
	  		die(mysql_error());
	  		}}}
	  		?>
	  		</div>
	      </div>
  </div>

