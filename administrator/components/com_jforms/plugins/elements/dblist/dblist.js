/**
* Javascript object for list plugin
*
* @version		$Id: dblist.js 384 2010-05-01 01:27:38Z dr_drsh $
* @package		Joomla
* @subpackage	JForms
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

var dblist = new Class({
	
	Extends : CLabeledElement,

	_dbSignature : function (){return hex_sha1(this.options.tableName + '|_|' + this.options.keyField + '|_|' + this.options.valueField +'|_|' + this.options.orderField + '|_|' + this.options.orderMode);},
	
	initialize: function( parent, id, beforeObject, options ) {
		
		this.type = "dblist";
	
		this.parent($(parent), id, $(beforeObject), options);

		this.elements = null;
		this.dbSignature = this._dbSignature();
		if(this.options.tableName != '' && 	this.options.keyField != '' && this.options.valueField != '' && this.options.orderField != ''){
			this.elements = invokeMethod( 'element.dblist', 'getDBElements', {'tableName':this.options.tableName,'keyField':this.options.keyField,'valueField':this.options.valueField, 'orderField':this.options.orderField, 'orderMode':this.options.orderMode});
		}
		
		var brClear = new Element('br', {'clear':'all'});
		
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
	
		this.htmlInput  = new Element('select', {
			'name':'input_' + this.type + this.index,
			'id':'input_' + this.type + this.index,
			'styles':{
				'width': this.options.cw + 'px',
				'height':this.options.ch + 'px'
			},
			'multiple' : this.options.multi
		});
	
	
		if(this.options.multi){
			this.htmlInput.makeResizable({
				handle:this.htmlControlResize,
				onDrag:dispatch_onResizeDrag,
				onComplete:dispatch_onResizeEnd,
				limit:{x:[50,200],y:[20,200]}
			});	
		} else {
			this.htmlInput.makeResizable({
				handle:this.htmlControlResize,
				onDrag:dispatch_onResizeDrag,
				onComplete:dispatch_onResizeEnd,
				modifiers: {x: 'width', y: false},
				limit:{x:[50,200]}
			});	
		}
	
		if(!this.elements){
			elements = new Array();
		} else {
			elements = JSON.decode(this.elements)
		}
		
		this.defaultValueArray = this.options.defaultValue.split('\n');
		
		this._refill( elements, this.defaultValueArray );

		this.htmlInput.inject( this.htmlContainer );
	
		brClear.inject( this.htmlContainer );

		this._alignControlResizeHandle();
	
	},
	vaildate : function(){
		var errors = new Array();
		var e = this.elements;
	
		if( !e || e.length == 0 ){
			errors.push( "<?php echo JText::_('Table information are incorrect'); ?>" );
		}
		return errors;
	},
	
	_refill : function( elements, defaults){
	
		//Clear this list
		this.htmlInput.options.length = 0;
		this.htmlInput.multiple = this.options.multi;
		//Reload it
		
		if(elements == null )return;
		for(var i in elements){
			listItem = elements[i];
			var o = new Option( listItem.value, listItem.key);
			o.selected = false;
			for( j=0; j<defaults.length; j++ )
				if(defaults[j] == listItem.key )
					o.selected = true;
			this.htmlInput.options[i] = o;
		};
		
	},
	
	onUpdate : function(){

		this._updateDefault();

		if(this.options.label.trim().length == 0)this.options.label = 'DBList ' + this.index;
		this.htmlLabel.set('html', this.options.label );

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
		
		if( 
			this.dbSignature != this._dbSignature() &&
			this.options.tableName != '' && 	this.options.keyField != '' && this.options.valueField != '' && this.options.orderField != ''
			){
			
			
			this.dbSignature = this._dbSignature();
			this.elements = invokeMethod( 'element.dblist', 'getDBElements', {'tableName':this.options.tableName,'keyField':this.options.keyField,'valueField':this.options.valueField, 'orderField':this.options.orderField, 'orderMode':this.options.orderMode});
		}
		
		var elements = JSON.decode(this.elements);
		this._refill( elements, this.defaultValueArray );
		
		//Modify resize options based on list "mult select" property
		if(this.options.multi){
			if( this.options.ch < 50 )this.options.ch = 50;
			this.htmlInput.setStyle('height',this.options.ch);
			this.htmlInput.makeResizable({
				handle:this.htmlControlResize,
				onDrag:dispatch_onResizeDrag,
				onComplete:dispatch_onResizeEnd,
				limit:{x:[50,400],y:[50,400]}
			});
		} else {
			this.htmlInput.makeResizable({
				handle:this.htmlControlResize,
				onDrag:dispatch_onResizeDrag,
				onComplete:dispatch_onResizeEnd,
				modifiers: {x: 'width', y: false},
				limit:{x:[50,400]}
			});	
			this.htmlInput.set('styles', {'height':'auto'});
		}
		this._alignControlResizeHandle();
		resizeDragHandle( this.htmlDragHandle  );
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
			  edge  	: 'bottom'
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

	_updateDefault: function(){
  		
		this.defaultValueArray = new Array();
		for(i=0;i<this.htmlInput.options.length;i++){
			if(this.htmlInput.options[i].selected){
				this.defaultValueArray.push(this.htmlInput.options[i].value);
			}
	  	}	  
	},

  
	serialize: function(){
		this._updateDefault();
		this.options.defaultValue	= this.defaultValueArray.join('\n');
		return this.genericSerialize();
	}
  
});