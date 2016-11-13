<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>
<jdoc:include type="head" />
<link rel="stylesheet" href="templates/system/css/system.css" type="text/css" />
<link rel="stylesheet" href="templates/<?php echo $this->template ?>/css/template.css" type="text/css" />
<!--[if lte IE 6]>
<link rel="stylesheet" href="templates/<?php echo $this->template ?>/css/ie7.css" type="text/css" />
<![endif]-->
</head>
<body class="body_bg">
<div id="logo">
<jdoc:include type="modules" name="topleft" />
</div>

<div id="top1">
<jdoc:include type="modules" name="top1" />
</div>

<div id="search">
<jdoc:include type="modules" name="user4" />
</div>
<div class="clr"></div>


	<div id="page_bg">
	<div id="page_bg_1">
		<div id="topw">
        		<div class="pill_m">
				<div id="pillmenu">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td style="text-align: left; vertical-align: middle;">

								<jdoc:include type="modules" name="user3" />

								<div class="clr"></div>

							</td>
						</tr>
					</table>

				</div>
			</div>
	</div>

	<div class="clr"></div>

	<object classid="clsid:D27CDB6E-AE6D-11CF-96B8-444553540000" id="obj1" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" border="0" width="950" height="400">
	<param name="movie" value="templates/wm_06_j15/intro.swf">
	<param name="quality" value="High">
	<param name="wmode" value="transparent">
	<embed src="templates/wm_06_j15/intro.swf" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" name="obj1" width="950" height="400" quality="High" wmode="transparent"></object>


		<div style="height:10px;">&nbsp;</div>

		<div class="center">

			<div id="wrapper">

				<div id="content">

					<?php if($this->countModules('left') and JRequest::getCmd('layout') != 'form') : ?>

						<div id="leftcolumn">	

							<jdoc:include type="modules" name="left" style="rounded" />

						</div>
						<?php endif; ?>

						<?php if($this->countModules('right') and JRequest::getCmd('layout') != 'form') : ?>

						<div id="maincontent">

							<div class="m1">

						<?php else: ?>

						<div id="maincontent_full">

							<div class="m1_f">

						<?php endif; ?>

								<div class="nopad">			

									<jdoc:include type="message" />

									<?php if($this->params->get('showComponent')) : ?>

										<jdoc:include type="component" />

									<?php endif; ?>

								</div>
							</div>

						</div>

						<?php if($this->countModules('right') and JRequest::getCmd('layout') != 'form') : ?>

						<div id="rightcolumn" style="float:right;">

						<jdoc:include type="modules" name="right" style="rounded" />								

					</div>

					<?php endif; ?>

					<div class="clr"><?php if($this->countModules('footer')) : ?><div id="footer">

<jdoc:include type="modules" name="footer" style="none" /></div><?php endif; ?><div style="padding-bottom:15px;">

<a href="http://sitiweb.valloshow.it" target="_blank">Free Joomla Template</a></div></div>

				</div>		

			</div>

		</div>

</div>	

	</div>	

	<jdoc:include type="modules" name="debug" />


</body>
</html>