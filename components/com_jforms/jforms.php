<?php
/**
* Frontend entry point for JForms Component
*
* @version		$Id: jforms.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Core
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'globals.php' ;
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'libraries'.DS.'pluginmanager'.DS.'manager.php';
require_once JPATH_COMPONENT.DS.'controller.php';

JHTML::addIncludePath ( JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers' );

JFormsInitializePluginManager();

// Create the controller "Frontend"
$classname	= 'FrontendController';
$controller = new $classname();

// Perform the Request task
$controller->execute( JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();