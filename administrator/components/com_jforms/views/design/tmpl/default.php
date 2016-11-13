<?php
/** 
* Default layout for design view
*
* @version		$Id: default.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Layouts
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

$form = $this->form?$this->form:null;

JHTML::_('JForms.Design.javascript', $form?$form->fields:null);

?>
	<div id='side-bar'>
		<div id='btn-toolbar' class='tab-button active-tab-button' onclick='javascript:enableTab("toolbar");'><?php echo JText::_('Toolbar')?></div>
		<div id='btn-form'    class='tab-button' onclick='javascript:enableTab("form");'><?php echo JText::_('Form...')?></div>
		<div id='btn-element' class='tab-button' onclick='javascript:enableTab("element");'><?php echo JText::_('Element...')?></div>
		<br clear='all' />
	
		<div class='tab-body' style='display:block' id='tab-toolbar'>
			<div id='tab-toolbar-container'>
				<?php JHTML::_('JForms.Design.toolbar'); ?>
			</div>
		</div>
	
		<div class='tab-body' style='display:none' id='tab-form'>
			<div id='tab-form-container'>
				<form action="index.php" method="post" name="adminForm" id="adminForm">
					<input type="hidden" name="params[id]" value='<?php echo $form?$form->id:'' ?>' />
					<input type="hidden" name="params[fieldInformation]" id="fieldInformation" value="" />
					<input type="hidden" name="params[paramIds]" id="paramIds" value="" />
					<input type="hidden" name="option" value="com_jforms" />
					<input type="hidden" name="task" value="" />
					<?php echo JHTML::_( 'form.token' )?> 
					<?php JHTML::_('JForms.Design.generalForm'    , $form); ?>
					<?php JHTML::_('JForms.Design.redirectionForm', $form); ?>
					<?php JHTML::_('JForms.Design.pluginForms'    , $form);?>
				</form>
			</div>
		</div>

		<div class='tab-body' style='display:none' id='tab-element'>
			<div id='tab-element-container'>
				<h3><?php echo JText::_('Element Properties'); ?><div id="lock-toggle" class='unlocked' onclick="toggleElementPaneScroll(this)"></div></h3>
				<br clear='all' />
				<?php JHTML::_('JForms.Design.properties'); ?>
			</div>
		</div>
	
		
	</div>
	<div id='workarea-td'>
		<input onclick='alignLabels()' type='button' value='<?php echo JText::_('Align Labels') ?>' style='float:left;margin-bottom:10px;font-size:12px' />
		<input onclick='alignControls()' type='button' value='<?php echo JText::_('Align Controls') ?>' style='float:left;margin-bottom:10px;font-size:12px' />
		<input onclick='hideAllErrors()' type='button' value='<?php echo JText::_('Hide errors') ?>' style='float:left;margin-bottom:10px;font-size:12px' />
		<br clear='all' />
		<div id='workarea' class='workarea'>
			<ul id='clist'></ul>
		</div>
	</div>
<?php JHTML::_('JForms.General.version'); ?>