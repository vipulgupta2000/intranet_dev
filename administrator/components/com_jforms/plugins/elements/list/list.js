<?php
/**
* Javascript object for list plugin
*
* @version		$Id: list.js 374 2010-03-28 23:32:05Z dr_drsh $
* @package		Joomla
* @subpackage	JForms
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

function getExtension( $filename ){return strtolower( array_pop( explode('.', $filename ) ));}
$dir = JFORMS_BACKEND_PATH.DS.'plugins'.DS.'elements'.DS.'list'.DS.'common_lists'.DS;
$lists = array();
if ($dh = opendir($dir)) {
    while (($file = readdir($dh)) !== false) {
		if(is_dir( $dir . $file ))continue;
		if( getExtension( $file ) != 'list')continue;
		$index = basename( $file, '.list' );
		$list = str_replace( "\r\n" , "\n" , file_get_contents( $dir . $file ));
		$lists[$index] = explode("\n", $list );
    }
    closedir($dh);
}
$buffer = "var commonLists = {\n";
$listEntries = array();
foreach( $lists as $name => $list ){
		
		$l = implode("\\n",$list);
		$listEntries[] = "'$name' : \"$l\"";	
		
}
$buffer .= "\t".implode(",\n\t",$listEntries)."\n";
$buffer .= "};\n\n";
 
echo $buffer;
?>
var list = new Class({
	
	Extends : CLabeledElement,
  
	
	initialize: function( parent, id, beforeObject, options ) {
	
		this.type	       = "list";

		this.parent($(parent), id, $(beforeObject), options);

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
	
		var e = null;
		
		
		if(this.options.elements.length == 0){
			e = new Array();
		} else {
			e = this.options.elements.split("\n");
		}
		
		this.defaultValueArray = this.options.defaultValue.split('\n');
		var validElements = new Array();
	
		this._constructList();
		
		if( this.options.commonList != "Manual" ){
			$('JFormsEPlugin_listelements').disabled = true;
		} else {
			$('JFormsEPlugin_listelements').disabled = false;
		}
	
		this.htmlInput.inject( this.htmlContainer );
		brClear.inject( this.htmlContainer );

		this._alignControlResizeHandle();
	
	},
	
	vaildate : function(){
		var errors = new Array();
		var e = this.options.elements.trim().split("\n");
		if( e.length < 2 ){
			errors.push( "<?php echo JText::_('You must have at least two elements'); ?>" );
		}
		return errors;
	},
	
	_constructList: function(){
	
		var e = this.options.elements.split("\n");
		var validElements = new Array();
	
		this.htmlInput.options.length = 0;
		this.htmlInput.multiple = this.options.multi;
		for(i=0;i<e.length;i++){
			
			if(e[i].trim().length == 0)continue;
			validElements.push( e[i] );
			
			var o = new Option( e[i], i);
			o.selected = false;
			for(j=0;j<this.defaultValueArray.length;j++){
				if(this.defaultValueArray[j] == e[i] ){
					o.selected = true;
					break;
				}
			}
			this.htmlInput.options[i] = o;
		}
		this.options.elements = validElements.join("\n");
		
	},
	
	onUpdate : function(){
	
		this._updateDefault();

		if(this.options.label.trim().length == 0)this.options.label = 'List ' + this.index;
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
		
		if( this.options.commonList != "Manual" ){
			this.options.elements = commonLists[this.options.commonList];
			$('JFormsEPlugin_listelements').disabled = true;
			$('JFormsEPlugin_listelements').value = this.options.elements;
		} else {
			$('JFormsEPlugin_listelements').disabled = false;
		}
		
		this._constructList();
		
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
			  edge  	: 'bottomRight'
		});
	},
	
	deselect: function() {
	
		this.htmlContainer.removeClass('selected'); 
		this.htmlLabelResize.set({'styles':{ 'visibility' : 'hidden' }});
		this.htmlControlResize.set({'styles':{ 'visibility' : 'hidden' }});
		this.htmlDragHandle.set({'styles':{ 'visibility' : 'hidden' }});
		this.htmlDeleteButton.set({'styles':{ 'visibility' : 'hidden' }});
	
	},
	
	select  : function() {
		
		this.htmlContainer.addClass('selected');
		this.htmlLabelResize.set({'styles':{ 'visibility' : 'visible' }});
		this.htmlControlResize.set({'styles':{ 'visibility' : 'visible' }});
		this.htmlDragHandle.set({'styles':{ 'visibility' : 'visible' }});
		this.htmlDeleteButton.set({'styles':{ 'visibility' : 'visible' }});
		this._alignLabelResizeHandle();		

		if( this.options.commonList != "Manual" ){
			$('JFormsEPlugin_listelements').disabled = true;
		} else {
			$('JFormsEPlugin_listelements').disabled = false;
		}
	
	},

	_updateDefault: function(){
  		
		this.defaultValueArray = new Array();
		for(i=0;i<this.htmlInput.options.length;i++){
			if(this.htmlInput.options[i].selected){
				this.defaultValueArray.push(this.htmlInput.options[i].text);
			}
	  	}	  
	},

  
	serialize: function(){
		this._updateDefault();
		this.options.defaultValue	= this.defaultValueArray.join('\n');
		return this.genericSerialize();
	}
  
});