/**
* Javascript object for HTML plugin
*
* @version		$Id: html.js 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

var html = new Class({

	
	Extends : CElement,
	initialize: function( parent, id, beforeObject,  options ) {
	
		this.type	    = "html";
				
		this.parent( parent, id, beforeObject, options );
		
		this.options.htmlValue = Base64.decode(this.options.htmlValue);
		this.dragObject = null;
	
		var br = new Element('br');
		
		//Create the "edit button"
		this.htmlEditButton  = new Element('div',{
		
			'class':'edit-button',
			'events': {
				'click': function() { var h = new Function("elementArray["+ix+"].onEdit();");h();}		
			}
		});
		//Add the edit button to this element "li" contianer
		
		this.htmlEditButton.inject(this.htmlContainer);
		this.htmlSaveButton = new Element('input',{
			'type': 'button',
			'value': '<?php echo JText::_("Done");?>',
			'events':{
				'click': function() { var h = new Function("elementArray["+ix+"].onUpdate();");h();}		
			},
			'styles':{
				'display':'none',
				'float':'left',
				'width':'150px',
				'height':'18px',
				'padding':'2px',
				'margin-top':'1px',
				'margin-bottom':'1px'
				
			}
		});
		this.htmlSaveButton.inject( this.htmlContainer );
		
		this.htmlInnerDiv = new Element('div',{
			'id':parent.id + '_' +  this.type +  this.index + '_InnerDiv',
			'html':this.options.htmlValue,
			'styles':{
				'display':'block'
			}
		});
		this.htmlInnerDiv.inject( this.htmlContainer );
		this._insertClearBr( this.htmlContainer );

		
		var ix = this.index;
		//Editor stuff
	

		

	
			
		this.htmlEditor = new Element('textarea', {
			'name':'input_' + this.index,
			'id':'input_' + this.index,
			'styles':{
				'margin':'0px',
				'display':'none',
				'float':'left',
				'width':'150px'
			},
			'class':'mce_editable'
			
		
		});
		this.htmlEditor.inject( this.htmlContainer );
		
		
		
		//We're just starting up , probably not  in edit mode
		this.isEditMode = false;
	
	},
	onEdit : function(){

		if(this.isEditMode)return;	
		
        
		this.isEditMode = true;
	
		//this.htmlEditor.innerHTML     = this.options.htmlValue;
		this.htmlEditor.value         = this.options.htmlValue.replace(/<br \/>/ig,"\n");
		
		var elementSize  = this.htmlContainer.getSize();
		var editorWidth  = elementSize.x - 5;
		var editorHeight = 200; 
		
		this.htmlEditor.set('styles', {
			'width':editorWidth+'px',
			'height':editorHeight+'px'
		});
	
		//Hide element body
		this.htmlInnerDiv.setStyle('styles', {'display':'none'});
		//Show Editor and save button
		this.htmlEditor.set('styles', {'display':'block'});
		this.htmlSaveButton.set('styles', {'display':'block'});
		
		this.htmlContainer.set('styles',{'padding':'0px'});
	
		//Disable dragging while editing
		this.htmlDragHandle.set('styles',{'visibility':'hidden'});
	
		this.htmlInnerDiv.set('html','');

		tinyMCE.execCommand("mceAddControl", true, this.htmlEditor.get('id'));

	},
  
	onUpdate : function(){
	
		this.isEditMode = false;
		
		tinyMCE.execCommand("mceRemoveControl", true, this.htmlEditor.get('id'));

		this.htmlDragHandle.set('styles',{'visibility':'visible'});
	
		this._cleanUp(this.htmlEditor);

		this.options.htmlValue 				= this.htmlEditor.get('value');
		this.htmlInnerDiv.innerHTML = this.options.htmlValue;
	
		//Attach onClick listener to All newly acquired children
		this._recurse(this.htmlInnerDiv);
	
		//Show element body
		this.htmlInnerDiv.setStyle('styles', {'display':'block'});
		//Hide Editor and save button
		this.htmlEditor.set('styles', {'display':'none'});
		this.htmlSaveButton.set('styles', {'display':'none'});
	

	
		//displayProperties();
	
	},
    

	onBlur : function() {
		if( this.isEditMode ){
			this.onUpdate();
		}
	},
	
	deselect : function () {
  
		this.htmlContainer.removeClass("selected"); 
		this.htmlDragHandle.set('styles',{'visibility':'hidden'});
		this.htmlDeleteButton.set('styles',{'visibility':'hidden'});
		this.htmlEditButton.set('styles',{'visibility':'hidden'});
	},
	
	select  : function() {
	
		this.htmlContainer.addClass("selected"); 	
		this.htmlDragHandle.set('styles',{'visibility':'visible'});
		this.htmlDeleteButton.set('styles',{'visibility':'visible'});
		this.htmlEditButton.set('styles',{'visibility':'visible'});
		
	},	

	serialize: function(){
		this.options.htmlValue = Base64.encode(this.options.htmlValue);
		return this.genericSerialize();
	} ,
    
	_insertClearBr: function ( obj ) {
		
		var brClear = new Element('br',{'clear':'all'});
		brClear.inject( obj );

	},
	
	_cleanUp : function( editor ){
	
<?php
			$badTags = array('input','option','script','link','html','body','head','select','applet','base','area','button','fieldset','legend','iframe','map','meta','noscript','optgroup','textarea');
			echo "\t\tvar regExpressions = new Array();\n";
			for($i=0;$i<count($badTags);$i++){
				$tag = $badTags[$i];
				echo "\t\tregExpressions[$i] = /<(\/)*$tag(.|\\n)*?>/ig;\n";
			}
		?>
		//var r = /<(\/)*script(.|\n)*?>/ig
		//alert(editor.get('value').replace(r,''));
		var buffer = editor.get('value');
		
		for(i=0;i<regExpressions.length;i++){
			buffer = buffer.replace(regExpressions[i], '');
		}
		editor.value = buffer.replace(/\n/g,'<br />');
	},
	_recurse : function( child ){
			
		if( child.getChildren )children = child.getChildren();
		else return;
	
		for(i=0;i<children.length;i++){
			children[i].onclick = dispatch_onClick;	
			//Forbid the use of 'clist' as ID , it will break the getHighParent rountine
			if( children[i].id == 'clist' )
				children[i].id = '';
	
			if(children[i].getChildren().length )this._recurse( children[i] );	
		}
	}
 
});