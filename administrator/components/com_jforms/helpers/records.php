<?php
/**
* Record View Helper
* This class is the HTML Workhorse for the Records view 
*
* @version		$Id: records.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Helpers
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.html.pane');

/**
 * Records View Helper
 * This class is the HTML Workhorse for the Records view
 *
 * @package    Joomla
 * @subpackage JForms.Helpers
*/
class JFormsRecords{
	
	function elementForms( $form ){
		
		$pane     =& JPane::getInstance('sliders');
		$pManager =& JFormsGetPluginManager();
		
		$pManager->loadPlugins('element');
		$plugins  = $pManager->settings['element'];
		
		$output  = $pane->startPane("search-pane");
		$output .= '<ul id="search-pane-list">';
		foreach($form->fields as $f){
			$pluginType = $plugins[$f->type];
			if( $pluginType->storage ){
					
					$title   = stripslashes( $f->parameters['label'] );
					$hash    = $f->parameters['hash'];
					
					$parameters = new JParameter('', $pluginType->searchXML );
				
					$output .= '<li title="'.$hash.'|'.$title.'">';
					$output .= '<div class="search-pane-list-handle"></div>';
					$output .= '<div class="search-pane-list-check"><input value="$hash|$title" type="checkbox" name="loaded_headers[]" checked="checked" id="header_'.$hash.'" /></div>';
					$output .= $pane->startPanel( $title, $hash."-page" );
					$output .= $parameters->render('JFormEPlugin'.$hash.'Parameters');
					$output .= $pane->endPanel();
					$output .= '</li>';
			}	
		}
		$output .= '</ul>';
		$output .= $pane->endPane();
		echo $output;
	}
	
	function exportForms( $form ){
		
		$pane     =& JPane::getInstance('sliders');
		$pManager =& JFormsGetPluginManager();

		$pManager->loadPlugins('export');
		$plugins  = $pManager->settings['export'];
		

		$output = $pane->startPane('export-pane');
		foreach($plugins as $p){

			$parameters = new JParameter('', $p->paramXML );
			$parameters->set('name', $p->name);
			$parameters->set('fid' , $form->id);
			
			$title   = JText::_( $p->name );
			$output .= $pane->startPanel( $title, $p->name."-page" );
			$output .= _line('<form action="index.php" method="post" name="JFormXPlugin'.$p->name.'" id="JFormXPlugin'.$p->name.'">',1);	
			$output .= $parameters->render('JFormXPlugin'.$p->name.'Parameters');
			$output .= _line('<input type="hidden" name="option" value="com_jforms" />',2);
			$output .= _line('<input type="hidden" name="task" value="export" />',2);
			$output .= _line('<input type="hidden" name="controller" value="records" />',2);
			$output .= _line("<input class='export-button' type='button' onclick='exportRecords(\"JFormXPlugin{$p->name}\");' class='button' value='".JText::_('Export')."' />",2);
			$output .= _line(JHTML::_( 'form.token' ),2); 
			$output .= _line('</form>',1);
			$output .= $pane->endPanel();
			

		}
		$output .= $pane->endPane();
		
		JHTML::_('JForms.General.fixPane');
		echo $output;
	}
	
	function mapping($form){
	
		$pane     =& JPane::getInstance('sliders');
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
				
		$output = $pane->startPane('mapping-pane');
		$output .= $pane->startPanel( JText::_('Expand'), "mapping-page" );
		$output .= _line('<table style="font-weight:bold;">',1);
		$output .= _line('<tr>',1);
		$output .= _line('<td>ID</td>',2);
		$output .= _line('<td style="color:green">id</td>',2);
		$output .= _line('</tr>',1);
		
		foreach($form->fields as $f){
			if( !$pManager->invokeMethod('element','hasStorageRequirements', array('_MANAGER'), array($f)))continue;
			$output .= _line('<tr>',1);
			$output .= _line('<td>'.stripslashes($f->parameters['label']).'</td>',2);
			$output .= _line('<td style="color:green">'.$f->parameters['hash'].'</td>',2);
			$output .= _line('</tr>',1);
		}
		$output .= _line('</table>',1);
		$output .= $pane->endPanel();
		$output .= $pane->endPane();
				
		echo $output;
	}
	
	function controls(){
	
		$output  = "<label for='record_per_page'>".JText::_('Records per page')."</label>";
		$output .= "<select id='record_per_page' name='record_per_page' onchange='refreshPageList()'>";
		$output .= "<option value='5'>5</option>";
		$output .= "<option value='20' selected='selected'>20</option>";
		$output .= "<option value='50'>50</option>";
		$output .= "<option value='100'>100</option>";
		$output .= "<option value='200'>200</option>";
		$output .= "</select><br clear='all' />";
		
		$output .= "<label for='current_page'>".JText::_('Current Page')."</label>";
		$output .= "<select id='current_page' name='current_page' >";
		$output .= "<option value='1'>1</option>";
		$output .= "</select><br clear='all' />";
		
		$output .= "<input id='reload-button' value='".JText::_('Reload')."' onclick='reloadRecords()' type='button' />";		
		$output .= "<input id='delete-button' value='".JText::_('Delete Selected')."' onclick='deleteSelected()' type='button' />";		
		
		echo $output;
	}
	
	function javascript_constructKeyword( $form ){
	
		$output  = _line("<script type='text/javascript'>",1);
		$output .= _line('//<![CDATA[',1);
		
		$output .= _line("function constructKeyword(){",1);
		$output .= _line("var KeywordsObject = new Object();",2);
		foreach( $form->fields as $f ){
			$hash = $f->parameters['hash'];
			$arrayBase = 'JFormEPlugin'.$hash.'Parameters';
			$output .= _line("var Children = getHTMLArrayChildren('$arrayBase',$('filter_form'));",2);
			$output .= _line("KeywordsObject.$hash = new Object();",2);
			$output .= _line("for(i=0;i<Children.length;i++){",2);
			$output .= _line("KeywordsObject.$hash [Children[i][1]]=$('filter_form').elements[Children[i][0]].value;",3);
			$output .= _line("}",2);
		}
		$output .= _line("var orderedKeywords = new Object();",2);
		$output .= _line("$$('#search-pane-list li').each(function(li) { var hash = li.get('title').split('|')[0];orderedKeywords[hash] = KeywordsObject[hash]; })",2);
		$output .= _line("return JSON.encode(orderedKeywords);",2);
		$output .= _line("}",1);
		
		$output .= _line('//]]>',1);
		$output .= _line('</script>',1);
		
		echo $output;
		
	}	
	
	/**
	 * Outputs HTML <script> tags that includes the javascript
	 *
	 * @return void
	 */
	function javascript( $form )
	{	
		$jsScriptsURI  = JURI::root() . 'media/com_jforms/scripts/';
		$jsRecordsPath  = JFORMS_BACKEND_PATH.DS.'helpers'.DS.'scripts'.DS.'records'.DS;

		JHTML::_('script', 'utilities.js'	    ,$jsScriptsURI);
		JHTML::_('script', 'dhtmlxcommon.js'	,$jsScriptsURI);
		JHTML::_('script', 'dhtmlxgrid.js'		,$jsScriptsURI);
		JHTML::_('script', 'dhtmlxgridcell.js'	,$jsScriptsURI);

		JHTML::_('JForms.General.script', 'records.js' ,$jsRecordsPath, $form);
		JHTML::_('JForms.General.script', 'events.js'  ,$jsRecordsPath);
		JFormsRecords::javascript_constructKeyword( $form );
	
	}
	
}