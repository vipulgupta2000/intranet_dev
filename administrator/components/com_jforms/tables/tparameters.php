<?php
/**
* Translatable parameters table class
*
* @version		$Id: tparameters.php 383 2010-05-01 01:25:07Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Tables
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Translatable parameters Table Class
 *
 * @package    Joomla
 * @subpackage JForms.Tables
 */
class TableTparameters extends JTable
{

	/**
	 * @var int
	 */
	var $id = null;
	
	
	/**
	 * @var int
	 */
	var $fid = null;

	/**
	 * @var int
	 */
	var $pid = null;

	/**
	 * @var string
	 */
	var $plugin_name = null;

	/**
	 * @var int
	 */
	var $plugin_type = null;

	/**
	 * @var int
	 */
	var $parameter_name = null;
	
	/**
	 * @var string
	 */
	var $parameter_value = null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableTparameters(& $db) {
		parent::__construct('#__jforms_tparameters', 'id', $db);
	}

	/**
	 * Inserts a new row if id is zero or updates an existing row in the database table
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @param boolean If false, null object variables are not updated
	 * @return null|string null if successful otherwise returns and error message
	 */
	function store( $updateNulls=false )
	{
		$k = $this->_tbl_key;

		$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
		if( !$ret )
		{
			$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
			return false;
		}
		else
		{
			return true;
		}
	}
	function delayedStore(){
		
		if( !isset($this->_delayedInserts) || !is_array($this->_delayedInserts) ){
			$this->_delayedInserts = array();
		}
		$this->_delayedInserts[] = $this->_getInsertedValues();
	}
	
	function commitDelayedStore(){
		if( !isset($this->_delayedInserts) || empty($this->_delayedInserts)){
			return false;
		}
		
		$sql = $this->_delayedInsertSQLFormat. implode(",\r\n",$this->_delayedInserts);
		$this->_db->setQuery($sql);
		if(!$this->_db->query()){	
			$this->setError(get_class( $this ).'::delayedStore failed - '.$this->_db->getErrorMsg());
			return false;
		}
		return true;		
	}
	function _getInsertedValues($updateNull=true){
		
		$fmtsql = '( %s ) ';
		$fields = array();
		foreach (get_object_vars( $this ) as $k => $v) {
			if (is_array($v) || is_object($v) || ($v === NULL && !$updateNull )) {
				continue;
			}
			if ($k[0] == '_') { // internal field
				continue;
			}
			$fields[] = $this->_db->nameQuote( $k );
			$values[] = $this->_db->isQuoted( $k ) ? $this->_db->Quote( $v ) : (int) $v;
		}
		if(!isset($this->_delayedInsertSQLFormat))
			$this->_delayedInsertSQLFormat = sprintf('INSERT INTO '.$this->_db->nameQuote($this->_tbl).' ( %s ) VALUES ',implode(',', $fields));

		return sprintf( $fmtsql, implode( ",", $values ) );
	}
}
