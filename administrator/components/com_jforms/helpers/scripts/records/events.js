function onDeleteSuccess( responseText, responseXML ){
	
	var validInput = /^(\d+,)*\d+$/.test( responseText );
	if(!validInput){
		alert("<?php echo JText::_("An Error has occured:"); ?>\n"+responseText.stripTags());
		return;
	}
	
	ids = responseText.split(',');
	for(i=0;i<ids.length;i++){	
		jsDataGrid.deleteRow ( ids[i] );
	}
	$('loadingDiv').set('styles',{'display':'none'});
}
function onDeleteFailure( xhr ){
	
	var statusText = xhr.statusText.br2nl().singleSpace().stripTags().trim();
	var errorBody  = xhr.responseText.br2nl().singleSpace().stripTags().trim();
	alert("<?php echo JText::_("An Error has occured:"); ?>\n"+statusText+"\n\n"+errorBody);
	$('loadingDiv').set('styles',{'display':'none'});

}

function onReloadSuccess( responseText, responseXML ){

	var validInput = /^\d+;\[\[.*\]\]$/.test( responseText );
	
	if(!validInput && responseText != '0;'){
		alert("<?php echo JText::_("An Error has occured:"); ?>\n"+responseText.stripTags());
		$('loadingDiv').set('styles',{'display':'none'});
		return;
	}
	
	
	var recordPerPage = parseInt($('record_per_page').options[$('record_per_page').selectedIndex].value,10); 
	totalRecords  = parseInt((responseText.substr(0,responseText.indexOf(';'))),10);
	var data = responseText.substr(responseText.indexOf(';')+1);
	
	//Update Globlals
	currentPage        = parseInt($('current_page').options[$('current_page').selectedIndex].value,10); 
	currentRecrodCount = totalRecords;
	
	if( data != '' ){
		eval('var d = ' + data);
		jsDataGrid.parse(d,"jsarray");
	}	
		
		
	pageCount = Math.floor(totalRecords / recordPerPage);
	if( totalRecords % recordPerPage)pageCount++;
		
	if( totalRecords == 0 )pageCount = 1;
		
	//Update Globals
	currentPageCount = pageCount;
		
	$('current_page').options.length = 0;
		
	for(i=1;i<pageCount+1;i++){
		var option = new Option(i,i);
		if( currentPage == i ){
			option.selected = true;
		}
		$('current_page').options[i-1] = option;
	}
		
	$('loadingDiv').set('styles',{'display':'none'});
	htmlDataGrid.set('styles',{'display':'block'});
}

function onReloadFailure( xhr ){
	
	var statusText = xhr.statusText.br2nl().singleSpace().stripTags().trim();
	var errorBody  = xhr.responseText.br2nl().singleSpace().stripTags().trim();
	alert("<?php echo JText::_("An Error has occured:"); ?>\n"+statusText+"\n\n"+errorBody);
	$('loadingDiv').set('styles',{'display':'none'});
}

function onValidateFilter( form ){
	reloadRecords();
	return false;
}