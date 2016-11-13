<?php
/**
* Database upgrade routine
* Upgrades from JForms DB from 0.5 to 0.6
*
* @version		$Id: upgrade_5_6.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Install
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
defined('_JEXEC') or die(); 

define('JFORMS_UPGRADE_SOURCE_VERSION', 5);
define('JFORMS_UPGRADE_DEST_VERSION'  , 6);

/**
 * Upgrade class for JForms
 * Upgrades from JForms DB from 0.5 to 0.6
 * 
 * @package    Joomla
 * @subpackage JForms.Install
 */
class JFormsUpgrade_5_6{
	
	/**
	* Checks whether or not the DB structure is up-to-date
	*
	* @access	public
	* @return boolean True if the DB is up-to-date, otherwise returns false
	*/
	function isUpgraded(){
		$db =& JFactory::getDBO();
		$fields = $db->getTableFields( '#__jforms_forms' );
		if (array_key_exists('theme', $fields['#__jforms_forms']))return true;
		else return false;
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
		
		$sql = 'SELECT `parameter_name`,`parameter_value` FROM `#__jforms_parameters` WHERE
		`plugin_name` = "Database" AND fid='.$fid;
		$db->setQuery( $sql );
		$dbPluginParameters = $db->loadAssocList('parameter_name');
		$tableName = $dbPluginParameters['tableName']['parameter_value'];
				
		$fields = unserialize(base64_decode( $dbPluginParameters['fields']['parameter_value']));
		$fields['Date'] = new stdClass();
		$fields['IP']   = new stdClass();
				
		$fields['Date']->type	    = 'datetime';
		$fields['Date']->size 		= 0;
		$fields['Date']->requirefs	= false;
				
		$fields['IP']->type		    = 'number';
		$fields['IP']->size 		= 4;
		$fields['IP']->requirefs	= false;
		$fieldsString = base64_encode(serialize( $fields ));
		
		$sql = 'UPDATE `#__jforms_parameters` SET `parameter_value`="'.$fieldsString.'" WHERE
		`plugin_name` = "Database" AND `parameter_name`="fields" AND fid='.$fid;
		$db->setQuery( $sql );
		$db->query();
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr() );
			return;
		}		
			
		$sql = 
		'ALTER TABLE `#__jforms_'.$tableName.'` ADD '
		.'`uid` INT( 11 ) UNSIGNED NOT NULL DEFAULT "0" AFTER `id` ,ADD INDEX ( uid )';
		$db->setQuery( $sql );
		$db->query();
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr() );
			return;
		}		
				
		$sql =  'INSERT INTO `#__jforms_fields` '
				.' (`pid` ,`type` ,`position`) '
				.' VALUES '
				."($fid, 'entrydate', '998')";
		$db->setQuery( $sql );
		$db->query();
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr() );
			return;
		}		
		$lastInsertId = $db->insertid();
		$sql = 'INSERT INTO `#__jforms_parameters` '
			   .'(`fid`, `pid`, `plugin_name`, `plugin_type`, `parameter_name`, `parameter_value`) VALUES '
			   ."($fid, $lastInsertId, 'entrydate', 1, 'hash', 'Date')";
		$db->setQuery( $sql );
		$db->query();
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr() );
			return;
		}		
		
		$sql = 'INSERT INTO `#__jforms_parameters` '
			   .'(`fid`, `pid`, `plugin_name`, `plugin_type`, `parameter_name`, `parameter_value`) VALUES '
			   ."($fid, $lastInsertId, 'entrydate', 1, 'label', 'Entry Date')";
		$db->setQuery( $sql );
		$db->query();
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr() );
			return;
		}		
		
		$sql =  'INSERT INTO `#__jforms_fields` '
				.' (`pid` ,`type` ,`position`) '
				.' VALUES '
				."($fid, 'ip', '999')";
		$db->setQuery( $sql );
		$db->query();
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr() );
			return;
		}		
		
		$lastInsertId = $db->insertid();
				
		$sql = 'INSERT INTO `#__jforms_parameters` '
			   .'(`fid`, `pid`, `plugin_name`, `plugin_type`, `parameter_name`, `parameter_value`) VALUES '
			   ."($fid, $lastInsertId, 'ip', 1, 'hash', 'IP')";
		$db->setQuery( $sql );
		$db->query();
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr() );
			return;
		}		
				
		$sql = 'INSERT INTO `#__jforms_parameters` '
			   .'(`fid`, `pid`, `plugin_name`, `plugin_type`, `parameter_name`, `parameter_value`) VALUES '
			   ."($fid, $lastInsertId, 'entrydate', 1, 'label', 'IP Address')";
		$db->setQuery( $sql );
		$db->query();
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr() );
			return;
		}
			
		//Convert IPs from Strings to Integers
		$sql = 'SELECT id,IP FROM `#__jforms_'.$tableName.'`';
		$db->setQuery( $sql );
		$entries = $db->loadObjectList();

		foreach($entries as $entry){
			$newIP = ip2long($entry->IP);
			$sql = "UPDATE `#__jforms_{$tableName}` SET IP='$newIP' WHERE id=$entry->id";
			$db->setQuery( $sql );
			$db->query();
		}
		
		$sql = 'ALTER TABLE `#__jforms_'.$tableName.'` CHANGE `IP` `IP` INT( 4 ) NOT NULL';
		$db->setQuery( $sql );
		$db->query();
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
				if(JFormsUpgrade_5_6::isUpgraded())return 'JForms is already up to date';
				$sql = 'ALTER TABLE `#__jforms_forms` ADD `theme` VARCHAR( 100 ) NOT NULL DEFAULT "default" AFTER `plugins`';
				$db->setQuery( $sql );
				$db->query();
				if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
				return JURI::base(). "index.php?option=com_jforms&controller=upgrade&task=upgrade&$src_dst&step=2&param=&$token=1";

			case 2:
				$sql = 'SELECT id FROM `#__jforms_forms`';
				$db->setQuery( $sql );
				$forms = implode(',', $db->loadResultArray(0));
				if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
				if(!strlen($forms))return 'JForms was successfully upgraded to version 0.6';
				return JURI::base(). "index.php?option=com_jforms&controller=upgrade&task=upgrade&$src_dst&step=3&param=$forms&$token=1";
				
			case 3:
				$formsArray  = explode(',', $param );
				$currentForm = array_pop( $formsArray );
				JFormsUpgrade_5_6::upgradeForm( $currentForm );
				$formsString = implode( ',', $formsArray );
				if( empty( $formsArray ) )return 'JForms was successfully upgraded to version 0.6';
				return JURI::base(). "index.php?option=com_jforms&controller=upgrade&task=upgrade&$src_dst&step=2&param=$formsString&$token=1";
		}
	}
}