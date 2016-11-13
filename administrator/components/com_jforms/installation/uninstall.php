<?php
/**
* JForms Uninstall script
*
* @version		$Id: uninstall.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Install
* @copyright	Copyright (C) 2009 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

ignore_user_abort( true );

/**
 * Uninstalls JForms content plugin
 * 
 * @access	public
 */
function com_uninstall() {

	$db =& JFactory::getDBO();
	/*					uninstall plugin 					*/
	JFile::delete(JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'jforms.php');
	JFile::delete(JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'jforms.xml');

	$query = 'DELETE FROM `#__plugins` WHERE `element`="jforms"';
	$db->setQuery($query);
	$db->query();
	/*					Done uninstalling plugin					*/

	/*					Delete record tables							*/
	$db =& JFactory::getDBO();
		
	$sql = 'SELECT `parameter_value` FROM `#__jforms_parameters` WHERE 
	`parameter_name` = "tableName" AND `plugin_name` ="Database" AND `plugin_type`=0';
	$db->setQuery( $sql );
	$tableNames = implode(',', $db->loadResultArray(0));
	
	$sql = "DROP TABLE $tableNames";
	$db->setQuery( $sql );
	$db->query();
	/*					Done delete records								*/
	
	JFolder::delete(JPATH_ROOT.DS.'media'.DS.'com_jforms');	
	
}