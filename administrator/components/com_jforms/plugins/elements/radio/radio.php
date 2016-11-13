<?php
/**
* Radio group element plugin
*
* @version		$Id: radio.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * Radio group element plugin
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
*/
class JFormEPluginRadio extends JFormEPlugin{
	
	function getSQL( $elementData, $criteria ){
		
		$db =& JFactory::getDBO();
		$f = $elementData->parameters['hash'];
		
		$mode = $criteria->mode=='or'?' OR ':' AND ';
		$keywordJoin = '';
		$kArray = null;
		switch($criteria->keyword_mode){
		
			case 'All':
				$keywordJoin = ' AND ';
				$kArray = explode(' ', $criteria->keywords );
			break;
			
			case 'Any':
				$keywordJoin = ' OR ';
				$kArray = explode(' ', $criteria->keywords );
			break;
			
			case 'Exact':
				$keywordJoin = ' ';
				$kArray = array( $criteria->keywords );
			break;
				
		}
		
		
		$fragments = array();
		foreach( $kArray as $k ){
			$k = $db->getEscaped( "$k", true );
			if( $k == '')continue;
			$fragments[] = '`'.$f.'` LIKE '."'%".$k."%'";
		}
		$sql = implode( $keywordJoin,  $fragments );
		if( trim( $sql ) != '' ){
			$sql = "($sql) $mode";
		}
		return $sql;
	}
	
	function render( $elementData ){
	
		$p = JArrayHelper::toObject($elementData->parameters);

		$error   = property_exists($elementData,'validationError' )?$elementData->validationError:'';

		$css = $p->css;
		$inputClass = $css . (empty( $error )?'':' input-error');
		$labelClass = $css . (empty( $error )?'':' label-error');
		
		$output  = '';
		
		$htmlId = $p->hash.'_'.$elementData->id;
		
		//Load the correct default value
		$default = null;
		if( property_exists($elementData,'defaultValue' ) ){
			$default = $elementData->defaultValue;
		} else {
			$default = $p->defaultValue;
		}
		//Done with default values
		$inputStyle = $p->useCss?'':"width:{$p->cw}px;height:{$p->ch}px;";

	
	
		
		$output .= _line("<div class='error-message' id='{$htmlId}_error'>$error</div>",2	);
		$output .= _line("<fieldset class='$inputClass' id='$htmlId' style='$inputStyle'>",2);
		
		$p->label = htmlspecialchars($p->label, ENT_QUOTES);
		if( $p->required ) {
			$p->label = $p->label . '<span class="required" style="color:red"> * </span>';
		}
		
		$output .= _line('<legend class="'.$labelClass.'" id="'.$htmlId.'_label">'.$p->label.'</legend>',2);
		
		$elements = explode("\n" ,$p->elements);
		
		
		for($i=0;$i<count($elements);$i++){
			
			$label   = htmlspecialchars($elements[$i], ENT_QUOTES);
			$checked = $elements[$i]==$default?'checked=checked':'';
			if($p->align == 'Left' ){
			  $output .= _line("<input $checked class='radio' value='$label' id='{$htmlId}_{$i}' name='$p->hash' type='radio' />",3);
			  $output .= _line("<label class='radio' for='{$htmlId}_{$i}'>$label</label>",3);
			} else {
			  $output .= _line("<label class='radio' for='{$htmlId}_{$i}'>$label</label>",3);
			  $output .= _line("<input $checked value='$label' class='radio' id='{$htmlId}_{$i}' name='$p->hash' type='radio' />",3);
			}
			if( $p->layout == 'List' ){
				$output .= _line('<br />',3);
			}

		}
		
		$output .= _line('</fieldset>',2);
		$output .= _line('<div class="clear"></div>',2);
		return $output;
		
	}

	function jsClearErrors( $elementData ){
		
		$p = JArrayHelper::toObject($elementData->parameters);
		
		$htmlId = $p->hash.'_'.$elementData->id;
		$css = $p->css;
		
		$output  = '';
		$output .= _line("var v = document.getElementById('$htmlId');" ,2);
		$output .= _line("var {$p->hash}_error = document.getElementById('{$htmlId}_error');",2);
		$output .= _line("v.className ='$css';",2);
		$output .= _line("{$p->hash}_error.innerHTML='';",2);
		return $output;
	}

	function jsValidation( $elementData ){
		
		$p = JArrayHelper::toObject($elementData->parameters);
		
		$htmlId = $p->hash.'_'.$elementData->id;
		$css = $p->css;
			
		$output  = "\n";
		$output .= _line("var v = document.getElementById('$htmlId');" ,2);
		$output .= _line("var rGroup = form.{$p->hash};" ,2);
		$output .= _line("var {$p->hash}_error = document.getElementById('{$htmlId}_error');" ,2);
		$output .= _line("var anySelected = false;" ,2);
		$output .= _line("for(i=0;i<rGroup.length;i++){" ,2);
		$output .= _line("if(rGroup[i].checked == true ){" ,3);
		$output .= _line("anySelected = true;" ,4);
		$output .= _line("break;" ,4);
		$output .= _line("}" ,3);
		$output .= _line("}" ,2);
		$p->required = $p->required?'true':'false';
		$output .= _line("var required = $p->required;" ,2);
		$output .= _line("if(required && !anySelected){" ,2);
		$output .= _line("errorArray.push({id:v,msg:'error'});" ,3);
		$output .= _line("v.className ='input-error $css';",3);
		$output .= _line("{$p->hash}_error.innerHTML='".JText::_('Field Required')."';",3);	
		$output .= _line("}" ,2);
		return $output;
	}

	

	function validate( $elementData, $input ){
		
		$p = JArrayHelper::toObject($elementData->parameters);
		
		//Didn't make any choice
		if( $input == null ) {
			if( $p->required ){
				return JText::_("Field Required");
			}
		}
		return '';
	}
}