/*
	A good amount of the code found here is adopted
	from Luca Pillonel's formcheck which is released under MIT-style license
	
	For more information: http://mootools.floor.ch/en/
	
*/
		

var CErrorTip = new Class({
	
	currentDisplacement  : 0 ,
	isValidationTooltipsVisible: false,
	
	start   : function( parent ) {this.parent = parent;this.destroyAll();this.currentDisplacement=0;this.isValidationTooltipsVisible=true;},
	
	destroyAll : function(){
		var ee = this;

		$$('li.element').each(function(itm,index){ee.destroy(itm);});
		this.isValidationTooltipsVisible = false;
	},
	
	destroy : function( parent, fade){
		
		parent.removeClass('error');
		if(fade == null)fade=true;
		
		var element = $(parent.id + '_error_tip');
		if( !element )return;
		if( !fade ){
			element.dispose();
			return;
		}
		var fx = new Fx.Morph(element);
		fx.onComplete = 
		function(e) {	
			element.dispose();
		}
		fx.start({'opacity': '0'});
	},
	
	create  : function( parent, errors ){
		
		if( $(parent.id + '_error_tip') != null )this.destroy(parent,false);
		
		var position = parent.getCoordinates();
		var size     = parent.getSize();
		
		parent.addClass('error');
		
		var styles = {
			'position' : 'absolute',
			'float'    : 'left',
			'left'     : position.left + this.currentDisplacement + 20,
			'top'      : position.top   - 35
		}
		var errorDiv = new Element('div', {
			'id'       : parent.id + '_error_tip',
			'class'    : 'fc-tbx',
			'styles'   : styles
		});
		errorsArray = [];
		errors.each(function(error) {
			errorsArray.push(new Element('p').set('html', error));
		});
		
		var table = new Element('table');
			table.cellPadding ='0';
			table.cellSpacing ='0';
			table.border ='0';
			
			var tbody = new Element('tbody').injectInside(table);
				var tr1 = new Element('tr').injectInside(tbody);
					new Element('td', {'class' : 'tl'}).injectInside(tr1);
					new Element('td', {'class' : 't'}).injectInside(tr1);
					new Element('td', {'class' : 'tr'}).injectInside(tr1);
				var tr2 = new Element('tr').injectInside(tbody);
					new Element('td', {'class' : 'l'}).injectInside(tr2);
					var cont = new Element('td', {'class' : 'c'}).injectInside(tr2);
						var errorsDiv = new Element('div', {'class' : 'err'}).injectInside(cont);
						errorsArray.each(function(error) {
							error.injectInside(errorsDiv);
						});
						var ee = this;
						new Element('a',{'class' : 'close',	'events':{'click':function(){ee.destroy(parent);}}}).injectInside(cont);
					new Element('td', {'class' : 'r'}).injectInside(tr2);
				var tr3 = new Element('tr').injectInside(tbody);
					new Element('td', {'class' : 'bl'}).injectInside(tr3);
					new Element('td', {'class' : 'b'}).injectInside(tr3);
					new Element('td', {'class' : 'br'}).injectInside(tr3);			
		table.injectInside(errorDiv);
		errorDiv.injectInside(this.parent);
		if( this.currentDisplacement + errorDiv.getDimensions().x + errorDiv.getPosition().x > parent.getPosition().x + size.x)
			this.currentDisplacement = 0;
		else
			this.currentDisplacement += errorDiv.getDimensions().x;
		
	}
});