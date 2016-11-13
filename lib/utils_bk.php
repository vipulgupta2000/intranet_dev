 <?php
function createpage()
{
//Set Category
$category=isset($_GET['catname'])?$_GET['catname']:1;
echo '<input type=\"hidden\" name=\"catid\" value="'.$category.'" />';

//call category function to retrieve cat names based on catid
$cat=category();
echo "<h2>".strtoupper($cat[$category])."</h2>";

//Set Title
echo "<h3>Title:</h3>".'<input type="text" name="title" id="title" size="95" value="" required/>';
echo "<br /><br />";
echo "<textarea id=\"message\" name=\"message\" rows=\"15\" cols=\"80\">enter</textarea>";

if (get_magic_quotes_gpc()) $_POST = array_map('stripslashes', $_POST);

$catid = isset($_POST['catid']) ? $_POST['catid'] : 1;
$message = isset($_POST['message']) ? addslashes($_POST['message']) : 'Message';
$title=isset($_POST['title']) ? addslashes($_POST['title']) : 'Title';
$title=cleanup($title);
$message_filter=cleanup($message);
$message_filter=strtolower($cat[$category])." ".$title.$message_filter;

if(isset($_POST['submit']))
{

$tbl="pages";
$time=time();

$sql="insert into $tbl (catid,page,page_filter,status,author,time,link,title,modified_by) values($catid,'$message','$message_filter','draft','$_SESSION[SESS_uname]',$time,0,'$title','$_SESSION[SESS_uname]')";
//extractimage($message);
if(!$result=mysql_query($sql))
{ die(mysql_error());
}

}
echo "<input class=\"btn btn-primary\" name=\"submit\" type=\"submit\" value=\"Submit\" />";
}

function cleanup($msg)
{
$msg= strip_tags(html_entity_decode($msg));
//$msg=preg_replace('|[^a-zA-Z0-9_.,\s\t\r]|', '', $msg);
$msg=preg_replace('|[^a-zA-Z0-9_.,]([\s\t\r\n]+)|', '', $msg);
//$msg=preg_replace("|[']|", "", $msg);
$msg=strtolower ( $msg );
return $msg;
}

function extractimage($message)
{
$message=stripslashes($message);
$i=0;
while(strpos($message,'<img'))
{
$k=strpos($message,'<img');
$j= stripos($message,'/>',$k);
//echo "start of img ".$k."till position".$j."total length ".strlen($message);
//$sub1=substr($message,$k-1,$j);
$sub1=substr($message,$k,$j-$k+2);
//echo htmlentities($sub1);
echo $sub1;
$message=substr($message,$j+1,strlen($message)-$j+1);
//echo $message;
$i++;
}
//echo addslashes($sub1);
$sub2=strstr($sub1,'/>',true)."/>";
//echo $sub2;
//echo substr($sub1,1,strstr($sub1,'/>')+2);
// and now we print out all the images
//preg_match_all('/< img.+ src = [\'"](?P< src >.+)[\'"].*>/i', $message, $images);
//$path="C:/wamp/www/editor/tinymce/uploads/";
//$ext=".jpeg";
//$fname=$path.md5((mt_rand(10,10000000000000))).$ext;
//$data=$sub1;
//$data = base64_decode($data);
//$im = imagecreatefromstring($data);
//if ($im !== false) {
  //  header('Content-Type: image/jpeg');
  //imagejpeg($im,$fname);
//    imagedestroy($im);
//}
// lets see the images array
//print_r( $images['src'] );
//echo $message_filter;
//echo htmlentities($sub1);
//echo htmlentities($message);
//preg_match_all("|<img(.*)/>|", $message, $match,PREG_PATTERN_ORDER);
//print_r($match);
//foreach($match as $val)
//{$i=0;
//echo "<img ".stripslashes($val[0][$i])." />";$i++;}
//echo "<img ".stripslashes($match[0][1])." />";
//echo stripslashes($match[1][0]);

}

function category($out='catname')
{
$sql="select catid,catname from category";
$result=mysql_query($sql) or die(mysql_error());
while($row_cat = mysql_fetch_array($result))
{
if($out=='catid')
$cat[$row_cat['catname']]=$row_cat['catid'];
else
$cat[$row_cat['catid']]=$row_cat['catname'];
}
return $cat;
}

function getPagelink($iteration)
	{
	echo "<ul class=\"pagination\"><li><a href=\"#\">&laquo;</a></li>";
	for($i=1;$i<=$iteration;$i++)
	{ echo "<li><a href=home.php?page=".$_GET['page']."&num=".$i;
	echo ">".$i."</a></li>";
	}$x=$i-1;echo "<li><a href=\"home.php?page=".$_GET['page']."&num=".$x."\">&raquo;</a></li></ul>";
	}

function getPagesql($sql,$rec_limit)
	{

		$start=0;

		$result_page=mysql_query($sql);
		$total=mysql_num_rows($result_page);

		if(isset($_GET['num']))
		{$start=(($_GET['num'])-1)*$rec_limit;

		}
		if(ceil($total/$rec_limit)>1)
		getPagelink(ceil($total/$rec_limit));
		$sql=$sql." limit ".$start.", ".$rec_limit;
		return $sql;
	}

function getmydate($time)
{
$tdate = date_create();
date_timestamp_set($tdate,$time);
return date_format($tdate, 'd-m-Y');
}
function getmytime($time)
{
$tdate = date_create();
$time=$time+19800;
date_timestamp_set($tdate,$time);
return date_format($tdate, 'd-m-Y H:i:s');
}
function setmydate($time)
{
$ts1 = date_create($time);
return date_format($ts1,'U');
}
?>