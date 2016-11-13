<?php
/**
* Base class for Element Plugins
*
* These plugins handle every aspect of elements "textboxes, buttons, radio list , etc..."
*
* @version		$Id: JFormEPlugin.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @author		Mostafa Muhammad <mostafa.mohmmed@gmail.com>
* @copyright	Copyright (C) 2009 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();


/**
* Base class, All Element plugins should inherit from class
*
* @package		Joomla
* @subpackage	JForms.Plugins
*/
class JFormEPlugin extends JObject {

	/**
	* Returns custom SQL "WHERE" Clause based on supplied criteria
	*
	* @access	public
	* @param	object $elementData information about the element instance
	* @param	criteria $criteria user supplied search parameters
	  @return	string Cleaned up SQL "WHERE" Clause, Without the "WHERE" keyword
	*/
	function getSQL( $elementData, $criteria ){return '';}

	/**
	* Translates stored element data into a particular format prior to sending it to the view
	*
	* @access	public
	* @param	object $elementData information about the element instance
	* @param	string $input raw data coming from database
	* @param	string $format requested output format , every element should support "raw,html,object"
	* @param	string $segment the name of segment of data requested e.g. a value of "month in (Date element)" would return the translated month and not the whole date 
	* @return	mixed translated input in the required format or null if format isn't supported
	*/
	function translate( $elementData, $input, $format='html', $segment='' ){return $input;}
	
	/**
	* Renders the form element
	*
	* @access	public
	* @param	object $elementData information about the element instance
	* @return	string HTML output that renders the form element
	*/
	function render( $elementData ){return '';}
	 
	/**
	* Returns the Javascript client side validation code
	*
	* @access	public
	* @param	object $elementData information about the element instance
	* @return	string Javascript code that validate this element
	*/
	function jsValidation( $elementData ){return '';}
	
	/**
	* Returns the Javascript code that clears any Javascript error notices produced by code in "jsValidation" method
	*
	* @access	public
	* @param	object $elementData information about the element instance
	* @return	string Javascript code that removes any Javascript error notifications
	*/
	function jsClearErrors( $elementData ){return '';}

	/**
	* Prepares the input data before sending to database
	*
	* @access	public
	* @param	object $elementData information about the element instance
	* @param	string $input input data coming from the submitted form
	* @param	array $fsInfo Allocated File system resource information "Path to the directory and the corresponding url", This will be null unless the element has file system storage needs 
	* @return	string Javascript code that validate this element
	*/
	function beforeSave( $elementData, $input, $fsInfo=null ){return $input;}

	/**
	* Validates input on the server side
	*
	* @access	public
	* @param	object $elementData information about the element instance
	* @param	string $input input data coming from the submitted form
	* @return	string Error message to present to the user or empty string if input passed validation 
	*/
	function validate( $elementData, $input ){return '';}

}