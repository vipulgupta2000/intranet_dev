<?php
/**
* General Purpose Helper
* This class is the HTML Workhorse that provides several general purpose functions
*
* @version		$Id: general.php 375 2010-04-12 23:12:50Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Helpers
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
* General purpose HTML Helper
* This class is the HTML Workhorse that provides several general purpose functions
 *
 * @package    Joomla
 * @subpackage JForms.Helpers
*/
class JFormsGeneral{

	function IE(){
		$document =& JFactory::getDocument();
		$document->addCustomTag(
			
			 '<!--[if IE]>'."\r\n"
			.'<link rel="stylesheet" href="'.JURI::root().'media/com_jforms/styles/design-backend-ie.css" type="text/css" />'."\r\n" 
			.'<![endif]-->'."\r\n"
		);
	}
	
	function createFunction($args, $code){
	    static $n = 0;
	    $functionName = sprintf('ref_lambda_%d',++$n);
	    $declaration = sprintf('function %s(%s) {?> %s <?php } ',$functionName,$args,$code);
		eval($declaration);
   	    return $functionName;
	}

	function script( $filename, $path ){
		
		if (!is_file($path.DS.$filename))return;
		//A fix for PHP 5.3 func_get_args() warning
		$func = JFormsGeneral::createFunction('',file_get_contents($path.DS.$filename));
	 
		ob_start();
		$params = func_get_args();
		call_user_func_array($func,$params);
		$contents = ob_get_contents();
		ob_end_clean();
		
		
		echo
		"<script type='text/javascript'>"
		."\n// <![CDATA["
		."\n$contents"
		."\n// ]]>"
		."\n</script>";
	}
	
	function version(){
		
		$version = new JFormsVersion();
		
		echo '<div style="text-align:right;font-size:x-small;font-weight:bold;color:green">';
		echo $version->getLongVersion();
		echo '</div>';
		
		echo '<div style="text-align:center;font-size:xx-small">';
		echo $version->URL;
		echo '</div>';

	}
	
	function mootools(){
		//Nasty hack warning [Unload Mootools 1.11 and load 1.2]
		JHTML::_('script', 'dummy.js', 'media/com_jforms/scripts/');
		$doc = &JFactory::getDocument();
		$newScriptArray = array();
		$foundMootools = false;
		foreach( $doc->_scripts as $k => $v )
			if( strpos($k, 'media/system/js/mootools') === false )$newScriptArray[$k] = $v;
			else $foundMootools=true;
		if( $foundMootools )$newScriptArray[JURI::root().'media/com_jforms/scripts/mootools.js'] = 'text/javascript' ;
		$doc->_scripts = $newScriptArray;
		//End of nasty hack
	}
	
	function fixPane(){
		
		$pane	=& JPane::getInstance('sliders');
		$output  = '<div style="display:none">';
		$output .= $pane->startPane('xyz');
		$output .= $pane->startPanel( 'xyz-p', 'xyz-p' );
		$output .= $pane->endPanel();
		$output .= $pane->endPane();
		$output .= '</div>';
		echo $output;
	}
	

	

	function indentedLine($text, $indention){
		$tabs = str_repeat( "\t" , $indention );
		return $tabs.$text."\n";
	}

	function legend()
	{

		?>
		<table cellspacing="0" cellpadding="4" border="0" align="center">
		<tr align="center">
			<td>
			<img src="images/publish_y.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Pending' ); ?>" />
			</td>
			<td>
			<?php echo JText::_( 'Published, but is' ); ?> <u><?php echo JText::_( 'Pending' ); ?></u> |
			</td>
			<td>
			<img src="images/publish_g.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Visible' ); ?>" />
			</td>
			<td>
			<?php echo JText::_( 'Published and is' ); ?> <u><?php echo JText::_( 'Current' ); ?></u> |
			</td>
			<td>
			<img src="images/publish_r.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Finished' ); ?>" />
			</td>
			<td>
			<?php echo JText::_( 'Published, but has' ); ?> <u><?php echo JText::_( 'Expired' ); ?></u> |
			</td>
			<td>
			<img src="images/publish_x.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Finished' ); ?>" />
			</td>
			<td>
			<?php echo JText::_( 'Not Published' ); ?>
			</td>


		</tr>
		<tr>
			<td colspan="10" align="center">
			<?php echo JText::_( 'Click on icon to toggle state.' ); ?>
			</td>
		</tr>
		</table>
		<?php
	}
}