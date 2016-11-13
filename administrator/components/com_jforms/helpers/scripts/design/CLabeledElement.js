/**
 * CLabeledElement
 * @version		$Id: CLabeledElement.js 374 2010-03-28 23:32:05Z dr_drsh $
 * @package		Joomla
 * @subpackage	JForms
 * @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
 * @license		GNU/GPL
 * @class This class inherits from CElement and is used to create elements that has a label "like textarea,textbox ,etc", It handles tasks like 
 * - Setting member variables with values from the constructor
 * - Creates the label tag for that element and makes it resizable
 * - Proivde utility functions to update the label resize handle
 *
 * @constructor
 * @param {Object} parent: Reference to the "<ul>" element that acts as the container for the whole WYSIWYG enviornment
 * @param {int}id: index of this CElement instance in the elementArray array
 * @param {Object}beforeObject: Reference to the object before which this element will be added (No actual use here, this parameters is just passed to the parent class "CElement"
 * @param {object} params : list of parameters in key:value pairs
**/
 var CLabeledElement = new Class({
  
  Extends: CElement,

  initialize: function( parent, id, beforeObject, options ) 
  {
	
	this.parent( $(parent), id, $(beforeObject), options );

	//Create the <label> element
	this.htmlLabel = new Element('label',{
		'html': this.options.label,
		'styles': {
			'position': 'static',
			'width' :this.options.lw + 'px',
			'height' :this.options.lh + 'px'
		},
		'for': 'input_' + this.type + this.index
	
	});
	//Add the label to the "li" container
	this.htmlLabel.inject( this.htmlContainer );
	
	//Create resize handle
	this.htmlLabelResize = new Element('img',{
		'id': 'resizeHandle_label_' + this.type + this.index,
		'class': 'resize-handle hideondrag'
	});
	this.htmlLabelResize.inject(this.htmlContainer);
	
	//Make resizable and register events with our event dispatcher
	//TODO: Fix default limits
	
	this.htmlLabel.makeResizable({
		handle:this.htmlLabelResize,
		onStart:dispatch_onResizeStart,
		onDrag:dispatch_onResizeDrag,
		onComplete:dispatch_onResizeEnd,
		limit:{x:[50,400],y:[20,400]}
	});
		
	//Update resize handle position to match the label size
	this._alignLabelResizeHandle();
	
  },
  setControlSize: function(x,y){
	if( this.htmlInput != null ){
		if( x != -1 ){
			this.options.cw = x;
		}	
		if( y != -1 ){
			this.options.ch = y;
		}
		this.htmlInput.set('styles', {'width'  : x+'px',
									  'height' : y+'px'});		
		this._alignLabelResizeHandle();
	}
  },
  setLabelSize: function(x,y){
	
	if( x != -1 ){
		this.options.lw = x;
	}
	if( y != -1 ){
		this.options.lh = y;
	}
	this.htmlLabel.set('styles', {'width'  : x+'px',
								  'height' : y+'px'});		
	this._alignLabelResizeHandle();

   },
   /**
       * Aligns the resize handle with the lower right corner of the label
       * @method _alignLabelResizeHandle
       * @return  void
       */
  _alignLabelResizeHandle: function (){
	   
	this.htmlLabelResize.setPosition({
		  relativeTo: this.htmlLabel,
		  position  : 'bottomRight',
		  edge      : 'bottomRight'
	});

	//Do we have a resizable input field?
	if( this.htmlControlResize != undefined ){
		//Align its handle as well
		this._alignControlResizeHandle();
	}
  },
   /**
       * Handles onResizeDragStart Event
       * @method onResizeDragStart
       * @return  void
       */
  onResizeDragStart: function(size, type) {;},
  
   /**
       * Handles onDragEnd Event
       * @method onDragEnd
       * @return  void
       */
  onDragEnd:  function() 
  {
		this._alignLabelResizeHandle();
  } ,
  
  select  : function() { 
	this.htmlContainer.addClass('selected'); 
    this.htmlDeleteButton.set({'styles':{ 'visibility' : 'visible' }})
	this._alignLabelResizeHandle();
  }
  
});