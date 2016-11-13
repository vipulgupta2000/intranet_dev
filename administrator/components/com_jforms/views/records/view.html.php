<?php
/**
* Records View
*
* @version		$Id: view.html.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Tables
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * Records View
 *
 * @package    Joomla
 * @subpackage JForms.Tables
 */
class RecordsViewRecords extends JView
{

	/**
	 * Records view display method
	 *
	 * Displays Records (Stored data)
	 *
	 * @return void
	 **/
	function display($form)
	{
		if( !count($form->records)){
			echo '<div style="font-size:150%;text-align:center;color:red;font-weight:bold">'.JText::_('No records stored yet').'<br /><br /><a href="javascript:history.back();">&lt;&lt; '.JText::_('Go Back').'</a></div>';
			return;
		}
	
		//JHTML::_('stylesheet', 'reset.css'          , 'media/com_jforms/styles/general/');
		JHTML::_('stylesheet', 'records-backend.css', 'media/com_jforms/styles/');
		JHTML::_('stylesheet', 'grid.css'			, 'media/com_jforms/styles/');

		JRequest::setVar('hidemainmenu', 1);		
		
		JHTML::_('JForms.General.mootools');

		JToolBarHelper::title(   JText::_( 'Records' ), 'jforms-records' );
		JToolBarHelper::back();

		$this->assignRef('form', $form);
	
		//Display the template
		parent::display();
	}
}