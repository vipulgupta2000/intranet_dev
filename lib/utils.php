
 <?php
function createpage()
{
//Set Category
$category=isset($_GET['catname'])?$_GET['catname']:1;
echo "<input type=\"hidden\" name=\"catid\" value=\"".$category."\" />";

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

function datasearch($search=NULL,$qual)
{
if(isset($search))
		{
		$search=strtolower($search);
		//$sql_filter=" and page_filter like '%$search%'";
		//$search=cleanup($search);
		}

	if(strlen($search)<=1)
	echo "Search term too short";
	else{
			echo "You searched for <b>$search</b> <hr size='1'></br>";
			//mysql_connect("localhost","your mysql username","password");
			//mysql_select_db("your database name");
 
			$search_exploded = explode (" ", preg_replace('/\s+/', ' ',trim($search)));
$construct=NULL;
$construct1=NULL; 
$construct2=NULL;
$construct3=NULL;$x=0;
$sql_filter="select distinct id,catid,page_filter,page,title,status from (SELECT id,catid,page_filter,page,title,status, "; 
$sql_filter2=" as rnk";
//echo "<br>";
//echo $sql_filter;
//echo "<br>"; 
foreach($search_exploded as $search_each)
{
$x++;
if($x==1)
$construct1 .="case when locate(' $search_each ',page_filter) > 0 THEN 1 ELSE 0 end " ;
else
$construct1 .=" + case when locate(' $search_each ',page_filter) > 0 THEN 1 ELSE 0 end ";
 
}
//echo "<br>";
//echo $construct1;
$y=0;
foreach($search_exploded as $search_each)
{
$y++;
if($y==1)
$construct2 .="page_filter LIKE '%$search_each%'";
else
$construct2 .="or page_filter LIKE '%$search_each%'";
 
}
//echo "<br>";
$construct2 ="where $construct2 and status='published')";
//echo $construct2;
$construct3=$sql_filter.$construct1.$sql_filter2;
//echo $construct3;
//echo "<br>";

$constant5=NULL;
$constant5=" from pages ";
$construct3=$construct3.$constant5.$construct2." final order by rnk desc";


 $run = mysql_query($construct3);
$foundnum = mysql_num_rows($run);

//yaha se mene add keya hei new code 


if ($foundnum==0)
echo "Sorry, there are no matching result for <b>$search</b>.</br></br>1. 
Try more general words. for example: If you want to search 'for a particular artical and other information'
then use general keyword like 'blood' 'world' 'country' 'etc.'</br>3. Try different words with similar
/meaning</br>3. Please check your spelling";
else
{
	//only get pages if this is main table
	//if($_GET['page']==$tbl)
	//$data_sql=$construct3;
	$data_sql=getPagesqlSearch($construct3,7,urlencode($search));
	$res = mysql_query($data_sql);
$cat=category();
//echo "$foundnum results found !<p>";
 
while($runrows = mysql_fetch_array($res))
{
							echo"<br>";
							echo "<li><a href=\"home.php?page=update&id=".$runrows['id']."\"><b>".$cat[$runrows['catid']]."</b></a></li>";
							echo "<b>".strtoupper($runrows['title'])."</b>";
							echo substr($runrows['page_filter'],0,200);

 
}
}
/*
echo "<html>";
echo  "<body>";
echo "<div id='pagination'>";
        echo "<div id='pagiCount'>";
            
                if(isset($pages))
                {
					echo "Value of pages".$pages;
					echo "<br>";
					echo "I am in 1st if block";
					echo "<br>";
                    if($pages > 1)        
                    {
						echo "I am in middle if block";
						echo "<br>";
						
						echo "value of cur_page".$cur_page;
						echo "<br>";
						echo "value of num_links".$num_links;
						echo "<br>";
						
					if($cur_page > $num_links)     // for taking to page 1 //
                        {
							echo "I am in 2nd if block";
							echo "<br>";
							$dir = "first";
                            //echo '<span id="prev"> <a href="'.$_SERVER['PHP_SELF'].'?page=search'.(1).'">'.$dir.'</a> </span>';
							echo '<span id="prev"> <a href="'.$_SERVER['PHP_SELF'].'?page=search&num='.(1).'">'.$dir.'</a> </span>';
							
                        }
                       if($cur_page > 1) 
                        {
							echo "I am in 3nd if block";
							echo "<br>";
                            $dir = "prev";
                            //echo '<span id="prev"> <a href="'.$_SERVER['PHP_SELF'].'?page=search'.($cur_page-1).'">'.$dir.'</a> </span>';
							//echo '<span id="prev"> <a href="'.$_SERVER['PHP_SELF'].'?page=search''">'.$dir.'</a> </span>';
							echo '<span id="prev"> <a href="'.$_SERVER['PHP_SELF'].'?page=search&num='.($cur_page-1).'">'.$dir.'</a> </span>';
                   
                        }                 
                        
                        for($x=$start ; $x<=$end ;$x++)
                        {
                            //echo "I am in for loop";
							//echo "<br>";
                            //echo ($x == $cur_page) ? '<strong>'.$x.'</strong> ':'<a href="'.$_SERVER['PHP_SELF'].'?page= search'.$x.'">'.$x.'</a> ';
							//echo ($x == $cur_page) ? "<strong>".$x."</strong>":'<a href="'.$_SERVER['PHP_SELF'].'?page=search''">'.$x.'</a> ';
							 echo ($x == $cur_page) ? '<strong>'.$x.'</strong> ':'<a href="'.$_SERVER['PHP_SELF'].'?page=search&num='.$x.'">'.$x.'</a> ';
                        }
                        if($cur_page < $pages )
                        {   echo "I am in 4nd if block";
							echo "<br>";
							$dir = "next";
                            //echo '<span id="next"> <a href="'.$_SERVER['PHP_SELF'].'?page=search'.($cur_page+1).'">'.$dir.'</a> </span>';
							echo '<span id="next"> <a href="'.$_SERVER['PHP_SELF'].'?page=search&num='.($cur_page+1).'">'.$dir.'</a> </span>';
                        }
                        if($cur_page < ($pages-$num_links) )
                        {   echo "I am in 4nd if block";
							echo "<br>";
							$dir = "last";
                       
                            //echo '<a href="'.$_SERVER['PHP_SELF'].'?page=search'.$pages.'">'.$dir.'</a> '; 
							 echo '<a href="'.$_SERVER['PHP_SELF'].'?page=search&num='.$pages.'">'.$dir.'</a> ';
                        }   
                    }
                }
            
        echo "</div>";
    echo "</div>";
   
echo "</body>";
echo "</html>";
*/ 
}

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
$sub1="";
while(strpos($message,'<img'))
{
$k=strpos($message,'<img');
$j= stripos($message,'/>',$k);
//echo "start of img ".$k."till position".$j."total length ".strlen($message);
//$sub1=substr($message,$k-1,$j);
$sub1=substr($message,$k,$j-$k+2);
//echo htmlentities($sub1);
//echo $sub1;
$message=substr($message,$j+1,strlen($message)-$j+1);
//echo $message;
break;
}
return $sub1;
//echo addslashes($sub1);
//$sub2=strstr($sub1,'/>',true)."/>";
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
$sql="select id,catname from category";
$result=mysql_query($sql) or die(mysql_error());
while($row_cat = mysql_fetch_array($result))
{
if($out=='catid')
$cat[$row_cat['catname']]=$row_cat['id'];
else
$cat[$row_cat['id']]=$row_cat['catname'];
}
return $cat;
}

function search()
{
echo "<input type=\"text\" name=\"qual\" value=\"\" />";
$qual=isset($_POST['qual'])? $_POST['qual']." and status='draft'":"status='draft'";
if(isset($_POST['qual']))
{
$qual=$_POST['qual'];
while(strpos($qual,'time'))
{$k=strpos($qual,'time');
$j= stripos($qual,'"',$k);
$p= stripos($qual,'"',$k+$j);
$sub=substr($qual,$j+1,$p);
echo $sub;
$qual=str_replace($sub,setmydate($sub),$qual);
echo $qual;
}
}
}

function getPagelink($iteration)
	{
	echo "<ul class=\"pagination\"><li><a href=\"#\">&laquo;</a></li>";
	for($i=1;$i<=$iteration;$i++)
	{ echo "<li><a href=home.php?page=".$_GET['page']."&num=".$i;
	echo ">".$i."</a></li>";
	}$x=$i-1;echo "<li><a href=\"home.php?page=".$_GET['page']."&num=".$x."\">&raquo;</a></li></ul>";
	}
	
function getPagelinkSearch($iteration,$qry)
	{
	echo "<ul class=\"pagination\"><li><a href=\"#\">&laquo;</a></li>";
	for($i=1;$i<=$iteration;$i++)
	{ echo "<li><a href=home.php?page=".$_GET['page']."&num=".$i."&string=".$qry;
	echo ">".$i."</a></li>";
	}$x=$i-1;echo "<li><a href=\"home.php?page=".$_GET['page']."&num=".$x."&string=".$qry."\">&raquo;</a></li></ul>";
	}
	function getPagelinkSearch1($iteration,$qry)
	{
	echo "<ul class=\"pagination\"><li><a href=\"#\">&laquo;</a></li>";
	for($i=1;$i<=$iteration;$i++)
	{ echo "<li><a href=home.php?page=".$_GET['page']."&num=".$i."&qual=".$qry;
	echo ">".$i."</a></li>";
	}$x=$i-1;echo "<li><a href=\"home.php?page=".$_GET['page']."&num=".$x."&qual=".($qry)."\">&raquo;</a></li></ul>";
	}

	function getPagesqlSearch1($sql,$rec_limit,$qry)
	{

		$start=0;

		$result_page=mysql_query($sql);
		$total=mysql_num_rows($result_page);

		if(isset($_GET['num']))
		{$start=(($_GET['num'])-1)*$rec_limit;

		}
		if(ceil($total/$rec_limit)>1)
		getPagelinkSearch1(ceil($total/$rec_limit),$qry);
		$sql=$sql." limit ".$start.", ".$rec_limit;
		return $sql;
	}
function getPagesqlSearch($sql,$rec_limit,$qry)
	{

		$start=0;

		$result_page=mysql_query($sql);
		$total=mysql_num_rows($result_page);

		if(isset($_GET['num']))
		{$start=(($_GET['num'])-1)*$rec_limit;

		}
		if(ceil($total/$rec_limit)>1)
		getPagelinkSearch(ceil($total/$rec_limit),$qry);
		$sql=$sql." limit ".$start.", ".$rec_limit;
		return $sql;
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
//$time=$time+19800;
date_timestamp_set($tdate,$time);
return date_format($tdate, 'd-m-Y H:i:s');
}
function setmydate($time)
{
$ts1 = date_create($time);
return date_format($ts1,'U');
}


function comments($pageid)
{
$db_name="editor";
$tbl1="comments";
if(isset($_POST['comment']) && isset($_POST['com']))
 {
  insert_data('comments');


//echo "<meta http-equiv=\"refresh\" content=\".1\">";

 // header("location: home.php?page=update&id=$pageid");
//echo "<meta http-equiv='refresh' content='0;url=home.php?update&id=$pageid'>";  
//exit();
}
$sql="select id,comment,author,time from $tbl1 where pageid=$pageid";
$time=time();
$i=1;
if($result=mysql_query($sql))
{
while($row=mysql_fetch_array($result))
{

echo "<p>";
echo "<b>".'Comment'.$i."."."</b>";
echo "<br />commneted by :".$row['author'];
echo "<br />at :".getmytime($row['time']);
echo "<br />".$row['comment'];
echo"</p>";
echo "<hr/>";
$i++;
}
}else{
 die(mysql_error());
}
//if($status!='draft')
{
echo "<form role=\"form\" name=\"form1\" id=\"frm1\" action=\"home.php?page=update&id=$pageid\" method=\"POST\">";
echo "<p><h3>Leave a comment</h3><br />";
echo  "<b>Message:</b>&nbsp;<textarea name=\"comment\" rows=\"4\" cols=\"40\"></textarea>";
echo  "<br /><input type=\"hidden\" name=\"author\" value=\"".$_SESSION['SESS_uname']."\">";
echo  "<br /><input type=\"hidden\" name=\"time\" value=\"".time()."\">";
echo  "<br /><input type=\"hidden\" name=\"pageid\" value=\"".$pageid."\">";
echo "<input class=\"btn btn-primary\" name=\"com\" type=\"submit\" value=\"comment\" /></p>";
echo "</form>";
}
}

function edit($pageid,$mod){
$cat=NULL;
$category=NULL;
//include('rowaccess.php');
if (get_magic_quotes_gpc()) $_POST = array_map('stripslashes', $_POST);

$subject = isset($_POST['subject']) ? $_POST['subject'] : 'Subject';
$message = isset($_POST['message']) ? addslashes($_POST['message']) : 'Message';
$title = isset($_POST['title']) ? addslashes($_POST['title']) : 'Title';

$message_filter=cleanup($message);
$message_filter=cleanup($message);
$message_filter=strtolower($cat[$category])." ".$title.$message_filter;


$db_name="editor";
$tbl="pages";



if(isset($_POST['submit']))
{
	$dd=$_POST['id'];
	$cat=category('catid');
	echo $subject;

	$d='draft';
	$time=time();
//echo "Post author is :".$_POST['author1'];
//echo "session name is :".$_SESSION['SESS_uname'];
//echo " authorinsubmit= ".$author;

	if($_POST['author1']==$_SESSION['SESS_uname'])
	{

	$date=time();
	$sql="update $tbl set page='$message',page_filter='$message_filter',time='$date',modified_by='$_SESSION[SESS_uname]'where id='$pageid'";
	//echo $sql;
		if(!$result=mysql_query($sql))
		{ die(mysql_error());
		}//end if
	}else
	{
	$sql="INSERT INTO $tbl (catid,page,page_filter,status,author,time,link,title) VALUES ('$cat[$subject]','$message','$message_filter','$d','$_SESSION[SESS_uname]','$time','$dd','$title') ";
if(!$result=mysql_query($sql))
		{ die(mysql_error());
		}//end if	
}
}
	$sql="select id,title,catid,page,page_filter,status,author,time,modified_by,flag from $tbl where id=$pageid";
	$cat=category();
	if($result=mysql_query($sql))
	{
		while($row=mysql_fetch_array($result))
		{
			if(isset($_POST['edit']))
			{
			echo "author<input type=\"hidden\" name=\"author1\" value=\"".$row['author']."\" />";
			echo "<h2>".strtoupper($cat[$row['catid']])."</h2>";
			echo  '<textarea id="message" name="message" rows="15" cols="80">'.$row['page'].'</textarea>';
			echo  "<input type=\"hidden\" name=\"subject\" value=\"".$cat[$row['catid']]."\" />";
			echo "<input type=\"hidden\" name=\"id\" value=\"".$row['id']."\" />";
			echo "<input type=\"hidden\" name=\"title\" value=\"".$row['title']."\" />";
	
			echo "<input class=\"btn btn-primary\" name=\"submit\" type=\"submit\" value=\"Submit\" />";
			}else
			{
	
			echo "<input type=\"hidden\" name=\"author\" value=\"".$row['author']."\" />";
			echo "<h2>".strtoupper($cat[$row['catid']])."</h2>";
			echo "<b>Title :".$row['title']."</b>";
			echo "<br />";
			echo "<b>Author :".$row['author']."</b>";
			echo "<br /><b>Last Update time :".getmytime($row['time'])."</b>";
			echo "<br /><b>Last Update by :".$row['modified_by']."</b>";
			echo "<br />".$row['page'];
			if(	$_SESSION['SESS_uname']==$row['author'] || $mod==1 || !$row['status']=='draft' || $_SESSION['SESS_perm']=='admin')
			echo "<input class=\"btn btn-primary\" name=\"edit\" type=\"submit\" value=\"Edit\" />";
			echo "<hr/>";
			echo "<p>";
			echo "</p>";

			}
		$flag=$row['flag'];
		return $flag;
		}
	}

}

function show()
{

$db_name="editor";
$tbl="pages";

$pageid=isset($_GET['id'])?$_GET['id'] : 3;
$sql="select id,title,catid,page,page_filter,status,author,time,modified_by from $tbl where id=$pageid";
$cat=category();
if($result=mysql_query($sql))
{
while($row=mysql_fetch_array($result))
{
if(isset($_POST['edit']))
{//echo '<input type="text" name="subject" value="'.$cat[$row['catid']].'" />';
echo "<h2>".strtoupper($cat[$row['catid']])."</h2>";
echo  '<textarea id="message" name="message" rows="15" cols="80">'.$row['page'].'</textarea>';
echo  "subject<input type=\"text\" name=\"subject\" value=\"".$cat[$row['catid']]."\" />";
echo '<input type="hidden" name="id" value="'.$row['id'].'" />';
echo '<input type="text" name="title" value="'.$row['title'].'" />';

echo "<input class=\"btn btn-primary\" name=\"submit\" type=\"submit\" value=\"Submit\" />";
}elseif(!(isset($_POST['edit'])) || isset($_POST['search']))
{


echo "<h2>".strtoupper($cat[$row['catid']])."</h2>";
echo "<b>Title :".$row['title']."</b>";
echo "<br />";
echo "<b>Author :".$row['author']."</b>";
echo "<br /><b>Last Update time :".getmytime($row['time'])."</b>";
echo "<br /><b>Last Update by :".$row['modified_by']."</b>";
echo "<br />".$row['page'];

//if(isset($_SESSION['SESS_uname']=='admin'))
//{
if($row['status']!='draft' || $_SESSION['SESS_uname']=='admin' || $_SESSION['SESS_uname']==$author)
echo "<input class=\"btn btn-primary\" name=\"edit\" type=\"submit\" value=\"Edit\" />";//}
echo "<hr/>";
echo "<p>";
echo "</p>";
//return array($row['id'],$row['catid'],$cat[$row['catid']],$row['page'],$row['title'],$row['status']);
}
}}

}
function enc()
{
$key_value = "123321";
$plain_text = "YqvjywySafVDSDej";
$encrypted_text = mcrypt_ecb(MCRYPT_DES, $key_value, $plain_text, MCRYPT_ENCRYPT);


$decrypted_text = mcrypt_ecb(MCRYPT_DES, $key_value, $encrypted_text, MCRYPT_DECRYPT);
return $decrypted_text;
}

?>
