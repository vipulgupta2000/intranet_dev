<?php
/**
* Base class for Storage Plugins
*
* These plugins handle data throught out JForms
*
* @version		$Id: JFormSPlugin.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @author		Mostafa Muhammad <mostafa.mohmmed@gmail.com>
* @copyright	Copyright (C) 2009 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();


/**
* Base class, All Storage plugins should inherit from class
*
* @package		Joomla
* @subpackage	JForms.Plugins
*/
class JFormSPlugin  extends JObject {
	
	
	/**
	 * Returns records from table based on $criteria
	 *
	 * @param $form Object Form Object
	 * @param $fields Array Field names
	 * @param $start int Starting record
	 * @param $rpp int Records Per Page
	 * @param $criteria , Object    : structured as
	 *  $criteria->field_hash <- Object containing critieria specific to each field type
	 * @access	public
	 * @return	array structured as follows<br />
	 *				'total' 		=> number of records returned by the query
	 *				'loaded_fields' => comma dilmated string containing names of the loaded table fields,
	 *				'records' 		=> a numerically indexed array of records
	 */
	function searchRecords($form, $fields, $start, $rpp, $criteria ){
		return array('total' => 0,
					'loaded_fields' => '',
					'records' => array());
	}
	
	/**
	 * Called when a form is first created
	 *
	 * @param $form Object Form Object
	 * @access	public
	 * @return	true on success, false on failure
	 */
	function onFormCreate( &$form ){return true;}

	/**
	 * Called when a form is modified and is about to be saved
	 *
	 * @param $form Object Form Object
	 * @access	public
	 * @return	true on success, false on failure
	 */
	function onFormSave( &$form ){return true;}
	
	/**
	 * Called when a form is about to be deleted
	 *
	 * @param $form Object Form Object
	 * @access	public
	 * @return	true on success, false on failure
	 */
	function onFormDelete( &$form ){return true;}

	/**
	 * Retrieves records from storage medium based on ids
	 *
	 * @access	public
	 * @param object $form The form object whos records are to be retrieved
	 * @param array $ids Record Ids , if null the method will return all records
	 * @return array , id indexed array of records on success, null on failure 
	 */
	function getRecords( $form, $ids ){return array();}
	
	/**
	 * Saves a record to the storage medium
	 *
	 * @access	public
	 * @param object $form The form object
	 * @param array $data Data coming from submitted form , indexed by hash values
	 * @return	bool , true on success,  false on failure
	 */
	function saveRecord( $form, $data ){return true;}

}