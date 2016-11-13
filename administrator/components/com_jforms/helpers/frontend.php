<?php
/**
* Frontend View Helper
* This class is the HTML Workhorse for the frontend
*
* @version		$Id: frontend.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Helpers
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
jimport('joomla.html.pane');

/**
* Frontend View Helper
* This class is the HTML Workhorse for the frontend
 *
 * @package    Joomla
 * @subpackage JForms.Helpers
*/
class JFormsFrontend{
	
	
	function javascript_validateForm( $form ){

		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$output  = _line('<script type="text/javascript">',1);
		$output .= _line('//<![CDATA[',1);
		$output .= _line('function validateForm_'.$form->id.'( form ){',1);
		$output .= _line('var errorArray = new Array();',2);
		$output .= _line('clearErrors_'.$form->id.'();',2);
		foreach( $form->fields as $f )
			$output .= $pManager->invokeMethod('element','jsValidation', array($f->type), array( $f ) ) . "\n";
		$output .= _line('if(errorArray.length){',2);
		$output .= _line('var scroll = new Fx.Scroll(window, {',3);
		$output .= _line('wait: false,',3);
		$output .= _line('duration: 1000,',3);
		$output .= _line('offset: {"x": 0, "y": 0},',3);
		$output .= _line('transition: Fx.Transitions.Quad.easeInOut',3);
		$output .= _line('});',3);
		$output .= _line('scroll.scrollTo(window.getScrollLeft(),errorArray[0].id.getPosition().y);'  ,3);
		//$output .= _line('for(i=0;i<errorArray.length;i++){',3);
		//$output .= _line('',4);
		//$output .= _line('',4);
		//$output .= _line('',4);
		//$output .= _line('}',3);
		$output .= _line('return false;',3);
		$output .= _line('}',2);
		$output .= _line('return true;',2);
		$output .= _line('}',1);
		$output .= _line('//]]>',1);
		$output .= _line('</script>',1);
		echo $output;
		
	}
	
	function javascript_clearErrors( $form ){

		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		$output  = _line('<script type="text/javascript">',1);
		$output .= _line('//<![CDATA[',1);
		$output .= _line('function clearErrors_'.$form->id.'( form ){',1);
		foreach( $form->fields as $f )
			$output .= $pManager->invokeMethod('element','jsClearErrors', array($f->type), array( $f ) ) . "\n";
		$output .= _line('}',1);
		$output .= _line('//]]>',1);
		$output .= _line('</script>',1);
		echo $output;
		
	}
	
	function elements( $form ){
		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		foreach ($form->fields as $f ){
			echo $pManager->invokeMethod('element','render', array($f->type), array( $f ) );
		}
	
	}
	
	function javascript( $form ){
		JHTML::_( 'behavior.mootools' );
		JFormsFrontend::javascript_clearErrors( $form );
		JFormsFrontend::javascript_validateForm( $form );
	}
	
	function stripSlashes( &$object ){
		$argCount = func_num_args();
		if( $argCount < 2)return;
		$args = func_get_args();
		
		for($i=1;$i<$argCount;$i++){
			if(property_exists($object, $args[$i] )){
				$object->$args[$i] = str_replace( "\\n","\n",$object->$args[$i]);
				$object->$args[$i] = stripSlashes($object->$args[$i]);
			}
		}
	}
}