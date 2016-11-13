<?php
/**
* Global definitions and some useful functions
*
* @version		$Id: globals.php 366 2010-03-26 12:50:13Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Core
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

//Debug
define('JFORMS_DEBUG_STATE'   , 0 );

define( 'JFORM_PLUGIN_STORAGE', 0 );
define( 'JFORM_PLUGIN_ELEMENT', 1 );
define( 'JFORM_PLUGIN_EXPORT' , 2 );

define( 'JFORMS_TYPE_NORMAL'  , 0 );
define( 'JFORMS_TYPE_PROFILE' , 1 );

define('JFORMS_FS_PATH'      , JPATH_ROOT.DS.'media'.DS.'com_jforms'.DS.'files'.DS);
define('JFORMS_FS_URL'       , JURI::root().'media/com_jforms/files/');
define('JFORMS_BACKEND_PATH' , JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jforms');
define('JFORMS_FRONTEND_PATH', JPATH_ROOT.DS.'components'.DS.'com_jforms');

define('JFORM_SYSTEM_FIELDS','id');


//Display errors only if in Debug state
if(JFORMS_DEBUG_STATE > 0){ini_set('display_errors',1);error_reporting(E_ALL);}

//Temporary debug function
function d($v){if(JFORMS_DEBUG_STATE>0){echo "<pre>";var_dump($v);echo "</pre>";}}

//gets a handle to the global plugin manager object
function &JFormsGetPluginManager(){return $GLOBALS['JFormGlobals']['JFormsPlugin'];}

//Initalizes plugin manager
function JFormsInitializePluginManager(){require_once (JFORMS_BACKEND_PATH.DS.'libraries'.DS.'pluginmanager'.DS.'manager.php');$GLOBALS['JFormGlobals'] = array();$GLOBALS['JFormGlobals']['JFormsPlugin'] = new JFormsPluginController();}


if ( !function_exists( 'property_exists' ) ) {
    function property_exists( $class, $property ) {
        if ( is_object( $class ) ) {
            $vars = get_object_vars( $class );
        } else {
            $vars = get_class_vars( $class );
        }
        return array_key_exists( $property, $vars );
    }
}

function _isURL( $url ){
	return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}

	
if ((version_compare(phpversion(), '5.0') < 0) && !function_exists( 'clone' ) ) {
    eval('
    function clone($object) {
      return $object;
    }
    ');
}

function indexByHash( $fields ){
		
	$newArray = array();
	foreach( $fields as $f ){
		
		if( !isset( $f->parameters['hash'] ))continue;
		$hash = $f->parameters['hash'];
		$newArray[$hash] = $f;
	}
	return $newArray;

}

/**
 * A utility function that is used to output text that is properly indented for readability.
 *
 * @param string $line the text to be printed to a new line
 * @param int $level the level of indention of the line
 * @return string the passed text indented by "level" tabs
 */
function _line($line, $level)
{
	$tabs = str_repeat( "\t" , $level );
	return $tabs.$line."\n";
}