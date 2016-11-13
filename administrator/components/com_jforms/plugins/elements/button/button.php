<?php
/**
* Button Element plugin
*
* @version		$Id: button.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
/**
 * Button Element plugin 
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
 */
class JFormEPluginButton extends JFormEPlugin{
	
	
	function render( $elementData ){

		$p = JArrayHelper::toObject($elementData->parameters);
		$output  = '';
		
		$useCss = $p->useCss;
		$css    = $p->css;
		$style  = $useCss?'':"width:{$p->cw}px;height:{$p->ch}px;";
		$p->label = htmlspecialchars($p->label, ENT_QUOTES);
		
		switch( $p->func ){
			
			case 'Submit':
				$output .= _line("<input name='$p->hash' class='$css' type='submit' value='$p->label' style='$style' />",2);
				break;

			case 'Reset':
				$output .= _line("<input name='$p->hash' class='$css' type='reset' value='$p->label' style='$style' />",2);	
				break;

			case 'Button':
			
				$p->clickTrigger = str_replace("\\\"", "'", $p->clickTrigger);
				$p->clickTrigger = str_replace("\\'", "'", $p->clickTrigger);
				$onClickScript = strlen(trim($p->clickTrigger))?"onclick=\"$p->clickTrigger\"":'';
				$output .= _line("<input name='$p->hash' type='button' class='$css' $onClickScript  value='$p->label' style='$style' />",2);	
				break;
				
		}
		$output .= _line('<div class="clear"></div>',2);

		return $output;

	}
}