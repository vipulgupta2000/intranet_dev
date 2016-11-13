<?php
/**
* Upgrade controller
*
* @version		$Id: upgrade.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Controllers
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

jimport('joomla.application.component.controller');

/**
 * Upgrade backend Controller
 *
 * @package    Joomla
 * @subpackage JForms.Controllers
 */
class UpgradeController extends JController{

	/**
	 * constructor (registers additional tasks to methods)
	 *
	 * @return void
	 */
	function __construct(){
		parent::__construct();
		// Register Extra tasks
		$this->registerTask( 'upgrade'  , 'upgrade');
	}

	/**
	 * Task handler (Upgrades JForms to the current version, called via Ajax)
	 *
	 * @return void
	 */
	function upgrade(){
		
		JRequest::checkToken('get') or jexit( 'Invalid Token' );

		$sourceVersion	= JRequest::getInt( 'src' , 'get' );
		$destVersion	= JRequest::getInt( 'dest', 'get' );
		
		if(JFile::exists(JFORMS_BACKEND_PATH.DS.'installation'.DS.'upgrade'.DS.'upgrade_'.$sourceVersion.'_'.$destVersion.'.php')){
			include_once JFORMS_BACKEND_PATH.DS.'installation'.DS.'upgrade'.DS.'upgrade_'.$sourceVersion.'_'.$destVersion.'.php';
		} else {
			jexit( 'Invalid version' );
		}
		$className      = "JFormsUpgrade_{$sourceVersion}_{$destVersion}";
		$step			= JRequest::getInt( 'step' , 'get' );
		$param			= JRequest::getVar( 'param', 'get' );
		
		$return = call_user_func_array(array($className, 'process'), array( $step, $param));

		if( strpos( $return, JURI::base()) === false  ){
			jexit( $return );		
		} else { 
			$this->setRedirect( $return );
		}
	}
	
	/**
	 * Default Task handler (Displays Upgrade view)
	 *
	 * @return void
	 */
	function display()
	{
		$document =& JFactory::getDocument();

		$viewType	= $document->getType();
		$viewName	= 'upgrade';
		$viewLayout	= 'default';
		$view = & $this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->_basePath));

		// Set the layout
		$view->setLayout($viewLayout);
		
		// Display the view
		$view->display();
	}
}
