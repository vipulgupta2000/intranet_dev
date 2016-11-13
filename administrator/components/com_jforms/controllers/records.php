<?php
/**
* Records backend controller
*
* @version		$Id: records.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Controllers
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

jimport('joomla.application.component.controller');

/**
 * Records backend controller
 *
 * @package    Joomla
 * @subpackage JForms.Controllers
 */
class RecordsController extends JController
{

	/**
	 * constructor (registers additional tasks to methods)
	 *
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'export'  , 'export'   );
		$this->registerTask( 'delete'  , 'delete'   );
		$this->registerTask( 'get'     , 'retrieve' );
		$this->registerTask( 'back'    , 'back'     );
		
	}
	
	/**
	 * Task handler (Exports records)
	 *
	 * @return void
	 */
	function export(){
	
		JRequest::checkToken('post') or jexit( 'Invalid Token' );
		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$pManager->loadPlugins('export');
	
		$tempArray = JRequest::get( '', null, 'post' );
		
		$requestParameters = reset( $tempArray );
		
		require_once JFORMS_BACKEND_PATH.DS.'libraries'.DS.'Services_JSON'.DS.'Services_JSON.php';
	
		//Decode JSON value
		$json = new Services_JSON();
		
		$name         = $requestParameters['name'];
		$fid          = $requestParameters['fid'];
		$rowStart     = $requestParameters['start'];
		$rowCount     = $requestParameters['rpp'];
		$fields       = $requestParameters['fields'];
		$requestParameters['labels'] = $json->decode($requestParameters['labels']);
		
		$criteria     = $requestParameters['keyword'];
		$currentPage  = $requestParameters['page'];
		$pageCount    = $requestParameters['pageCount'];
		$totalRecords = $requestParameters['recordCount'];
		$selectedIds  = $requestParameters['ids'];
		

		$postVarName  = 'JFormXPlugin'.$name.'Parameters';
		$pluginParams =  JRequest::getVar( $postVarName, null, 'post' );
		

		$criteria = $json->decode($criteria);	

		if( isset( $pluginParams['exportRange'] )){
			switch( $pluginParams['exportRange'] ){
			
			case 'selected':
				$criteria->id = new stdClass();
				$criteria->id->numbers = explode(',', $selectedIds);
				$criteria->id->mode = 'or';
				break;
				
			case 'visible':
				$selectedIds = null;
				break;
				
			case 'pages':
				$selectedIds = null;
				$rowStart = 0;
				$rowCount = $totalRecords;
				break;
				
			case 'all':
				$selectedIds = null;
				$rowStart = -1;
				$rowCount = -1;
				$keyword  = '';
				break;
			
			}
		}
		


		if( $fields ){
			$fields = explode(',', $fields);
		}
		
		if( !array_key_exists( $name, $pManager->settings['export'] )){
			die(JText::_('Export plugin not found'));
		}
		
		//Translation mode is passed to the Element plugin to let it know in which format should it output the data
		//For instance, JUser element can output the data in HTML format or in raw format, the translation mode lets it know which to use
		$translationMode = $pManager->settings['export'][$name]->format;
		
		$recordModel = & $this->getModel('record', 'JFormsModel');		
		
		$response = $recordModel->search( $fid, $fields, $rowStart, $rowCount, $criteria, $translationMode, true );

		$pManager->invokeMethod('export' ,'onExport', array($name), array( $pluginParams, $requestParameters, $response ) );
		
		jexit( 0 );
		
	}
	
	
	/**
	 * Default task handler (View Records for a given form)
	 *
	 * @return void
	 */
	function display()
	{

		$document =& JFactory::getDocument();
		$db =& JFactory::getDBO();
		
		$viewType	= $document->getType();
		$viewName	= 'records';
		$viewLayout	= 'default';
		
		$view = & $this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->_basePath));

		$id = JRequest::getInt( 'id', 0, 'get' );
		
		// Get/Create the model
		$recordsModel = & $this->getModel('record','JFormsModel');
		$form = $recordsModel->get( $id );

		// Set the layout
		$view->setLayout($viewLayout);

		// Display the view
		$view->display( $form );
	
	}
	
	/**
	 * Task handler (Back)
	 *
	 * @return void
	 */
	function back(){$this->setRedirect('index.php?option=com_jforms');}

	/**
	 * Task handler (Deletes records)
	 *
	 * @return void
	 */
	function delete(){
		
		//TODO: Harden
		JRequest::checkToken('get') or jexit( 'Invalid Token' );
		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$pManager->loadPlugins('storage');
				
		
		$document =& JFactory::getDocument();
		$document->setCharset('utf-8');
		$document->setMimeEncoding('text/plain');
		
		$ids   = JRequest::getVar( 'ids'   , array(), 'get' );
		$fid   = JRequest::getInt( 'fid'   , 0      , 'get' );
		$jsIds = JRequest::getVar( 'jsRows', array(), 'get' );
		
		//Sanitize incoming ids
		JArrayHelper::toInteger( $ids );
		JArrayHelper::toInteger( $jsIds );
		
		$model = & $this->getModel('record','JFormsModel');		
		$model->delete( $fid, $ids );

		echo implode( ',', $jsIds );

		jexit( 0 );
	}

	/**
	 * Task handler (retrieves records , called via Ajax)
	 *
	 * @return void
	 */
	function retrieve(){

		$requestMode = 'get';
		//TODO: Harden
		JRequest::checkToken($requestMode) or jexit( 'Invalid Token' );
		
		require_once JFORMS_BACKEND_PATH.DS.'libraries'.DS.'Services_JSON'.DS.'Services_JSON.php';
		
		
		
		$document =& JFactory::getDocument();
		$document->setCharset('utf-8');
		$document->setMimeEncoding('text/plain');

		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		
		$fid   	  = JRequest::getInt( 'fid', 0, $requestMode );
		$rowStart = JRequest::getInt( 'start', -1, $requestMode );
		$rowCount = JRequest::getInt( 'count', -1, $requestMode );
		$fields   = JRequest::getString( 'fields', null, $requestMode );
		$keywords = JRequest::getString( 'keyword', null, $requestMode ); 	
		$ids      = JRequest::getString( 'ids', null, $requestMode );
		
		//Decode JSON value
		$json = new Services_JSON();
		$criteria = $json->decode($keywords);	

		if( $fields ){
			$fields = explode(',', $fields);
		}
		
		if( $rowCount > 200 ){
			return;
		}
		$model = & $this->getModel('record','JFormsModel');		
		
		$response = $model->search( $fid, $fields, $rowStart, $rowCount, $criteria );

		echo $response;
		
		jexit( 0 );
	}
}
