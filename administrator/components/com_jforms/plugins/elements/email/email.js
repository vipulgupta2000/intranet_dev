/**
* Javascript object for textbox plugin
*
* @version		$Id: email.js 374 2010-03-28 23:32:05Z dr_drsh $
* @package		Joomla
* @subpackage	JForms
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
var email = new Class({

	Extends: CLabeledElement,

	initialize: function( parent, id, beforeObject, options ) {
		
		this.type = "email";
		
		this.parent($(parent), id, $(beforeObject), options);
		
		//Red star that denotes a required field
		this.htmlRequiredStar = new Element('span', {
			'html': ' * ',
			'styles': {
				'color' : 'red'
			}
		});
		
		//Input element Resize handle	
		this.htmlControlResize = new Element('img',{
			'id': 'resizeHandle_control_' + this.type + this.index,
			'class': 'resize-handle hideondrag'
		});


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
			'value': ''
		});

		this.htmlImage = new Element('img', {
			'src' : '../media/com_jforms/plugins/elements/email/email-image.png',
			'styles':{'margin':'0px','padding':'0px'}
		});
		
		this.htmlInput.makeResizable({
			handle:this.htmlControlResize,
			onDrag:dispatch_onResizeDrag,
			onComplete:dispatch_onResizeEnd,
			modifiers: {x: 'width', y: false},
			limit:{x:[50,400]}
		});
		this._recreateControl();
	},
	
	vaildate : function(){
		var errors = new Array();
		
		var additionalEmails = Array();
		if(this.options.emails.trim().length != 0){
			additionalEmails = this.options.emails.trim().split(',');
		}
		if(!this.options.uinput && additionalEmails.length == 0){
			errors.push( "<?php echo JText::_('With user input disabled you must specify at least one additional E-mail'); ?>" );
		}
		
		if(additionalEmails.length > 0){
			for(j=0;j<additionalEmails.length;j++){
				if( !this._validateEmail(additionalEmails[j]) && !this._validatePlaceholder(additionalEmails[j])){
					errors.push( "<?php echo JText::_('You have at least one invalid E-mail in (Additional E-mails) field'); ?>" );
				}
			}
		}
		
		if(this.options.subject.trim().length == 0){
				errors.push( "<?php echo JText::_('E-mail subject is empty'); ?>" );
		}

		if(this.options.message.trim().length == 0){
				errors.push( "<?php echo JText::_('E-mail body is empty'); ?>" );
		}
		
		return errors;
	},

	_validatePlaceholder: function( placeholder ){
		return /(\{field[:|=].*\})+/i.test(placeholder);
	},
	
	_validateEmail : function( email ){
		return ((email.indexOf(".") > 2) && (email.indexOf("@") > 0));
	},
	
	_recreateControl : function(){
		
		this.htmlControlResize = this.htmlControlResize.dispose();
		this.htmlRequiredStar  = this.htmlRequiredStar.dispose();
		this.htmlInput		   = this.htmlInput.dispose();
		this.htmlImage		   = this.htmlImage.dispose();
		this.htmlLabelResize   = this.htmlLabelResize.dispose();
		this.htmlLabel         = this.htmlLabel.dispose();
		
		$$('#' + this.htmlContainer.id + ' br').each(function(item,index){item.dispose();});
		
		var br = new Element('br', {'clear':'all'});
		if( this.options.uinput ){
			this.htmlLabel.inject( this.htmlContainer );
			this.htmlLabelResize.inject( this.htmlContainer );
			this.htmlControlResize.inject( this.htmlContainer );
			if( this.options.required )
				this.htmlRequiredStar.inject( this.htmlLabel );
			this.htmlInput.inject( this.htmlContainer );
			br.inject( this.htmlContainer );
		} else {
			this.htmlImage.inject( this.htmlContainer )
			br.inject( this.htmlContainer );
		}
		this._alignLabelResizeHandle();
		this._alignControlResizeHandle();
		resizeDragHandle( this.htmlDragHandle  );

	
	},
	onUpdate : function(){
		
		if(this.options.label.trim().length == 0)this.options.label = 'E-mail ' + this.index;
		this.htmlLabel.set('html', this.options.label );
		this._recreateControl();
		
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
		this.options.defaultValue = '';
		return this.genericSerialize();
	}
});
