<?php
/**
* Upgrade view
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
 * Upgrade View
 *
 * @package    Joomla
 * @subpackage JForms.Views
 */
class UpgradeViewUpgrade extends JView
{
	/**
	 * Upgrade view display method
	 *
	 * Displays Upgrade view (Where the user can upgrade from earlier versions of JForms)
	 *
	 * @return void
	 **/
	function display()
	{
	
		JHTML::_('stylesheet', 'upgrade-backend.css', 'media/com_jforms/styles/');
	
		//JRequest::setVar('hidemainmenu', 1);		
		JHTML::_('JForms.General.mootools');

		
		//Toolbar
		JToolBarHelper::title(   JText::_( 'Upgrade' ), 'jforms-upgrade' );

		//Display the template
		parent::display();
	}
}
