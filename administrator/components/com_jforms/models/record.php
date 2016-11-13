<?php
/**
* Record Model
*
* @version		$Id: record.php 365 2010-03-26 12:42:55Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Models
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

//Clean up is left to the bottom teir , I.E plugins , not the best design choice but helps avoid confusion

jimport('joomla.application.component.model');

/**
 * Record Model
 *
 * @package    Joomla
 * @subpackage JForms.Models
 */
class JFormsModelRecord extends JModel
{
	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Saves a record
	 *
	 * @access	public
	 * @param mixed id of the target form or instance of TableForms class
	 * @param array record data
	 * @return	bool true on success , false on failure
	 */
	function save( $form, $data ){
		
		if( is_integer( $form )){
			$formModel =& JModel::getInstance('form','JFormsModel');
			$form      = $formModel->get( $form );
		}
		if( !is_object( $form )){
			return false;
		}
		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('storage');
		$pManager->invokeMethod('storage','saveRecord', explode(',', $form->plugins), array( $form, $data ) );
		return true;
	}	
	
	/**
	 * Retrieves record data based on User ID "Used for profile mode forms"
	 *
	 * @access	public
	 * @param mixed id of the target form or instance of TableForms class
	 * @param int User ID whose record to be Retrieved
	 * @return	object record data on succes, null on failure
	 */
	function getByUid( $form, $uid ){
		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$pManager->loadPlugins('storage');
		
		if( is_integer( $form )){
			$formModel =& JModel::getInstance('form','JFormsModel');
			$form      = $formModel->get( $form );
		}
		if( !is_object( $form )){
			return null;
		}
		
		$fields = array('uid');
		foreach( $form->fields as $f ){
			if( $pManager->settings['element'][$f->type]->storage ){		
				$fields[] = $f->parameters['hash'];
			}
		}
		
		$start = 0;
		$rpp   = 1;
		$criteria = new stdclass();
		$criteria->uid = new stdclass();
		$criteria->uid->numbers = array( intval( $uid ) );
		$criteria->uid->mode    = 'or';
		
		foreach($form->fields as $f){
			$hash = $f->parameters['hash'];
			$criteria->$hash = null;
		}
		
		$response = $pManager->invokeMethod('storage','searchRecords', array('Database'), array( $form, $fields, $start, $rpp, $criteria ) );
		$record   = array();
		
		$fields   = $response['Database']['loaded_fields'];
		$records  = $response['Database']['records'];
		
		for($i=0;$i<count($fields);$i++){
			for($j=0;$j<count($records);$j++){
				$record[$fields[$i]] = $records[$j][$i];
			}
		}
		return $record;
	}
	
	//TODO: Update documentation
	
	/**
	 * Searches through records
	 * 
	 * @access	public
	 *
	 * @param int  Form id 
	 * @param array  fields to load 
	 * @param int  starting row
	 * @param int   number of records to load
	 * @param string  Keywords used for the LIKE clause
	 * @return string formated as follows "X;Y" where X=Total Record count for this request and Y=JSON Encoded records, null on failure
	 */
	function search( $fid, $fields=null, $start=-1, $count=-1, $criteria=null,$translationMode='html', $rawData=false ){
	

		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$pManager->loadPlugins('storage');
		
		
		//Load form data from the DB
		$formModel =& JModel::getInstance('form','JFormsModel');
		$form      = $formModel->get( $fid );
	
		if( $form == null ){
			if( $rawData )return null;
			else return '0;';
		}
		
		//Send work to the Database plugin
		$response = $pManager->invokeMethod('storage','searchRecords', array('Database'), 
											array( $form, $fields, $start, $count, $criteria ) );
											
		//Use response coming from Database plugin
		$result = $response['Database'];
		
		if( $result['total'] == 0 ){
			if( $rawData )return null;
			else return '0;';
		}
		
		//We need to pass the data received from the DB to element Data translator to make them human-readable
		// The data received from the plugin is a non-indexed plain array where fields are arranged in the same order of $_GET['fields']
		// I use this fact to identify the type of each array element and pass it to its translation handler
		//That's exactly what the next loop does, Needs clean up?, totally agreed!
		
		//Index this form fields by hash value (Field name)
		$indexedFields = indexByHash ( $form->fields );
		
		$fieldsLoaded = $result['loaded_fields'];
		
		//Match each field with its element
		for( $i=0; $i<count($result['records']); $i++){
			for( $j=0; $j<count($fieldsLoaded); $j++ ){
		
				$f = $fieldsLoaded[$j];
				if( array_key_exists($f, $indexedFields) ){
					$type = $indexedFields[$f]->type;
					$result['records'][$i][$j] = $pManager->invokeMethod('element','translate', array($type), 
													array( $indexedFields[$f], $result['records'][$i][$j], $translationMode ) );
				}
			}
		}

		if( $rawData ){
			$result['form'] = $form;
			return $result;
		} else{ 
			$json = new Services_JSON();
			return $result['total'].';'.$json->encode( $result['records']);
		}
	}
	
	/**
	 * Returns a selected group  of records based on IDs
	 * 
	 * @access	public
	 * @param int $fid Form id 
	 * @param array $ids ids of the records to be returned 
	 * @return object form object that has "records" property loaded with requested records,null on failure
	 */
	function get( $fid, $ids=null ){
		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('storage');
		
		$formModel =& JModel::getInstance('form','JFormsModel');
		$form      = $formModel->get( $fid );
		
		if( $form == null ){
			return null;
		}
		
		$response = $pManager->invokeMethod('storage', 'getRecords', array('Database'), 
											array( $form, $ids ) );
		
		//Use response coming from Database plugin
		$form->records = $response['Database'];
		
		return $form;
	}
	
	
	/**
	 * Deletes records from a form table
	 *
	 * @access	public
	 * @param int $fid Form id 
	 * @param array $rids ids of records to delete
	 * @return boolean true on success, false on failure
	 */
	function delete( $fid, $rids ){

		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('storage');
		
		$db   = & JFactory::getDBO();
		
		$formModel =& JModel::getInstance('form','JFormsModel');		
		$form      =  $formModel->get( $fid );

		if( $form == null ){
			return false;
		}

		$response = $pManager->invokeMethod('storage','deleteRecords', null, 
											array( $form, $rids ) );	
		
		return $response['Database'];
		
	}
}