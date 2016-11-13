<?php
/**
* A simple Form element that displays an HTML heading tag
*
* @version		$Id: jformsheading.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Elements
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * A simple Form element that displays an HTML heading tag
 *
 * @package    Joomla
 * @subpackage JForms.Elements
 */
class JElementjformsheading extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'jformsheading';
	
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
		if ($value) {
			return '<h2>'.JText::_($value).'</h2>';
		} else {
			return '<hr />';
		}
	}
}
