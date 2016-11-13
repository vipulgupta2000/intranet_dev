<?php
/**
* Hidden Element plugin
*
* @version		$Id: hidden.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
/**
 * Hidden Element plugin 
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
 */
class JFormEPluginHidden extends JFormEPlugin{

	function getSQL( $elementData, $criteria ){
		$db =& JFactory::getDBO();
		$value = $db->getEscaped($criteria->value);
		if(!strlen(trim($value)))return '';
		$field = $elementData->parameters['hash'];
		$sql = " `$field` = '$value' ";	
		return $sql;
	}
	
	function translate( $elementData, $input, $format='html', $segment=''){return $input;}

	function render( $elementData ){
		$p = JArrayHelper::toObject($elementData->parameters);
		$htmlId = $p->hash.'_'.$elementData->id;

		$default = property_exists($elementData,'defaultValue' )?$elementData->defaultValue:$p->defaultValue;
		$default = htmlspecialchars($default,ENT_QUOTES);

		return _line("<input type='hidden' value='$default' name='$p->hash' id='$htmlId' />",2);
	}
	
	function beforeSave($elementData, $input){return $input;}
}