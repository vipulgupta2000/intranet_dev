function text_get( object ){return object.value;}
function text_set( object, value ){object.value=value;}
window.addEvent('load'  ,function(e){
	$$('#ppage_container input.text_area').each(function(item,index){
		item.addEvent('blur'  ,function(){saveProperties();displayProperties();});
		item.addEvent('change',function(){saveProperties();displayProperties();});
	})
});
