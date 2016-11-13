<?php
/**
 * Textarea element plugin
*
* @version		$Id: textarea.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * Textarea element plugin
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
*/
class JFormEPluginTextarea extends JFormEPlugin{
	
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
	
	function translate ( $elementData, $input, $format='html' ){
				
		$s = str_replace( "\r\n",'<br />', $input );
		$s = str_replace( "\n",'<br />', $s );
		return $s;

	}	
	
	function beforeSave( $elementData, $input ){		
		//We want pure text only!
		return strip_tags( $input );
	}

	function render( $elementData ){
		$p = JArrayHelper::toObject($elementData->parameters);
		
		$htmlId = $p->hash.'_'.$elementData->id;

		$default = property_exists($elementData,'defaultValue' )?$elementData->defaultValue:$p->defaultValue;
		$error   = property_exists($elementData,'validationError' )?$elementData->validationError:'';

		$css = $p->css;
		$inputClass = $css . (empty( $error )?'':' input-error');
		$labelClass = $css . (empty( $error )?'':' label-error');

		$p->label = htmlspecialchars($p->label, ENT_QUOTES);
		if( $p->required ) {
			$p->label = $p->label . '<span class="required" style="color:red"> * </span>';
		}
		
		if( $p->sizeMode == 'html' ){
			$inputStyle = '';
			$rows = $p->rows;
			$cols = $p->cols;
		} else {
			$inputStyle = $p->useCss?'':"width:{$p->cw}px;height:{$p->ch}px;";
			$rows = 0;
			$cols = 0;
		}
		
		$output  = '';
		$output .= _line("<div class='error-message' id='{$htmlId}_error'>$error</div>",2	);
		$output .= _line("<label class='$labelClass' for='{$htmlId}' id='{$htmlId}_label' for='$p->hash' style='width:{$p->lw}px;height:{$p->lh}px'>$p->label</label> ",2);
		$output .= _line("<textarea rows='$rows' cols='$cols' class='$inputClass' name='$p->hash' id='$htmlId' style='$inputStyle'>$default</textarea>",2);
		$output .= _line('<div class="clear"></div>',2);

		return $output;
		
	}

	function jsValidation( $elementData ){
		
		$p = JArrayHelper::toObject($elementData->parameters);
	
		$minLength = $p->minLength;
		$maxLength = $p->maxLength;
		
		$htmlId = $p->hash.'_'.$elementData->id;
		$css = $p->css;
		

		$output  = "\n";
		$output .= _line("var $p->hash = document.getElementById('$htmlId');" ,2);
		$output .= _line("var {$p->hash}_error = document.getElementById('{$htmlId}_error');" ,2);
		$output .= _line("var {$p->hash}_label = document.getElementById('{$htmlId}_label');" ,2);
	
		$p->required = $p->required?'true':'false';
	
		$output .= _line("if($p->hash.value.length == 0){" ,2);
		$output .= _line("var required = $p->required;" ,3);
		$output .= _line("if(required){" ,3);
		$output .= _line("errorArray.push({id:$p->hash,msg:'error'})" ,4);
		$output .= _line("{$p->hash}.className ='input-error $css';",4);
		$output .= _line("{$p->hash}_label.className ='label-error $css';",4);
		$output .= _line("{$p->hash}_error.innerHTML='".JText::_('Field Required')."';",4);	
		$output .= _line("}" ,3);
		$output .= _line("} else {" ,2);
		
		$output .= _line("if( $p->hash.value.length > $maxLength ){" ,3);
		$output .= _line("errorArray.push({id:$p->hash,msg:'error'})" ,4);
		$output .= _line("{$p->hash}.className = 'input-error $css';",4);
		$output .= _line("{$p->hash}_label.className = 'label-error $css';",4);
		$output .= _line("{$p->hash}_error.innerHTML='".JText::_('Too long')."';",4);	
		$output .= _line("}" ,3);
		
		$output .= _line("if( $p->hash.value.length < $minLength ){" ,3);
		$output .= _line("errorArray.push({id:$p->hash,msg:'error'})" ,4);
		$output .= _line("{$p->hash}.className = 'input-error $css';",4);
		$output .= _line("{$p->hash}_label.className = 'label-error $css';",4);
		$output .= _line("{$p->hash}_error.innerHTML='".JText::_('Too short')."';",4);	
		$output .= _line("}" ,3);
		
		$output .= _line("}" ,2);
		return $output;
	
	
	}
	function jsClearErrors( $elementData ){

		$p = JArrayHelper::toObject($elementData->parameters);
		$output  = '';
		
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
	
		$minLength = $p->minLength;
		$maxLength = $p->maxLength;
		$len = strlen( trim( $input ));
		
		if( !$len ) {
			if( $p->required ){
				return JText::_("Field Required");
			}
		} else {
			
			if( $len > $maxLength ){	
				return JText::_("Too long");
			}
			
			if( $len < $minLength ){
				return JText::_("Too short");
			}
			
		}

		return "";
	}

}
