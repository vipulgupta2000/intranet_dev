/**
* Javascript object for button plugin
*
* @version		$Id: button.js 374 2010-03-28 23:32:05Z dr_drsh $
* @package		Joomla
* @subpackage	JForms
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
var button = new Class({
	
	Extends: CElement,
	
	initialize: function(  parent, id, beforeObject, options ) {	
	
		this.type	    = 'button';
	
		this.parent( $(parent), id, $(beforeObject), options );

		this.options.clickTrigger = this.options.clickTrigger.replace(/\"/g,'"').replace(/\'/g,"'");
		
		this.htmlInput = new Element('input', {
			
			'type': 'button',
			'value': this.options.label,
			'name': 'input_' + this.type + this.index,
			'id': 'input_' + this.type + this.index,
			'styles':{
				'width': this.options.cw + 'px',
				'height': this.options.ch + 'px',
				'float' : 'none'
			}
		});
		
		this.htmlControlResize = new Element('img',{

			'id': 'resizeHandle_control_' + this.type + this.index,
			'class': 'resize-handle hideondrag'
		});
		this.htmlInput.makeResizable({
			handle:this.htmlControlResize,
			onDrag:dispatch_onResizeDrag,
			onComplete:dispatch_onResizeEnd,
			limit:{x:[50,200],y:[20,200]}
		});
	
		if( this.options.func != 'Button' ){
			$('JFormsEPlugin_buttonclickTrigger').disabled = true;	
		} else {
			$('JFormsEPlugin_buttonclickTrigger').disabled = false;
		}
		
		this.htmlInput.inject( this.htmlContainer );
		this.htmlControlResize.inject( this.htmlContainer );
	
		this._alignControlResizeHandle();
	
	},
	
	onDragEnd:  function() {
		this._alignControlResizeHandle();
	},
	
	onUpdate : function(){
		if(this.options.label.trim().length == 0)this.options.label = 'Button ' + this.index;
		this.htmlInput.set('value', this.options.label );

		if( this.options.func != 'Button' ){
			$('JFormsEPlugin_buttonclickTrigger').disabled = true;	
		} else {
			$('JFormsEPlugin_buttonclickTrigger').disabled = false;
		}
		
	},
	
	onResizeDrag: function(newSize,type) {
	
		this.htmlInput.set('styles', { 'border': '1px solid white' } );	
		this._alignControlResizeHandle();	
	
	},


	onResizeEnd: function( newSize, type ){

		this.options.cw = newSize.x;
		this.options.ch = newSize.y;	
		this._alignControlResizeHandle();	
		
	},
	
	_alignControlResizeHandle : function(){

		this.htmlControlResize.setPosition({
			  relativeTo: this.htmlInput,
			  position  : 'bottomRight' ,
			  edge  	: 'bottomRight'
		});
		
	},
	
	deselect: function() {
	
		this.htmlContainer.removeClass('selected'); 
		this.htmlControlResize.set({'styles':{ 'visibility' : 'hidden' }});
		this.htmlDragHandle.set({'styles':{ 'visibility' : 'hidden' }});
		this.htmlDeleteButton.set({'styles':{ 'visibility' : 'hidden' }});
	
	},
	
	select  : function() {
		
		this.htmlContainer.addClass('selected');
		this.htmlControlResize.set({'styles':{ 'visibility' : 'visible' }});
		this.htmlDragHandle.set({'styles':{ 'visibility' : 'visible' }});
		this.htmlDeleteButton.set({'styles':{ 'visibility' : 'visible' }});
		if( this.options.func != 'Button' ){
			$('JFormsEPlugin_buttonclickTrigger').disabled = true;	
		} else {
			$('JFormsEPlugin_buttonclickTrigger').disabled = false;
		}
		
		this._alignControlResizeHandle();		
	
	},

	serialize: function(){return this.genericSerialize();}
});