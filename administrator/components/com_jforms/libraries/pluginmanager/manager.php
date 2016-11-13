<?php
/**
* PluginManager class
*
* Active element plugins are retrieved from "com_jforms/plugins/element/plugins.list" 
* each plugin is stored in a separate directory with the same name as the plugin
* for more details about the structure please view the "com_jforms/plugins/element/" directory
*
* @version		$Id: manager.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Core
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

jimport('joomla.utilities.simplexml');

include 'managers'.DS.'base.php';
include 'managers'.DS.'element.php';
include 'managers'.DS.'export.php';
include 'managers'.DS.'storage.php';

/**
 * PluginManager class, This class controls the installed plugins and allows the core to communicate with them
 *
 * Active element plugins are retrieved from "com_jforms/plugins/element/plugins.list" 
 * each plugin is stored in a separate directory with the same name as the plugin
 * for more details about the structure please view the "com_jforms/plugins/element/" directory
 *
 * @package    Joomla
 * @subpackage JForms.Core
 */
class JFormsPluginController{

	var $settings = array();
	var $managers = array();
	
	function __construct(){
		
		$this->managers['export']  = new JFormsExportPluginManager(); 
		$this->managers['storage'] = new JFormsStoragePluginManager();
		$this->managers['element'] = new JFormsElementPluginManager();
		
		$this->settings['export']  = null; 
		$this->settings['storage'] = null;
		$this->settings['element'] = null;
		
	}
	
	function invokeMethod( $type, $name, $which, $params ){
		//Check $type for errors
		
		if( array_key_exists( $type , $this->managers)){
			return  $this->managers[$type]->invokeMethod( $name, $which, $params );
		}
	}

	function loadPlugins($type){
		//Check $type for errors
		if( array_key_exists( $type , $this->managers)){
			//Check if plugins are already loaded
			if( $this->settings[$type] == null )
				//Load requested plugin settings
				$this->managers[$type]->loadPlugins();
				//Copy plugin settings from the inner class to this class
				$this->settings[$type] = $this->managers[$type]->getSettings();
		}
	}
}