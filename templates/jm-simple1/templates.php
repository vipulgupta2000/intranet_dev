<?if( $sg == 'banner' ):?>
<div style="width:137px;text-align:center;margin:0 auto;">
<br />
<table style="width:137px;text-align:center;" cellpadding="0" cellspacing="0">
	<tr>
		<td><font class="sgf1">Designed by:</font></td>
	</tr>
</table>
<table style="width:137px;height:16px;text-align:center;border:none;" cellpadding="0" cellspacing="0">
	<tr>
		
		<td width="2" height="16"></td>
		<td style="background:url(templates/<?php echo $this->template ?>/images/banner_02.jpg);width:107px;height:16px;text-align:center;">
			<a href="http://www.jm-experts.com/templates.html" style="font-size: 10px; font-family: Verdana,Arial,Helvetica,sans-serif; color: #333333;text-decoration:none;">Joomla Templates</a>
		</td>
	</tr>
</table>
</div>  
<?else:?>
<?php echo $mainframe->getCfg('sitename') ;?>, Powered by <a href="http://joomla.org/" class="sgfooter" target="_blank">Joomla!</a>; <a href="http://www.jm-experts.com/templates.html" target="_blank" class="sgfooter">Joomla templates</a> <a href="http://www.jm-experts.com/" target="_blank" class="sgfooter">Joomla Professional Services</a>
<?endif;?> 