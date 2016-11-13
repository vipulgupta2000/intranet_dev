<?php 
/**
* SQL Export plugin [STUB]
*
* @version		$Id: Sql.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * SQL Export plugin 
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
 */
class JFormXPluginSql{
	
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
			
			case 'number':
				$sql = ' `'.$n.'` INT( '.$s->size.' ) NOT NULL';
				break;
		}
		return $sql;
	}
	
	function _createTable( $form ){
		
		/*
		global $JFormGlobals;
		$pManager =& $JFormGlobals['JFormsPlugin'];
		$pManager->loadElementPlugins();
		$dbFields = unserialize(base64_decode($form->storagePluginParameters['Database']['fields'])));
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
			$columnSQL[] = JFormSPluginDatabase::_getSQLField($storage, $f->hash);
			
		}
		$sql .= implode( ',', $columnSQL );
		$sql .= ' ,PRIMARY KEY ( `id` )'.
				' )  TYPE=InnoDB;';	
		
		
		$fields = array();
		
		foreach( $form->fields as $f ){
			$fields[$f->parameters]->hash = $f;
		}
		return $form;
		*/
	}
	
	function onExport( $pluginParameters, $requestParameters, $data ){

		/*
		$tableSQL = JFormXPluginSql::_createTable( $data['form'] );
		d( $tableSQL );
		*/
	}
	
}