<?php
/**
* E-mail Element plugin
*
* @version		$Id: email.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
/**
 * E-mail Element plugin 
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
 */

jimport('joomla.filesystem.file');

class JFormEPluginEmail extends JFormEPlugin{



	function render( $elementData ){
	
		$p = JArrayHelper::toObject($elementData->parameters);
		$htmlId = $p->hash.'_'.$elementData->id;
		if( !$p->uinput ){
			return _line("<input value='' name='$p->hash' id='$htmlId' type='hidden' />",2);
		}
			
		
		$default = property_exists($elementData,'defaultValue' )?$elementData->defaultValue:$p->defaultValue;
		$error   = property_exists($elementData,'validationError' )?$elementData->validationError:'';
		
		$css = $p->css;
		$inputClass = $css . (empty( $error )?'':' input-error');
		$labelClass = $css . (empty( $error )?'':' label-error');
		$inputStyle = $p->useCss?'':"width:{$p->cw}px;height:{$p->ch}px;";
		$labelStyle = $p->useCss?'':"width:{$p->lw}px;height:{$p->lh}px;";
		
		$p->label = htmlspecialchars($p->label, ENT_QUOTES);
		if( $p->required ) {
			$p->label = $p->label . '<span class="required" style="color:red"> * </span>';
		}

		$output  = '';
	
		$output .= _line("<div class='error-message' id='{$htmlId}_error'>$error</div>",2	);
		$output .= _line("<label class='$labelClass' id='{$htmlId}_label' for='$htmlId' style='$labelStyle'>$p->label</label> ",2);
		$output .= _line("<input type='text' class='$inputClass' value='$default' name='$p->hash' id='$htmlId' style='$inputStyle' />",2);
		$output .= _line('<div class="clear"></div>',2);
		

		return $output;
		
	}
			 
	function jsValidation( $elementData ){
	
		$p = JArrayHelper::toObject($elementData->parameters);
		if( !$p->uinput )return '';
		
		$htmlId = $p->hash.'_'.$elementData->id;
		$css = $p->css;
		
		$validationRule = '^[-.\w]+\@[-.\w]+$';
		
		$p->required = $p->required?'true':'false';
	
		$output  = "\n";
		$output .= _line("var $p->hash = document.getElementById('$htmlId');" ,2);
		$output .= _line("var {$p->hash}_label = document.getElementById('{$htmlId}_label');" ,2);
		$output .= _line("var {$p->hash}_error = document.getElementById('{$htmlId}_error');" ,2);
		$output .= _line("if($p->hash.value.length == 0){" ,2);
		$output .= _line("var required = $p->required;" ,3);
		$output .= _line("if(required){" ,3);
		$output .= _line("errorArray.push({id:$p->hash,msg:'error'});" ,4);
		$output .= _line("{$p->hash}.className ='input-error $css';",4);
		$output .= _line("{$p->hash}_label.className ='label-error $css';",4);
		$output .= _line("{$p->hash}_error.innerHTML='".JText::_('Field Required')."';",4);	
		
		$output .= _line("}" ,3);
		$output .= _line("} else {" ,2);
		$output .= _line("var regEx = /$validationRule/" ,3);
		if( $p->minput ){
			$output .= _line("var emailArray = $p->hash.value.split(',');" ,3);
			$output .= _line("for(i=0;i<emailArray.length;i++){" ,3);
			$output .= _line("emailArray[i] = emailArray[i].replace(/^\s+|\s+$/g, '');" ,3);
			$output .= _line("if(!regEx.test(emailArray[i])){ " ,4);
			$output .= _line("errorArray.push({id:$p->hash,msg:'error'})" ,5);
			$output .= _line("{$p->hash}.className ='input-error $css';",5);
			$output .= _line("{$p->hash}_label.className ='label-error $css';",5);
			$output .= _line("{$p->hash}_error.innerHTML='".JText::_('Invalid format')."';",5);	
			$output .= _line("break;" ,5);
			$output .= _line("}" ,4);
			$output .= _line("}" ,3);
		} else {
			$output .= _line("$p->hash.value = $p->hash.value.replace(/^\s+|\s+$/g, '');" ,3);
			$output .= _line("if(!regEx.test($p->hash.value)){ " ,3);
			$output .= _line("errorArray.push({id:$p->hash,msg:'error'})" ,4);
			$output .= _line("{$p->hash}.className ='input-error $css';",4);
			$output .= _line("{$p->hash}_label.className ='label-error $css';",4);
			$output .= _line("{$p->hash}_error.innerHTML='".JText::_('Invalid format')."';",4);	
			$output .= _line("}" ,3);
		}
		$output .= _line("}" ,2);
		return $output;
	
	
	}
	function jsClearErrors( $elementData ){

		$p = JArrayHelper::toObject($elementData->parameters);
		if( !$p->uinput )return '';

		$output  = "";

		$htmlId = $p->hash.'_'.$elementData->id;
		$css = $p->css;
		
		$output .= _line("var $p->hash = document.getElementById('$htmlId');" ,2);
		$output .= _line("var {$p->hash}_error = document.getElementById('{$htmlId}_error');" ,2);
		$output .= _line("var {$p->hash}_label = document.getElementById('{$htmlId}_label');" ,2);
		
		$output .= _line("{$p->hash}.className = '$css';",2);
		$output .= _line("{$p->hash}_label.className = '$css';",2);
		$output .= _line("{$p->hash}_error.innerHTML = '';",2);	
		
		return $output;
	}
	
	function validate( $elementData, $input ){
		
		$p = JArrayHelper::toObject($elementData->parameters);
		if( !$p->uinput )return '';

		
		$validationRule = '^[-.\w]+\@[-.\w]+$';
	
		if( !strlen( trim( $input ))) {
			if( $p->required ){
				return JText::_("Field Required");
			}
		} else {
			if( $p->minput ){
				$emailArray = explode(',',$input);
				foreach($emailArray as $email){
					if(!preg_match('/'.$validationRule.'/',trim($email) )){
						return JText::_("Invalid format");
					}
				}
			} else {
				if(!preg_match('/'.$validationRule.'/',trim($input) )){
					return JText::_("Invalid format");
				}
			}
		}
		return '';
	}
}