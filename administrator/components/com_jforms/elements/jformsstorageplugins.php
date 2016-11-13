<?php
/**
* A Form element that lists supported storage plugins for JForms
* 
* @version		$Id: jformsstorageplugins.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Elements
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * A Form element that lists supported storage plugins for JForms
 *
 * @package    Joomla
 * @subpackage JForms.Elements
 */
class JElementJFormsStoragePlugins extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'JFormsStoragePlugins';
	
	/**
	* Generates the HTML for the element
	*
	* @access	public
	* @param string index of the element in the HTML array
	* @param string default value for the element
	* @param object the XML parameters object
	* @param string HTML array name
	* @return string HTML for the element
	*/
	function fetchElement($name, $value, &$node, $control_name)
	{

		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('storage');
		$value = explode(',', $value);
		
		//Force Database to be enabled
		if( !in_array( 'Database', $value )){
			array_push( $value, 'Database' ); 
		}
		

		$attribs	= ' ';
		$attribs	.= 'class="inputbox" style="width:150px;height:90px;"';
		$attribs	.= 'multiple="multiple"';
		$ctrl	     = $control_name .'['. $name .']';
		$ctrl		.= '[]';

		return JHTML::_('select.genericlist',   $pManager->settings['storage'] , $ctrl, $attribs, 'name', 'name', $value, $control_name.$name );
	}
}
