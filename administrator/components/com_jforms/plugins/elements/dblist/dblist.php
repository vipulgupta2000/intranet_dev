<?php
/**
* Database List Element plugin
*
* @version		$Id: dblist.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
/**
 * Database List Element plugin 
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
 */

class JFormEPluginDblist extends JFormEPlugin{


	function getDBElements( $params ){
	
		$db =& JFactory::getDBO();
		
		$tableName  = str_replace('`','',$params->tableName);
		$keyField   = str_replace('`','',$params->keyField);
		$valueField = str_replace('`','',$params->valueField);
		$orderField = str_replace('`','',$params->orderField);
		$orderMode  = in_array( strtoupper($params->orderMode) , array('ASC','DESC'))?strtoupper($params->orderMode):'ASC';

		$db->setQuery( "SELECT `$keyField` as `key`, `$valueField` as `value` FROM `$tableName` ORDER BY `$orderField` $orderMode" );
		$lists = $db->loadObjectlist();
		
		require_once JFORMS_BACKEND_PATH.DS.'libraries'.DS.'Services_JSON'.DS.'Services_JSON.php';
	
		//Decode JSON value
		$json = new Services_JSON();
		return $json->encode( $lists );
		
	}
	
	function beforeSave( $elementData, $input, $fsInfo ){return $input?implode(',',$input):'';}
	
	function translate( $elementData, $input, $format='html', $segment=''){
		
		static $dbCache = array();

		$p = JArrayHelper::toObject($elementData->parameters);
		
		$tableName  = str_replace('`','',$p->tableName);
		$keyField   = str_replace('`','',$p->keyField);
		$valueField = str_replace('`','',$p->valueField);
		$orderField = str_replace('`','',$p->orderField);
		$orderMode  = in_array( strtoupper($p->orderMode) , array('ASC','DESC'))?strtoupper($p->orderMode):'ASC';
		$dbSignature = md5( $tableName.'|_|'.$keyField.'|_|'.$valueField.'|_|'.$orderField.'|_|'.$orderMode );

		if( !array_key_exists($dbSignature, $dbCache)){
			$db =& JFactory::getDBO();
			$db->setQuery( "SELECT `$keyField` as `key`, `$valueField` as `value` FROM `$tableName` ORDER BY `$orderField` $orderMode" );
			$lists = $db->loadObjectlist('key');
			$dbCache[$dbSignature] = $lists;
		}
		if( $dbCache[$dbSignature] )$elements = $dbCache[$dbSignature];
		else $elements = array();
		
		$input = explode(',',$input);
		$response = array();
		
		foreach( $input as $value ){
			if( array_key_exists($value, $elements))
				$response[$value] = addSlashes($elements[$value]->value);
			else
				$response[$value] = 'null';
		}
		
		switch($format){
			case 'html':
			case 'raw':
				return implode(',', $response);
			case 'object':
				return $response;
		}
		return null;
	}
	
	function render( $elementData ){

		static $dbCache = array();

		$p = JArrayHelper::toObject($elementData->parameters);
		
		$tableName  = str_replace('`','',$p->tableName);
		$keyField   = str_replace('`','',$p->keyField);
		$valueField = str_replace('`','',$p->valueField);
		$orderField = str_replace('`','',$p->orderField);
		$orderMode  = in_array( strtoupper($p->orderMode) , array('ASC','DESC'))?strtoupper($p->orderMode):'ASC';
		$dbSignature = md5( $tableName.'|_|'.$keyField.'|_|'.$valueField.'|_|'.$orderField.'|_|'.$orderMode );
		

		if( !array_key_exists($dbSignature, $dbCache)){
			$db =& JFactory::getDBO();
			$db->setQuery( "SELECT `$keyField` as `key`, `$valueField` as `value` FROM `$tableName` ORDER BY `$orderField` $orderMode" );
			$lists = $db->loadObjectlist();
			$dbCache[$dbSignature] = $lists;
		} 
		if( $dbCache[$dbSignature] )$elements = $dbCache[$dbSignature];
		else $elements = array();
		
		$htmlId = $p->hash.'_'.$elementData->id;
		
		$error   = property_exists($elementData,'validationError' )?$elementData->validationError:'';

		$css = $p->css;
		$inputClass = $css . (empty( $error )?'':' input-error');
		$labelClass = $css . (empty( $error )?'':' label-error');
		

		
		//Load the correct default value
		$default = null;
		if( property_exists($elementData,'defaultValue' ) ){
			if( is_array($elementData->defaultValue) ){
				$default = $elementData->defaultValue;
			}
			if( $elementData->defaultValue == null ){
				$default = array();
			}
		} else {
			$default = explode("\n",$p->defaultValue);
		}
		//Done with default values
		
		$output  = '';
		$multi   = $p->multi?'multiple="multiple"':'';
		
		$p->label = htmlspecialchars($p->label, ENT_QUOTES);
		if( $p->required ) {
			$p->label = $p->label . '<span class="required" style="color:red"> * </span>';
		}

		$labelStyle = $p->useCss?'':'float:left;width:{$p->lw}px;height:{$p->lh}px';
		
		$output .= _line("<div class='error-message' id='{$htmlId}_error'>$error</div>",2	);
		$output .= _line("<label class='$labelClass' id='{$htmlId}_label' for='{$htmlId}' style='$labelStyle'>$p->label</label> ",2	);
		
		$inputStyle   = '';
		if(!$p->useCss)
			$inputStyle = $multi?"width:{$p->cw}px;height:{$p->ch}px;":"width:{$p->cw}px;";
		
		$output .= _line("<select class='$inputClass' $multi name='".$p->hash."[]' id='$htmlId' style='$inputStyle'>",2);
		
		foreach($elements as $item){
		  $selected = '';
		  if( in_array( $item->key, $default ) ){
			$selected = 'selected="selected"';
		  }
		  $item->key = htmlspecialchars($item->key, ENT_QUOTES);
		  $item->value = htmlspecialchars($item->value, ENT_QUOTES);
		  
		  $output .= _line("<option value='{$item->key}' $selected>{$item->value}</option>",3);
		}
		
		$output .= _line('</select>',2);
		$output .= _line('<div class="clear"></div>',2);
		
		return $output;
		
	}
			 
	function jsClearErrors( $elementData ){
		
		$p = JArrayHelper::toObject($elementData->parameters);
		
		$htmlId = $p->hash.'_'.$elementData->id;
		$css = $p->css;
		
		$output  = "\n";
		$output .= _line("var $p->hash = document.getElementById('$htmlId');" ,2);
		$output .= _line("var {$p->hash}_error = document.getElementById('{$htmlId}_error');" ,2);
		$output .= _line("var {$p->hash}_label = document.getElementById('{$htmlId}_label');" ,2);
		
		$output .= _line("{$p->hash}.className = '$css';",2);
		$output .= _line("{$p->hash}_label.className = '$css';",2);
		$output .= _line("{$p->hash}_error.innerHTML = '';",2);		
		return $output;
	}

	function jsValidation( $elementData ){
		
		$p = JArrayHelper::toObject($elementData->parameters);
		
		$htmlId = $p->hash.'_'.$elementData->id;
		$css = $p->css;
		
		$output  = "\n";
		$output .= _line("var {$p->hash} = document.getElementById('$htmlId');" ,2);
		$output .= _line("var {$p->hash}_label = document.getElementById('{$htmlId}_label');" ,2);	
		$output .= _line("var {$p->hash}_error = document.getElementById('{$htmlId}_error');" ,2);	

		$p->required = $p->required?'true':'false';
		
		$output .= _line("var required = $p->required;" ,2);
		$output .= _line("if(required){" ,2);
		$output .= _line("if( {$p->hash}.multiple ){" ,3);
		$output .= _line("if( {$p->hash}.selectedIndex == -1 ){" ,4);
		$output .= _line("errorArray.push({id:$p->hash,msg:'error'});" ,5);
		$output .= _line("{$p->hash}.className ='input-error $css';",5);
		$output .= _line("{$p->hash}_label.className ='label-error $css';",5);		
		$output .= _line("{$p->hash}_error.innerHTML='".JText::_('Field Required')."';",5);	
		$output .= _line("}" ,4);
		$output .= _line("}" ,3);
		$output .= _line("}" ,2);
		return $output;
	}
	
	function validate( $elementData, $input ){
		
		$p = JArrayHelper::toObject($elementData->parameters);
		
		if( $input == null )$input = array();
		
		//Selected nothing in a required multi-select list
		if( $elementData->parameters['multi'] && $p->required && count( $input ) == 0){
			return JText::_('Field Required');	
		}

		return '';
	}
}