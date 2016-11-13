<?php
/**
* Backend Entry point for JForms Component
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

JTable::addIncludePath( JPATH_COMPONENT_ADMINISTRATOR.DS.'tables' );
JHTML::addIncludePath( JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers' );

require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'globals.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'libraries'.DS.'version'.DS.'version.php';

JFormsInitializePluginManager();

$controller = JRequest::getWord('controller');
if($controller == ''){
	$controller = 'Forms';
} else {
	$controller = ucfirst(strtolower($controller));
}

JSubMenuHelper::addEntry(JText::_('Forms')    , 'index.php?option=com_jforms&controller=Forms'   ,$controller=='Forms'  );
JSubMenuHelper::addEntry(JText::_('Upgrade')  , 'index.php?option=com_jforms&controller=Upgrade' ,$controller=='Upgrade');
	
$controllerFilename  = JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.strtolower($controller).'.php';
$controllerClassname = $controller.'Controller';

if(!JFile::exists($controllerFilename)){
	JError::raiseError( 500, "Couldn't find file $controllerFilename" );
}

require_once $controllerFilename;

if(!class_exists($controllerClassname)){
	JError::raiseError( 500, "Couldn't find class $controllerClassname" );
}

$controller = new $controllerClassname();

// Perform the Request task
$controller->execute( JRequest::getVar('task') );

// Redirect if set by the controller
$controller->redirect();