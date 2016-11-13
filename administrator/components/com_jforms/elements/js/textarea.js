function textarea_get( object ){return object.value;}
function textarea_set( object, value ){object.value=value;}
window.addEvent('load'  ,function(e){
	$$('#ppage_container textarea').each(function(item,index){
		item.addEvent('blur'  ,function(){saveProperties();displayProperties()});
		item.addEvent('change',function(){saveProperties();displayProperties()});
	})
});
