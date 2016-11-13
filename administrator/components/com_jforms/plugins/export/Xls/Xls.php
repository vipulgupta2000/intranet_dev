<?php 
/**
* XLS Export plugin
*
* @version		$Id: Xls.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

require_once dirname(__FILE__).DS.'excel-2.0'.DS.'Writer.php';

/**
 * XLS Export Element plugin 
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
 */
class JFormXPluginXls extends JFormXPlugin{
	
	
	function _u($s){return mb_convert_encoding( $s, 'UTF-16LE', 'UTF-8');}

	function onExport( $pluginParameters, $requestParameters, $data ){
		
		// Creating a workbook
		$workbook = new Spreadsheet_Excel_Writer();
		$workbook->setVersion(8);
		
		$worksheet =& $workbook->addWorksheet();
		$worksheet->setInputEncoding('UTF-16LE');
		
		// Creating the format
		$labelFormat =& $workbook->addFormat();
		$labelFormat->setBold();
		$labelFormat->setBgColor('blue');
		$labelFormat->setFgColor('white');
	
		$records = $data['records'];
		$labels  = $requestParameters['labels'];
		
		for($i=0;$i<count($labels);$i++){
			$worksheet->write(0, $i, JFormXPluginXls::_u($labels[$i]), $labelFormat);
		}
		

		for($i=0;$i<count($records);$i++){
			for($j=0;$j<count($records[$i]);$j++){
				if (preg_match("/^([+-]?)(?=\d|\.\d)\d*(\.\d*)?([Ee]([+-]?\d+))?$/", $records[$i][$j]))
					$worksheet->writeNumber( $i+1, $j, $records[$i][$j]);
				else
					$worksheet->write( $i+1, $j, JFormXPluginXls::_u($records[$i][$j]) );
			}
		}
		$workbook->send('exported-data.xls');
		$workbook->close();
	}
	
}