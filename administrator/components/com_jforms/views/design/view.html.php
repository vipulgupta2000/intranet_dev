<?php
/**
* Design View
*
* @version		$Id: view.html.php 373 2010-03-27 12:34:45Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Views
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

/**
 * Design View
 *
 * @package    Joomla
 * @subpackage JForms.Views
 */
class FormsViewDesign extends JView
{
	/**
	 * Design view display method
	 *
	 * The WYSIWYG form design environment , Where all the magic is going to happen 
	 *
	 * @return void
	 **/
	function display( $form )
	{	
		//Disable top menu "Thanks Ercan :)"
		JRequest::setVar('hidemainmenu', 1);		
		
		//Loads latest mootools version
		JHTML::_('JForms.General.mootools');
		JHTML::_('JForms.General.IE');
		
		JHTML::_('stylesheet', 'design-backend.css'  , 'media/com_jforms/styles/');
		JHTML::_('stylesheet', 'design-form.css'     , 'media/com_jforms/styles/');
		
		//Toolbar
		JToolBarHelper::title(   JText::_( 'Design' ), 'jforms-design' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
		
		$this->assignRef('form' ,$form);

		//Display the template
		parent::display();
		
	}
}