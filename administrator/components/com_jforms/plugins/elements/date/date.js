/**
* Javascript object for date plugin
*
* @version		$Id: date.js 354 2009-10-01 03:43:49Z dr_drsh $
* @package		Joomla
* @subpackage	JForms
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
var date = new Class({

	Extends: CLabeledElement,
	
	initialize: function( parent, id, beforeObject, options ) {
		
		this.type = "date";

		this.parent($(parent), id, $(beforeObject), options);
		
		//Defaults are always stored in "DD/MM/YYYY" format
		
		var thisYear = new Date().getFullYear();
		this._currentSpan = this.options.span;
		//Till Present
		if(this.options.span == 0){
			this._currentSpan = thisYear - this.options.startYear;
		}
		//Other
		if(this.options.span == -1){
			$('JFormsEPlugin_dateospan').disabled = false;
			this._currentSpan = this.options.ospan;
		} else {
			$('JFormsEPlugin_dateospan').disabled = true;
		}
		
		
		
		
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
		
		this._create();
		

	},
	_doYearList : function( e, start, span, selected ){
		
		e.options.length = 0;
		span  = parseInt(span, 10);
		start = parseInt(start, 10);
		selected =  parseInt(selected, 10);
		
		for(i=0;i<span+1;i++){
		
			var o = new Option( i+parseInt(start,10), i+parseInt(start,10));
			o.selected = false;			
			if( i+start == selected || i == selected ){
				o.selected = true;
			}
			e.options[i] = o;
		}
	},
	
	_doDayList : function( e, selected ){
		
		e.options.length = 0;
		
		for(i=0;i<31;i++){
		
			var o = new Option( i+1, i+1);
			o.selected = false;			
			if( i+1 == selected ){
				o.selected = true;
			}
			e.options[i] = o;
		}
	},
	_doMonthList : function( e, selected ){
		
		e.options.length = 0;
		var months = new Array('January','February','March','April','May','June',
							   'July','August','September','October','November','December');
	
		for(i=0;i<months.length;i++){
		
			var o = new Option( months[i], i+1);
			o.selected = false;			
			if( months[i] == selected || i+1 == selected ){
				o.selected = true;
			}
			e.options[i] = o;
		}
	},
	
	_create : function(){
		
		//Main input elements
		var br = new Element('br', {'clear':'all'});
		
		if(this.htmlInputDay && this.htmlInputMonth && this.htmlInputYear){
			this.htmlInputDay.dispose();
			this.htmlInputMonth.dispose();
			this.htmlInputYear.dispose();
			$$('#'+this.htmlContainer.id+' br').each(function(item,index){item.dispose();});
		}
		
		var format = this.options.format.split('/');
		var values = this.options.defaultValue.split("\n");
		
		for(k=0;k<format.length;k++){
			
			if(format[k] == 'DD'){
				this.htmlInputDay = new Element('select', {
					'name': 'input_' + this.type + this.index + '_d',
					'id':'input_' + this.type + this.index + '_d',
					'styles':{
						'width':'60px',
						'margin-right':'10px'
					}
				});
				this._doDayList(this.htmlInputDay, values[2]);
				this.htmlInputDay.inject( this.htmlContainer );
			}
			
			if(format[k] == 'MM'){
				this.htmlInputMonth = new Element('select', {
					'name': 'input_' + this.type + this.index + '_m',
					'id':'input_' + this.type + this.index + '_m',
					'styles':{
						'width':'80px',
						'margin-right':'10px'
					}
				});
				this._doMonthList(this.htmlInputMonth, values[1]);
				this.htmlInputMonth.inject( this.htmlContainer );
			}
			
			if(format[k] == 'YYYY'){
				this.htmlInputYear = new Element('select', {
					'name': 'input_' + this.type + this.index + '_y',
					'id':'input_' + this.type + this.index + '_y',
					'styles':{
						'width':'120px'
					}
				});
				this._doYearList(this.htmlInputYear, this.options.startYear, this._currentSpan, values[0]);
				this.htmlInputYear.inject( this.htmlContainer );
			}
		}
		br.inject( this.htmlContainer );
	},
	
	onUpdate : function(){
		
		var thisYear = new Date().getFullYear().toInt();

		this._currentSpan = this.options.span;
		if(this.options.span == 0){
			this._currentSpan = thisYear - this.options.startYear;
		}
		if(this.options.span == -1){
			$('JFormsEPlugin_dateospan').disabled = false;
			this._currentSpan = this.options.ospan.toInt();
		} else {
			$('JFormsEPlugin_dateospan').disabled = true;
		}
		
		if(this.options.label.trim().length == 0)this.options.label = 'Date ' + this.index;
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

		if(this._currentSpan < 1){
			this.options.span  = -1;
			this.options.ospan =  1;
			this._currentSpan  =  1;
			$('JFormsEPlugin_dateospan').disabled = false;
			displayProperties();
		}

		this.options.defaultValue = 
			parseInt(this.htmlInputYear.get('value') ,10)+"\n"+
			parseInt(this.htmlInputMonth.get('value'),10)+"\n"+
			parseInt(this.htmlInputDay.get('value')  ,10);

		this._create();
	},
	
	onResizeDrag: function(newSize,type) {
	
		switch(type){
			
			case "label":
				this.htmlLabel.set('styles', { 'border': '1px solid white' } );	
		}
		this._alignLabelResizeHandle();	
	},


	onResizeEnd: function( newSize, type ){

		switch(type){
		
			case 'label':
				this.options.lw = newSize.x;
				this.options.lh = newSize.y;
				this.htmlLabel.set('styles', { 'border' : '0'});
		}
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
	
	serialize: function(){
		this.options.defaultValue = 
			parseInt(this.htmlInputYear.get('value') ,10)+"\n"+
			parseInt(this.htmlInputMonth.get('value'),10)+"\n"+
			parseInt(this.htmlInputDay.get('value')  ,10);
		return this.genericSerialize();
		
	}
});
