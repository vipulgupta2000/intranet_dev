<?php
/**
* A Form element that lists installed form themes for JForms
* 
* @version		$Id: jformstheme.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Elements
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * A Form element that lists installed form themes for JForms
 *
 * @package    Joomla
 * @subpackage JForms.Elements
 */
class JElementjformstheme extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'jformstheme';
	
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
		$themeFiles = JFolder::files(JFORMS_FRONTEND_PATH.DS.'views'.DS.'form'.DS.'tmpl','^(.+)_thank.php$');
		$ctrl	= $control_name .'['. $name .']';
				
		$themes = array();
		foreach($themeFiles as $f){
			$t = str_replace( '_thank.php', '', $f );
			$themes[] = array('value' => $t, 'text' => $t);
		}
		return JHTML::_('select.genericlist', $themes, $ctrl, null    , 'value', 'text', $value, $control_name.$name );
	
	}
}
