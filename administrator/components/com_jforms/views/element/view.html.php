<?php
/**
* Element View
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
 * Element View (Handles the Form Selection dialog in menu manager)
 *
 * @package    Joomla
 * @subpackage JForms.Views
 */
class FormsViewElement extends JView
{
	/**
	 * Element view display method
	 *
	 * Displays a list of all forms available in the database
	 *
	 * @return void
	 **/
	function display($forms)
	{
		//Send data to the view
		$this->assignRef('forms', $forms);

		//Display the template
		parent::display();
	}
}
