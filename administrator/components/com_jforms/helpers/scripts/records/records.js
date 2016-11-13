<?php
/**
*  Main Javascript functions for the records view
*
* @version		$Id: records.js 370 2010-03-27 01:38:56Z dr_drsh $
* @package		Joomla
* @subpackage	JForms
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

$arguments = func_get_args();
$form      = $arguments[2];
?>

var jsDataGrid 	 = null;
var htmlDataGrid = null;
var totalRecords = 0;

var lastRequestKeyword = '';
var lastRequestRPP 	   = 0;

var ajaxRequest = null;
	
var currentRows        = null;
var currentFormId      = null;
var currentKeyword     = null;
var currentFields      = null;
var currentStart       = null;
var currentRPP         = null;
var currentPageCount   = null;
var currentPage        = null;
var currentRecrodCount = null;
var currentLabels	   = null;
	
var searchListSortable = null;	
	
function reloadRecords(){

	var recordPerPage = parseInt($('record_per_page').options[$('record_per_page').selectedIndex].value,10); 
	var currentPage   = parseInt($('current_page').options[$('current_page').selectedIndex].value,10); 
	
	var startRecord = (currentPage-1) * recordPerPage;

	var tableHeaders = new Array({'label':'ID','hash':'id'});
	$$('#search-pane-list li').each(function(li) {	
		var hash  = li.get('title').split('|')[0];
		var label = li.get('title').split('|')[1]; 
		if( $('header_'+hash).checked){
			tableHeaders.push({
				'label': label,
				'hash' : hash
			});
		}
	});
		
	var fields  = new Array();
	var headers = new Array();

	for(i=0;i<tableHeaders.length;i++){
		headers[i] = tableHeaders[i].label;
		fields[i]  = tableHeaders[i].hash;
	}


		
	var keyword = constructKeyword();
	//$('headers_filter').value;
	
	//Restore page pointer to the 1st page if Keyword is changed OR RecordPerPage is changed
	if( lastRequestKeyword != keyword || lastRequestRPP != recordPerPage ){
		startRecord = 0;
		$('current_page').selectedIndex = 0;
		lastRequestRPP = recordPerPage;
		lastRequestKeyword = keyword;
	}
	

	var jtoken    = '<?php echo JUtility::getToken(); ?>';
	var fid       = '<?php echo $form->id ?>';
	var url       = "<?php echo JURI::base().'index.php'; ?>";
	
	recreateGrid(headers);
		ajaxRequest = new Request({
			url: url,
			method: 'get', 
			onSuccess: onReloadSuccess,
			onFailure: onReloadFailure
	});

	//Update Global Variables
	currentKeyword = keyword;
	currentFormId  = fid;
	currentFields  = fields;
	currentLabels  = headers;
	currentStart   = startRecord;
	currentRPP     = recordPerPage;
	
	
	ajaxRequest.send(
	   "option=com_jforms"
	 + "&task=get"
	 + "&controller=records"
	 + "&fid=" + fid
	 + "&keyword=" + keyword
	 + "&fields=" + fields.join(',')
	 + "&start=" + startRecord
	 + "&count=" + recordPerPage
	 + "&"+jtoken+"=1"
	 + '&format=raw'
	);
}

function exportRecords( form ){

	var exportForm = $(document.forms[form]);
	var prefix = exportForm.get('name') + 'Parameters';
	
	exportForm.elements[prefix+"[fid]"].value = currentFormId;
	exportForm.elements[prefix+"[keyword]"].value = currentKeyword;
	exportForm.elements[prefix+"[fields]"].value = currentFields;
	exportForm.elements[prefix+"[labels]"].value = JSON.encode(currentLabels);
	exportForm.elements[prefix+"[start]"].value = currentStart;
	exportForm.elements[prefix+"[rpp]"].value = currentRPP;
	exportForm.elements[prefix+"[page]"].value = currentPageCount;
	exportForm.elements[prefix+"[pageCount]"].value = currentPageCount;
	exportForm.elements[prefix+"[recordCount]"].value = currentRecrodCount;
	exportForm.elements[prefix+"[ids]"].value= '';
	var exportPluginName = exportForm.elements[prefix+"[name]"].value;
	
	//Did user select any records?
	selectedIds = jsDataGrid.getSelectedId();
	exportRangeElement = exportForm.elements['JFormXPlugin'+exportPluginName+'Parameters[exportRange]'];
	if(exportRangeElement && exportRangeElement.value == 'selected' && selectedIds == null ){
		alert("<?php echo JText::_('Please select the records you would like to export')?>");
		return;
	}
	
	if(selectedIds != null ){
		var gridSelectedIds = selectedIds.split(',');
		var recordIds = new Array();
		for(i=0;i<gridSelectedIds.length;i++){
			id = parseInt( gridSelectedIds[i], 10 );
			recordIds[i] = jsDataGrid.cells( id, 0 ).getValue();
		}
		exportForm.elements[prefix+"[ids]"].value = recordIds.join(',');
	}
	exportForm.submit();
}
	
function deleteRecords( rowIds ){

	if( jsDataGrid == null ) return;
	var cfm = confirm("<?php echo JText::_('Are you sure you want to delete this/these record(s)?'); ?>");
	if( !cfm )return;

	var dbRowIdText = '';
	var jsRowIdText = '';
	var recordIds = new Array();

		
	for(i=0;i<rowIds.length;i++){
		id = parseInt( rowIds[i], 10 );
		recordIds[i] = jsDataGrid.cells( id, 0 ).getValue();
		dbRowIdText += 'ids[]='+recordIds[i]+'&';
		jsRowIdText += 'jsRows[]='+rowIds[i]+'&';
	}
	var jtoken    = '<?php echo JUtility::getToken(); ?>';
	var fid	      = '<?php echo $form->id ?>';
	var url       = "<?php echo JURI::base().'index.php'; ?>";
		
	ajaxRequest = new Request({
		url: url,
		method: 'get', 
		onSuccess: onDeleteSuccess,
		onFailure: onDeleteFailure
	});
		

	ajaxRequest.send(	  
		'option=com_jforms'
		+ '&task=delete'
		+ "&controller=records"
		+ '&fid=' + fid + '&'
		+ jsRowIdText
		+ dbRowIdText
		+ jtoken+'=1'
		+ '&format=raw'
	);
	$('loadingDiv').set('styles',{'display':'block'});		
		
}

function deleteSelected(){
	
	if( jsDataGrid == null ) return;
	var selectedIds = jsDataGrid.getSelectedId().split(',');
	if( selectedIds.length == 0 )return;
	deleteRecords( selectedIds );

}

	
function recreateGrid(headers){
		
	if( htmlDataGrid != null )
		htmlDataGrid.destroy();
		
	colWidthArray = new Array();
	if( jsDataGrid ){
		//Save current column width
		var c = jsDataGrid.getColumnsNum();
		if( c == headers.length ){
			for(i=0;i<c;i++){
				colWidthArray[i] = jsDataGrid.getColWidth(i);
			}
		}
		//Delete grid
		jsDataGrid = null;
	}
		
	//Couldn't get last column width
	if( colWidthArray.length == 0 ){
		//Calculate default value
		var colWidth = Math.floor( ( (screen.width-100) * 0.8) / headers.length);
		colWidthArray = array_repeat( colWidth, headers.length );
	}
	
	//ID Column
	colWidthArray[0] = 50;
	
	$('loadingDiv').set('styles',{'display':'block'});
		
	htmlDataGrid = new Element('div',{
		'id': 'dataGridDiv',
		'styles':{
			'width':'100%',
			'height':'500px',
			'display':'none'
		}
	});

		
	htmlDataGrid.inject($('grid_container'));
	
	var sortArray  = array_repeat( 'str', headers.length ); 
	sortArray[0] = 'int';
	
	var alignArray = array_repeat( 'left', headers.length );
	alignArray[0] = 'center';
	
	
	jsDataGrid = new dhtmlXGridObject('dataGridDiv');
	jsDataGrid.setImagePath("../media/com_jforms/images/records/grid/");
	jsDataGrid.setSkin("light");
	jsDataGrid.enableMultiline(true);
	jsDataGrid.setHeader(headers);
	jsDataGrid.setInitWidths(colWidthArray.join(','));
	jsDataGrid.setColAlign(alignArray.join(','));
	jsDataGrid.setColSorting(sortArray.join(','));
	jsDataGrid.setColTypes(array_repeat( 'ro', headers.length ).join(','));
	jsDataGrid.init();
	
}
	
function refreshPageList(){
		
	var recordPerPage = parseInt($('record_per_page').options[$('record_per_page').selectedIndex].value,10); 
	var pageCount = Math.floor(totalRecords / recordPerPage);
	if( totalRecords % recordPerPage)pageCount++;
	if( totalRecords == 0 )pageCount = 1;
	$('current_page').options.length = 0;
	for(i=1;i<pageCount+1;i++){
		var option = new Option(i,i);
		if( i == 1 ){
			option.selected = true;
		}
		$('current_page').options[i-1] = option;
	}
}
	
/*
*
* Used to deal with form elements named like "formElement[key_1]" which is not treated as Array by DOM
*
*/
function getHTMLArrayChildren( basename, parentForm ){
	var children = new Array();
	var regEx = new RegExp(basename+"\\[(.*)\\]");
	//The following works on FF but not IE!
	//for( e in parentForm.elements ){
	
	for (var i=0; i < parentForm.elements.length; i++) {
		var element = $(parentForm.elements[i]);
		if( element == null )continue;
		var match = regEx.exec(element.get('name'));
		if( match != null ){				
			children.push(match);
		}
	}
	return children;
}

function initSearchList(){
	searchListSortable = new Sortables($('search-pane-list'),{
		handle:'.search-pane-list-handle',
		clone:true,
		revert:true
	});
}


window.addEvent('load'  ,function(e){
	reloadRecords();
	initSearchList();
});
