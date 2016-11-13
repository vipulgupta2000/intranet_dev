<?php
/**
* Random code Element plugin
*
* @version		$Id: random.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * Random code plugin
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
*/
class JFormEPluginRandom extends JFormEPlugin{

	function getSQL( $elementData, $criteria ){
		$db =& JFactory::getDBO();
		$value = $db->getEscaped($criteria->value);
		if(!strlen(trim($value)))return '';
		$field = $elementData->parameters['hash'];
		$sql = " `$field` = '$value' ";	
		return $sql;
	}
	
	function render( $elementData ){
		$p = JArrayHelper::toObject($elementData->parameters);
		$htmlId = $p->hash.'_'.$elementData->id;
		return _line("<input type='hidden' value='' name='$p->hash' id='$htmlId' />",2);
	}
	
	function beforeSave($elementData, $input){
		
		$p = JArrayHelper::toObject($elementData->parameters);
		
		//Based on http://www.phptoys.com/e107_plugins/content/content.php?content.42
		$code = '';
		list($usec, $sec) = explode(' ', microtime());
		srand((float) $sec + ((float) $usec * 100000));
		$counter   = 0;
		while ($counter < intval($p->length)) {
			$actChar = substr($p->validCharacters, rand(0, strlen($p->validCharacters)-1), 1);
			// All character must be different
			if (!strstr($code, $actChar)) {
				$code .= $actChar;
				$counter++;
			}
		}
		return $p->prefix.$code.$p->suffix;
	}
}