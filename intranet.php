<form method="post" action="LoginServlet" id="MyForm" name="MyForm">
		<input type="hidden" name="user" value="<?php echo $_SESSION['SESS_empid']?>">
		<input type="hidden" name="pass" value="<?php echo $_SESSION['pass'] ?>">
		<input	type="hidden" name="role" value=$_SESSION['SESS_perm']>
			<input type="hidden" name="intra" value="yes">
	</form>
<script type="text/javascript">
	function openCMS() {
		/*window.open('<?php echo "http://".$_SERVER['HTTP_HOST'].":8080"; ?>/intranet_cms/LoginServlet');*/
form1.setAttribute("target", "_blank");
		document.forms["form1"].action = '<?php echo "http://".$_SERVER['HTTP_HOST'].":8080"; ?>/intranet_cms/LoginServlet';
		document.forms["form1"].submit();
	}
	function openSyncAWS() {
		form1.setAttribute("target", "_blank");
		document.form1.action = '<?php echo "https://tms.inputzero.com/awssync.php"; ?>';
		document.form1.submit();
	}
	function openSyncCMS() {
		form1.setAttribute("target", "_blank");
//		document.form1.action = '<?php echo "http://intranet.inputzero.com/intranet/home.php?page=awssync.php"; ?>';
		document.forms["form1"].action = '<?php echo "http://".$_SERVER['HTTP_HOST']; ?>/intranet/home.php?page=awssync.php';
		document.form1.submit();
	}
	function openPMS() {
		form1.setAttribute("target", "_blank");
		//document.form1.action = '<?php echo "http://".$_SERVER['HTTP_HOST']; ?>/pms/check_login.php';
		//document.form1.action = '<?php echo "http://"."106.51.121.251"; ?>/sms/';
		//document.form1.action = '<?php echo "http://"."52.88.142.82"; ?>/sms/';
		document.form1.action = '<?php echo "https://sms.inputzero.com"; ?>';
	//	document.form1.action = '<?php echo "http://"."49.204.68.58"; ?>/sms/';
		document.form1.submit();
	}
		function openAMS() {
		form1.setAttribute("target", "_blank");
		document.form1.action = '<?php echo "http://".$_SERVER['HTTP_HOST']; ?>/ams/check_login.php';
		document.form1.submit();
	}
	function openEDS() {
		form1.setAttribute("target", "_blank");
		document.form1.action = '<?php echo "http://".$_SERVER['HTTP_HOST'].":8080"; ?>/eds_test2/getname.jsp';
		document.form1.submit();
	}
	function openTC()
	{
	 window.open('<?php echo "http://".$_SERVER['HTTP_HOST'].":8080"; ?>/tc');
	}
	function openHRA()
	{
	 window.open('<?php echo "http://".$_SERVER['HTTP_HOST'].":8080"; ?>/hra');
	}
	function openPS() {

		document.form1.action = '../ps/PS';
		document.form1.submit();
	}
	function openLMS() {
form1.setAttribute("target", "_blank");
		/*window.open('../lms/check_login.php');*/		
	//	document.form1.action = '../lms/check_login.php';
		//document.form1.action = '<?php echo "http://"."106.51.121.251"; ?>/lms/';
		//document.form1.action = '<?php echo "http://"."52.88.142.82"; ?>/lms/check_login.php';
		document.form1.action = '<?php echo "https://lms.inputzero.com"; ?>/check_login.php';
		//document.form1.action = '<?php echo "http://"."49.204.68.58"; ?>/lms/';
		document.form1.submit();
		

	}
	function openWIKI() {
		document.form1.action = 'home.php?page=intranet.php';
		document.form1.submit();
	}
	function openTMS() {
		/*window.open('../tms/check_login.php');*/


		form1.setAttribute("target", "_blank");
		document.form1.action = '<?php echo "https://tms.inputzero.com"; ?>/check_login.php';
		//document.form1.action = '<?php echo "http://"."52.88.142.82"; ?>/tms/check_login.php';
		//document.form1.action = '../tms/check_login.php';
		document.form1.submit();
	}
	function openARS()
	{
	  window.open('/docs/ARS7.5');
	}
	function openCMDB()
	{
	  window.open('/docs/CMDB');
	}
	function openITSM()
	{
	  window.open('/docs/ITSM%207.6.03');
	}
	function openSLM()
	{
	  window.open('/docs/SLM');
	}
	function openSRM()
	{
	  window.open('/docs/SRM');
	}
	function openGP()
	{
	  window.open('/intranet/home.php?page=update&id=1');
	}
	function openTP()
	{
	  window.open('/intranet/home.php?page=update&id=288');
	}
	function openOCP()
	{
	  window.open('/intranet/home.php?page=update&id=306');
	}
	function openLP()
	{
	  window.open('/intranet/home.php?page=update&id=305');
	}
        function openRP()
	{
	  window.open('/intranet/home.php?page=update&id=152');
	}

</script>

	
<div>
<center><font size="+2" color="blue">Hi, <?php echo $_SESSION['SESS_ename']; ?>	Welcome to Intranet</font></center>
</div>
<div class="row clearfix">
			<div class="col-md-2 col-xs-1 column">
				<div class="panel panel-primary">
				<div class="panel-heading"><center>Main Menu</center></div>
				<div class="panel-body">
					<ul class="list-group">
					<li class="list-group-item"><a href='<?php echo "http://".$_SERVER['HTTP_HOST']; ?>/intranet/changepassword.php'
					title='CLICK TO CHANGE THE PASSWORD'>Change Password </a>
					</li>
					<li class="list-group-item"><a href="#" onclick="openSyncAWS();"
					title='Click to Sync AWS Passwords'>Sync LMS&TMS PassWords</a></li>
					<li class="list-group-item"><a href="#" onclick="openSyncCMS();"
					title='Click to Sync CMS Passwords'>Sync CMS PassWords</a></li>
					</ul>
				</div>
				</div>
				<div class="panel panel-primary">
				<div class="panel-heading"><center>Application Links</center></div>
  				<div class="panel-body">
					
					<ul class="list-group">
					<li class="list-group-item"><a href="#" onclick="openCMS();"
					title='CLICK TO ENTER INTO CMS'>CMS</a></li>
					<li class="list-group-item"><a href="javascript:openPMS()"
					title='CLICK TO ENTER INTO SMS'>SMS</a></li>
					<li class="list-group-item"><a href="javascript:openEDS()"
					title='CLICK TO ENTER INTO EDS'>EDS</a></li>
					<li class="list-group-item"><a href="javascript:openLMS()"
					title='CLICK TO ENTER INTO LMS'>LMS</a></li>
					<li class="list-group-item"><a href="javascript:openTMS()"
					title='CLICK TO ENTER INTO TMS'>TMS</a></li>
					<li class="list-group-item"><a href="javascript:openAMS()"
					title='CLICK TO ENTER INTO TMS'>AMS</a></li>
					</ul></center>
				</div>
				</div>
				<div class="panel panel-primary">
				<div class="panel-heading"><center>Kiosks</center></div>
  				<div class="panel-body">
					<ul  class="list-group">
					<li class="list-group-item"><a href="javascript:openTC()"
					title='CLICK TO OPEN TAX CALCULATOR'>Tax Calculator</a></li>
					<li class="list-group-item"><a href="javascript:openHRA()"
					title='CLICK TO OPEN HRA PLANNER'>HRA Planner</a></li>
					<li class="list-group-item"><a href="javascript:openPS()"
					title='CLICK TO ENTER INTO PACKAGE SELECTOR'>Package Selector</a></li>
					</ul>
				</div>
				</div>
		</div>
		<div class="col-md-8 col-xs-3 column">
		<div class="panel panel-primary">
				<div class="panel-heading"><center>Latest Articles</center></div>
  				<div class="panel-body">
				
			
						
				<?php 
					$a="<div id=\"carousel-example-generic\" class=\"carousel slide\" data-ride=\"carousel\"><ol class=\"carousel-indicators\">";
					$b="<div class=\"carousel-inner\">";
					$j=0;
					$tbl="pages";
					$sql="select id,title,page,page_filter,status,catid from $tbl where catid in ('3','7','8') and status='published' ORDER BY time DESC LIMIT 10";
					//$sql="select id,title,page,page_filter,status,catid from $tbl where status='published' ORDER BY time DESC LIMIT 10";
					if($result=mysql_query($sql))
					{
					while($row=mysql_fetch_array($result))
					{
					
					
					
					if($j==0)
					{$b=$b."<div class=\"item active\"><div class=\"row\">
								<div class=\"col-md-3\">";
					$a=$a."<li data-target=\"#carousel-example-generic\" data-slide-to=\"".$j."\" class=\"active\"></li>";
					}else
					{
					if (($j % 2) == 0)
					{$b=$b."<div class=\"item\"><div class=\"row\">";
					}	
					$b=$b."<div class=\"col-md-3\">";
					$a=$a."<li data-target=\"#carousel-example-generic\" data-slide-to=\"".$j."\" class=\"active\"></li>";
					}
					
					$b=$b.extractimage($row['page']);
					//$b=$b."<div class=\"carousel-caption\">";
					$b=$b."</div><div class=\"col-md-3\">";
					$title=strtoupper($row['title']);
					$b=$b."<b><a href=\"home.php?page=update&id=".$row['id']."\">".$title."</a></b>";
					$string=substr($row['page_filter'],0,100);
					$b=$b."<br /><p>".$string;
					$j++;
					
					$b=$b."</p></div>";
					if (($j % 2) == 0)
					{
					$b=$b."</div></div>";
					}
					}
					}else{
					die(mysql_error());
					}
$a=$a."</ol>";		
		$b=$b."</div><a class=\"left carousel-control\" href=\"#carousel-example-generic\" role=\"button\" data-slide=\"prev\">
    <span class=\"glyphicon glyphicon-chevron-left\"></span>
  </a>
  <a class=\"right carousel-control\" href=\"#carousel-example-generic\" role=\"button\" data-slide=\"next\">
    <span class=\"glyphicon glyphicon-chevron-right icon-magenta\"></span>
  </a>";
echo $a.$b;				
				?>
				</div>	
				
				
				</div></div>
<script type="text/javascript">
				for(i=1;i<11;i++)
				{document.images[i].style.height="150px";
				
				document.images[i].style.width="150px";}
				//document.getElementById(p1).style.display=none		
</script>
		<?php include("text.php");?>
		</div>
		<div class="col-md-2 col-xs-1 column">
				<div class="panel panel-primary">
				<div class="panel-heading"><center>Training Docs</center></div>
  				<div class="panel-body">
					<ul class="list-group">
					<li class="list-group-item"><a href="javascript:openARS()"
					title='CLICK TO DOWNLOAD ARS TRAINING DOCUMENTS'>ARS</a>
					</li>
					<li class="list-group-item"><a href="javascript:openCMDB()"
					title='CLICK TO DOWNLOAD CMDB TRAINING DOCUMENTS'>CMDB</a>
					</li>
					<li class="list-group-item"><a href="javascript:openITSM()"
					title='CLICK TO DOWNLOAD ITSM TRAINING DOCUMENTS'>ITSM</a>
					</li>
					<li class="list-group-item"><a href="javascript:openSLM()"
					title='CLICK TO DOWNLOAD SLM TRAINING DOCUMENTS'>SLM</a>
					</li>
					<li class="list-group-item"><a href="javascript:openSRM()"
					title='CLICK TO DOWNLOAD SRM TRAINING DOCUMENTS'>SRM</a>
					</li>
					</ul>
				</div>
				</div>
				<div class="panel panel-primary">
				<div class="panel-heading"><center>Policies</center></div>
  				<div class="panel-body">
					<ul class="list-group">
					<li class="list-group-item"><a href="javascript:openGP()"
					title='CLICK TO DOWNLOAD THE GENERAL POLICY DOCUMENTS'>General Policy</a>
					</li>
					<li class="list-group-item"><a href="javascript:openTP()"
					title='CLICK TO DOWNLOAD THE Domestic Travel Policy DOCUMENTS'>Domestic Travel Policy</a>
					</li>
					<li class="list-group-item"><a href="javascript:openOCP()"
					title='CLICK TO DOWNLOAD THE ON CALL POLICY DOCUMENTS'>Oncall and Night Shift Policy</a>
					</li>
					<li class="list-group-item"><a href="javascript:openLP()"
					title='CLICK TO DOWNLOAD THE leave Policy DOCUMENTS'>leave Policy</a>
					</li>
                                        <li class="list-group-item"><a href="javascript:openRP()"
					title='CLICK TO DOWNLOAD THE Referral Policy DOCUMENTS'>Referral Policy</a>
					</li>
					</ul>
				</div>
				</div>
				</div>
</div>

<form method="post" action="LoginServlet" name="MyForm">
		<input type="hidden" name="user" value="<?php echo $_SESSION['SESS_empid']?>">
		<input type="hidden" name="pass" value="<?php echo $_SESSION['pass'] ?>">
		<input			type="hidden" name="role" value=$_SESSION['SESS_perm']>
			<input type="hidden" name="intra" value="yes">
	</form>
