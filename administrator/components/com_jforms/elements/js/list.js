function list_get( object ){return (object.options[object.selectedIndex].value);}
function list_set( object, value ){
			
	if(object == null )return;

	//Loop through all <option> elements 
	for(i=0;i<object.length;i++){
		//Did we encounter the value?
			
		if( object[i].value == value ){
			//Set selectedindex and quit
			object.selectedIndex = i;
			return;
		}
	}
}
window.addEvent('load'  ,function(e){
	$$('#ppage_container select').each(function(item,index){
		item.addEvent('blur'  ,function(){saveProperties();displayProperties();});
		item.addEvent('change',function(){saveProperties();displayProperties();});
	})
});