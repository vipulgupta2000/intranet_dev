<?php
/**
* Frontend form View for JForms Component
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
 * Form View
 *
 * @package    Joomla
 * @subpackage JForms.Views
 */
class FrontendViewForm extends JView
{

	function thank( $form ){

		JHTML::_('stylesheet', $form->theme.'.css' , 'media/com_jforms/styles/themes/');
		
		$this->assignRef('form', $form );
		
		parent::display();
		
	}

	/**
	 * Form view display method
	 *
	 * Displays requested form
	 *
	 * @return void
	 **/
	function form($form)
	{
		global $Itemid;
		
		JHTML::_('stylesheet', $form->theme.'.css' , 'media/com_jforms/styles/themes/');

		$user   =& JFactory::getUser();
		$this->assignRef('form'     , $form );
		$this->assignRef('user'     , $user );
		$this->assignRef('Itemid'  , $Itemid );
		

		parent::display();
	}
}