<?php
	defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

	$option = $processPage->getVar('option');
	$templateList = $processPage->getVar('templateList');	
?>

<?php JHTML::script('common.js', 'administrator/components/' . $option . '/js/', false); ?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" width="20"><?php echo JText::_( 'Num' ); ?></th>
			<th class="title" width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($templateList); ?>);"/></th>
			<th class="title"><?php echo JHTML::_('grid.sort', AriQuizWebHelper::getResValue('Label.Name'), 'TemplateName', AriQuizHelper::getSortDirection('TemplateName'), AriQuizHelper::getSortField('TemplateName'), 'texttemplate_list'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	if (!empty($templateList))
	{
		$i = 0;
		foreach ($templateList as $template)
		{
	?>
		<tr>
			<td align="center"><?php echo ($i + 1); ?></td>
			<td align="center"><?php echo JHTML::_('grid.id', $i, $template->TemplateId, false, 'templateId'); ?></td>
			<td align="left"><a href="index.php?option=<?php echo $option; ?>&hidemainmenu=1&task=texttemplate_add&templateId=<?php echo $template->TemplateId ?>"><?php AriQuizWebHelper::displayDbValue($template->TemplateName); ?></a></td>
		</tr>
	<?php
			++$i;
		}
	}
	?>
	</tbody>
</table>
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="task" value="gtemplate_list" />
<input type="hidden" name="boxchecked" value="0" />
</form>