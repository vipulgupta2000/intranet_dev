/**
* Javascrentrydatet object for entrydate plugin
*
* @version		$Id: entrydate.js 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
var entrydate = new Class({
	
	Extends : CElement,
	
	initialize: function(  parent, id, beforeObject, options ) {
	
		this.type	    = "entrydate";

		this.parent( $(parent), id, $(beforeObject), options );
		
		this.htmlInput 	     = new Element('img', {
			'src' : '../media/com_jforms/plugins/elements/entrydate/entrydate-image.png',
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

    onUpdate : function(){;},
	serialize: function(){return this.genericSerialize();}
});