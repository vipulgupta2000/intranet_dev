/**
* Javascript object for ip plugin
*
* @version		$Id: random.js 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
var random = new Class({
	
	Extends : CElement,
	
	initialize: function(  parent, id, beforeObject, options ) {
	
		this.type	    = "random";
		
		this.parent( $(parent), id, $(beforeObject), options );
		
		this.htmlInput 	     = new Element('img', {
			'src' : '../media/com_jforms/plugins/elements/random/random-image.png',
			'styles':{
				'margin':'0px',
				'padding':'0px'
			}
		});
		this.htmlInput.inject( this.htmlContainer );
		
	},
	
	deselect: function() {
	
		this.htmlContainer.removeClass('selected'); 
		this.htmlDragHandle.set({'styles':{ 'visibility' : 'hidden' }});
		this.htmlDeleteButton.set({'styles':{ 'visibility' : 'hidden' }});
	
	},
	
	select  : function() {
		
		this.htmlContainer.addClass('selected');
		this.htmlDragHandle.set({'styles':{ 'visibility' : 'visible' }});
		this.htmlDeleteButton.set({'styles':{ 'visibility' : 'visible' }});

	},

    onUpdate : function(){
	
		if(this.options.label.trim().length == 0)this.options.label = 'Random ' + this.index;
		if(this.options.validCharacters.length == 0)this.options.validCharacters = '0123456789abcdfghjkmnpqrstvwxyz';
		if( this.options.length.toInt() <= 0 || isNaN(this.options.length.toInt()) )this.options.length = 5;

	},
	serialize: function(){return this.genericSerialize();}
});