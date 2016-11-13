<?php 
/**
* Database Storage plugin
*
* @version		$Id: Database.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
/**
 * Database storage Element plugin 
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
 */
class JFormSPluginDatabase extends JFormSPlugin{
	
	function getFSPath( $form, $fieldName, $recordId, $create=true ){
		
		$tableName    = $form->storagePluginParameters['Database']['tableName'];
		if( $create ){
			JFormSPluginDatabase::_createFSRecord( $tableName, $fieldName, $recordId );
		}
		return 
		array(	
			'path' => $tableName.DS.$fieldName.DS.$recordId,
			'url'  => $tableName.'/'.$fieldName.'/'.$recordId
		);
	}
	
	function getNextInsertID( $form ){
		
		$db =& JFactory::getDBO();
	
		$tableName    = $form->storagePluginParameters['Database']['tableName'];
				
		$db->setQuery('SHOW CREATE TABLE `#__jforms_'.$tableName.'`');
		$result = $db->loadRow();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
		
		$nextAutoIndex = 1;
		$matches = array();
		preg_match('/AUTO_INCREMENT=(\d+)/', $result[1], $matches );
		if( count( $matches ) ){
			$nextAutoIndex = intval( $matches[1] );
		}
		return $nextAutoIndex;
	}
	function _createEmptyHtmlFile( $path ){
		if( !JFile::exists( $path.DS.'index.html' ) ){
			JFile::write( $path.DS.'index.html','<html><body bgcolor="#FFFFFF"></body></html>' );
		}
	}
	function _createFSTable( $tableName ){
		if( !JFolder::exists( JFORMS_FS_PATH.$tableName ) ){
			 JFolder::create( JFORMS_FS_PATH.$tableName );
			 JFormSPluginDatabase::_createEmptyHtmlFile( JFORMS_FS_PATH.$tableName );
		}
	}

	function _deleteFSTable( $tableName ){
		if( JFolder::exists( JFORMS_FS_PATH.$tableName ) ){
			JFolder::delete( JFORMS_FS_PATH.$tableName );
		}
	}

	function _createFSField( $tableName, $fieldName ){
		JFormSPluginDatabase::_createFSTable( $tableName );
		if( !JFolder::exists( JFORMS_FS_PATH.$tableName.DS.$fieldName ) ){
			 JFolder::create( JFORMS_FS_PATH.$tableName.DS.$fieldName );
			 JFormSPluginDatabase::_createEmptyHtmlFile( JFORMS_FS_PATH.$tableName.DS.$fieldName );
		}
	}
	
	function _deleteFSField( $tableName, $fieldName ){
		if( JFolder::exists( JFORMS_FS_PATH.$tableName.DS.$fieldName ) ){
			JFolder::delete( JFORMS_FS_PATH.$tableName.DS.$fieldName );
		}
	}
	
	function _createFSRecord( $tableName, $fieldName, $recordId ){
		
		JFormSPluginDatabase::_createFSTable( $tableName );
		JFormSPluginDatabase::_createFSField( $tableName, $fieldName );
		if( !JFolder::exists( JFORMS_FS_PATH.$tableName.DS.$fieldName.DS.$recordId ) ){
			 JFolder::create( JFORMS_FS_PATH.$tableName.DS.$fieldName.DS.$recordId );
 			 JFormSPluginDatabase::_createEmptyHtmlFile( JFORMS_FS_PATH.$tableName.DS.$fieldName.DS.$recordId  );

		}
	}
	
	function _deleteFSRecords( $tableName, $ids ){
		
		$isDeleteAll = !is_array( $ids );
		
		$fields = JFolder::folders( JFORMS_FS_PATH.$tableName.DS );
		
		if( !$fields ){
			return;
		}
		
		foreach( $fields as $field ){
			$records = JFolder::folders( JFORMS_FS_PATH.$tableName.DS.$field.DS );
		 	foreach( $records as $record ){	
				if( $isDeleteAll || in_array( $record, $ids ) ){
					if( JFolder::exists( JFORMS_FS_PATH.$tableName.DS.$field.DS.$record ) ){
						JFolder::delete( JFORMS_FS_PATH.$tableName.DS.$field.DS.$record );
					}
				}
			}
		}
	}

	function _getSQLField( $s, $n ){
		
		switch($s->type){
			
			case 'textfield':
				if( intval( $s->size ) > 255 ){
					$sql = ' `'.$n.'` TEXT NOT NULL';
				} else {
					$sql = ' `'.$n.'` VARCHAR( '.$s->size.' ) NOT NULL';
				}
				break;
			
			case 'datetime':
				$sql = ' `'.$n.'` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00"';
				break;

			case 'date':
				$sql = ' `'.$n.'` DATE NOT NULL DEFAULT "0000-00-00"';
				break;
			
			case 'number':
				$sql = ' `'.$n.'` INT( '.$s->size.' ) NOT NULL';
				break;
		}
		return $sql;
	}
	
	function _clean( $array ){
		
		$string = implode(',', $array);
		$string =  str_replace(array("\"","'","\\",";"),'', $string );
		$temp1 = explode( ',', $string );
		$temp2 = array();
		
		for($i=0;$i<count($temp1);$i++){
			$temp1[$i] = trim($temp1[$i]);
			if( strlen( $temp1[$i] ) ){
				$temp2[] = $temp1[$i];
			}
		}
		return implode( ',', $temp2 );
		
	}
	/**
	 * Returns records from table based on $criteria
	 *
	 * @param $form   , Object : Form Object
	 * @param $fields , Array  : Field names
	 * @param $start  , int    : Starting record
	 * @param $rpp    , int    : Records Per Page
	 * @param $criteria , Object    : structured as
	 *  $criteria->field_hash <- Object containing critieria specific to each field type
	 * @access	public
	 * @return	form object or null if not found/published
	 */
	function searchRecords($form, $fields, $start, $rpp, $criteria ){


		$db =& JFactory::getDBO();
		
		//Remove all unwanted characters, this array will only allow commas and alpha nuemrical values,
		//it is after all mySQL field names
		$requestedFields = JFormSPluginDatabase::_clean( $fields );
		$start = intval( $start );
		$rpp   = intval( $rpp );
		//Keyword cleanup is left to element plugins
		
		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		
		$tableName = $form->storagePluginParameters['Database']['tableName'];
	
		$whereSQL = '';
		$whereFragments = array();

		//Contains fields that has been confirmed to be valid , i.e , existant in the current table
		$confirmedFieldsArray = explode( ',', JFORM_SYSTEM_FIELDS );
		
		/* This loop performs two task
		1) Check the validity of the incoming "$_GET['fields']"
		2) Collects the "Where" conditions from element plugins
		*/
		$requestedFieldsArray = explode( ',', $requestedFields );
		
		//Sort form fields according to keywords order
		$arrangedFields = array();
		if( $criteria != null ){
			$criteriaArray = JArrayHelper::fromObject( $criteria );
			foreach( $criteriaArray as $hash => $c ){
				foreach( $form->fields as $f ){
					if( $f->parameters['hash'] == $hash )$arrangedFields[] = $f;
				}
			}
			$form->fields = $arrangedFields;
		}
		foreach( $form->fields as $f ){

			$fieldHash = $f->parameters['hash'];
			//Is this field among requested fields?
			if( in_array( $fieldHash, $requestedFieldsArray )){
				
				//Add to confirmed fields
				$confirmedFieldsArray[] = $fieldHash;
				
				//If we have search criteria ,send it to the plugin handler
				if( $criteria != null && property_exists($criteria, $fieldHash) ){
						
						//Grab the particular search criteria for this field
						$fieldCriteria = $criteria->$fieldHash;
						if($fieldCriteria == null)continue;
						
						
						//Plugin handler is supposed to clean up the keyword and return a "where" expression 
						$w = $pManager->invokeMethod( 'element','getSQL', 
													array($f->type), array($f , $fieldCriteria ) );
						if( $w != '' ){
							$whereFragments[] = $w;
						}
				}
			}
		}
		
		//Process core fields "id, uid"
		if( in_array('id', $requestedFieldsArray )){
			if( $criteria && property_exists($criteria, 'id')){
				JArrayHelper::toInteger( $criteria->id->numbers  );
				$ids = implode( ',', $criteria->id->numbers );
				$op = $criteria->id->mode=='or'?' OR ':' AND ';
				$whereFragments[] = ' `id` IN ( '.$ids.') '.$op;
			}
		}
		
		if( in_array('uid', $requestedFieldsArray ) ){
			if( $criteria && property_exists($criteria, 'uid')){
				JArrayHelper::toInteger( $criteria->uid->numbers  );
				$ids = implode( ',', $criteria->uid->numbers );
				$op  = $criteria->uid->mode=='or'?' OR ':' AND ';
				$whereFragments[] = ' `uid` IN ( '.$ids.') '.$op;
			}
		}
		
		
		$whereSQL = '';
		if( count($whereFragments) ){

			//Build Where part of the query
			$whereSQL = ' WHERE '. implode( ' ' , $whereFragments );
			$whereSQL = trim( $whereSQL, ' AND ');
			$whereSQL = trim( $whereSQL, ' OR ');
		}


		//Get total count of records that match the request
		$db =& JFactory::getDBO();
		$sql =  "SELECT COUNT(*) FROM #__jforms_".$tableName." $whereSQL";
		$db->setQuery($sql);
		$count = $db->loadResult();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
	
		//Apply limits
		$limit = '';
		if( $start != -1 && $rpp != -1 ){
			$limit = " LIMIT $start, $rpp ";
		}

		//Join confirmed fields
		$fields = implode(',', $confirmedFieldsArray );

		//Build query
		$sql =  "SELECT $fields FROM #__jforms_".$tableName." $whereSQL $limit";

		$db =& JFactory::getDBO();
		$db->setQuery($sql);
		$rowList = $db->loadRowList();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
		
		return array(
					'total' 		=> $count,
					'loaded_fields' => $confirmedFieldsArray,
					'records' 		=> $rowList
					);
		
	}
	
	function onFormCreate( &$form ){
		
		$db =& JFactory::getDBO();
		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$elementPlugins = $pManager->settings['element'];
		
		//Automatically generate Tablename
		//And save table name to plugin parameters for this form
		$form['storagePluginParameters']['Database']['tableName'] = substr( md5(uniqid(rand(), true)), 0, 5 );
		
		//Short name
		$tableName = $form['storagePluginParameters']['Database']['tableName'];
		
		$sql  = 'CREATE TABLE `#__jforms_'.$tableName.'` ('.
				' `id` INT(11) NOT NULL AUTO_INCREMENT ,'
			   .' `uid` INT(11) UNSIGNED NOT NULL DEFAULT "0",';
 
		$columnSQL = array();
		
		$hashes = array();
		
		foreach( $form['fieldInformation'] as $f ){
			
			//Never mind fields with no storage requirments
			if($elementPlugins[$f->type]->storage == null)continue;
			
			//Will take the first storage field from XML paramters for now
			$storage = $elementPlugins[$f->type]->storage;
			
			if( $storage->requirefs ){
				JFormSPluginDatabase::_createFSField( $tableName, $f->hash ); 
			}
			
			$columnSQL[] = JFormSPluginDatabase::_getSQLField($storage, $f->hash);
		
			
			$field 		      = new stdClass();
			$field->type 	  = $storage->type;
			$field->size 	  = $storage->size;
			$field->requirefs = $storage->requirefs;
			
			$hashes[strval($f->hash)] = $field;
			
		}
		$form['storagePluginParameters']['Database']['fields'] = base64_encode(serialize($hashes )) ;
		$sql .= implode( ',', $columnSQL );
		$sql .= ' ,PRIMARY KEY ( `id` )'.
				' )  TYPE=InnoDB;';	
		
		$db->setQuery($sql);
		$db->query();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
		
		return true;
	}
	
	function onFormSave( &$form ){
		
		
		
		$databaseParameters = $form['storagePluginParameters']['Database'];
		$tableName = $databaseParameters['tableName'];
		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$elementPlugins = $pManager->settings['element'];

		
		$sqlBlocks  = array();
		
		//This parameter is used to save the current Table structure, To avoid additional queries to DB
		//It is used to compare the current table structure with incoming form fields to see if they match and add/modify/remove table fields if needed
		$oldFields = unserialize(base64_decode( $databaseParameters['fields']));
		
		
		foreach( $form['fieldInformation'] as $f ){
			
			//Never mind fields with no DB storage requirments 
			if($elementPlugins[$f->type]->storage == null)continue;
			
			//Is this an existant field?
			if( array_key_exists( $f->hash , $oldFields ) ){
				
				$currentStorage  = $elementPlugins[$f->type]->storage;
				$previousStorage = $oldFields[$f->hash];
				
				//Creates Filesystem requirements for the field
				if( $currentStorage->requirefs != $previousStorage->requirefs ){
					if( $currentStorage->requirefs ){
						JFormSPluginDatabase::_createFSField( $tableName, $f->hash );
					} else {
						JFormSPluginDatabase::_deleteFSField( $tableName, $f->hash );
					}
				}
				
				//See if this Field has been altered "e.g. from Textfield to textarea"
				if( $currentStorage->type != $previousStorage->type ||
					$currentStorage->size != $previousStorage->size  ){
					
					
					//Alter table to meet new settings
					$sqlBlocks[] = 'MODIFY'.JFormSPluginDatabase::_getSQLField($currentStorage, $f->hash);
					
					//Update table sturcture parameter for this plugin
					$oldFields[$f->hash]->type	     = $currentStorage->type;
					$oldFields[$f->hash]->size 		 = $currentStorage->size;
					$oldFields[$f->hash]->requirefs  = $currentStorage->requirefs;
					
					
				} else {
					//Field already exists and has the same settings, do nothing then
					continue;
				}
			//New Field
			} else {
					//See what storage requirements does it need
					$currentStorage = $elementPlugins[$f->type]->storage;
					
					//Create FS (FileSystem) Requirements for this field
					if( $currentStorage != null && $currentStorage->requirefs ){
						JFormSPluginDatabase::_createFSField( $tableName, $f->hash );
					}
					
					//Generate SQL
					$sqlBlocks[] = 'ADD'.JFormSPluginDatabase::_getSQLField($currentStorage, $f->hash);
					
					//Update table sturcture parameter for this plugin
					$oldFields[$f->hash]            = new stdClass();
					$oldFields[$f->hash]->type      = $currentStorage->type;
					$oldFields[$f->hash]->size      = $currentStorage->size;
					$oldFields[$f->hash]->requirefs = $currentStorage->requirefs;
			}
		}
		//Check for deleted fields
		foreach($oldFields as $oldFieldHash => $storageInfo){
			foreach( $form['fieldInformation'] as $newField ){
				//Never mind fields with no DB storage requirments 
				$deleted = true;
				if($elementPlugins[$newField->type]->storage == null)continue;
				
			
				if( $oldFieldHash == $newField->hash ){
					//Field exists
					$deleted = false;
					break;
				}
			}
			//Field is present in the previous table sturcture but no longer exist in the incoming data (i.e. Deleted)
			if( $deleted  ){
				
				//Remove FS allocated for this field
				if( $storageInfo != null && $storageInfo->requirefs ){
					JFormSPluginDatabase::_deleteFSField( $tableName, $oldFieldHash );
				}
				
					
				//Construct query
				$sqlBlocks[] = 'DROP COLUMN `'. $oldFieldHash . '`';
				
				//Update table sturcture parameter for this plugin
				unset($oldFields[$oldFieldHash]);
			}
		}

		
		//No changes in Table structure
		if(!count($sqlBlocks)){
			//return harmlessly
			return true;
		}
		//Build SQL query and execute it
		$sql  = 'ALTER TABLE `#__jforms_'.$tableName.'` ';
		$sql .= implode( ',' , $sqlBlocks );

		$db =& JFactory::getDBO();
		
		$db->setQuery($sql);

	
		$db->query();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
				
		//Modify the table structure parameter for the plugin
		$form['storagePluginParameters']['Database']['fields'] = base64_encode(serialize( $oldFields )) ;
		
		return true;
		
	
	}
	
	function onFormDelete( &$form ){
		//Delete records and drop table
		JFormSPluginDatabase::deleteRecords( $form, null, true );
	}
	
	function deleteRecords( $form , $ids, $drop=false ){
	
		$db =& JFactory::getDBO();
		$tableName = $form->storagePluginParameters['Database']['tableName'];
		
		if( is_array( $ids ) && count( $ids ) && !$drop ){
			
			$idList = implode( ',' ,$ids );
			
			//Delete Selected records only from FS and DB
			JFormSPluginDatabase::_deleteFSRecords( $tableName, $ids );	
			$db->setQuery('DELETE FROM `#__jforms_'.$tableName.'` WHERE id IN ('.$idList.')');	
		
		} else {
		
			//Should we drop the table as well?
			if( $drop ){
				//Drop FS and DB Tables
				JFormSPluginDatabase::_deleteFSTable( $tableName );	
				$db->setQuery('DROP TABLE `#__jforms_'.$tableName.'`');	
			} else {
				//delete all records
				JFormSPluginDatabase::_deleteFSRecords( $tableName, null );	
				$db->setQuery('DELETE FROM `#__jforms_'.$tableName.'`');	
			}
		}

		$db->query();
		//if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
		
		return true;
		
	}
	
	
	/**
	 * Retrieves records from DB based on ids
	 *
	 * @access	public
	 * @param object $form : The form to retrieve records from
	 * @param array $ids : Record Ids , if null the method will return all records
	 * @return array , Returns records on success, null on failure 
	 */
	function getRecords( $form, $ids ){
		

		$db   = & JFactory::getDBO();
		$tableName = $form->storagePluginParameters['Database']['tableName'];
		
		
		if( is_array( $ids ) && count( $ids ) ){
			$idList = implode( ',' , $ids );
			$db->setQuery('SELECT * FROM `#__jforms_'.$tableName.'` WHERE id IN ('.$idList.')');
		} else {
			$db->setQuery('SELECT * FROM `#__jforms_'.$tableName.'`');	
		}
		
		$objectList = $db->loadObjectList();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
				
		return $objectList;
	}
	
	/**
	 * Saves a record to DB
	 *
	 * @access	public
	 * @param object $form : The form we're saving this record under
	 * @param array $data : Data to be saved, indexed by hash values
	 * @return	bool , true on success,  false on failure
	 */
	function saveRecord( $form, $data ){


		
		if( $form == null || $data == null ){
			return false;
		}
	
		$pluginManager =& JFormsGetPluginManager();
		$pluginManager->loadPlugins('element');
		$pluginManager->loadPlugins('storage');
		
		
		
		$db     =& JFactory::getDBO();
		$config	=& JFactory::getConfig();
		$now	=& JFactory::getDate();

		$tableName  = $form->storagePluginParameters['Database']['tableName'];
		
		//Loop through all fields of the current form
		foreach( $form->fields as $f ){

			//If a certain field has no storage requirements
			if( $pluginManager->settings['element'][$f->type]->storage == null ){
				//Ignore it
				continue;
			}
			//Otherwise retreive its value from the request ,clean it up and save it
			$fname = $f->parameters['hash'];
			
			$fieldArray[] = $fname;
			
			//Clean-up content
			$valueArray[] = $db->quote( $db->getEscaped( $data[$fname], false ), false );
		}
		
		//Inserts user ID and processes Profile mode forms
		$uid = intval( $data['uid'] );
		if( $form->type == JFORMS_TYPE_PROFILE ){
			if( $uid != 0 ){
				$sql = 'SELECT id FROM `#__jforms_'.$tableName.'` WHERE uid='.$uid;
				$db->setQuery($sql);
				$fieldId = $db->loadResult();
				if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
			
				JFormSPluginDatabase::_deleteFSRecords( $tableName, array( $fieldId ) );
				$sql = 'DELETE FROM `#__jforms_'.$tableName.'` WHERE uid='.$uid;
				$db->setQuery($sql);
				$db->query();
				if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
		
			}
		}
		$fieldArray[] = 'uid';
		$valueArray[] = $uid;
		
		//Build Query
		$fields = implode( ',', $fieldArray );
		$values = implode( ',', $valueArray );
		$sql = 'INSERT INTO `#__jforms_'.$tableName.'` ( '.$fields.' ) VALUES ( '.$values.' )';	
		$db->setQuery($sql);

		//Execute it
		$db->query();
		if ($db->getErrorNum())JError::raiseError( 500, $db->stderr() );
		
		return true;
		
	}
}