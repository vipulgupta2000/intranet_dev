<?php
/**
* Element PluginManager class
*
* @version		$Id: element.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Core
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

require_once JFORMS_BACKEND_PATH.DS.'models'.DS.'JFormEPlugin.php';

/**
* Element PluginManager class
*
* @package		Joomla
* @subpackage	JForms.Core
*/
class JFormsElementPluginManager extends JFormsPluginManager{

	/**
	 * @var array $loadedPlugins Stores loaded plugins parameters and settings
	 */
	var $loadedPlugins = null;
	
	function &getSettings(){return $this->loadedPlugins;}
	
	function invokeMethod( $name, $which, $params ){
		
		//Deals with call_user_func_array warning as of PHP 5.3
		if( $params == null )$params = array();
		
		//Error checking
		if( $which == null || count( $which ) != 1 )return null;
		
		
		if( $which[0] == '_MANAGER' ){
			if( !method_exists($this,$name) )return null;
			return call_user_func_array(array($this,$name),$params);		
		}

		require_once $this->loadedPlugins[$which[0]]->php;
		$className = 'JFormEPlugin'.ucfirst($this->loadedPlugins[$which[0]]->name);
		//PHP 4 fix
		$methodExists = false;
		eval( '$x = new '.$className.'();$methodExists = method_exists($x,"'.$name.'");$x=null;' );
		//End of PHP 4 Fix
			
		if( !$methodExists )return null;
		return call_user_func_array(array($className,$name),$params);
	}

	function getCategorizedElements(){
		
		$this->loadPlugins();
		
		$categories = array();
		
		foreach( $this->loadedPlugins as $e ){
			
			if( !array_key_exists($e->group, $categories)){
				$categories[$e->group] = array();
			}
			$categories[$e->group][$e->name] = $e;
		}
		return $categories;
	}

	
	/**
	 *  Loads the active element plugins "listed in plugins/plugin.list" 
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
	 *  Loads a single Element plugin from XML file
	 *
	 * @return object : an object that holds information that was loaded from the XML file
	 */
	function _loadPlugin( $name )
	{
		$xml  =& JFactory::getXMLParser('Simple');
		$pluginCorePath  = JFORMS_BACKEND_PATH.DS.'plugins'.DS.'elements'.DS.$name.DS;
		$pluginMediaPath = JPATH_ROOT.DS.'media'.DS.'com_jforms'.DS.'plugins'.DS.'elements'.DS.$name.DS;
		$filename = $pluginCorePath.$name.".xml";
		
		$xml->loadFile($filename);
		$root = $xml->document;
	
		$a = $root->attributes();
		if( $a['type'] != 'element' ){
			unset($xml);
			return null;
		}
		
		$pluginURI  = JURI::root()."media/com_jforms/plugins/elements/$name/";
		
		$plugin = new stdClass();
		$plugin->name = $root->name[0]->data();
		$plugin->description = $root->description[0]->data();
		$plugin->limit = isset($a['limit'])?$a['limit']:0;
		$plugin->group = isset($a['group'])?$a['group']:'basic';
		$plugin->searchXML = $pluginCorePath . 'search.xml';
		$plugin->paramXML  = $pluginCorePath . 'parameters.xml';


		
		//Read <files>
		foreach( $root->files[0]->children() as $child ){
		
			$a = $child->attributes();
			$type = $a['type'];
			switch( $type ){
				
				case 'jsEntryPoint':
					$plugin->js = $pluginCorePath . $child->data();
					break;

				case 'phpEntryPoint':
					$plugin->php = $pluginCorePath . $child->data();
					break;

				case 'icon':
					$plugin->button = $pluginURI . $child->data();
					break;
			}
		}
		
		//Read <storage>
		if( !isset( $root->storage ) ){
			$plugin->storage = null;
		} else {
			$a = $root->storage[0]->attributes();
			
			$plugin->storage = new stdClass();
			$plugin->storage->type = $a['type'];
			
			$plugin->storage->size = 0;
			if( isset($a['size']))
				$plugin->storage->size = $a['size'];
			
			$plugin->storage->requirefs = false;
			if( isset( $a['requirefs'] ))
				$plugin->storage->requirefs = strtolower($a['requirefs'])=='true'?true:false;
			
		}
		
		$xml  =& JFactory::getXMLParser('Simple');
		
		//Read parameters.xml
		$xml->loadFile($plugin->paramXML);
		$root = $xml->document;
		
		$plugin->parameters = array();
		foreach( $root->params[0]->children() as $child ){
			
			$a = $child->attributes();
			$parameterName = $a['name'];
			$plugin->parameters[$parameterName] = new stdClass();
			$plugin->parameters[$parameterName]->name = $parameterName;

			$plugin->parameters[$parameterName]->valueType = $a['valuetype'];
			$plugin->parameters[$parameterName]->type = $a['type'];
	
			if( array_key_exists('translate',$a) && $a['translate'] == '1' ){
				$plugin->parameters[$parameterName]->translate = true;
			} else {
				$plugin->parameters[$parameterName]->translate = false;
			}
			
			$plugin->parameters[$parameterName]->default = $a['default'];
		}

		//Load language files
		$lang =& JFactory::getLanguage();
		$lang->load('element.'.ucfirst($name),JFORMS_BACKEND_PATH,null,false);

		return $plugin;

	}
	
	function hasStorageRequirements( $e ){
		$this->loadPlugins();
		if(
			property_exists($this->loadedPlugins[$e->type],'storage') && 
			$this->loadedPlugins[$e->type]->storage == null 
		   )return false;
		return true;
	}
	
	function getPluginsCategories(){
		
		$this->loadPlugins();
		
		$categories = array();
		
		foreach( $this->loadedPlugins as $e ){
			
			if( !array_key_exists($e->group, $categories)){
				$categories[$e->group]   = array();
			}
			$categories[$e->group][$e->name] = $e;
		}
		return $categories;
		
	}
	
	/**
	 *  Reads the "plugins.list" file and returns an array containing the names of element plugions 
	 *
	 * @return array : Element plugins to be loaded
	 */
	function _getPlugins()
	{
		$b = file_get_contents(JFORMS_BACKEND_PATH.DS."plugins".DS."elements".DS."plugins.list");
		$plugins = explode( "\r\n", $b );
		for($i=0;$i<count($plugins);$i++){
			$plugins[$i] = trim($plugins[$i]);
		}
		return $plugins;
	}

}