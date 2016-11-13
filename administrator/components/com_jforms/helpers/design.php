<?php
/**
* Design View Helper
* This class is the HTML Workhorse for the Design view (The WYSIWYG Editor)
*
* @version		$Id: design.php 374 2010-03-28 23:32:05Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Helpers
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
jimport('joomla.html.pane');

/**
 * Design View Helper class
 * This class is the HTML Workhorse for the Design view (The WYSIWYG Editor)
 *
 * @package    Joomla
 * @subpackage JForms.Helpers
*/
class JFormsDesign{
	
	
	function redirectionForm( $form ){
		
		$pane =& JPane::getInstance('sliders');
		$parameters = new JParameter('');
		$parameters->loadSetupFile(JPATH_COMPONENT.DS.'models'.DS.'redirection.xml');
		if( $form ){
			$parameters->set('thank'   , $form->redirections['thank']   );
			$parameters->set('not_auth', $form->redirections['not_auth']);
			$parameters->set('expired' , $form->redirections['expired'] );
		}
		$title   = JText::_( 'Redirections' );
		$output  = $pane->startPane("redirections-pane");
		$output .= $pane->startPanel( $title, "redirections-page" );
		$output .= $parameters->render(); 
		$output .= $pane->endPanel();
		$output .= $pane->endPane();
		echo $output;
		
	}
	
	function pluginForms($form){
	
		$pane     =& JPane::getInstance('sliders');
		$pManager =& JFormsGetPluginManager();
		
		$output = $pane->startPane("plugins-pane");
		foreach($pManager->settings['storage'] as $p){ 
			$title = JText::_( $p->name );
			$parameters = new JParameter('', $p->paramXML );
			if( $form && array_key_exists($p->name, $form->storagePluginParameters)){
				$parameters->bind( $form->storagePluginParameters[$p->name] );
			}
			$output .= $pane->startPanel( $title, $p->name."-page" );
			$output .= $parameters->render('JFormSPlugin'.$p->name.'Parameters');
			$output .= $pane->endPanel();
		}	
		$output .= $pane->endPane();
		echo $output;
		
	}
	
	function generalForm( $form ){
	
		$pane =& JPane::getInstance('sliders');
		$db   =& JFactory::getDBO();
		
		$nullDate = $db->getNullDate();
		
		$parameters = new JParameter('', JPATH_COMPONENT.DS.'models'.DS.'form.xml');

		if( $form ){
			$parameters->set('title', $form->title);
			$parameters->set('state', $form->state);
			$parameters->set('type' , $form->type);
			$parameters->set('theme', $form->theme);
			$parameters->set('plugins', $form->plugins );
			$parameters->set('publish_up', JHTML::_('date', $form->publish_up, '%Y-%m-%d %H:%M:%S'));
			if (JHTML::_('date', $form->publish_down, '%Y') <= 1969 || $form->publish_down == $nullDate) {
				$parameters->set('publish_down', JText::_('Never'));
			} else {
				$parameters->set('publish_down', JHTML::_('date', $form->publish_down, '%Y-%m-%d %H:%M:%S'));
			}
			$parameters->set('maximum' , $form->maximum );
			$parameters->set('groups'  , $form->groups );
		}
		
		$title   = JText::_( 'Form information' );
		$output  = $pane->startPane("form-pane");
		$output .= $pane->startPanel( $title, "form-page" );
		$output .= $parameters->render(); 
		$output .= $pane->endPanel();
		$output .= $pane->endPane();
		echo $output;
		
	}
	
	function properties(){
		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$plugins  = $pManager->settings['element'];

		$output = "<div id='ppage_container'>";
		foreach($plugins as $plugin){
			$output .= "<div id='ppage_{$plugin->name}' class='ppage' style='display:none'>";
			$pluginPropertiesForm = new JParameter('JFormsEPlugin_'.$plugin->name, $plugin->paramXML);
			$output .= $pluginPropertiesForm->render('JFormsEPlugin_'.$plugin->name);

			
			//Conversion tools (Only for plugins that require DB storage)
			if( count($plugin->storage) ){
				$output .= _line('<hr />',3);
				$output .= _line('<label style="font-weight:bold;float:left;margin-right:10px;margin-left:20px;" for="select_convert_'.$plugin->name.'">'.JText::_('Convert to').'</label>',3);
				$output .= _line('<select style="float:left;margin-right:10px;" id="select_convert_'.$plugin->name.'">',4);
				foreach($plugins as $p){
					//Only add those with storage demands
					if( !count($p->storage) )continue;
					
					//Don't add the current plugin to the list
					if( $p->name == $plugin->name )continue;
				
					$label = JText::_($p->name);
					$output .= _line('<option value="'.$p->name.'">'.$label.'</option>',5);
				}
				$output .= _line('</select>',4);
				$output .= _line("<input style='float:left;' onclick='if(confirm(\"".JText::_('Are you sure you want to convert this Element?')."\"))convert($(\"select_convert_{$plugin->name}\"))' type='button' value='".JText::_('Convert')."' />",4);
				$output .= _line('<br clear="all" />',4);
			}
			$output .= "</div>";
		}
		$output .= "</div>";
		echo $output;
	}
	
	function toolbar(){
		
		$pane	  =& JPane::getInstance('sliders');

		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$groupedElementPlugins = $pManager->invokeMethod('element', 'getCategorizedElements',array('_MANAGER'),null);
		
		$output = '';
		$windowLoadFunction = '';
		foreach($groupedElementPlugins as $group => $plugins){

			$title   = JText::_( $group );
			$output .= $pane->startPane($group);	
			$output .= $pane->startPanel( ucfirst($title), $group );
			$output .= '<ul class="toolbar-category">';
			foreach($plugins as $name => $data){
				$jsId = $name.'_control';
				$output .= '<li><div id="'.$jsId.'" class="controls hasTip" title="'.JText::_($data->description).'" style="background-image:url('.$data->button.');"  name="'.$name.'">'.JText::_($name).'</div></li>';
			}
			$output .= "</ul>";
			$output .= $pane->endPanel();
			$output .= $pane->endPane();
		}
		echo $output;
	}
	
	function javascript_placeElements( $elements ){
		
		$output  = _line("<script type='text/javascript'>",1);
		$output .= _line('//<![CDATA[',1);
		
		
		if( !$elements ){
			$output .= _line('function placeElements(){;}',2);
			$output .= _line('//]]>',1);
			$output .= _line('</script>',1);
			echo $output;
			return;
		}		
		
		$output .= _line('function placeElements(){',2);
	
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$plugins  = $pManager->settings['element'];
		
		$output  = _line("<script type='text/javascript'>",1);
		$output .= _line('//<![CDATA[',1);
		
		$output .= _line('function placeElements(){',2);
	
		foreach( $elements as $element ){
		
			$output  .= _line('elementArray.push(new '.$element->type.'($("clist"),autoIncrement,null,',3);
			$output  .= _line('{',3);
		
			$paramIdList = _line('paramIdList += "'.$element->parameters['hash'].';";',3);
			$idArray = array();
		
			$parameters = $plugins[$element->type]->parameters;
		
			foreach( $parameters as $param ){
			
				//Get parameter info
				$valueType    = $plugins[$element->type]->parameters[$param->name]->valueType;
				$defaultValue = $plugins[$element->type]->parameters[$param->name]->default;
				
				$parameterId    = 0;
				$parameterValue = $defaultValue;
				if( array_key_exists( $param->name, $element->parameters ) ){
					$parameterId    = $element->parametersId[$param->name];
					$parameterValue = $element->parameters[$param->name];
				}
			
				if($valueType == 'string' ){
					//Fix for forms generated using version prior to 0.5 RC2
					$parameterValue = str_replace("\r", '', $parameterValue);
					
					//Prepare for Javascript
					$parameterValue = addslashes( $parameterValue );
					$parameterValue = str_replace( "\n",'\n', $parameterValue);
					
					$output .= _line($param->name . ':"' . $parameterValue .'",',4);
				} else {
					$parameterValue = intval( $parameterValue );
					$output .= _line($param->name . ':' .$parameterValue .',' ,4);
				}
				if($parameterId)$idArray[] =  $param->name . '=>' . $parameterId;
			}
			$paramIdList .= _line('paramIdList += "'.implode(',',$idArray).'"',3);
			$paramIdList .= _line('paramIdList += "|";',3);
			$output   = substr($output,0,strlen($output)-2)."\n";
			$output  .= _line('}));',3);
			$output  .= _line('autoIncrement++;',3);
			$output  .= $paramIdList;
		}			
		$output .= _line('}',2);
		$output .= _line('//]]>',1);
		$output .= _line('</script>',1);
		echo $output;
	}
	
	function javascript_initialization(){
		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$plugins  = $pManager->settings['element'];
		
		$output  = _line("<script type='text/javascript'>",1);
		$output .= _line('//<![CDATA[',1);
		
		//Obligatory elements list
		//A list that defines a set of elements which at least one of them must exist in the form
		$count  = 0;
		foreach( $plugins as $p ){
			//Does it have a storage requirment?
			if( count($p->storage) ){
				//Add it to the list
				$output .= _line('obligatoryList['.$count.'] = "'.$p->name.'";',2);
				$count++;
			}
		}
		
		//Count limit for elements
		//A list that defines how many instances of any given element can be present in one form
		$countLimit = 'var countLimit = {';
		$lines = array();
		foreach( $plugins as $p ){
			$lines[] = "'". $p->name ."':" . $p->limit;
		}
		$countLimit .= implode(',',$lines);
		$countLimit .= "};";
		$output .= _line($countLimit,2);
		
		$output .= _line('//]]>',1);
		$output .= _line('</script>',1);
		echo $output;
	
	}
	
	function javascript_loadGators(){
			$jsGatorPath = JFORMS_BACKEND_PATH.DS.'elements'.DS.'js';
			$files = JFolder::files($jsGatorPath,"\\.js$");
			foreach( $files as $file )
				JHTML::_('JForms.General.script', $file ,$jsGatorPath);
	}
	
	function javascript_addElement(){
		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$plugins  = $pManager->settings['element'];
		
		$output  = _line("<script type='text/javascript'>",1);
		$output .= _line('//<![CDATA[',1);
		
		$output .= _line('function addElement(element){',2);
		$output .= _line('element = $(element)',3);
		$output .= _line('var areaCoords = $("workarea-td").getCoordinates();',3);		
		$output .= _line('var elementPosition = element.getPosition();',3);
		$output .= _line('var elementSize     = element.getSize();',3);
		$output .= _line('var x = elementPosition.x ;',3);
		$output .= _line('var y = elementPosition.y + (elementSize.y/2) ;',3);
		$output .= _line('if( x < areaCoords.left || x > areaCoords.right)return;',3);	
		$output .= _line('if( y < areaCoords.top  || y > areaCoords.bottom)return;',3);
		$output .= _line('var insertBeforeObject = beforeWhich(x,y);',3);
		$output .= _line('var order = getLiIndex(insertBeforeObject);',3);
		$output .= _line('addElementEx(element.get("name"),order);',3);
		$output .= _line('}',2);
		
	
		$output .= _line('function addElementEx(type,order){',2);
		$output .= _line('if(reachedLimit(type)){',3);
		$output .= _line('alert("'.JText::_('You cannot place anymore instances of this element').'");',4);
		$output .= _line('return;',4);
		$output .= _line('}',3);
		$output .= _line('var insertBeforeObject = getLiAt( order );',3);
		$output .= _line('switch(type){',3);
		foreach($plugins as $name => $data){
			$output .= _line('case "'.$name.'":',4);
			$output .= _line('elementArray.push(new '.$name.'($("clist"),autoIncrement,insertBeforeObject,',5);
			if( count( $data->parameters ) ){
				$output .= _line('{',5);
				foreach( $data->parameters as $p ){
					if($p->valueType == 'integer' ){
							$propertyText = $p->name.':'.$p->default.',';
					} else {
							$propertyText = $p->name.':"'.$p->default.'",';
					}
					$output .= _line($propertyText,6);	
				}
				$output = substr($output,0,strlen($output)-2)."\n";
				$output .= _line('}))',5);
			}
			$output .= _line('autoIncrement++;',5);
			$output .= _line('break;',4);
		}
		$output .= _line('}',3);
		$output .= _line('if(selectedElement){',3);
		$output .= _line('//Some sort of "refreash"',4);
		$output .= _line('selectedElement.select();',4);
		$output .= _line('}',3);
		$output .= _line('return elementArray[elementArray.length-1];',3);
		$output .= _line('}',2);
		$output .= _line('//]]>',1);
		$output .= _line('</script>',1);
		echo $output;
	}
	
	function javascript_saveProperties(){
		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$plugins  = $pManager->settings['element'];
		
		$output  = _line("<script type='text/javascript'>",1);
		$output .= _line('//<![CDATA[',1);
		
		$output .= _line('function saveProperties(){',1);
		$output .= _line('if(selectedElement == null)return;',2);
		$output .= _line('switch( selectedElement.type ){',2);
		
		foreach($plugins as $name => $data){
		
			$output .= _line('case "'.$name.'":',3);
			foreach( $data->parameters as $p ){
				
				if( $p->type == 'hidden')continue;
				$jsId = "JFormsEPlugin_{$name}{$p->name}";
				if( $p->valueType == 'integer')
					$output .= _line("selectedElement.options.{$p->name} = parseInt({$p->type}_get($('$jsId')),10);",4);
				else
					$output .= _line("selectedElement.options.{$p->name} = {$p->type}_get($('$jsId'));",4);
				
			}
			$output .= _line('break;',3);
		}
		$output .= _line("}",2);
		$output .= _line("//Trigger update Event",2);
		$output .= _line("selectedElement.onUpdate();",2);
		$output .= _line('$$("li.element .drag-handle").each(resizeDragHandle);',2);
		$output .= _line("}",1);

		$output .= _line('//]]>',1);
		$output .= _line('</script>',1);
		echo $output;
	}
	
	function javascript_displayProperties(){
		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$plugins  = $pManager->settings['element'];
		
		$output  = _line("<script type='text/javascript'>",1);
		$output .= _line('//<![CDATA[',1);
		$output .= _line('function displayProperties(){',1);
		$output .= _line('if(selectedElement == null)return;',2);
		$output .= _line('switch( selectedElement.type ){',2);
		foreach($plugins as $name => $data){
			$output .= _line("case '$name':",3);
			foreach( $data->parameters as $p ){
				$jsId = "JFormsEPlugin_{$name}{$p->name}";
				$output .= _line("{$p->type}_set($('$jsId'),selectedElement.options.{$p->name});",4);
			}
			$output .= _line('break;',3);
		}
		$output .= _line('}',2);
		$output .= _line('}',1);
		$output .= _line('//]]>',1);
		$output .= _line('</script>',1);
		echo $output;
	}
	
	function javascript( $elements ){
	
		$jsScriptsURI  = JURI::root() . 'media/com_jforms/scripts/';
		$jsTinyMCEURI  = JURI::root() . 'plugins/editors/tinymce/jscripts/tiny_mce/';
		$jsDesignPath  = JFORMS_BACKEND_PATH.DS.'helpers'.DS.'scripts'.DS.'design'.DS;

		JHTML::_('script', 'sha1.js' 	 ,$jsScriptsURI);
		JHTML::_('script', 'utilities.js',$jsScriptsURI);
		JHTML::_('script', 'tiny_mce.js' ,$jsTinyMCEURI);
		
		JHTML::_('JForms.General.script', 'CErrorTip.js'		,$jsDesignPath);
		JHTML::_('JForms.General.script', 'design.js'			,$jsDesignPath);
		JHTML::_('JForms.General.script', 'event.js'			,$jsDesignPath);
		JHTML::_('JForms.General.script', 'CElement.js'			,$jsDesignPath);
		JHTML::_('JForms.General.script', 'CLabeledElement.js'	,$jsDesignPath);
		JHTML::_('JForms.General.script', 'utilities.js'		,$jsDesignPath);
		JHTML::_('JForms.General.script', 'tinymce.js'			,$jsDesignPath);
		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$plugins  = $pManager->settings['element'];
		
		foreach($plugins as $p)
			JHTML::_('JForms.General.script', basename($p->js), dirname($p->js) );
		
		
		
		JFormsDesign::javascript_initialization();
		JFormsDesign::javascript_loadGators();
		JFormsDesign::javascript_displayProperties();
		JFormsDesign::javascript_saveProperties();
		JFormsDesign::javascript_addElement();
		JFormsDesign::javascript_placeElements($elements);
		
		
		
	}
}