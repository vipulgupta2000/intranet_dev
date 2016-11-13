<?php 
/**
*  Main Javascript functions for the WYSIWYG form editor
*
* @version		$Id: design.js 380 2010-04-22 12:07:35Z dr_drsh $
* @package		Joomla
* @subpackage	JForms
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

//The array that holds all the elements in the WYSIWYG area
var elementArray = new Array();

//Keeps track of the selectedElement
var selectedElement = null;

//Used to give each element a unique id
var autoIncrement   = 0;

var correctionPixels = Browser.Engine.trident?0:0;

var errorTooltipEngine = new CErrorTip();

//Holds ids of parameters , used for Multilingual support
var paramIdList = '';

//Holds a list of elements that need storage space
//The form shouldn't be created unless it has atleast one field that requires storage space "textbox,textarea,etc" and a submit button
//Will be filled by php
var obligatoryList = new Array();

var workspaceSortable = null;


var elementScrollLocked = false;

function refreshSelectedElement(){if( selectedElement != null )selectedElement.select();}

/**
*  Retrieves the order of any given element within the WYSIWYG list,
*
* @param {Object} li : List item whose order is to be retrieved
* @return{int} the 0-based order of the element within the list or -1 if the element couldn't be found
*/
function getOrder( li ) {
	
	var a = workspaceSortable.serialize();
	
	for(i=0;i<a.length;i++){
		if(a[i] == $(li).get('id')){
			return i;
		}
	}
	return -1;
}

/**
* Retrieves the element found the given position within the WYSIWYG list.
* 
* @param {int} the 0-based order of the element within the list or -1 if the element couldn't be found
* @return{Object} li : List item whose order is to be retrieved
*/
function getLiAt( index ){

	if( workspaceSortable == null )return null;

	var a = workspaceSortable.serialize();

	if( a[index] )return $(a[index]);
	return null;
	
}

function getLiIndex( li ){

	if( workspaceSortable == null )return 0;
	
	var a = workspaceSortable.serialize();
	for(j=0;j<a.length;j++){
		if(li == null)return a.length;
		if(a[j] == li.get('id'))return j;
	}
	return j;
}


/**
*  Determines Where to insert new elements based on the coordinates where they were dropped
* 
* @param {int} x : X Coordinate "left"
* @param {int} y: Y Coordinate "right"
* @return{object} the element before which the new element should be inserted, null is returned if it should be inserted at the last position
*/
function beforeWhich( x, y){
	
	
	var e = $('clist').getChildren();
	for(j=0;j<e.length;j++){
		
		
		if(e[j].get('tag') == 'li'){
			
			
			var position = e[j].getPosition();
			var size     = e[j].getSize();
			
			var xMatch = true;
			//( x >  position.x && x < position.x + size.x );

			var yMatch = ( y >  position.y && y < position.y + size.y );

			
			if( xMatch && yMatch ){
				
				var halfHeight = size.y / 2;

				if( (position.y + size.y  - y) < halfHeight ){
					//Lower half?
					if( j+1 < e.length ){
						return e[j+1];
					} else {
						return null;
					}
				
				} else {
					//Upper half?
					return e[j];
				}
			}
		}
	}
	return null;
	
}
  
/**
*  Retrieves the index of an element in  "elementArray" when given the "li" container element
* 
* @param {Object} li : li Container for the element
* @return{int} the index within the elementArray list or -1 if the element is not found "deleted"
*/
function getIndexFromContainerElement(e){
	for(i=0;i<elementArray.length;i++){
		if( elementArray[i] == null )continue;
		if( elementArray[i].htmlContainer == e )return i;
	}
	return -1;
}



/**
* Hides all Property pages.
* 
*/
function hideAllPropertyPages(){
	//All property pages have .ppage css class
	propertyPages = $$(".ppage").each(function(itm,index){itm.setStyle('display','none')});
}

function hideAllErrors(){
	errorTooltipEngine.destroyAll();
}
function alignControls(){

	highestWidth = 0;
	for(i=0;i<elementArray.length;i++){
		if( elementArray[i].htmlInput != null ){
			var size = elementArray[i].htmlInput.getSize()
			if(size.x > highestWidth)highestWidth = size.x;
		}
	}
	for(i=0;i<elementArray.length;i++){
		if( elementArray[i].setControlSize != null ){
			elementArray[i].setControlSize(highestWidth,-1);
		}
	}
	$$("li.element .drag-handle").each(resizeDragHandle);
}
function alignLabels(){

	highestWidth = 0;
	for(i=0;i<elementArray.length;i++){
		if( elementArray[i].htmlLabel != null ){
			var size = elementArray[i].htmlLabel.getSize()
			if(size.x > highestWidth)highestWidth = size.x;
		}
	}
	for(i=0;i<elementArray.length;i++){
		if( elementArray[i].setLabelSize != null ){
			elementArray[i].setLabelSize(highestWidth,-1);
		}
	}
	$$("li.element .drag-handle").each(resizeDragHandle);
}

/**
* Deselects all elements in the WYSIWYG area
* 
*/
function unselectAllEntries(){
	for(i=0;i<elementArray.length;i++){
		if( elementArray[i] == null )continue;
		elementArray[i].deselect();
	}
	selectedElement = null;
}




/**
*  Invokes the "serialize" method on all objects on the WYSIWYG field and collects the output to be sent to the server
*
*/
function serializeFieldInformation(){
	
	var fields = new Array()
	
	//Cumulative buffer
	var buffer    = "";
	
	//Loop through all elements invoking the "serialize" method
	
	//For some reason, when I use "i" as a loop counter things become messed up, the loop exits prematurely if I change the order of the elements
	var counter = 0;
	for(counter=0;counter<elementArray.length;counter++){
		
		//If a deleted element is encountered skup it to the next one
		if(elementArray[counter] == null)continue;
		
		fields.push( elementArray[counter].serialize() );
		
		//Place serialized element data into the cumulative buffer
		//buffer += elementArray[counter].serialize() + separator;
	}
	//Move data to HTML "form" field
	$('fieldInformation').value = JSON.encode(fields);
	$('paramIds').value = paramIdList;
	
}

function updateValidationTooltips(){if(errorTooltipEngine.isValidationTooltipsVisible)validateFieldInformation();}
 
function validateFieldInformation(){
	
	var isOkay = true;
	
	errorTooltipEngine.start($('workarea'));

	//For some reason, when I use "i" as a loop counter things become messed up, the loop exits prematurely if I change the order of the elements
	var counter = 0;
	for(counter=0;counter<elementArray.length;counter++){
		
		//If a deleted element is encountered skup it to the next one
		if(elementArray[counter] == null)continue;
		
		var result = elementArray[counter].vaildate();
		if(result.length>0){
			errorTooltipEngine.create( elementArray[counter].htmlContainer, result );
			isOkay = false;
		}
	}
	return isOkay;
}

function invokeMethod( plugin, method, parameters) {

	var jtoken      = '<?php echo JUtility::getToken(); ?>';
	var url         = '<?php echo JURI::base()."index.php"; ?>';
	var paramString = JSON.encode( parameters );
	
	ajaxRequest = new Request({url:url, method: 'GET', async:false});
	
	ajaxRequest.send(
		"option=com_jforms"
		+ "&controller=plugins"
		+ "&task=invoke"
		+ "&method=" + method
		+ "&plugin=" + plugin
		+ "&parameter=" + escape(paramString)
		+ "&"+jtoken+"=1"
	);
	
	if( ajaxRequest.xhr.status == 200 )text = ajaxRequest.xhr.responseText;
	else text = ajaxRequest.xhr.statusText;
	if(text == 'null')return null;
	return text;
}

/**
*  Called when the user clicks the save button (i.e submits the adminform)
*/
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if( pressbutton == "cancel" ){
		submitform( pressbutton );
		return;
	}
	
	
	var hasSubmitButton      = false;
	var hasObligatoryElement = false;
	var counter = 0;
	for(counter=0;counter<elementArray.length;counter++){
		
		//If a deleted element is encountered skip it to the next one
		if(elementArray[counter] == null)continue;
		
		//Is this element on the obligatory list? "i.e. Element types of which at least 1 must be present in the form"
		if( obligatoryList.contains( elementArray[counter].type ) ){
			hasObligatoryElement = true;
		}
		
		//Ugly code : Using plugin specific parameters in core context, sorry for that :P
		if( elementArray[counter].type == 'button' && elementArray[counter].options.func == 'Submit' ){
			hasSubmitButton = true;
		}
	}
	
	if( !hasObligatoryElement ){
		alert("<?php echo JText::_("You must have atleast one element that allows user input");?>");
		enableTab('toolbar');
		return false;
	}

	if( !hasSubmitButton ){
		alert("<?php echo JText::_("You must have atleast one submit button in the form");?>");
		enableTab('toolbar');
		return false;
	}
	
	if( form.elements['params[title]'].value.length == 0 ){
		alert("<?php echo JText::_("Your form must have a title"); ?>");
		enableTab('form');
		return false;		
	}
	
	//var regEx = /^([A-Za-z]+:\/\/)*[A-Za-z0-9-_]+\.[A-Za-z0-9-_%&\?\/.=]+$/;
	if(form.elements['params[thank]'].value.length == 0){
		alert("<?php echo JText::_("Your form must set the 'Thank you' URL"); ?>");
		enableTab('form');
		return false;
	}
	
	if(!validateFieldInformation())return false;
	serializeFieldInformation();
	submitform( pressbutton );
	
	
}

/**
* Checks if a given element has reached maximum allowed number per-form
* 
* @param {string} type : the element type to check
* @return(boolean) : whether or not has the element reached its limit
*/
function reachedLimit( type ){

	//0 means no limit
	if( countLimit[type] == 0 )return false;
	
	var count = 0;
	for(i=0;i<elementArray.length;i++){
	
		if( elementArray[i] == null )continue;
		if( elementArray[i].type == type )count++;
	}
	
	if( countLimit[type] <= count )return true;
	return false;

}

/**
* Generates a uniqueId, used for creating Hash values
*
* @param {int} count : number of characters of the desired unique value
* @return(string) : unique value of "count" character length
*/
function uniqueId( count ){
	var dateObject = new Date();
	var uniqueId =
			dateObject.getFullYear() + '' +
			dateObject.getMonth()    + '' +
			dateObject.getDate()     + '' +
			dateObject.getTime();
	var randomnumber = Math.floor(Math.random()*21321)
	var uid =  "h"+hex_sha1(uniqueId + randomnumber);
	return uid.substr(0,count);
}

function resizeWorkarea(){
	
	var paneScrollLocked = elementScrollLocked;
	if(paneScrollLocked)
		toggleElementPaneScroll();
	
	var safetyMargin = 50;
	var clientArea  = $('element-box').getSize();
	var sidebarArea = $('side-bar').getSize();
	var workareaWidth = clientArea.x - sidebarArea.x - safetyMargin;
	$('workarea').setStyle('width', workareaWidth+'px');
	
	var workareaArea = $('workarea-td').getSize();
	
	$('side-bar')   .setStyle('height', workareaArea.y+'px');
	$('tab-toolbar').setStyle('height', (workareaArea.y - 20) +'px');
	$('tab-form')   .setStyle('height', (workareaArea.y - 20) +'px');
	$('tab-element').setStyle('height', (workareaArea.y - 20) +'px');
	
	if(paneScrollLocked)
		toggleElementPaneScroll();

}


/**
* Handles clicks on the "convert" button, prepares data and sends it to adapt()
* 
* @param {Object} selectObject : <select> object whose "convert" button has been clicked
*/
function convert( selectList ){

	var destination = selectList.options[selectList.selectedIndex].value;
	var source      = selectedElement;
	
	if( source == null )return;
	
	adapt( source, destination );
}

function initializeToolbar(){
	
	var buttons = $$('div.controls');
	
	for(i=0;i<buttons.length;i++){
		
		var button = buttons[i];
		new Drag(button,{

			onStart:function(e){
			
				e.set("styles",{
					"position":  "absolute",
					"z-index" :  999
				});
				e.addClass('held');
				
			},
		
			onComplete:function(e){
				e.removeClass('held');
				$$('.tip').each(function(itm,index){itm.setStyle('display','none');});
				var top  = e.getParent().getPosition().y + correctionPixels;
				var left = e.getParent().getPosition().x + correctionPixels;
				var fx = new Fx.Morph(e);
				fx.onComplete = function(){
					e.style.position = "static";
					$$('.tip').each(function(itm,index){itm.setStyle('display','block');});
				}
				fx.start({
					"top" : top  + "px",
					"left": left + "px"
				});	
				addElement(e);
				$$("li.element .drag-handle").each(resizeDragHandle);
			}
		});

		button.setPosition({
				  relativeTo: button.getParent(),
				  position  : 'center',
				  edge      : 'center'
		});
		button.style.position = 'static';

	}
}
/**
*  Converts src element to destination element
* 
* @param {Object} src : Element Object that is to be converted
* @param {String} dest : The type of element to which src should be converted
*/
function adapt( src, dest ){
	
	//Create destination element
	var newElement = addElementEx( dest, getOrder(src.htmlContainer)  );
	//Copy source parameters
	newElement.adapt( src );

	elementArray[src.index].onDelete();
	elementArray[src.index] = null;
	
	hideAllPropertyPages();

	newElement.htmlContainer.fireEvent('click',newElement.htmlContainer,0);
	
	
}

function resizeDragHandle( itm, index ){
		
		var previousDisplay = itm.getStyle('display');
		itm.setStyle('display','none');
		
		var newHeight = 0;
		newHeight = itm.getParent().getComputedSize()['height'];
		
		/*
		For elements that display only an Image in the WYSIWYG Editor "e.g. Inivisble elements"
		getComputedSize doesn't behave consistantly because at some point it might be called just before
		The image loads which makes the element empty and its height is 0 , after the image loads dimensions
		are reported correctly.
		
		This is a suggested solution for this issue, wait till image to load and recall "ResizeDragHandle"
		*/
		if(newHeight == 0)
			$$('#'+itm.getParent().id + ' img').each(function(img,y){img.addEvent('load',function(){resizeDragHandle(itm,-2);})});

		itm.setStyle('display',previousDisplay);
		itm.set('styles', {'height' : (newHeight) + 'px'});
}

function toggleElementPaneScroll(){
	
	btn = $('lock-toggle');
	if(btn.hasClass('unlocked')){
		btn.removeClass('unlocked');
		btn.addClass('locked');
		elementScrollLocked = true;
	} else {
		btn.removeClass('locked');
		btn.addClass('unlocked');
		elementScrollLocked = false;
	}

}

function lockInput( itm, index ){itm.set('readonly','readonly');}


window.addEvent('resize', function(e){resizeWorkarea();});

window.addEvent('domready', function(e){
	resizeWorkarea();
	placeElements();
	$('tab-element-container').setStyle('position','relative');
	//$('tab-toolbar-container').setStyle('position','relative');
	$$('li.element .drag-handle').each(resizeDragHandle);
	$$('.id').each(lockInput)
	initializeToolbar();
});

window.addEvent('scroll', function(){
	refreshSelectedElement();
	
	if(!elementScrollLocked){
		if(Window.getScrollTop() > $('side-bar').getPosition().y){
			new Fx.Morph($('tab-element-container'), {duration:100, wait:true}).start({"top":  Window.getScrollTop()-$('side-bar').getPosition().y});
			$('tab-element-container').setStyle('position','relative');
		} else {
			$('tab-element-container').setStyle('position','static');
		}
	}
	//new Fx.Morph($('tab-toolbar-container'), {duration:100, wait:true}).start({"top":  Window.getScrollTop()});
});

