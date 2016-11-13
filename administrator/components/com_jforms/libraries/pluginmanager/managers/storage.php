<?php
/**
* Storage PluginManager class
*
* @version		$Id: storage.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Core
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

require_once JFORMS_BACKEND_PATH.DS.'models'.DS.'JFormSPlugin.php';

/**
* Storage PluginManager class
*
* @package		Joomla
* @subpackage	JForms.Core
*/
class JFormsStoragePluginManager extends JFormsPluginManager{

	/**
	 * @var array $loadedPlugins Stores loaded plugins parameters and settings
	 */
	var $loadedPlugins = null;
	
	function &getSettings(){return $this->loadedPlugins;}
	
	function invokeMethod( $name, $which, $params ){
		
		//Deals with call_user_func_array warning as of PHP 5.3
		if( $params == null )$params = array();
		
		if( $which != null && count($which) > 1 && $which[0] == '_MANAGER' ){
			if( !method_exists($this,$name) )return null;
			return call_user_func_array(array($this,$name),$params);		
		}
		
		$response = array();
		foreach( $this->loadedPlugins as $p ){

			if( !is_null( $which ) && !in_array( $p->name, $which ) )continue;
			
			require_once $p->php;
			$className = 'JFormSPlugin'.$p->name;
			//PHP 4 fix
			$methodExists = false;
			eval( '$x = new '.$className.'();$methodExists = method_exists($x,"'.$name.'");$x=null;' );
			//End of PHP 4 Fix
			
			if( !$methodExists )return null;
			$response[$p->name] = call_user_func_array(array($className,$name),$params);
	
		}
		return $response;
	}
	

	/**************************************************************************************************************/
	
	/**
	 *  Loads the active storage plugins "listed in plugins/plugin.list" 
	 *
	 * @return void
	 */
	function loadPlugins()
	{
		//Performance check
		if( !empty($this->loadedPlugins)){
			return;
		}
		
		$path = JFORMS_BACKEND_PATH.DS."plugins".DS."elements".DS;
		$plugins = $this->_getPlugins();
		foreach($plugins as $plugin){
			$p = $this->_loadPlugin( $plugin );
			if($p != null){
				$this->loadedPlugins[$plugin] = $p;
			}
		}
	}


	
	/**
	 *  Loads a single Storage plugin from XML file
	 *
	 * @return object : an object that holds information that was loaded from the XML file
	 */
	function _loadPlugin( $name )
	{
		$xml = new JSimpleXML();
		$pluginCorePath  = JFORMS_BACKEND_PATH.DS.'plugins'.DS.'storage'.DS.$name.DS;
		$pluginMediaPath = JPATH_ROOT.DS.'media'.DS.'com_jforms'.DS.'plugins'.DS.'storage'.DS.$name.DS;
		$filename = $pluginCorePath.$name.".xml";
		
	
		$xml->loadFile($filename);
		$root = $xml->document;
	
		
		$a = $root->attributes();
		if( $a['type'] != 'storage' ){
			unset($xml);
			return null;
		}
		
		$plugin = new stdClass();
		$plugin->name = $root->name[0]->data();
		$plugin->php = $pluginCorePath . $name . '.php';
		$plugin->paramXML = $pluginCorePath . 'param.xml';

		//Load language files
		$lang =& JFactory::getLanguage();
		$lang->load('storage.'.ucfirst($name),JFORMS_BACKEND_PATH,null,false);

		
		return $plugin;
	}

	/**
	 *  Reads the "plugins.list" file and returns an array containing the names of storage plugions 
	 *
	 * @return array : storage plugins to be loaded
	 */
	function _getPlugins()
	{
		$plugins = file(JFORMS_BACKEND_PATH.DS."plugins".DS."storage".DS."plugins.list");
		for($i=0;$i<count($plugins);$i++){
			$plugins[$i] = trim($plugins[$i]);
		}
		return $plugins;
	}

}