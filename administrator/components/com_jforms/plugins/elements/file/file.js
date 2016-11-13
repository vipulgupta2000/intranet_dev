/**
* Javascript object for file plugin
*
* @version		$Id: file.js 319 2009-09-08 15:06:51Z dr_drsh $
* @package		Joomla
* @subpackage	JForms
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
var file = new Class({
  
	Extends: CLabeledElement,

	initialize: function( parent, id, beforeObject, options ) {
	
		this.type  = 'file';

		this.parent( $(parent), id, $(beforeObject), options);
		
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
	
		//Main input element
		var br = new Element('br', {'clear':'all'});
		this.htmlInput  = new Element('input', {
			'type': 'file',
			'name': 'input_' + this.type + this.index,
			'id': 'input_' + this.type + this.index 
		});	
		this.htmlInput.inject( this.htmlContainer );
		br.inject( this.htmlContainer );	
		
	},
	

	onUpdate : function(){
		
		if(this.options.label.trim().length == 0)this.options.label = 'Upload ' + this.index;
		this.htmlLabel.set('html', this.options.label );
		
		if(	this.options.maxSize.toFloat() <= 0 || isNaN(this.options.maxSize.toFloat())){
			this.options.maxSize = 0.5;
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

		this.htmlLabel.set('styles', { 'border': '1px solid white' } );	
		this._alignLabelResizeHandle();	
		
	},


	onResizeEnd: function( newSize, type ){

		this.options.lw = newSize.x;
		this.options.lh = newSize.y;
		this.htmlLabel.set('styles', { 'border' : '0'});
		this._alignLabelResizeHandle();	
		
	},
	deselect: function() {
	
		this.htmlContainer.removeClass('selected'); 
		this.htmlLabelResize.set({'styles':{ 'visibility' : 'hidden' }})
		this.htmlDragHandle.set({'styles':{ 'visibility' : 'hidden' }})
		this.htmlDeleteButton.set({'styles':{ 'visibility' : 'hidden' }})
	
	},
	
	select  : function() {
		
		this.htmlContainer.addClass('selected');
		this.htmlLabelResize.set({'styles':{ 'visibility' : 'visible' }})
		this.htmlDragHandle.set({'styles':{ 'visibility' : 'visible' }})
		this.htmlDeleteButton.set({'styles':{ 'visibility' : 'visible' }})
		this._alignLabelResizeHandle();		
	
	},
	
	serialize: function(){return this.genericSerialize();}
  
});
