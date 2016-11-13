<?php
/**
* JForms Installation script
*
* @version		$Id: install.php 362 2010-02-20 06:50:23Z dr_drsh $
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

define('JFORMS_BACKEND_PATH' ,  JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jforms');
define('JFORMS_FRONTEND_PATH',  JPATH_ROOT.DS.'components'.DS.'com_jforms');

/**
 * Installs and publishes JForms content plugin
 * 
 * @access	public
 */
function com_install() {

	$db =& JFactory::getDBO();

	/*					Install plugin 					*/
	if(!JFile::exists(JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'jforms.php')){
		JFile::move(
				JFORMS_BACKEND_PATH.DS.'plugins'.DS.'content'.DS.'jforms.php',
				JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'jforms.php'
		);
		JFile::move(
				JFORMS_BACKEND_PATH.DS.'plugins'.DS.'content'.DS.'jforms.xml',
				JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'jforms.xml'
		);
	}
	JFolder::delete(JFORMS_BACKEND_PATH.DS.'plugins'.DS.'content');

	$query = 'SELECT `published` FROM `#__plugins` WHERE `element`="jforms"';
	$db->setQuery($query);
	$result = $db->loadObject();
	
	if($result == null){
		$query = 
		"INSERT INTO `#__plugins` ( `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)"
		."\nVALUES"
		."\n('Content - JForms', 'jforms', 'content', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', '')";
		$db->setQuery($query);
		$db->query();
	} else {
		$query = 'UPDATE `#__plugins` SET `published` = 1 WHERE `element`="jforms"';
		$db->setQuery($query);
		$db->query();
	}

	/*					Done installing plugin					*/
	
	//Set Component Icon
	$db->setQuery("UPDATE #__components SET admin_menu_img='../media/com_jforms/images/icon-16-component.png' WHERE admin_menu_link='option=com_jforms'");
	$res = $db->query();
	
	if(!JFolder::exists(JPATH_ROOT.DS.'media'.DS.'com_jforms')){
		JFolder::create(JPATH_ROOT.DS.'media'.DS.'com_jforms');
		JFile::write( JPATH_ROOT.DS.'media'.DS.'com_jforms'.DS.'index.html','<html><body bgcolor="#FFFFFF"></body></html>' );
	}
	
	//Copy Media files to Joomla's media directory
	$return =
	JFolder::move(  JFORMS_BACKEND_PATH.DS.'media'.DS.'com_jforms'.DS.'styles',
					JPATH_ROOT.DS.'media'.DS.'com_jforms'.DS.'styles' );
	if( !is_bool($return))echo $return;
	
	
	$return =
	JFolder::move(  JFORMS_BACKEND_PATH.DS.'media'.DS.'com_jforms'.DS.'scripts',
					JPATH_ROOT.DS.'media'.DS.'com_jforms'.DS.'scripts' );
	if( !is_bool($return))echo $return;

	$return =
	JFolder::move(  JFORMS_BACKEND_PATH.DS.'media'.DS.'com_jforms'.DS.'images',
					JPATH_ROOT.DS.'media'.DS.'com_jforms'.DS.'images' );
	if( !is_bool($return))echo $return;

	$return =
	JFolder::move(  JFORMS_BACKEND_PATH.DS.'media'.DS.'com_jforms'.DS.'plugins',
					JPATH_ROOT.DS.'media'.DS.'com_jforms'.DS.'plugins' );
	if( !is_bool($return))echo $return;
					
	$return =
	JFolder::move(  JFORMS_BACKEND_PATH.DS.'media'.DS.'com_jforms'.DS.'files',
					JPATH_ROOT.DS.'media'.DS.'com_jforms'.DS.'files' );					
	if( !is_bool($return))echo $return;

	JFolder::delete(JFORMS_BACKEND_PATH.DS.'media');	
			
	
}