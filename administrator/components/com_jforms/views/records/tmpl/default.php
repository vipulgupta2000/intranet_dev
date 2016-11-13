<?php
/**
* Default layout for records view
*
* @version		$Id: default.php 371 2010-03-27 02:29:54Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Layouts
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
JHTML::_('JForms.Records.javascript' , $this->form);  ?>

<table width='100%'>
<tr>
	<td valign='top' width='300'>
		
		<div id='btn-filters' class='tab-button active-tab-button' onclick='javascript:enableTab("filters");'><?php echo JText::_('Filters')?></div>
		<div id='btn-export' class='tab-button' onclick='javascript:enableTab("export");'><?php echo JText::_('Export')?></div>
	
		<div class='tab-body' style='display:block' id='tab-filters'>
		
				<form action='<?php echo JRoute::_("index.php");?>' id='filter_form' name='filter_form' method='post' onsubmit="return onValidateFilter(this);">
					<input value='' id='headers_filter' type='hidden' name='headers_filter'  />
			
					<h2><?php echo JText::_('Fields') ?></h2>
					<?php JHTML::_('JForms.Records.elementForms', $this->form ); ?>
				</form>			
				<hr />
		
				<h2><?php echo JText::_('Database fields') ?></h2>
				<?php JHTML::_('JForms.Records.mapping', $this->form ); ?>
		
				<hr />
	
				<?php JHTML::_('JForms.Records.controls' ); ?>
		

		</div>
		
		<div class='tab-body' style='display:none' id='tab-export'>
			<?php JHTML::_('JForms.Records.exportForms', $this->form ); ?>
		</div>
		
	</td>
	
	<td id='grid_container' valign='top'><div style='text-align:center;width:100%' id='loadingDiv'><?php echo JText::_('Processing...'); ?><br /><div class='processing'>&nbsp;</div></div></td>

</tr>
</table>
<?php JHTML::_('JForms.General.version'); ?>