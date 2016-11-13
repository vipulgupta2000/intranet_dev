<?php
/**
* Form list View
*
* @version		$Id: view.html.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Views
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * Form list View
 *
 * @package    Joomla
 * @subpackage JForms.Views
 */
class FormsViewForms extends JView
{
	/**
	 * List view display method
	 *
	 * Displays a list of all forms available in the database showing 
	 *   - id
	 *   - Title
	 *   - Storage plugins
	 *   - Author
	 *   - Date of creation
	 *   - Database table
	 *
	 * @return void
	 **/
	function display( $forms )
	{
	
		//JHTML::_('stylesheet', 'reset.css', 'media/com_jforms/styles/general/');
		JHTML::_('stylesheet', 'forms-backend.css', 'media/com_jforms/styles/');
	
		//Toolbar
		JToolBarHelper::title(   JText::_( 'Forms' ), 'jforms-logo' );
		JToolBarHelper::deleteList();
		JToolBarHelper::customX( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );		
		JToolBarHelper::editListX();
		JToolBarHelper::addNew();

		//Send data to the view
		$this->assignRef('forms', $forms);

		//Display the template
		parent::display();
	}
}
