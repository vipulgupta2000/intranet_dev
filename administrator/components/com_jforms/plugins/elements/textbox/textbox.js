/**
* Javascript object for textbox plugin
*
* @version		$Id: textbox.js 374 2010-03-28 23:32:05Z dr_drsh $
* @package		Joomla
* @subpackage	JForms
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
var textbox = new Class({

	Extends: CLabeledElement,
	initialize: function( parent, id, beforeObject, options ) {
		
		this.type = "textbox";
		
		this.parent($(parent), id, $(beforeObject), options);
		
		//Red star that denotes a required field
		this.htmlRequiredStar = new Element('span', {
			'html': ' * ',
			'styles': {
				'color' : 'red'
			}
		});
		if( this.options.required ){
			this.htmlRequiredStar.inject( this.htmlLabel );
		}
		
		//Input element Resize handle	
		this.htmlControlResize = new Element('img',{
			'id': 'resizeHandle_control_' + this.type + this.index,
			'class': 'resize-handle hideondrag'
		});
		this.htmlControlResize.inject( this.htmlContainer );

		//Main input element
		var br = new Element('br', {'clear':'all'});
		this.htmlInput = new Element('input', {
			'name': 'input_' + this.type + this.index,
			'id':'input_' + this.type + this.index,
			'styles':{
				'position': 'static',
				'width': this.options.cw + 'px',
				'height': this.options.ch + 'px'
			},
			'value': this.options.defaultValue
		});
		this.htmlInput.inject( this.htmlContainer );
		br.inject( this.htmlContainer );
		
	
		this.htmlInput.makeResizable({
			handle:this.htmlControlResize,
			onDrag:dispatch_onResizeDrag,
			onComplete:dispatch_onResizeEnd,
			modifiers: {x: 'width', y: false},
			limit:{x:[50,400]}
		});
		
		$('JFormsEPlugin_textboxaltValidation').disabled     = true;
		if( this.options.validation == 'Other'){
			$('JFormsEPlugin_textboxaltValidation').disabled = false;
		} 
		
		this._alignControlResizeHandle();
		
	},
	
	onUpdate : function(){
		

		if(this.options.label.trim().length == 0)this.options.label = 'Textbox ' + this.index;
		this.htmlLabel.set('html', this.options.label );
		
		if( this.options.maxLength.toInt() <= 0 || isNaN(this.options.maxLength.toInt()) )this.options.maxLength = 1;
		this.htmlInput.set('maxlength', this.options.maxLength );
		
		$('JFormsEPlugin_textboxaltValidation').disabled     = true;
		if( this.options.validation == 'Other'){
			$('JFormsEPlugin_textboxaltValidation').disabled = false;
		} 
		//Red star that denotes a required field
		//Fix for IE!
		this.htmlRequiredStar.dispose();
		this.htmlRequiredStar = new Element('span', {
			'html': ' * ',
			'styles': {
				'color' : 'red'
			}
		});
	
		if( this.options.required ){
			this.htmlRequiredStar.inject( this.htmlLabel );
		}
	},
	
	onResizeDrag: function(newSize,type) {
	
		switch(type){
			
			case "label":
				this.htmlLabel.set('styles', { 'border': '1px solid white' } );	
		
			default:
				break;
				
		}
		this._alignLabelResizeHandle();

	},


	onResizeEnd: function( newSize, type ){

		switch(type){
		
			case 'label':
				this.options.lw = newSize.x;
				this.options.lh = newSize.y;
				this.htmlLabel.set('styles', { 'border' : '0'});
				break;
				
			default:
				this.options.cw = newSize.x;
				this.options.ch = newSize.y;	
				break;

		}
		this._alignLabelResizeHandle();	
		
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
		this.htmlLabelResize.set({'styles':{ 'visibility' : 'hidden' }})
		this.htmlControlResize.set({'styles':{ 'visibility' : 'hidden' }})
		this.htmlDragHandle.set({'styles':{ 'visibility' : 'hidden' }})
		this.htmlDeleteButton.set({'styles':{ 'visibility' : 'hidden' }})
	
	},
	
	select  : function() {
		
		
		
		this.htmlContainer.addClass('selected');
		this.htmlLabelResize.set({'styles':{ 'visibility' : 'visible' }})
		this.htmlControlResize.set({'styles':{ 'visibility' : 'visible' }})
		this.htmlDragHandle.set({'styles':{ 'visibility' : 'visible' }})
		this.htmlDeleteButton.set({'styles':{ 'visibility' : 'visible' }})
		this._alignLabelResizeHandle();		
	
	},
	
	serialize: function(){
		this.options.defaultValue = this.htmlInput.value;
		return this.genericSerialize();
	}
});
