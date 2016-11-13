<?php
/**
* Database upgrade routine
* Upgrades from JForms DB from 0.6 to 0.7
* 
* @version		$Id: upgrade_6_7.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Install
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
defined('_JEXEC') or die();

define('JFORMS_UPGRADE_SOURCE_VERSION', 6);
define('JFORMS_UPGRADE_DEST_VERSION'  , 7);

/**
 * Upgrade class for JForms
 * Upgrades from JForms DB from 0.6 to 0.7
 * 
 * @package    Joomla
 * @subpackage JForms.Install
 */
class JFormsUpgrade_6_7{
	
	/**
	* Checks whether or not the DB structure is up-to-date
	*
	* @access	public
	* @return boolean True if the DB is up-to-date, otherwise returns false
	*/
	function isUpgraded(){

		$db =& JFactory::getDBO();
		$fields = $db->getTableFields( '#__jforms_forms' );
		if (
			array_key_exists('redirections', $fields['#__jforms_forms']) &&
			array_key_exists('maximum'     , $fields['#__jforms_forms'])
		)
			return true;
		else 
			return false;
	}
	
	/**
	* Upgrades a single form to the new Database version
	*
	* @access	public
	* @param int ID of the form to upgrade
	* @return void
	*/
	function upgradeForm( $id ){
		
		$db =& JFactory::getDBO();
		
		$fid = intval($id);
		
		$sql = 'SELECT `parameter_value` FROM `#__jforms_parameters` WHERE
		`parameter_name` = "tableName" AND `plugin_name` ="Database" AND `plugin_type`=0 AND `fid`='.$fid;
		$db->setQuery( $sql );
		$tableName = $db->loadResult();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );

		$sql = 'SELECT `parameter_value` FROM `#__jforms_parameters` WHERE
		`parameter_name` = "hash" AND `plugin_name` ="file" AND `plugin_type`=1 AND `fid`='.$fid;
		$db->setQuery( $sql );
		$fields = $db->loadResultArray(0);
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
		
		//No File system fields, Proceed to next form
		if(!count($fields))return;
		
		$fieldsText = implode(',', $fields);
		//Update filesystem path
		$sql = "SELECT id, $fieldsText FROM `#__jforms_{$tableName}`";
		$db->setQuery( $sql );
		$entries = $db->loadObjectList();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
			
		foreach($entries as $entry){
			$values = array();
			foreach( $fields as $field ){
				if( strlen(trim($entry->$field)) ){
					$value = unserialize(base64_decode($entry->$field));
					$value->path = str_replace( JFORMS_FS_PATH, '', $value->path );
					$value->link = str_replace( JFORMS_FS_URL , '', $value->link );
					$values[] = '`'.$field .'`="'. base64_encode(serialize($value)).'"';
				}
			}
			if(!count($values))continue;
			$valuesText = implode( ',', $values); 
			$sql = "UPDATE `#__jforms_{$tableName}` SET $valuesText WHERE `id`=$entry->id";
			$db->setQuery( $sql );
			$db->query();
			if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
							
		}
	}
	
	/**
	* Performs the upgrade process in stepped manner (Using HTTP redirects to avoid exceeding PHP execution time)
	*
	* @access	public
	* @param int The current step in the process
	* @param string Parameters sent from the previous iteration
	* @return string URL for the next step or a message denoting success of the process
	* 
	*/
	function process( $step, $param ){
	

		$token =  JUtility::getToken();
		$db    =& JFactory::getDBO();
		
		$src_dst = "src=".JFORMS_UPGRADE_SOURCE_VERSION."&dest=".JFORMS_UPGRADE_DEST_VERSION;
		
		switch( $step ){
			default:
			case 1:
			
				if(JFormsUpgrade_6_7::isUpgraded())return "JForms is already up to date";
				
				$sql = 'ALTER TABLE `#__jforms_forms` CHANGE `hits` `maximum` INT( 11 ) UNSIGNED NOT NULL DEFAULT "0"';
				$db->setQuery( $sql );
				$db->query();
				if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
			
				$sql = 'ALTER TABLE `#__jforms_forms` CHANGE `thank` `redirections` MEDIUMTEXT NOT NULL';
				$db->setQuery( $sql );
				$db->query();
				if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
				return JURI::base()."index.php?option=com_jforms&controller=upgrade&task=upgrade&$src_dst&step=2&param=&$token=1";
			 
			case 2:
			
				$sql = "UPDATE `#__jforms_parameters` SET `parameter_value` = REPLACE( `parameter_value`, '\\\\n','\\n' ) WHERE plugin_type=1";
				$db->setQuery( $sql );
				$db->query();
				if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
			
				$sql = "UPDATE `#__jforms_tparameters` SET `parameter_value` = REPLACE( `parameter_value`, '\\\\n','\\n' ) WHERE plugin_type=1";
				$db->setQuery( $sql ); 
				$db->query();
				if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
				return JURI::base()."index.php?option=com_jforms&controller=upgrade&task=upgrade&$src_dst&step=3&param=&$token=1";

				$sql = "UPDATE `#__jforms_tparameters` SET `parameter_value` = REPLACE( `parameter_value`, ',','\\n' ) WHERE plugin_type=1 AND `parameter_name`='defaultValue'";
				$db->setQuery( $sql ); 
				$db->query();
				if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
				return JURI::base()."index.php?option=com_jforms&controller=upgrade&task=upgrade&$src_dst&step=3&param=&$token=1";

			case 3:
				$sql = 'SELECT id FROM `#__jforms_forms`';
				$db->setQuery( $sql );
				$forms = implode(',', $db->loadResultArray(0));
				if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
				if(!strlen($forms))return 'JForms was successfully upgraded to version 0.7';
				return JURI::base()."index.php?option=com_jforms&controller=upgrade&task=upgrade&$src_dst&step=4&param=$forms&$token=1";
				
			case 4:
				$formsArray  = explode(',', $param );
				$currentForm = array_pop( $formsArray );
				JFormsUpgrade_6_7::upgradeForm( $currentForm );
				$formsString = implode( ',', $formsArray );
				if( empty( $formsArray ) )return JURI::base()."index.php?option=com_jforms&controller=upgrade&task=upgrade&$src_dst&step=5&param=&$token=1";
				return JURI::base()."index.php?option=com_jforms&controller=upgrade&task=upgrade&$src_dst&step=4&param=$formsString&$token=1";
				
			case 5:
				/*					Upgrade from 0.6 File system structure	*/
				$fileSystemFolders = JFolder::folders(JPATH_ROOT.DS.'media'.DS.'com_jforms', '.', false, false, array('files', 'styles', 'scripts', 'images', 'plugins', '.svn','CVS'));
				foreach($fileSystemFolders as $folder)
					JFolder::move( $folder, 'files'.DS.$folder, JPATH_ROOT.DS.'media'.DS.'com_jforms'); 
				return 'JForms was successfully upgraded to version 0.7';
		}
	}
}