/**
* Javascript object for textarea plugin
*
* @version		$Id: textarea.js 374 2010-03-28 23:32:05Z dr_drsh $
* @package		Joomla
* @subpackage	JForms
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
var textarea = new Class({
  
	Extends: CLabeledElement,

	initialize: function( parent, id, beforeObject, options ) {
	
		this.type	    = "textarea";
	
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

		this.htmlControlResize = new Element('img',{

			'id': 'resizeHandle_control_' + this.type + this.index,
			'class': 'resize-handle hideondrag'
		});
 		this.htmlControlResize.inject( this.htmlContainer );

		this.htmlInput = new Element('textarea', {
			'name': 'input_' + this.type + this.index,
			'id':'input_' + this.type + this.index,
			'styles':{
				'position': 'static',
				'width': this.options.cw + 'px',
				'height': this.options.ch + 'px'
			},
			'value': this.options.defaultValue
		});
	
		this.htmlInput.makeResizable({
			handle:this.htmlControlResize,
			onDrag:dispatch_onResizeDrag,
			onComplete:dispatch_onResizeEnd,
			modifiers: {x: 'width', y: 'height'},
			limit:{x:[50,400],y:[50,1000]}
		});
		this.htmlInput.inject( this.htmlContainer );
		var br = new Element('br', {'clear' : 'all'});
		br.inject( this.htmlContainer );
	
	

		this._alignControlResizeHandle();
		this.onUpdate();
	},
	vaildate : function(){
		var errors = new Array();
		if( this.options.maxLength < this.options.minLength )
			errors.push( "<?php echo JText::_('The minimum length cannot be greater than the maximum length'); ?>" );
		return errors;
	},
	
	onUpdate : function(){
	
		if(this.options.label.trim().length == 0)this.options.label = 'Textbox ' + this.index;
		this.htmlLabel.set('html', this.options.label );
		
		if( this.options.maxLength.toInt() <= 0 || isNaN(this.options.maxLength.toInt()) )this.options.maxLength = 50;
		if( this.options.minLength.toInt() <= 0 || isNaN(this.options.minLength.toInt()) )this.options.minLength = 1;


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

		if( this.options.sizeMode == 'html' ){
			this.htmlControlResize.setStyle('display','none');
			this.htmlInput.setStyle('width'  ,'auto');
			this.htmlInput.setStyle('height' ,'auto');
			
			if( this.options.cols.toInt() <= 0 || isNaN(this.options.cols.toInt()) )this.options.cols = 5;
			if( this.options.rows.toInt() <= 0 || isNaN(this.options.rows.toInt()) )this.options.rows = 5;

			this.htmlInput.set('cols',this.options.cols);
			this.htmlInput.set('rows',this.options.rows);
			$('JFormsEPlugin_textareacols').disabled = false;
			$('JFormsEPlugin_textarearows').disabled = false;
		} else {
			this.htmlControlResize.setStyle('display','block');
			this.htmlInput.setStyle('width'  ,this.options.cw+'px');
			this.htmlInput.setStyle('height' ,this.options.ch+'px');
			this._alignControlResizeHandle();
			this.htmlInput.set('cols',0);
			this.htmlInput.set('rows',0);
			$('JFormsEPlugin_textareacols').disabled = true;
			$('JFormsEPlugin_textarearows').disabled = true;
		}
		resizeDragHandle( this.htmlDragHandle  );
	},
	
	onDragEnd: function(){
		if( this.options.sizeMode == 'html' )
			this.htmlControlResize.setStyle('display','none');
		this._alignLabelResizeHandle();
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
			
			case "label":
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