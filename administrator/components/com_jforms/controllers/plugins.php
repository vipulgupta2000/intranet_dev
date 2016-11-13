<?php
/**
* Plugins Ajax calls controller
*
* @version		$Id: plugins.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Controllers
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

jimport('joomla.application.component.controller');

/**
 * Plugins Ajax calls controller
 *
 * @package    Joomla
 * @subpackage JForms.Controllers
 */
class PluginsController extends JController{

	/**
	 * constructor (registers additional tasks to methods)
	 *
	 * @return void
	 */
	function __construct(){
		parent::__construct();
		$this->registerTask( 'invoke'  , 'invoke');
	}
	
	/**
	 * Task handler (Invokes a method on a given plugin, called via Ajax, this allows element plugins to access server-side proceedures)
	 *
	 * @return void
	 */
	function invoke(){
		
		JRequest::checkToken('get') or jexit( 'Invalid Token' );
		
		$pManager =& JFormsGetPluginManager();
		
		require_once JFORMS_BACKEND_PATH.DS.'libraries'.DS.'Services_JSON'.DS.'Services_JSON.php';
	
		//Decode JSON value
		
		$json = new Services_JSON();
		
		$method = JRequest::getVar( 'method' , '', 'get' );
		list($pluginType, $pluginName) = explode('.',JRequest::getVar( 'plugin' , '', 'get' ));
		$parameters = $json->decode(JRequest::getVar( 'parameter'   , '', 'get' ));
		
		$pManager->loadPlugins( $pluginType );
		$output = $pManager->invokeMethod($pluginType, $method, array($pluginName), array( $parameters ) );
		jexit( $output );
	}
}