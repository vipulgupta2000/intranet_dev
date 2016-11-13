<?php
/**
* Forms Selector HTML Element
*
* @version		$Id: form.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Elements
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Forms Selector HTML Element, allows the user to pick a form from the Database
 *
 * @package    Joomla
 * @subpackage JForms.Elements
 */
class JElementForm extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Form';
	
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
		global $mainframe;

		$db			=& JFactory::getDBO();
		$doc 		=& JFactory::getDocument();
		$fieldName	= $control_name.'['.$name.']';
		
		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jforms'.DS.'tables' );
		$form =& JTable::getInstance( 'Forms', 'Table' );
		if ( $value ) {
			$form->load( $value );
		} else {
			$form->title = JText::_('Select a Form');
		}
	
		$js = "
		function jSelectForm(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			document.getElementById('sbox-window').close();
		}";
		$doc->addScriptDeclaration($js);

		$link = 'index.php?option=com_jforms&amp;task=element&amp;tmpl=component';

		JHTML::_('behavior.modal', 'a.modal');
		$html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($form->title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('Select a Form').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}">'.JText::_('Select').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';

		return $html;
	}
}