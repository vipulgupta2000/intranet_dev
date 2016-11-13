/**
* This file contains the events router , elements are required to register their events to point to one of the following functions
 * and these functions will know which element triggered the event and will send the event directly to the element's internal handler "onUpdate,OnDelete,etc.."
*
* @version		$Id: event.js 384 2010-05-01 01:27:38Z dr_drsh $
* @package		Joomla
* @subpackage	JForms
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
 
function dispatch_onDragStart( element ){	
	
	//Get  Array index from container Element (li)
	var index = getIndexFromContainerElement(element);
	
	$$('li.element .hideondrag').each(function(itm, index){itm.style.display = 'none';})

	
	//Trigger onDragStart
	elementArray[index].onDragStart();
}

function dispatch_onDrag( element ){	

	//Get  Array index from container Element
	var index = getIndexFromContainerElement(element);

	//Trigger onDrag 
	elementArray[index].onDrag();
}

function dispatch_onDragEnd( element ){	
	
	//Get  Array index from container Element
	var index = getIndexFromContainerElement(element);
	
	$$('li.element .hideondrag').each(function(itm, index){itm.style.display = '';})
	updateValidationTooltips();
		
	//Trigger onDragEnd
	elementArray[index].onDragEnd();
}	
	


function dispatch_onMenu( element, actionID ){
	
	//TODO : Use mootools to create dropdown menu for each element
	
	//Get  Array index from container Element
	var index = getIndexFromContainerElement(element);

	//Trigger onMenu
	elementArray[index].onMenu(actionID);
}


function dispatch_onResizeStart( element, mouseEvent ){

	
	element = $(element);

	//Get the type of element that triggered the event
	var type = element.tagName.toLowerCase();

	//Get  Array index from container Element
	var index = getIndexFromContainerElement(element.getParent());

	//Get the current size
	var newSize = element.getSize();

	//Trigger onResizeStart and send the new size to the element
	elementArray[index].onResizeDragStart(newSize, type);
	
	resizeWorkarea();
}

function dispatch_onResizeDrag( element, mouseEvent ){
	
	element = $(element);
	
	//Get the type of element that triggered the event
	var type = element.tagName.toLowerCase();
	
	//Get  Array index from container Element
	var index = getIndexFromContainerElement(element.getParent());


		
	//Get the current size
	var newSize = element.getSize();

	//Trigger onResizeDrag and send the new size to the element
	elementArray[index].onResizeDrag(newSize, type);
	
	var parentId = element.getParent().get('id');
	$$('#'+parentId+' .drag-handle').each(resizeDragHandle);
	
	resizeWorkarea();
}

function dispatch_onResizeEnd( element, mouseEvent ){
	
	element = $(element);
	
	//Get the type of element that triggered the event
	var type = element.tagName.toLowerCase();
	
	//Get  Array index from container Element
	var index = getIndexFromContainerElement(element.getParent());

	//Get the current size
	var newSize = element.getSize();
	
	updateValidationTooltips();
	
	//Trigger onResizeEnd and send the new size to the element
	elementArray[index].onResizeEnd(newSize, type);
	
	resizeWorkarea();
}

function getLiParentRecurse( e ){
	
	e = $(e);
	//Get the current Element's parent
	//if(e.getParent)
	var p = e.getParent();
	
	//Is the current parent "The workspace" area? if so return the current element
	if( p.id == 'clist' )return e;
	//Otherwise ascend up by one level
	else return getLiParentRecurse( p );
}

function dispatch_onClick( e ){

	var element = getTarget( e )
	
	//Go through parents until reaching the container Li
	//This is done so that Any click on any element within the main Li should be considered a click on the Li itself
	element = getLiParentRecurse( element );	

	//Get  Array index from container Element
	var index = getIndexFromContainerElement(element);

	//Shorter access
	e = elementArray[index];

	//Did we select the same element twice?
	if( selectedElement == 	e){
		return;
	}
	
	//Did we have a previosly selected Element?
	if( selectedElement != null ){
		//trigger onBlur event on previously element
		selectedElement.onBlur();
	}
	//Set the element the triggered the event as SelectedElement
	selectedElement = e;
	
	//Highlight selected entry , disable all others
	for(i=0;i<elementArray.length;i++){
		if(elementArray[i] != null)elementArray[i].deselect();
	}
	selectedElement.select();	
	
	//Show relevant property page, hide all others
	hideAllPropertyPages();
	$("ppage_" + selectedElement.type).setStyle('display','block');

	//Trigger onFocus on current element
	selectedElement.onFocus();

	//Display properties of the selected element
	displayProperties();
	
	//Show Element properties tab contents
	enableTab('element');
	
}

function getTarget( e ){
	
	//Did we receive an element as parameter
	if( e.tagName ){
		return $(e);
	} else {
		//Or an event object?
		var event = new Event(e);
		return event.target;
	}

}

function dispatch_onDelete(e){

	
	var target = getTarget( e );
	
	//Get target Element's parent (LI element)
	var element = target.parentNode;
	
	
	//Get  Array index from container Element
	var index = getIndexFromContainerElement(element);

	if( elementArray[index].allocatedDbField && 
		obligatoryList.contains( elementArray[index].type ) ){
		if(!confirm('The element you\'re about to delete has been allocated a field in the database, deleting it will result in deletion of all data stored in that field, proceed?')){
			return;
		}
	}
	//Hide the element "delete button" that triggered this event
	target.style.display = 'none';
	
	//Hide property pages
	hideAllPropertyPages();
	
	//Some Eye Candy
	var fx = new Fx.Morph(element);
	fx.onComplete = 
	function(e) {	
		
			updateValidationTooltips();

			//Call onDelete event handler on object
			elementArray[index].onDelete();
			
			//Delete object
			elementArray[index] = null;
			
			//Deselect All elements
			unselectAllEntries();
	}
	
	fx.start({'opacity': '0'});
	
	//Show toolbar tab contents
	enableTab('toolbar');

	
}
