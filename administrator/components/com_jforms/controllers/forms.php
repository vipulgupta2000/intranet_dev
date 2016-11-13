<?php
/**
* Forms backend controller
*
* @version		$Id: forms.php 386 2010-05-22 08:41:41Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Controllers
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

jimport('joomla.application.component.controller');

/**
 * Forms backend Controller
 *
 * @package    Joomla
 * @subpackage JForms.Controllers
 */
class FormsController extends JController
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
		$this->registerTask( 'add', 'add');
		$this->registerTask( 'edit', 'edit');
		$this->registerTask( 'save', 'save');
		$this->registerTask( 'publish', 'publish');
		$this->registerTask( 'unpublish', 'unpublish');
		$this->registerTask( 'remove', 'remove');
		$this->registerTask( 'copy'   ,  'copy');		
		$this->registerTask( 'cancel', 'cancel');
		$this->registerTask( 'back', 'back');
		
		
	}
	
	/**
	 * Task handler (Show Element dialog "used in menu manager")
	 *
	 * @return void
	*/
	function element()
	{
		$document =& JFactory::getDocument();

		$viewType	= $document->getType();
		$viewName	= 'element';
		$viewLayout	= 'default';
		$view = & $this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->_basePath));
		
		// Get/Create the model
		$formModel = & $this->getModel('form','JFormsModel');

		//Get a listing of all forms ( TODO : Add pagination )
		$response = $formModel->search();
		$forms    = $response['forms'];
		
		// Set the layout
		$view->setLayout($viewLayout);

		// Display the view
		$view->display($forms);
	}
	
	/**
	 * Task handler (Add new form)
	 *
	 * @return void
	 */
	function add()
	{
		$document =& JFactory::getDocument();
		$viewType	= $document->getType();
		$viewName	= 'design';
		$viewLayout	= 'default';

		$view = & $this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->_basePath));
		
		$view->setLayout($viewLayout);

		$view->display(null);
	}
	
	/**
	 * Task handler (Edit existing form)
	 *
	 * @return void
	 */
	function edit(){
	
		$idArray = JRequest::getVar( 'cid', array(), 'get' );
		$id      = intval( $idArray[0] );
	
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$pManager->loadPlugins('storage');
		
		
		$document =& JFactory::getDocument();
		$user	  =& JFactory::getUser();		
		
		// Get/Create the model
		$formModel =& $this->getModel('form','JFormsModel');	
		$form   =  $formModel->get( $id );
		
		//Invalid form?
		if( $form == null ){
			$this->setRedirect('index.php?option=com_jforms', JText::_('Form not found'), 'error');
			return;
		}
		
		//Locked Form?
		if ( JTable::isCheckedOut($user->get ('id'), $form->checked_out )){
			$msg = JText::sprintf(JText::_('DESCBEINGEDITTED'), JText::_('The item'), $form->title);
			$this->setRedirect('index.php?option=com_jforms', $msg, 'error');
			return;
		}
		
		//Check the field out
		$form->checkout($user->get('id'));

		//Prepare the view
		$viewType	= $document->getType();
		$viewName	= 'design';
		$viewLayout	= 'default';
		$view = & $this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->_basePath));
		$view->setLayout($viewLayout);

		//Sort elements
		$sortedElements = array();
		foreach( $form->fields as $f ){
			$sortedElements[$f->position] = $f;
		}
		$form->fields = $sortedElements;

		// Display the view
		$view->display($form);
	}
	
	/**
	 * Task handler (Save form to Database)
	 *
	 * @return void
	 */
	function save()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$pManager->loadPlugins('storage');
		

		require_once JFORMS_BACKEND_PATH.DS.'libraries'.DS.'Services_JSON'.DS.'Services_JSON.php';

		//Start preparing the data received via POST to be sent to the model for saving
		$params	= JRequest::getVar( 'params', array(), 'post', 'array');

		//Process active plugins
		if( !array_key_exists('plugins',$params) ){
			$params['plugins'] = array();
		}
		//Force Database plugin to be on
		if( !in_array( 'Database', $params['plugins'] )){
			array_push( $params['plugins'], 'Database' ); 
		}

		//Maximum records allowed for this form
		$params['maximum'] = intval($params['maximum']);

		//Process redirections
		$redirectionsObject = array();
		$redirectionsObject['thank']    = $params['thank'];
		$redirectionsObject['not_auth'] = $params['not_auth'];
		$redirectionsObject['expired']  = $params['expired'];
		$params['redirections']   = base64_encode(serialize($redirectionsObject));
	
		//Determine whether this is a new Entry or an already existant one "To be sent to StoragePlugin"
		$isNew = $params['id'] == 0?true:false;
		
		$params['storagePluginParameters'] = array();
		foreach( $pManager->settings['storage'] as $p ){
			$params['storagePluginParameters'][$p->name] = 
				JRequest::getVar( 'JFormSPlugin'.$p->name.'Parameters', array(), 'post','array');
		}

		//Decode JSON value
		$json = new Services_JSON();
		$jsonArray = $json->decode( $params['fieldInformation'] );
	
		$params['fieldInformation'] = array();
		foreach( $jsonArray as $entry ){
			$value = $json->decode($entry);	
			$params['fieldInformation'][] = $value;
		}

		
		$params['paramListId'] = null;	
		if( !$isNew ){
			//Param id list, for multilanguage support
			$pIdList = trim($params['paramIds'],'|');
			$tempList = explode( '|', $pIdList );
			$params['paramListId'] = array();
			foreach( $tempList as $entry ){
				list($hash,$data) = explode ( ';', $entry );
				$e = explode( ',', $data );
				$params['paramListId'][$hash] = array();
				foreach( $e as $parameter ){
					list($name,$id ) = explode('=>', $parameter );
					$params['paramListId'][$hash][$name] = intval( $id );
				}
			}
			//Ends here
		}
		$params['plugins'] = implode( ',' , $params['plugins'] );
		
		$formModel =& $this->getModel('form','JFormsModel');
		
		//Send to the model to for saving
		$id = $formModel->save($params);
		
		if( !$id ){
			$this->setRedirect('index.php?option=com_jforms', JText::_('Failed to save the form'), 'error');
		} else {
			$this->setRedirect('index.php?option=com_jforms', JText::_('Form saved'));
		}
	}
	
	/**
	 * Task handler (Publish form)
	 *
	 * @return void
	 */
	function publish()
	{
		JRequest::checkToken('get') or jexit( 'Invalid Token' );
		
		$cids = JRequest::getVar( 'cid', array(), 'get', 'array' );
		JArrayHelper::toInteger($cids);
		if ( !count($cids) ) {
			$this->setRedirect('index.php?option=com_jforms', JText::_('Select an item to publish'), 'error');
		}

		$formModel =& $this->getModel('form','JFormsModel');

		if( $formModel->publish($cids) ){
			$this->setRedirect('index.php?option=com_jforms', JText::_('Published successfuly'));
		} else {
			$this->setRedirect('index.php?option=com_jforms', JText::_('Failed to publish selected form(s)'), 'error');
		}
	}
	
	/**
	 * Task handler (unpublish form)
	 *
	 * @return void
	 */
	function unpublish()
	{
		
		JRequest::checkToken('get') or jexit( 'Invalid Token' );
		
		$cids = JRequest::getVar( 'cid', array(), 'get', 'array' );
		JArrayHelper::toInteger($cids);
		if (!count($cids)) {
			$this->setRedirect('index.php?option=com_jforms', JText::_('Select an item to unpublish'), 'error');
		}

		$formModel =& $this->getModel('form','JFormsModel');

		if( $formModel->unpublish($cids) ){
			$this->setRedirect('index.php?option=com_jforms', JText::_('Unpublished successfuly'));
		} else {
			$this->setRedirect('index.php?option=com_jforms', JText::_('Failed to unpublish selected form(s)'), 'error');
		}
	}
	
	/**
	 * Task handler (Duplicates a form)
	 *
	 * @return void
	 */
	 function copy(){
	
		JRequest::checkToken('get') or jexit( 'Invalid Token' );

		$cids	  = JRequest::getVar( 'cid'    , array(), 'get' , 'array' );
		$newName  = JRequest::getVar( 'newName', null   , 'get' );

		JArrayHelper::toInteger($cids);
		
		if ( !count($cids) ) {
			$this->setRedirect('index.php?option=com_jforms', JText::_('Select an item to copy'), 'error');
		}

		$formModel =& $this->getModel('form','JFormsModel');
		
		if( $newName == '' )$newName = 'Copy';
		
		if( $formModel->copy($cids,$newName) ){
			$this->setRedirect('index.php?option=com_jforms', JText::_('Copied successfuly'));
		} else {
			$this->setRedirect('index.php?option=com_jforms', JText::_('Failed to copy form'), 'error');
		}
	}
	
	/**
	 * Task handler (Delete form(s))
	 *
	 * @return void
	 */
	function remove()
	{
		JRequest::checkToken('get') or jexit( 'Invalid Token' );
		
		$pManager =& JFormsGetPluginManager();
		$formModel    =& $this->getModel('form','JFormsModel');

		$pManager->loadPlugins('storage');
		
		$cids = JRequest::getVar( 'cid', array(), 'get', 'array' );

		
		JArrayHelper::toInteger($cids);
		if ( !count($cids) ) {
			$this->setRedirect('index.php?option=com_jforms', JText::_('Select an item to delete'), 'error');
			return;
		}

		foreach( $cids as $id ){			
	
			$form = $formModel->get( $id );
			if( !$form )continue;
	
			//Trigger onDelete event for storagePlugins
			$pManager->invokeMethod('storage', 'onFormDelete', 
									null, array( &$form, array() ) );

		}
		
		//Delete forms
		$formModel->delete( $cids );

		$this->setRedirect('index.php?option=com_jforms',  JText::_('Form(s) deleted'));
	}
	
	/**
	 * Task handler (Back)
	 *
	 * @return void
	 */
	function back(){$this->setRedirect('index.php?option=com_jforms');}
	
	/**
	 * Task handler (Cancel)
	 *
	 * @return void
	 */
	function cancel()
	{
		$params	= JRequest::getVar( 'params', array(), 'post', 'array');
		$id = intval( $params['id']);
		if( $id ){
			$formModel = & $this->getModel('form','JFormsModel');
			$formModel->close($id);
		}
		$this->setRedirect('index.php?option=com_jforms',  JText::_('Action cancelled'), 'error');
	}
	
	/**
	 * Default task handler (Displays a list of all forms in the system)
	 *
	 * @access	public
	 */
	function display()
	{
		$document =& JFactory::getDocument();

		$viewType	= $document->getType();
		$viewName	= 'forms';
		$viewLayout	= 'default';
		$view = & $this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->_basePath));
		
		// Get/Create the model
		$formModel = & $this->getModel('form','JFormsModel');

		//Get a listing of all forms ( TODO : Add pagination )
		$response = $formModel->search();
		$forms    = $response['forms'];
		
		// Set the layout
		$view->setLayout($viewLayout);

		// Display the view
		$view->display($forms);
	}
}
