<?php
/**
* Form Model
*
* @version		$Id: form.php 383 2010-05-01 01:25:07Z dr_drsh $
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
 * Form Model
 *
 * @package    Joomla
 * @subpackage JForms.Models
 */
class JFormsModelForm extends JModel
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
	 * searches through forms
	 * 
	 * @access	public
	 * @param int $start starting records
	 * @param int $count number of records to return
	 * @param string $keyword keywords used for the LIKE clause
	 * @param bool $onlyPublished whether or not to return only the published forms
	 * @return array 'total' => total number the request returned
	 *  	         'forms' => list of forms
	 */
	function search( $start=0, $count=0, $keyword='', $onlyPublished=false ){
	
		$db =& JFactory::getDBO();
		
		$start = intval( $start );
		$count = intval( $count );
		$onlyPublished = (bool) $onlyPublished;
		$keyword = $db->getEscaped( $keyword, true );
		
		$limit = '';
		if( $count != 0 ){
			$limit = "LIMIT $start, $rpp ";
		}
		
		$whereFragments = array();
		
		if( $keyword != ''){
			$whereFragments[] = "f.name LIKE '%$keyword%'";
		}
		
		if( $onlyPublished ){ 
			$jnow =& JFactory::getDate();
			$now = $jnow->toMySQL();
			$nullDate = $db->getNullDate();
			$whereFragments[]	= ' ( f.state = 1 OR f.state = -1)' .
			 	 ' AND ( f.publish_up = '.$db->Quote($nullDate).' OR f.publish_up <= '.$db->Quote($now).' )' .
				 ' AND ( f.publish_down = '.$db->Quote($nullDate).' OR f.publish_down >= '.$db->Quote($now).' )';
		}
		
		$where = '';
		if( count($whereFragments)){
			$where = 'WHERE'.implode( ' AND ', $whereFragments );
		}
		
		$sql =  
		"SELECT f.* FROM `#__jforms_forms` as f"
		."\n".$where;
		$db->setQuery($sql);
		$count = $db->loadResult();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );

		
		$sql = 
		 "SELECT f.*,p.parameter_value as table_name, v.name as editor, u.name as author"
		."\nFROM `#__jforms_forms` as f "
		."\nLEFT JOIN `#__users` AS u on f.created_by = u.id "
		."\nLEFT JOIN `#__users` AS v ON f.checked_out= v.id " 
		."\nLEFT JOIN `#__jforms_parameters` as p on (p.fid = f.id AND `plugin_name` = 'Database' AND `parameter_name`='tableName')"
		."\n".$where
		."\n"."ORDER BY id ASC"
		."\n".$limit;


		$db->setQuery( $sql );
		$objectList = $db->loadObjectList();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );

		return array('total' => $count,
					 'forms' => $objectList  );
		
	}
	

	
	/**
	 * Returns all data about one form  based on ID
	 * 
	 * @access	public
	 
	 * @param int $fid Form id 
	 * @return object form object loaded with all data from the 3 core tables, null on failure
	 */
	function get( $fid, $loadFields=true )
	{
		$db   = & JFactory::getDBO();
		$form  = & JTable::getInstance('Forms','Table');
		$pManager =& JFormsGetPluginManager();

		$pManager->loadPlugins('element');

		$form->load($fid);
		
		$elementPluginsInformation = $pManager->settings['element'];
	

		if( $form == null ){
			return null;
		}

		//Load Related Fields
		$db->setQuery('SELECT * FROM `#__jforms_fields` WHERE `pid`=' . $fid.' ORDER BY `position` ASC');
		$form->fields = $db->loadObjectList();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
		
		if( $form->fields == null ){
			return null;
		}
		
		//Load field defaults
		//This prevents unwanted notices if the field parameter is missing from the database
		for($i=0;$i<count($form->fields);$i++){
			$field =& $form->fields[$i];
			
			$field->parameters   = array();
			$field->parametersId = array();
			
			foreach($elementPluginsInformation[$field->type]->parameters as $name => $data){
				$field->parameters[$name]  = $data->default;
				$field->parametersId[$name] = 0;
			}
		}
		
		//Load normal Paramters
		$db->setQuery('SELECT * FROM `#__jforms_parameters` WHERE `fid`=' . $fid);
		$nParameters = $db->loadObjectList();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
	
		//Load Translated paramters
		$db->setQuery('SELECT * FROM `#__jforms_tparameters` WHERE `fid`=' . $fid);
		$tParameters = $db->loadObjectList();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
		
		if( $nParameters == null && $tParameters == null ){
			return null;
		}
		
		$parameters 	  = array_merge( $tParameters, $nParameters );
		$parametersIdList = array(); 
		
		$form->storagePluginParameters = array();
		
		//Join each field or plugin with its parameters
		foreach( $parameters as $p ){
			switch( $p->plugin_type ){

				case JFORM_PLUGIN_STORAGE :
					$form->storagePluginParameters[$p->plugin_name][$p->parameter_name] = $p->parameter_value;
					break;
					
				case JFORM_PLUGIN_ELEMENT :
					//Loop through fields
					for($i=0; $i<count($form->fields); $i++){
						//Does this parameter belong to this field?
						if( $p->pid == $form->fields[$i]->id ){
							//First cycle for this field?
							if(!isset($form->fields[$i]->parameters) || !is_array($form->fields[$i]->parameters)){
								//Make sure we have storage for these parameters
								$form->fields[$i]->parameters   = array();
								$form->fields[$i]->parametersId = array();
							}
							//Assign this parameter to the field
							$form->fields[$i]->parameters  [$p->parameter_name] = $p->parameter_value;
							$form->fields[$i]->parametersId[$p->parameter_name] = $p->id;
							break;
						}
					}
				break;
			}
		}
		
		$recordsTable = $form->storagePluginParameters['Database']['tableName'];
		$db->setQuery('SELECT COUNT(*) FROM `#__jforms_'.$recordsTable.'`');
		$form->recordCount = $db->loadResult();
		//if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );

		return $form;
	}
	
	/**
	 * Checks the form in when the user clicks "cancel"
	 *
	 * @access	public
	 * @param int $fid Form id 
	 * @return	void
	 */
	function close( $fid )
	{
		if( $fid ){
			$row  = & JTable::getInstance('Forms','Table');
			$row->load( $fid );
			$row->checkin();
		}
	}
	
	/**
	 * stores form meta information in core tables,this will trigger "onFormSave" event for the selected storage plugins 
	 *
	 * @access	public
	 * @param   object $data Form Data to be saved 
	 * @return	form ID on success , 0 on failure
	 */
	function save( $data ){

		// TODO : Make save process Atomic
		$user = & JFactory::getUser();
		$db   = & JFactory::getDBO();
		$row  = & JTable::getInstance('Forms','Table');
		$nullDate = $db->getNullDate();

		$pManager =& JFormsGetPluginManager();

		if (!$row->bind($data))JError::raiseError( 500, $db->stderr() );

		$row->id = intval($row->id);
		
		$newEntry = $row->id?false:true;

		//Copied directly from com_content saveContent()  
		
		// Are we saving from an item edit?
		if (!$newEntry) {
			$datenow =& JFactory::getDate();
			$row->modified 		= $datenow->toMySQL();
			$row->modified_by 	= $user->get('id');
		}

		$row->created_by 	= $row->created_by ? $row->created_by : $user->get('id');

		if ($row->created && strlen(trim( $row->created )) <= 10) {
			$row->created 	.= ' 00:00:00';
		}

		$config =& JFactory::getConfig();
		$tzoffset = $config->getValue('config.offset');
		$date =& JFactory::getDate($row->created, $tzoffset);
		$row->created = $date->toMySQL();
		
		//Fixes Form "pending" issue after creation
		if(strlen(trim($row->publish_up)) == 0){
			$row->publish_up = $row->created;
		}

		// Append time if not added to publish date
		if (strlen(trim($row->publish_up)) <= 10) {
			$row->publish_up .= ' 00:00:00';
		}

		$date =& JFactory::getDate($row->publish_up, $tzoffset);
		$row->publish_up = $date->toMySQL();

		// Handle never unpublish date
		if (trim($row->publish_down) == JText::_('Never') || trim( $row->publish_down ) == '')
		{
			$row->publish_down = $nullDate;
		}
		else
		{
			if (strlen(trim( $row->publish_down )) <= 10) {
				$row->publish_down .= ' 00:00:00';
			}
			$date =& JFactory::getDate($row->publish_down, $tzoffset);
			$row->publish_down = $date->toMySQL();
		}
		
		$row->groups = implode( ',', $data['groups']);
		
		// Make sure the data is valid
		if (!$row->check())JError::raiseError( 500, $db->stderr() );

		// Store the content to the database
		if (!$row->store())JError::raiseError( 500, $db->stderr() );

		
		//End of faithful copy
		
		if($newEntry){
			$row->id = intval($this->_insertId());
		}
		
		if(!$newEntry){
			
			//Delete fields of this form
			$db->setQuery('DELETE FROM #__jforms_fields WHERE pid=' . $row->id);
			if (!$db->query())JError::raiseError( 500, $db->getErrorMsg() );
			
			//Delete parameters for this form
			$db->setQuery('DELETE FROM #__jforms_parameters WHERE fid=' . $row->id);
			if (!$db->query())JError::raiseError( 500, $db->getErrorMsg() );
			
			//Delete parameters for this form
			$db->setQuery('DELETE FROM #__jforms_tparameters WHERE fid=' . $row->id);
			if (!$db->query())JError::raiseError( 500, $db->getErrorMsg() );
		}

		
		//TODO :Should be placed in controller , not here
		if( $newEntry ){
			$data['id'] = $row->id;
			$pManager->invokeMethod('storage', 'onFormCreate', null,
									 array( &$data ) );
		} else {
			$pManager->invokeMethod('storage', 'onFormSave' , null,
									array( &$data ) );	
		}
		

		
		//Start saving Fields and their paramters
		
		$tparameterRow  = & JTable::getInstance('Tparameters','Table');
		$nparameterRow  = & JTable::getInstance('Parameters' ,'Table');
		
		//Start with saving Storage Plugin parameters
		foreach( $data['storagePluginParameters'] as $plugin_name => $plugin_object ){
		
			foreach( $plugin_object as $param_name => $param_value ){
			
				$parameterRow  = &$nparameterRow;

				$parameterRow->id  = null;
				$parameterRow->fid = $row->id;
				
				//Storage Plugins are not realted to a certain field
				$parameterRow->pid = 0;
				
				$parameterRow->plugin_name = $plugin_name;
				$parameterRow->plugin_type = JFORM_PLUGIN_STORAGE;
				$parameterRow->parameter_name  = $param_name;
				$parameterRow->parameter_value = $param_value;
				
				// Make sure the data is valid
				if (!$parameterRow->check())JError::raiseError( 500, $db->stderr() );
			
				// Store the content to the database
				$parameterRow->delayedStore();
			}
		}
		
		
		//Now Saving Fields and the their paramters
		foreach( $data['fieldInformation'] as $f ){
			
			
			$fieldsRow      = & JTable::getInstance('Fields','Table');
			
			$fieldsRow->pid = $row->id;
			$fieldsRow->type = $f->type;
			$fieldsRow->position = $f->position;

			// Make sure the data is valid
			if (!$fieldsRow->check())JError::raiseError( 500, $db->stderr() );

			// Store the field to the database
			if (!$fieldsRow->store())JError::raiseError( 500, $db->stderr() );
			
			$pid = intval($this->_insertId());
			$plugin_name = $f->type;
			$plugin_type = JFORM_PLUGIN_ELEMENT;
			
			//PHP 5 call
			//To avoid affecting the $form reference passed from controller we create a shallow copy of the current field
			//More info , here http://acko.net/node/54
			$tempField = clone($f);
			
			
			unset($tempField->position);
			unset($tempField->type);
			
			$parameters = JArrayHelper::fromObject($tempField);
			
			
			
			$idList = null;
			if( $data['paramListId'] && array_key_exists($parameters['hash'], $data['paramListId'])){
				$idList = $data['paramListId'][$parameters['hash']];
			}

			
			//Now store the field's parameters
			foreach($parameters as $name => $value){
			
				//If the parameters is translatable , put it in the translated table
				if( $pManager->settings['element'][$plugin_name]->parameters[$name]->translate ) {
					$parameterRow = &$tparameterRow;
				} else {
					$parameterRow = &$nparameterRow;
				}	
				
				if($idList && array_key_exists($name,$idList)){
					$parameterRow->id  = $idList[$name] ;
				} else {
					$parameterRow->id = null;
				}
				
				$parameterRow->fid = $row->id;
				$parameterRow->pid = $pid;
				$parameterRow->plugin_name = $plugin_name;
				$parameterRow->plugin_type = $plugin_type;
				$parameterRow->parameter_name = $name;
				$parameterRow->parameter_value = $value;		
				
				// Make sure the data is valid
				if (!$parameterRow->check())JError::raiseError( 500, $db->stderr() );
				
				// Store the parameter to the database
				$parameterRow->delayedStore();
				
				
			}
		}
		
		$nparameterRow->commitDelayedStore();
		$tparameterRow->commitDelayedStore();
		
		$row->checkin();
		
		return $row->id;
		
	}
	
	/**
	 * Get last insert id via mysql query
	 *
	 * @access	public
	 * @return	int last insert id for the current connection
	 */
	function _insertId(){$db   = & JFactory::getDBO();$db->setQuery( "SELECT LAST_INSERT_ID()" );return intval( $db->loadResult() );}
	
	/**
	 * Deletes a form.
	 *
	 * @access	public
	 * @param array $ids list of form IDs to delete 
	 * @return	bool true on success , false on failure
	 */
	function delete( $ids )
	{
		$db   = & JFactory::getDBO();

		JArrayHelper::toInteger( $ids );

		$additionalParameters = array();			
		
		if( !is_array( $ids ) || !count( $ids ) ){
			return false;
		}
		
		$idText = implode( ',', $ids );
		
		//Delete From the main Forms table
		$db->setQuery('DELETE FROM `#__jforms_forms` WHERE `id` IN (' . $idText  . ')');
		if (!$db->query())JError::raiseError( 500, $db->getErrorMsg() );
		
		
		//Delete From the Fields table
		$db->setQuery('DELETE FROM `#__jforms_fields` WHERE `pid` IN (' . $idText  . ')');
		if (!$db->query())JError::raiseError( 500, $db->getErrorMsg() );
		
		//Delete parameters for this form
		$db->setQuery('DELETE FROM `#__jforms_parameters` WHERE `fid` IN (' . $idText . ')');
		if (!$db->query())JError::raiseError( 500, $db->getErrorMsg() );
		
		return true;
	}
	
	/**
	 * Unpublishes the forms whose IDs are passed 
	 *
	 * @access	public
	 * @param array$ids list of form IDs to unpublish 
	 * @return	bool true on success , false on failure
	 */
	function unpublish($ids){
		
		JArrayHelper::toInteger( $ids );
		if( !is_array( $ids ) || !count( $ids ) ){
			return false;
		}
		$idText = implode( ',', $ids );
	
		$db   = & JFactory::getDBO();
			
		//Delete From the main Forms table
		$db->setQuery('UPDATE `#__jforms_forms` SET `state`=0 WHERE `id` IN (' . $idText . ') ');
		if (!$db->query())JError::raiseError( 500, $db->getErrorMsg() );
		
		return true;
	}
	
	/**
	 * Publishes the forms whose IDs are passed 
	 *
	 * @access	public
	 * @param array $ids list of form IDs to publish 
	 * @return	bool true on success , false on failure
	 */
	function publish($ids){
	
		JArrayHelper::toInteger( $ids );
		if( !is_array( $ids ) || !count( $ids ) ){
			return false;
		}
		$idText = implode( ',', $ids );
		
		$db   = & JFactory::getDBO();
			
		//Delete From the main Forms table
		$db->setQuery('UPDATE `#__jforms_forms` SET `state`=1 WHERE `id` IN (' . $idText . ') ');
		if (!$db->query())JError::raiseError( 500, $db->getErrorMsg() );
		
		return true;
	}
	
	/**
	 * Duplicates a form without its records, creates neccesary storage table
	 *
	 * @access	public
	 * @param array $ids array containing the id of the form to duplicate "will take the first element"
	 * @param string name of the duplicated form
	 * @return	bool true on success , false on failure
	 */
	function copy( $cids, $newName ){
		
		$id = intval($cids[0]);
		$newName = str_replace( array("\"","'",",",";"),"",$newName);

		$db = & JFactory::getDBO();
		$query = 
		"INSERT INTO `#__jforms_forms`" 
		."\n(`title`, `type`, `plugins`, `state`, `created`, `created_by`, `modified`,"
		."\n`modified_by`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `groups`, `maximum`, `redirections`)"
		."\n(SELECT"
		."\n'$newName',`type`, `plugins`, `state`, `created`, `created_by`, `modified`,"
		."\n`modified_by`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `groups`, `maximum`, `redirections`"
		."\nFROM `#__jforms_forms` WHERE `id`=$id)";
		$db->setQuery($query);
		$db->query();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
	
		$newFormId = $this->_insertId();
		$oldFormId = $id;
		
		//Form parameters
		$query = 
		"INSERT INTO `#__jforms_parameters`"
		."\n(`fid`, `pid`, `plugin_name`, `plugin_type`, `parameter_name`, `parameter_value`)"
		."\n(SELECT"
		."\n$newFormId, 0, `plugin_name`, `plugin_type`, `parameter_name`, `parameter_value` FROM #__jforms_parameters WHERE pid=0 AND fid=$oldFormId)";
		$db->setQuery($query);
		$db->query();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
		
		$query = 
		"INSERT INTO `#__jforms_tparameters`"
		."\n(`fid`, `pid`, `plugin_name`, `plugin_type`, `parameter_name`, `parameter_value`)"
		."\n(SELECT"
		."\n$newFormId, 0, `plugin_name`, `plugin_type`, `parameter_name`, `parameter_value` FROM #__jforms_parameters WHERE pid=0 AND fid=$oldFormId)";
		$db->setQuery($query);
		$db->query();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );

		//Field Parameters
		$query = "SELECT `id`  FROM `#__jforms_fields` WHERE `pid`=$oldFormId";
		$db->setQuery($query);
		$result = $db->loadResultArray(0);
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
		
		foreach( $result as $oldFieldId ){
			$query = 
			"INSERT INTO `#__jforms_fields`"
			."\n(`pid`, `type`, `position`)"
			."\n(SELECT"
			."\n$newFormId, `type`, `position` FROM `#__jforms_fields` WHERE `id`=$oldFieldId)";
			$db->setQuery($query);
			$db->query();
			if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );

			$newFieldId = $this->_insertId();
			
			$query = 
			"INSERT INTO `#__jforms_parameters`"
			."\n(`fid`, `pid`, `plugin_name`, `plugin_type`, `parameter_name`, `parameter_value`)"
			."\n(SELECT"
			."\n$newFormId, $newFieldId, `plugin_name`, `plugin_type`, `parameter_name`, `parameter_value` FROM `#__jforms_parameters` WHERE `pid`=$oldFieldId)";
			$db->setQuery($query);
			$db->query();
			if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );

			
			$query = 
			"INSERT INTO `#__jforms_tparameters`"
			."\n(`fid`, `pid`, `plugin_name`, `plugin_type`, `parameter_name`, `parameter_value`)"
			."\n(SELECT"
			."\n$newFormId, $newFieldId, `plugin_name`, `plugin_type`, `parameter_name`, `parameter_value`"
			."\nFROM #__jforms_tparameters WHERE pid=$oldFieldId)";
			$db->setQuery($query);
			$db->query();
			if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
			
		}
		$query = "SELECT `parameter_value` FROM `#__jforms_parameters` WHERE `parameter_name`='tableName' AND `plugin_name`='Database' AND `plugin_type`=0 AND `fid`=$oldFormId";
		$db->setQuery($query);
		
		$oldTableName = $db->loadResult();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );

		$newTableName = substr( md5(uniqid(rand(), true)), 0, 5 );

		$query = "CREATE TABLE `#__jforms_$newTableName` LIKE `#__jforms_$oldTableName`";
		$db->setQuery($query);
		$db->query();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );

		
		$query = "UPDATE `#__jforms_parameters` SET `parameter_value`='$newTableName' WHERE `parameter_name`='tableName' AND `plugin_name`='Database' AND `plugin_type`=0 AND fid=$newFormId";
		$db->setQuery($query);
		$db->query();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );

		return true;

	}
}