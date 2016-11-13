<?php
/**
* Base class for Export Plugins
*
* These plugins handle exports
*
* @version		$Id: JFormXPlugin.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @author		Mostafa Muhammad <mostafa.mohmmed@gmail.com>
* @copyright	Copyright (C) 2009 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();


/**
* Base class, All Export plugins should inherit from this class
*
* @package		Joomla
* @subpackage	JForms.Plugins
*/
class JFormXPlugin extends JObject {
	
	/**
	 * Performs export
	 *
	 * @param $pluginParameters array Additional Parameters specified by the user "Usually used to define the range"
	 * @param $requestParameters array information about the reqeust that resulted in the 
	 * @param $data array containing 
 	 *				'total' 		=> number of records returned by the query
	 *				'loaded_fields' => comma dilmated string containing names of the loaded table fields,
	 *				'records' 		=> a numerically indexed array of records
	 * @access	public
	*/
	function onExport( $pluginParameters,$requestParameters, $data ){
		
		$filename = 'exported-data.txt';
		header('Content-type: text/txt');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		
	}
	
}