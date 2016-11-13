<?php
/**
* A Form element that lists user groups, allows user to select what groups can use the form
* 
* @version		$Id: jformsusergroup.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Elements
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * A Form element that lists user groups, allows user to select what groups can use the form
 *
 * @package    Joomla
 * @subpackage JForms.Elements
 */
class JElementJFormsUserGroup extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'JFormsUserGroup';
	
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
		
		$acl	=& JFactory::getACL();
		$guestACL = new stdClass();
		$guestACL->value	= 0;
		$guestACL->text		= JText::_('Guest');
		$guestACL->disable	= false;
		
		$gtree  = array($guestACL);
		
		$gtree = array_merge($gtree, $acl->get_group_children_tree( null, 'USERS', false ));
		$ctrl	= $control_name .'['. $name .']';

	
		$attribs	= ' ';
		$v = $node->attributes('size');
		if ($v) {
			$attribs	.= 'size="'.$v.'"';
		}
		$v = $node->attributes('class');
		if ($v) {
			$attribs	.= 'class="'.$v.'"';
		} else {
			$attribs	.= 'class="inputbox"';
		}
		$attribs	.= 'multiple="multiple"';
		$ctrl		.= '[]';

		return JHTML::_('select.genericlist',   $gtree, $ctrl, $attribs, 'value', 'text', explode(',',$value), $control_name.$name );
	}
}
