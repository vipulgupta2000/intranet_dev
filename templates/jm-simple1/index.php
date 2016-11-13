<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
JPlugin::loadLanguage( 'tpl_SG1' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>
<jdoc:include type="head" />
<link rel="stylesheet" href="templates/system/css/system.css" type="text/css" />
<link rel="stylesheet" href="templates/<?php echo $this->template ?>/css/template.css" type="text/css" />
</head>
<body id="page_bg">
	<div id="top_bg">
		<div id="footer_bg">
			<div id="wrapper">
				<div id="header">
					<div id="top">
						<div id="search">
							<jdoc:include type="modules" name="user4" />
						</div>
						<div id="pillmenu">
							<jdoc:include type="modules" name="user3" />
						</div>
						<div class="clr"></div>
					</div>					
					<div id="logo"><h1><a href="index.php"><?php echo $mainframe->getCfg('sitename') ;?></a></h1></div>
				</div>
				
				<div id="content">				
					<?php if($this->countModules('left') and JRequest::getCmd('layout') != 'form') : ?>
					<div id="leftcolumn">	
						<jdoc:include type="modules" name="left" style="rounded" />
						<?php $sg = 'banner'; include "templates.php"; ?>
					</div>
					<?php endif; ?>	
					
					<?php if($this->countModules('left') and $this->countModules('right') and JRequest::getCmd('layout') != 'form') : ?>
					<div id="maincolumn">
					<?php elseif($this->countModules('left') and !$this->countModules('right') and JRequest::getCmd('layout') != 'form') : ?>
					<div id="maincolumn_left">
					<?php elseif(!$this->countModules('left') and $this->countModules('right') and JRequest::getCmd('layout') != 'form') : ?>
					<div id="maincolumn_right">
					<?php else: ?>
					<div id="maincolumn_full">
					<?php endif; ?>
						<div id="flashnews">
							<div id="flashnews_l">
								<jdoc:include type="modules" name="top" style="rounded" />
							</div>
						</div>
						<div class="nopad">				
							<jdoc:include type="message" />
							<?php if($this->params->get('showComponent')) : ?>
								<jdoc:include type="component" />
							<?php endif; ?>
						</div>
					</div>
					
					<?php if($this->countModules('right') and JRequest::getCmd('layout') != 'form') : ?>
					<div id="rightcolumn">
						<jdoc:include type="modules" name="right" style="rounded" />								
					</div>
					<?php endif; ?>
					<div class="clr"></div>
					
					<div id="footer">
					<jdoc:include type="modules" name="debug" />
						<div id="sgf">
							<? $sg = ''; include "templates.php"; ?>
						</div>
						<p style="text-align: center;">
							<a href="http://validator.w3.org/check/referer">valid xhtml</a>
							<a href="http://jigsaw.w3.org/css-validator/check/referer">valid css</a>
						</p>
					</div>	
				</div>
			</div>
		</div>
	</div><script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-6791269-10");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
</html>