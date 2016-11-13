<?php
/**
* Date Element plugin
*
* @version		$Id: date.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
/**
 * Date Element plugin 
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
 */

jimport('joomla.filesystem.file');

class JFormEPluginDate extends JFormEPlugin {

	function getSQL( $elementData, $criteria ){
		
		$db =& JFactory::getDBO();
		$from = $criteria->from;
		$to   = $criteria->to;
		$mode = $criteria->mode=='or'?' OR ':' AND ';
		$fragments = array();
		$field= $elementData->parameters['hash'];
		
		if($from != '' )
			$fragments[] = "`$field` >= '$from'";
		if($to != '' )
			$fragments[] = "`$field` <= '$to'";
		$sql = implode( ' AND ', $fragments );
		
		if( trim( $sql ) != '' ){
			$sql = "($sql) $mode";
		}
		return $sql;
		
	}
	
	function beforeSave( $elementData, $input ){ 
		$p = JArrayHelper::toObject($elementData->parameters);

		$arrangedValue = array();
		$format = explode('/',$p->format);
		foreach($format as $key => $e){
			if($e == 'DD')$arrangedValue[0]   = $input[intval($key)];
			if($e == 'MM')$arrangedValue[1]   = $input[intval($key)];
			if($e == 'YYYY')$arrangedValue[2] = $input[intval($key)];
		}
		//Store in mysql date Format
		return intval($arrangedValue[2]) . '-' . intval($arrangedValue[1]) . '-' . intval($arrangedValue[0]);
	}
	
	function _getSegment( $segment, $input ){
	
		if( $segment == '' )return $input;
		
		$output = explode( '-', $input );
		
		if( count($output) != 3 )return null;
		switch( $segment ){
			case 'day':
				return $output[2];
			case 'month':
				return $output[1];
			case 'year':
				return $output[0];
		}
		
		return null;
	}
	
	function translate( $elementData, $input, $format='html', $segment=''){ 
		
		$segment = trim($segment);
		$p = JArrayHelper::toObject($elementData->parameters);
		$object = JFormEPluginDate::_getSegment( $segment, $input );
		
		if( is_null($object) )return null;
		
		switch( $format ){
			case 'raw':
			case 'html':
				if($segment == '')return str_replace(array('YYYY','MM','DD'),explode('-',$object),$p->format);
				else return $object;
			case 'object':
				return $object;
		}
		return null;
	}

	function render( $elementData ){
	
		$p = JArrayHelper::toObject($elementData->parameters);
		
		$default = property_exists($elementData,'defaultValue' )?$elementData->defaultValue:$p->defaultValue;
		$error   = property_exists($elementData,'validationError' )?$elementData->validationError:'';

		if( !is_array( $default )){
			//coming from DB
			
			$default = str_replace('-',"\n",$default);
			$default = explode("\n", $default);
			
			$arrangedDefault = array();
			$format = explode('/',$p->format);

			
			foreach($format as $key => $e){
				if($e == 'DD')$arrangedDefault[0]   = $default[2];
				if($e == 'MM')$arrangedDefault[1]   = $default[1];
				if($e == 'YYYY')$arrangedDefault[2] = $default[0];
			}
			$default = $arrangedDefault;
			
		} else {
			//Coming from POST request
			$arrangedDefault = array();
			$format = explode('/',$p->format);
			foreach($format as $key => $e){
				if($e == 'DD')$arrangedDefault[0]   = $default[intval($key)];
				if($e == 'MM')$arrangedDefault[1]   = $default[intval($key)];
				if($e == 'YYYY')$arrangedDefault[2] = $default[intval($key)];
			}
			$default = $arrangedDefault;
		}


		$css = $p->css;
		$inputClass = $css . (empty( $error )?'':' input-error');
		$labelClass = $css . (empty( $error )?'':' label-error');
		
		$labelStyle = $p->useCss?'':"width:{$p->lw}px;height:{$p->lh}px";
		
		$htmlId = $p->hash.'_'.$elementData->id;
		
		$startYear = $p->startYear; 
		$endYear = 0;
		switch( $p->span ){
			case 0:
				$endYear = intval(date('Y'));
				break;
				
			case -1:
				$endYear = $p->startYear + $p->ospan;
				break;
				
			default:
				$endYear = $p->startYear + $p->span;
				break;
		}
		

		$p->label = htmlspecialchars($p->label, ENT_QUOTES);
		if( $p->required ) {
			$p->label = $p->label . '<span class="required" style="color:red"> * </span>';
		}
				
		$output  = '';
	
		$output .= _line("<div class='error-message' id='{$htmlId}_error'>$error</div>",2	);
		$output .= _line("<label class='$labelClass' id='{$htmlId}_label' for='{$htmlId}_d' style='$labelStyle'>$p->label</label> ",2);
		$format = explode('/',$p->format);
		
		
		foreach($format as $key => $e){
		
			if( $e == 'DD' ){
				$output .= _line( JFormEPluginDate::_intList( 1,31,1,
								  $p->hash.'[]', 
								  "class='$inputClass day'", 
								   $default[0], '', $htmlId.'_d' ), 2);
			}  
			if( $e == 'MM' ){
				$output .= _line( JHTML::_('select.genericlist',JFormEPluginDate::_doMonthOptions(),
								  $p->hash.'[]', 
								  "class='$inputClass month'"
								  ,'value','text', 
								   $default[1],$htmlId.'_m' ), 2);
			}   
			if( $e == 'YYYY' ){
				$output .= _line( JFormEPluginDate::_intList( $startYear,$endYear,1,
								  $p->hash.'[]', 
								  " class='$inputClass year'",
								  $default[2], '', $htmlId.'_y' ), 2);
			}
	}
	$output .= _line('<div class="clear"></div>',2);
	
	return $output;
		
	}
	
	function _doMonthOptions(){
	
		$months = array();
		
		$months[] = JHTML::_('select.option', 1, JText::_('January'));
		$months[] = JHTML::_('select.option', 2, JText::_('February'));
		$months[] = JHTML::_('select.option', 3, JText::_('March'));
		$months[] = JHTML::_('select.option', 4, JText::_('April'));
		$months[] = JHTML::_('select.option', 5, JText::_('May'));
		$months[] = JHTML::_('select.option', 6, JText::_('June'));
		$months[] = JHTML::_('select.option', 7, JText::_('July'));
		$months[] = JHTML::_('select.option', 8, JText::_('August'));
		$months[] = JHTML::_('select.option', 9, JText::_('September'));
		$months[] = JHTML::_('select.option', 10, JText::_('October'));
		$months[] = JHTML::_('select.option', 11, JText::_('November'));
		$months[] = JHTML::_('select.option', 12, JText::_('December'));
		
		return $months;
		
	}

	function _intList($start, $end, $inc, $name, $attribs = null, $selected = null, $format = "", $id = "" )
	{
		$start 	= intval( $start );
		$end 	= intval( $end );
		$inc 	= intval( $inc );
		$arr 	= array();

		for ($i=$start; $i <= $end; $i+=$inc)
		{
			$fi = $format ? sprintf( "$format", $i ) : "$i";
			$arr[] = JHTML::_('select.option',  $fi, $fi );
		}

		return JHTML::_('select.genericlist',   $arr, $name, $attribs, 'value', 'text', $selected, $id );
	}

	
	function validate( $elementData, $input ){
		
		$p = JArrayHelper::toObject($elementData->parameters);
		$arrangedValue = array();
		$format = explode('/',$p->format);
		foreach($format as $key => $e){
			if($e == 'DD')$arrangedValue[0] = $input[intval($key)];
			if($e == 'MM')$arrangedValue[1] = $input[intval($key)];
			if($e == 'YYYY')$arrangedValue[2] = $input[intval($key)];
		}

		if( !checkdate( intval( $arrangedValue[1] ),intval( $arrangedValue[0] ),intval( $arrangedValue[2] ))){
			return JText::_('Invalid date');
		}
		return '';
	}
}