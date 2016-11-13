<?php
/**
* JForms Form embed plugin
*
* @version		$Id: jforms.php 368 2010-03-27 01:37:11Z dr_drsh $
* @package		Joomla
* @subpackage	JForms
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// no direct access

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.utilities.arrayhelper' );
$mainframe->registerEvent( 'onPrepareContent', 'plgContentJForm' );


require_once JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jforms'.DS.'globals.php';
require_once JFORMS_FRONTEND_PATH.DS. 'views'.DS.'form'.DS.'view.html.php';

JFormsInitializePluginManager();

/**
* Plugin that loads module positions within content
*/
function plgContentJForm( &$row, &$params, $page=0 )
{
	global $option;
	
	jimport('joomla.application.component.model'); 
	jimport('joomla.database.table'); 	
	
	JModel::addIncludePath( JFORMS_BACKEND_PATH.DS.'models');
	JTable::addIncludePath( JFORMS_BACKEND_PATH.DS.'tables' );
	JHTML::addIncludePath ( JFORMS_BACKEND_PATH.DS.'helpers' );
	
	$db =& JFactory::getDBO();
	
	// simple performance check to determine whether bot should process further
	if ( JString::strpos( $row->text, '{jform=' ) === false ) {
		return true;
	}

	// Get plugin info
	$plugin =& JPluginHelper::getPlugin('content', 'jforms');

 	// expression to search for
 	$regex = '/{jform=(.*?)}/i';

 	$pluginParams = new JParameter( $plugin->params );


	// check whether plugin has been unpublished or running within JForms context
	if ( !$pluginParams->get( 'enabled', 1 ) || $option == 'com_jforms' ){
		$row->text = preg_replace( $regex, '', $row->text );
		return true;
	}
	

 	// find all instances of plugin and put in $matches
	preg_match_all( $regex, $row->text, $matches );

	// Number of plugins
 	$count = count( $matches[0] );

 	// plugin only processes if there are any instances of the plugin in the text
 	if ( $count ) {
 		plgContentEmbedForms( $row, $matches, $count, $regex );
	}
	return true;
}


function plgContentGenerateHTML( $id, $passedDefaults ){


	
	if(!defined('JFORMS_PLUGIN_RUNNING'))define('JFORMS_PLUGIN_RUNNING',1);

	$frmReturn = JRequest::getInt( 'JFormsReturn' );
	if( !$frmReturn ){
		unset($_SESSION['JFormsSession']);
	}
	
	$document =& JFactory::getDocument();
	
	// Get/Create the model
	$formModel =& JModel::getInstance('form','JFormsModel');	
	$form   =  $formModel->get( $id );
		
		
	//Form not found
	if( $form == null ){
		return  JText::_("Form not found");
	}

	//Form is either beyond record count or has expired
	if( !$form->isPublished || (($form->recordCount >= $form->maximum) && $form->maximum != 0) ){
		return JText::_('Form has Expired or maximum entries has been reached', $form);
	}

		
	//Check premissions
	$user   =& JFactory::getUser();
	$allowedGroups = explode(',', $form->groups);
	
	//User isn't among the allowed groups
	if( !in_array( $user->gid, $allowedGroups ) ){
		return JText::_("You're not authorized to view this form");
	}
		
	//If Profile mode form, Allow only non-guests
	if( !$user->id && $form->type == JFORMS_TYPE_PROFILE ){
		return JText::_("You need to login to view this form");
	}
	
	$previousState    = null;
	$hasPreviousState = array_key_exists( 'JFormsSession', $_SESSION ) &&
						array_key_exists( $id, $_SESSION['JFormsSession']['FormState'] );

	if($hasPreviousState){
		$previousState = $_SESSION['JFormsSession']['FormState'][$id];
	}   
	

	$sortedElements = array();
	foreach( $form->fields as $f ){
		$sortedElements[$f->position] = $f;
	}
	$form->fields = $sortedElements;
	
	// Get/Create the model
	$recordModel =& JModel::getInstance('record','JFormsModel');	
	
	//Load previously stored Data from DB
	if( $user->id && $form->type == JFORMS_TYPE_PROFILE ){
		$record =  $recordModel->getByUid( $form, $user->id );
		if( count($record) ){
			foreach($form->fields as $f){
				if( array_key_exists( $f->parameters['hash'], $record  ))
					$f->parameters['defaultValue'] = $record[$f->parameters['hash']];
			}
		}
	}
	
	//Load previous state from last faulty submission or supplied defaults
	if($hasPreviousState || count($passedDefaults) > 0){
		foreach( $form->fields as $key => $value ){
			//Fix for PHP4 since foreach doesn't return references.
			$f = &$form->fields[$key];
			
			//Read supplied Defaults first
			if( count($passedDefaults) && array_key_exists( strtolower($f->parameters['label']), $passedDefaults )){
				$f->defaultValue = $passedDefaults[strtolower($f->parameters['label'])];
			}
			
			//Previous session state overrides initial supplied defaults
			if( $hasPreviousState && array_key_exists( $f->parameters['hash'], $previousState )){
				$f->validationError = $previousState[$f->parameters['hash']][0];
				$f->defaultValue    = $previousState[$f->parameters['hash']][1];			
			}
		}
	}

	$view = new FrontendViewForm(array( 'base_path'=>JFORMS_FRONTEND_PATH ));
	$view->setLayout('embedded');
	ob_start();
	$view->form($form);
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
}


function plgContentEmbedForms ( &$row, &$matches, $count, $regex ){
 
	//Avoid embedding one form twice in one page
	$embededIds = array();
	
	for ( $i=0; $i < $count; $i++ ){
		$id = intval( $matches[1][$i] );
		if(in_array($id,$embededIds))continue;
		$embededIds[] = $id;
		$overrideDefaults = array();

		//Load defaults set by {Jforms} call
		$parameters=explode(",",trim($matches[0][$i],"}")); 

		//The first one is the plugin itself... 
		for ( $j=1; $j < count($parameters); $j++ ) { 
			list($fieldLabel, $fieldValue) = explode("=",$parameters[$j]); 
			$overrideDefaults[strtolower($fieldLabel)] = $fieldValue;
		} 
		
		$formHTML = plgContentGenerateHTML( $id, $overrideDefaults );

		$row->text 	= preg_replace( '{'. $matches[0][$i] .'}', $formHTML, $row->text );
 	}
}
