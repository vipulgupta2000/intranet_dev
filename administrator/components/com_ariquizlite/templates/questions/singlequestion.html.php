<?php
	defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');
	
	AriKernel::import('Web.JSON.JSONHelper');
?>

<?php JHTML::script('multiplierControls.js', 'administrator/components/' . $option . '/js/', false); ?>
<table id="tblQueContainer" style="width: 100%;" cellpadding="0" cellspacing="0">
	<tr>
		<th style="width: 1%; text-align: center;"><a href="javascript:void(0);" onclick="aris.controls.multiplierControls.addItem('tblQueContainer'); aris_updateHidCorrect();return false;">+</a></th>
		<th style="width: 1%; text-align: center;"><?php AriQuizWebHelper::displayResValue('Label.Correct'); ?></th>
		<th style="text-align: center;"><?php AriQuizWebHelper::displayResValue('Label.Answer'); ?></th>
		<th style="width: 5%; text-align: center;"><?php AriQuizWebHelper::displayResValue('Label.Actions'); ?></th>
	</tr>
	<tr id="trQueTemplate">
		<td>&nbsp;</td>
		<td style="text-align: center;"><input type="radio" onclick="aris_updateHidCorrect()" name="rbCorrect" id="rbCorrect" value="true" /></td>
		<td>
			<input type="text" id="tbxAnswer" name="tbxAnswer" class="text_area" style="width: 99%;" />
			<input type="hidden" id="hidQueId" name="hidQueId" />
			<input type="hidden" id="hidCorrect" name="hidCorrect" />
		</td>
		<td style="text-align: center; white-space: nowrap;">
			<a href="javascript:void(0);" onclick="if (confirm('<?php AriQuizWebHelper::displayResValue('Warning.QuestionAnswerRemove'); ?>')) aris.controls.multiplierControls.removeItem(aris.controls.multiplierControls.getCurrentTemplateItemId(this, 'trQueTemplate')); return false;"><?php echo JHTML::image('administrator/components/com_ariquizlite/images/publish_x.png', 'Remove', array('border' => 0)); ?></a>
			<a href="javascript:void(0);" onclick="aris.controls.multiplierControls.moveUpItem(this, 'trQueTemplate'); return false;"><?php echo JHTML::image('administrator/components/com_ariquizlite/images/uparrow.png', 'Up', array('border' => 0)); ?></a>
			<a href="javascript:void(0);" onclick="aris.controls.multiplierControls.moveDownItem(this, 'trQueTemplate', 'tblQueContainer'); return false;"><?php echo JHTML::image('administrator/components/com_ariquizlite/images/downarrow.png', 'Down', array('border' => 0)); ?></a>
		</td>
	</tr>
	<tr>
		<td colspan="4" align="left"><?php AriQuizWebHelper::displayResValue('Text.EmptyAnswerIgnored'); ?></td>
	</tr>
</table>
<script type="text/javascript">
	aris.controls.multiplierControls.init('trQueTemplate', 'tblQueContainer', 3, <?php echo AriJSONHelper::encode($specificQuestion->getDataFromXml($questionData, false)); ?>,
		function()
		{
			var hidCorrectList = aris.controls.multiplierControls.getContainerElements('tblQueContainer', 'hidCorrect');
			for (var i = 0; i < hidCorrectList.length; i++)
			{
				if (hidCorrectList[i].value == 'true')
				{
					var selRbCorrect = aris.controls.multiplierControls.getTemplateElement(hidCorrectList[i], 'trQueTemplate', 'rbCorrect');
					selRbCorrect.defaultChecked = true;
					selRbCorrect.checked = true;
					break;
				}
			};
			
			aris_updateHidCorrect();
		});
		
	function aris_updateHidCorrect()
	{
		var rbCorrectList = aris.controls.multiplierControls.getContainerElements('tblQueContainer', 'rbCorrect');
		if (rbCorrectList)
		{
			for (var i = 0; i < rbCorrectList.length; i++)
			{
				rbCorrectList[i].onclick = aris_setCorrect;
				rbCorrectList[i].onchange = aris_setCorrect;
			}
		}
	}
	
	function aris_setCorrect(e)
	{
		e = e || event;
		var ctrl = e.srcElement || e.target;
	
		var hidCorrectList = aris.controls.multiplierControls.getContainerElements('tblQueContainer', 'hidCorrect');
		for (var i = 0; i < hidCorrectList.length; i++)
		{
			hidCorrectList[i].value = '';
		}
			
		var curHidCorrect = aris.controls.multiplierControls.getTemplateElement(ctrl, 'trQueTemplate', 'hidCorrect');
		if (curHidCorrect)
		{
			curHidCorrect.defaultValue = 'true';
			curHidCorrect.value = 'true';
		}
	}
	
	aris.validators.validatorManager.addValidator(
		new aris.validators.customValidator(null,
			function(val)
			{
				var isValid = true;
				var isSetCorrect = false;
				var isNotEmpty = false;

				var templates = aris.DOM.getChildElementsByAttribute('tblQueContainer', aris.controls.multiplierControls.originalIdAttr, 'trQueTemplate');
				var templateCnt = templates ? templates.length : 0; 
				if (templateCnt > 0)
				{
					for (var i = 0; i < templateCnt; i++)
					{
						var template = templates[i];
						var rbCorrect = aris.DOM.getChildElementByAttribute(template, aris.controls.multiplierControls.originalIdAttr, 'rbCorrect');
						if (rbCorrect && rbCorrect.checked)
						{
							isSetCorrect = true;						
							var tbxAnswer = aris.DOM.getChildElementByAttribute(template, aris.controls.multiplierControls.originalIdAttr, 'tbxAnswer');
							var value = tbxAnswer.value;
							if (value && value.replace(/^\s+|\s+$/g, '').length > 0)
							{
								isNotEmpty = true;
								break;
							}
						}
					}
					
					if (!isSetCorrect)
					{
						this.errorMessage = aris.element.getNormalizeValue('<?php AriQuizWebHelper::displayResValue('Validator.QuestionNotCorrect'); ?>');
						isValid = false;
					}
					else if (!isNotEmpty)
					{
						this.errorMessage = aris.element.getNormalizeValue('<?php AriQuizWebHelper::displayResValue('Validator.QuestionNotAnswer'); ?>');
						isValid = false;
					}
				}
				else
				{
					this.errorMessage = aris.element.getNormalizeValue('<?php AriQuizWebHelper::displayResValue('Validator.QuestionNotAnswer'); ?>');
					isValid = false;
				}
				
				return isValid;
			},
			{emptyValidate : true, errorMessage : '<?php AriQuizWebHelper::displayResValue('Validator.QuestionNotAnswer'); ?>'}));
</script>