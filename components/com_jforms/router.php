<?php
/**
* Router for JForms
*
* @version		$Id: router.php 368 2010-03-27 01:37:11Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Core
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

function jformsBuildRoute(&$query){

	$segments = array();
	
	$menu = &JSite::getMenu();
	$menuItems = &$menu->getItems('type','component');

	if(isset($query['id'])){
		foreach($menuItems as $item){
			if( $query['task'] != 'thank' && $query['task'] != 'submit' && $item->query['option'] == 'com_jforms' && $item->query['id'] == $query['id'] && $item->query['view'] == 'form'){
				$query['Itemid'] = $item->id;
				unset($query['task']);
				unset($query['id']);
				unset($query['view']);
				return $segments;
			}
		}
	} else {
		if(isset($query['Itemid'])){
			unset($query['task']);
			unset($query['id']);
			unset($query['view']);
			return $segments;
		}
	}
	
	if(isset($query['task'])) {
		$segments[] = $query['task'];
		unset($query['task']);
	}

	if(isset($query['view'])) {
		//$segments[] = $query['view'];
		unset($query['view']);
	}
		
	if(isset($query['id'])) {
		$segments[] = $query['id'];
		unset($query['id']);
	}
		
	if(isset($query['Itemid'])) {
		//$segments[] = $query['Itemid'];
		unset($query['Itemid']);
	}
		
	return $segments;
	
}

function jformsParseRoute($segments)
{

	$vars = array();
	switch( $segments[0] ){
		case 'form':
		case 'submit':
		case 'thank':
			$vars['task']   = $segments[0];
			$vars['id']     = $segments[1];
			$vars['Itemid'] = @$segments[2];
	}
	return $vars;

}

