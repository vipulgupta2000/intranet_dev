/**
*  Utility Javascript functions
*
* @version		$Id: utilities.js 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

//  Base64 encode / decode (http://www.webtoolkit.info/)
var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(a){var b="";var c,chr2,chr3,enc1,enc2,enc3,enc4;var i=0;a=Base64._utf8_encode(a);while(i<a.length){c=a.charCodeAt(i++);chr2=a.charCodeAt(i++);chr3=a.charCodeAt(i++);enc1=c>>2;enc2=((c&3)<<4)|(chr2>>4);enc3=((chr2&15)<<2)|(chr3>>6);enc4=chr3&63;if(isNaN(chr2)){enc3=enc4=64}else if(isNaN(chr3)){enc4=64}b=b+this._keyStr.charAt(enc1)+this._keyStr.charAt(enc2)+this._keyStr.charAt(enc3)+this._keyStr.charAt(enc4)}return b},decode:function(a){var b="";var c,chr2,chr3;var d,enc2,enc3,enc4;var i=0;a=a.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(i<a.length){d=this._keyStr.indexOf(a.charAt(i++));enc2=this._keyStr.indexOf(a.charAt(i++));enc3=this._keyStr.indexOf(a.charAt(i++));enc4=this._keyStr.indexOf(a.charAt(i++));c=(d<<2)|(enc2>>4);chr2=((enc2&15)<<4)|(enc3>>2);chr3=((enc3&3)<<6)|enc4;b=b+String.fromCharCode(c);if(enc3!=64){b=b+String.fromCharCode(chr2)}if(enc4!=64){b=b+String.fromCharCode(chr3)}}b=Base64._utf8_decode(b);return b},_utf8_encode:function(a){a=a.replace(/\r\n/g,"\n");var b="";for(var n=0;n<a.length;n++){var c=a.charCodeAt(n);if(c<128){b+=String.fromCharCode(c)}else if((c>127)&&(c<2048)){b+=String.fromCharCode((c>>6)|192);b+=String.fromCharCode((c&63)|128)}else{b+=String.fromCharCode((c>>12)|224);b+=String.fromCharCode(((c>>6)&63)|128);b+=String.fromCharCode((c&63)|128)}}return b},_utf8_decode:function(a){var b="";var i=0;var c=c1=c2=0;while(i<a.length){c=a.charCodeAt(i);if(c<128){b+=String.fromCharCode(c);i++}else if((c>191)&&(c<224)){c2=a.charCodeAt(i+1);b+=String.fromCharCode(((c&31)<<6)|(c2&63));i+=2}else{c2=a.charCodeAt(i+1);c3=a.charCodeAt(i+2);b+=String.fromCharCode(((c&15)<<12)|((c2&63)<<6)|(c3&63));i+=3}}return b}}

function enableTab( tabName ){
 
	var selectedBtnId  = 'btn-' + tabName;
	var selectedTabId  = 'tab-' + tabName;
	
	var allTabs    = $$('div.tab-body');
	var allButtons = $$('div.tab-button');
  
	for(i=0;i<allTabs.length;i++){

		if( allTabs[i].get('id') == selectedTabId ){
			allTabs[i].style.display = 'block';
		} else {
			allTabs[i].style.display = 'none';
		}
	}

	for(i=0;i<allButtons.length;i++){
		if( allButtons[i].id == selectedBtnId){
			allButtons[i].addClass('active-tab-button');
		} else {
			allButtons[i].removeClass('active-tab-button');
		}
  }
}

//Source
//http://www.breakingpar.com/bkp/home.nsf/0/87256B280015193F87256BF8004D72D6
function dp(obj, parent) {
   // Go through all the properties of the passed-in object
   for (var i in obj) {
      // if a parent (2nd parameter) was passed in, then use that to
      // build the message. Message includes i (the object's property name)
      // then the object's property value on a new line
      if (parent) { var msg = parent + "." + i + "\n" + obj[i]; } else { var msg = i + "\n" + obj[i]; }
      // Display the message. If the user clicks "OK", then continue. If they
      // click "CANCEL" then quit this level of recursion
      if (!confirm(msg)) { return; }
      // If this property (i) is an object, then recursively process the object
      if (typeof obj[i] == "object") {
         if (parent) { dp(obj[i], parent + "." + i); } else { dp(obj[i], i); }
      }
   }
}


function array_repeat( value, number ){
	var a = new Array();
	for(i=0;i<number;i++)a[i] = value;
	return a;
}


String.implement({
	
	br2nl: function(){return this.replace(/<br\s.*?>/ig,'\n').replace(/<br>/ig,'\n');},
	singleSpace:function(){return this.replace(/\s{2,}/g,' ');},
	
	makeSafe: function(){return this.trim().replace(/\r/g,'').addSlashes().replace(/\n/g,"\\n");},
	
	addSlashes: function(){
		str = this;
		str = str.replace(/\\/g,'\\\\');
		str = str.replace(/\'/g,'\\\'');
		str = str.replace(/\"/g,'\\"');
		str = str.replace(/\0/g,'\\0');
		return str;
	},

	stripSlashes: function(){
		str = this;
		str = str.replace(/\\0/g,'\0');
		str = str.replace(/\\"/g,'"');
		str = str.replace(/\\'/g,'\'');
		str = str.replace(/\\\\/g,'\\');
		return str;
	}
});