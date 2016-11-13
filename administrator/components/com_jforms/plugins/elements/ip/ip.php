<?php
/**
* IP Address Element plugin
*
* @version		$Id: ip.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
define( 'RAW_IP_EXPORT', 0);

/**
 * IP Address Element plugin 
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
*/
class JFormEPluginIp extends JFormEPlugin{

	function getSQL( $elementData, $criteria ){
		$db =& JFactory::getDBO();
		$regEx = '/\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/';
		$from = $criteria->from;
		$to   = $criteria->to;
		
		if(	!preg_match($regEx, $from) ||
			!preg_match($regEx, $to)){
			return '';
		}
		
		$fromDec = intval( ip2long( $from ));
		$toDec   = intval( ip2long( $to   ));
		
		$mode = $criteria->mode=='or'?' OR ':' AND ';
		$fragments = array();
		$field= $elementData->parameters['hash'];
		
		if($from != '' )
			$fragments[] = "`$field` >= $fromDec";
		if($to != '' )
			$fragments[] = "`$field` <= $toDec";
		$sql = implode( ' AND ', $fragments );
		
		if( trim( $sql ) != '' ){
			$sql = "($sql) $mode";
		}
		return $sql;
	}
	
	function translate( $elementData, $input, $format='html', $segment=''){
		
		switch( $format ){
			default:
			case 'html':
				return long2ip( $input );
			case 'raw':
				if(RAW_IP_EXPORT){
					return $input;
				} else {
					return long2ip( $input );
				}
		}
	}

	function render( $elementData ){
		$p = JArrayHelper::toObject($elementData->parameters);
		$htmlId = $p->hash.'_'.$elementData->id;
		return _line("<input type='hidden' value='' name='$p->hash' id='$htmlId' />",2);
	}
	
	function beforeSave($elementData, $input){
		$ip = JRequest::getVar('REMOTE_ADDR','0.0.0.0', 'server');
		return ip2long( $ip );
	}
}