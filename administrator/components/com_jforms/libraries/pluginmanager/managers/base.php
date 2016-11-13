<?php
/**
* Base plugin Manager class
*
* Plugin manager classes manage one type of plugins "e.g. element plugins" , It loads them and invokes methods on them
*
* @version		$Id: base.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Core
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

/**
* Base class, All plugin managers should inherit this class
*
* @package		Joomla
* @subpackage	JForms.Core
*/
class JFormsPluginManager extends JObject{

	function JFormsPluginController(){$this->__construct();}
	
	/**
	 * @var array $loadedPlugins Stores loaded plugins parameters and settings
	*/
	var $loadedPlugins = null;
	
	/**
	 * Gets all stored information about this type of plugins , for instance on 
	 * a elementPluginManager this will return all information about elements 
	 * their names, their storage requirements their paramters ,etc...
	 *
	 * @access	public
	 * @return	a reference to the information structure
	 */
	function &getSettings(){return $this->loadedPlugins;}
	
	/**
	* Invokes a method on one or more plugins of a certian type "e.g. invoke onFormSave on all storage plugins"
	*
	* @access	public
	* @param	string $name Name of the method "See each plugin type for method names"
	* @param	array $which Names of plugins upon which to invoke this method, if null will invoke the requested method on all loaded plugins
	* @param	array $params parameters that are to be sent to the method
	* @return	array indexed by plugin names containing responses from every plugin
	*/
	function invokeMethod( $name, $which, $params ){return null;}
	
	/**
	* Reads the corresponding "plugins.list" files and loads all plugins located there
	*
	* @access	public
	* @return	void
	*/
	function loadPlugins(){;}
}