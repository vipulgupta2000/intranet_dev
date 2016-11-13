<?php
/**
* Utility class for Version
*
* @version		$Id: version.php 372 2010-03-27 02:32:42Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Core
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Utility class for Version
 * 
 * @package    Joomla
 * @subpackage JForms.Core
 */
class JFormsVersion
{
	/** @var string Product */
	var $PRODUCT 	= 'JForms';
	
	/** @var int Main Release Level */
	var $RELEASE 	= '0.7';
	
	/** @var string Development Status */
	var $DEV_STATUS = 'RC1';
	
	/** @var int build Number */
	var $BUILD = '392';
	
	/** @var string Date */
	var $RELDATE = '22-May-2010';
	
	/** @var string Time */
	var $RELTIME = '16:00';
	
	/** @var string Timezone */
	var $RELTZ 	= 'GMT';
	
	/** @var string Copyright Text */
	var $COPYRIGHT 	= 'Copyright (C) 2008 - 2010 Mostafa Muhammad. All rights reserved.';
	
	/** @var string URL */
	var $URL 	= '<a href="http://jforms.mosmar.com">JForms</a> is Free Software released under the GNU General Public License.';

	var $databaseMap = array();
	
	function __construct(){

		$this->databaseMap['0.5'] = 1;
		$this->databaseMap['0.6'] = 2;
		$this->databaseMap['0.7'] = 3;
		
	}

	/**
	 *
	 *
	 * @return string Long format version
	 */
	function getLongVersion()
	{
		return $this->PRODUCT .' '. $this->RELEASE .' '. $this->DEV_STATUS .' ('.$this->BUILD.')'
			.' [ '. $this->RELDATE .' '. $this->RELTIME .' '. $this->RELTZ.' ]';
	}
	
	function getNumericVersion($version=null){
		if($version == null )$version = $this->RELEASE;
		return intval(str_replace('.','',$version));
	}

	/**
	 *
	 *
	 * @return string Short version format
	 */
	function getShortVersion() {
		return $this->RELEASE .'.'. $this->DEV_LEVEL;
	}
	
	
	/*
		 0 if matching
		 1 if the extension code is newer
		-1 if db version is newer (quite unlikely)
	*/
	function dbMatches(){
		
		$ext = $this->getExtensionFromDB( $this->getDBVersion() );
		
		if( floatval( $ext ) == floatval( $this->RELEASE ))return  0;
		if( floatval( $ext )  > floatval( $this->RELEASE ))return -1;
		if( floatval( $ext )  < floatval( $this->RELEASE ))return  1;
	}
	
	function getExtensionFromDB( $dbVersion ){
		foreach( $this->databaseMap as $ext => $db )
			if( $db == $dbVersion )return $ext;
	}
	
	function getVersionsInBetween( $start, $end, $inclusive=false ){
		
		$versionInBetween = array();
		$startFlag = false;
		
		foreach( $this->databaseMap as $eVersion => $dVersion ){

			if( $eVersion == $start ){
				$startFlag = true;
				if( $inclusive )$versionInBetween[] = $eVersion;
				continue;
			}

			if( $eVersion == $end ){
				if( $inclusive )$versionInBetween[] = $eVersion;
				return $versionInBetween;
			}

			if($startFlag){
				$versionInBetween[] = $eVersion;
			}

		}
	}
	
	function getDBVersion(){
		
		$db =& JFactory::getDBO();
		$fields = $db->getTableFields( '#__jforms_forms' );
		
		if (!array_key_exists('theme', $fields['#__jforms_forms']))return 1;
		
		if (array_key_exists('redirections', $fields['#__jforms_forms']) &&
			array_key_exists('maximum'     , $fields['#__jforms_forms']))return  3;
		else return 2;
	}
}
