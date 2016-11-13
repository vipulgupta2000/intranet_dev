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
	function openPMS() {
		form1.setAttribute("target", "_blank");
		document.form1.action = '<?php echo "http://".$_SERVER['HTTP_HOST'].":8080"; ?>/pms/test.php';
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
		document.form1.action = '../lms/check_login.php';
		document.form1.submit();
		

	}
	function openWIKI() {
		document.form1.action = 'home.php?page=intranet.php';
		document.form1.submit();
	}
	function openTMS() {
		/*window.open('../tms/check_login.php');*/


		form1.setAttribute("target", "_blank");
		document.form1.action = '../tms/check_login.php';
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
	  window.open('/policies/General_Policy_v2.0.pdf');
	}
	function openLP()
	{
	  window.open('/policies/Leave_Policy_v2.0.pdf');
	}
	function openOCP()
	{
	  window.open('/policies/OnCall_Policy_v1.0.pdf');
	}
	function openHP()
	{
	  window.open('/policies/Holiday_list_2014_v0.1.pdf');
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
					title='CLICK TO ENTER INTO PMS'>PMS</a></li>
					<li class="list-group-item"><a href="javascript:openEDS()"
					title='CLICK TO ENTER INTO EDS'>EDS</a></li>
					<li class="list-group-item"><a href="javascript:openLMS()"
					title='CLICK TO ENTER INTO LMS'>LMS</a></li>
					<li class="list-group-item"><a href="javascript:openTMS()"
					title='CLICK TO ENTER INTO TMS'>TMS</a></li>
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
				<div class="panel-heading"><center>Announcements</center></div>
  				<div class="panel-body">
				<marquee direction="up" scrolldelay="200" onmouseover="javascript:this.setAttribute('scrollamount','0');" onmouseout="javascript:this.setAttribute('scrollamount','5');">
					<?php 
					$tbl="pages";
					$sql="select id,title,page_filter,status,catid from $tbl where catid='3' and status='published' ORDER BY time DESC LIMIT 5";
					if($result=mysql_query($sql))
					{
					while($row=mysql_fetch_array($result))
					{
					echo "<p align=\"justify\">";
					$title=strtoupper($row['title']);
					echo "<b><a href=\"home.php?page=update&id=".$row['id']."\">".$title."</a></b>";
					$string=substr($row['page_filter'],0,200);
					echo "<br />".$string;
					echo"</p>";
					}
					}else{
					die(mysql_error());
					}
					?></marquee>
				</div>
				</div>
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
					<li class="list-group-item"><a href="javascript:openLP()"
					title='CLICK TO DOWNLOAD THE LEAVE POLICY DOCUMENTS'>Leave Policy</a>
					</li>
					<li class="list-group-item"><a href="javascript:openOCP()"
					title='CLICK TO DOWNLOAD THE ON CALL POLICY DOCUMENTS'>On Call Policy</a>
					</li>
					<li class="list-group-item"><a href="javascript:openHP()"
					title='CLICK TO DOWNLOAD THE HOLIDAY POLICY DOCUMENTS'>Holiday Policy</a>
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
