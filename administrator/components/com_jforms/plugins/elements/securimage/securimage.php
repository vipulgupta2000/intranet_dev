<?php
/**
 * Securimage captcha element plugin
*
* @version		$Id: securimage.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * Securimage captcha element plugin
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
*/
class JFormEPluginSecurimage extends JFormEPlugin{

	function render( $elementData ){
	    
		$p = JArrayHelper::toObject($elementData->parameters);
		
		$error   = isset($elementData->validationError)?$elementData->validationError:'';
	
		$htmlId = $p->hash.'_'.$elementData->id;

		$output  = '';

		$css = $p->css;
		
		$output .= _line('<p style="margin-top:10px;">'.JText::_("Type the characters you see in the picture below").'</p>',2);           
		$output .= _line('<img class="'.$css.'" src="'.JURI::root().'media/com_jforms/plugins/elements/securimage/'.'securimage_show.php?sid='. session_id().'" /><br />',2);       
		$output .= _line('<br /><input name="'.$p->hash.'" class="'.$css.'" />',2);       
		$output .= _line("<div class='error-message' id='{$htmlId}_error'>$error</div>",2	);
		$output .= _line('<div class="clear"></div>',2);

		return $output;

	}
	
	function validate( $elementData, $input ){
		
		$p = JArrayHelper::toObject($elementData->parameters);
		include("securimagelib.php");
		$session =& JFactory::getSession();
		$correctValue = $session->get('securimage_code_value');
		if( strtolower($input) == strtolower($correctValue) ){
			return '';
		} else {
			return JText::_("The text you have entered didn't match the image");	
		}
	}
}