<?php
/**
* Joomla! User information Element plugin
*
* @version		$Id: juser.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * Joomla! User information plugin
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
*/
class JFormEPluginJuser extends JFormEPlugin{

	function render( $elementData ){ 	
	
		$p = JArrayHelper::toObject($elementData->parameters);
		return _line("<input type='hidden' value='' name='{$p->hash}' />",2);
	
	}

	function _getSegment( $segment, $input ){
	

		if($input == null){

			$input = new stdClass();
			$input->id = 0;
			$input->username = 'Guest';
			$input->name = 'Guest';
			$input->type = 'Guest';
			$input->email = '';
		}
		if( $segment == '' )return $input;
		
		switch( strtolower($segment) ){
			case 'id':
				return $input->id;
			case 'username':
				return $input->username;
			case 'name':
				return $input->name;
			case 'email':
				return $input->email;
			case 'type':
				return $input->type;
		}
		return null;
	}
	function translate ( $elementData, $input, $format='html', $segment='' ){
		
		$segment = trim($segment);

		$object = JFormEPluginJuser::_getSegment( $segment, unserialize(base64_decode( $input )));
		if( is_null($object) )return null;

		$output  = '';
		switch( $format ){
		
			case 'object':
				return $object;
			
			case 'raw':
				//Return the requested value immediatley if we have a custom segment
				if( $segment != '')return $object;

				//A guest?
				if( $object->type == 'Guest' ){
					$output = JText::_('Guest');
					break;
				}
				//Otherwise
				$output .= JText::_('ID').':'.$object->id;
				$output .= "\n".JText::_('Username').':'.$object->username; 
				$output .= "\n".JText::_('Name').':'.$object->name;
				$output .= "\n".JText::_('User type').':'.$object->type;
				$output .= "\n".JText::_('E-mail').':'.$object->email;
			break;

			case 'html':
				//Return the requested value immediatley if we have a custom segment
				if( $segment != '')return $object;
				
				//A guest?
				if( $object->type == 'Guest'){
					$output = JText::_('Guest');
					break;
				}
				//Otherwise
				$output .= '<strong>'.JText::_('ID').'</strong>: ' . $object->id.'<hr />';
				$output .= "\n".'<strong>'.JText::_('Username').'</strong>: ' . $object->username .'<hr />';
				$output .= "\n".'<strong>'.JText::_('Name').'</strong>: ' . $object->name.'<hr />';
				$output .= "\n".'<strong>'.JText::_('User type').'</strong>: ' . $object->type.'<hr />';
				$output .= "\n".'<strong>'.JText::_('E-mail').'</strong>: ' . $object->email.'<hr />';
			break;
				
			default:
				return null;
		}
		
		return $output;

	}	
	
	function beforeSave( $elementData, $input ){
	
		//We don't need any input
		$input = null;
		
		$user =& JFactory::getUser();
		
		if( $user->guest ){
			return base64_encode(serialize(null));
		}
		
		$output = new stdClass();
		$output->id = $user->id;
		$output->name = $user->name;
		$output->username = $user->username;
		$output->email = $user->email;
		$output->type = $user->usertype;
		
		return base64_encode(serialize($output));

	}
}
			 