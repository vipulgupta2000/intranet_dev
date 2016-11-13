<?php 
/**
* CSV Export plugin
*
* @version		$Id: Csv.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * CSV Export plugin 
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
 */
class JFormXPluginCsv extends JFormXPlugin{
	
	function onExport( $pluginParameters,$requestParameters, $data ){
		
		$filename = 'exported-data.csv';
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		$records = $data['records'];
		
		$labels  = $requestParameters['labels'];
		
		echo '"'.implode('","',$labels)."\"\r\n";
	
		foreach( $records as $r ){
			echo '"'.implode('","',$r)."\"\r\n";
		}

	}
	
}