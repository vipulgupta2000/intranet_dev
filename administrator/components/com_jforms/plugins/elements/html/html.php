<?php
/**
* HTML Element plugin
*
* @version		$Id: html.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
/**
 * HTML Element plugin 
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
 */
class JFormEPluginHtml extends JFormEPlugin{

	function render( $elementData ){
		$p = JArrayHelper::toObject($elementData->parameters);
		$p->htmlValue = base64_decode($p->htmlValue);
		return "<div class='{$p->css}'>{$p->htmlValue}</div>";
	}
}