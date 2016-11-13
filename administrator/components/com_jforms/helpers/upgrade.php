<?php
/**
* Upgrade View Helper
* This class is the HTML Workhorse for the Upgrade view
*
* @version		$Id: upgrade.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Helpers
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

define("JFORMS_UPGRADE_U2D"  , 0);
define("JFORMS_UPGRADE_DB"   , 1);
define("JFORMS_UPGRADE_CODE" , 2);

/**
 * Upgrade View Helper
 * This class is the HTML Workhorse for the Upgrade view
 *
 * @package    Joomla
 * @subpackage JForms.Helpers
*/
class JFormsUpgrade{

	function javascript(){
	
	$versionObject = new JFormsVersion();
	$sourceVersion      = $versionObject->getExtensionFromDB( $versionObject->getDBVersion() );
	$destinationVersion = $versionObject->RELEASE;
	
	$versionList = $versionObject->getVersionsInBetween( $sourceVersion, $destinationVersion, true );
	
	?>
	<script type='text/javascript'>
	// <![CDATA[
		
		var upgradeArray = new Array();
		
		<?php
		for($i=0; $i<count($versionList)-1; $i++){
			$fromToString = $versionList[$i].','.$versionList[$i+1];
			echo "upgradeArray[$i] = '$fromToString';\n";
		}
		?>
		
		function autoUpgrade(){
			
			var upgradeDivs = new Array(); 
			var success = true;
			for( i=0; i< upgradeArray.length; i++){
				var v = upgradeArray[i].split(',');
				upgradeDivs[i] =
				new Element('div',{
 					'class': 'upgrading',
					'html' : '<?php echo JText::_('Upgrading from version')?> ('+v[0]+') <?php echo JText::_('to version')?> ('+v[1]+')'
				});
				
				upgradeDivs[i].inject( $('loading-container'));
				var from = parseInt(v[0].replace(/\./,''),10);
				var to   = parseInt(v[1].replace(/\./,''),10);

				var result =  upgrade( from, to );
				if( result.status == 200 ){
					upgradeDivs[i].set('html',upgradeDivs[i].get('html') + '......<span class="ok"><?php echo JText::_('Success')?></span><br />');
				} else {
					success = false;
					upgradeDivs[i].set('html',upgradeDivs[i].get('html') + '......<span class="fail"><?php echo JText::_('Failed')?></span><br />');
				}
				upgradeDivs[i].set('html',upgradeDivs[i].get('html') + result.text);
				
			}
			if(success)location.reload(true);
		}
		
		function upgrade(from, to){
			var jtoken    = '<?php echo JUtility::getToken(); ?>';
			var url       = "<?php echo JURI::base().'index.php'; ?>";
			ajaxRequest = new Request({
				url:url,
				method: 'GET', 
				async:false
			});
			ajaxRequest.send(
				"option=com_jforms"
				+ "&controller=upgrade"
				+ "&task=upgrade"
				+ "&src=" + from
				+ "&dest=" + to
				+ "&step=1"
				+ "&"+jtoken+"=1"
			);
 			
			var text = '';
			if( ajaxRequest.xhr.status == 200 )text = ajaxRequest.xhr.responseText;
			else text = ajaxRequest.xhr.statusText;
			
			return { 
				'status' : ajaxRequest.xhr.status,
				'text'   : text
			};
		}
	//]]>
	</script>	
	<?php
	}
	
	function notice(){
		echo "<div class='notice'>".JText::_('JFORMSUPGRADENOTICE')."</div>";
	}
	
	function versions(){
		
		$versionObject = new JFormsVersion();
		$result = $versionObject->dbMatches();
		
		$dbVersion   = $versionObject->getDBVersion();
		$codeVersion = $versionObject->RELEASE;
		
		switch($result){
			
			case  0:
				echo "<div class='version'>".JText::_('Extension @ Version')." $codeVersion</div>";
				echo "<div class='version'>".JText::_('Database @ Version')." $dbVersion</div>";
				echo "<br clear='all' />";
				break;

			case  1:
				echo "<div class='version'>".JText::_('Extension @ Version')." $codeVersion</div>";
				echo "<div class='version red'>".JText::_('Database @ Version')." $dbVersion</div>";
				echo "<br clear='all' />";	
				break;

			case -1:
				echo "<div class='version red'>".JText::_('Extension @ Version')." $codeVersion</div>";
				echo "<div class='version'>".JText::_('Database @ Version')." $dbVersion</div>";
				echo "<br clear='all' />";
				break;
		}
	}
	
	function _versionToNumber($version){return intval( str_replace('.', '', $version));}
	

	
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