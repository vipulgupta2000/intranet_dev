<?php
/**
* Default layout for upgrade view
*
* @version		$Id: default.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Layouts
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

JHTML::_('JForms.Upgrade.javascript');
JHTML::_('JForms.Upgrade.versions');

$versionObject = new JFormsVersion();
$status = $versionObject->dbMatches();

switch( $status ){
	case JFORMS_UPGRADE_U2D:
		echo '<div class="large-message">'.JText::_('No upgrades required').'</div>';
		break;

	case JFORMS_UPGRADE_DB:
		echo '<div class="large-message red">'.JText::_('Database is out of date').' <a href="javascript:autoUpgrade()">'.JText::_('Upgrade now').'</a></div>';
		break;

	case JFORMS_UPGRADE_CODE:
		echo '<h1 class="large-message red">'.JText::_('Extension is out of date').'</h1>';
		break;
}
?>

<div id='loading-container'></div>

<?php JHTML::_('JForms.Upgrade.notice'); ?>
<br />
<br />
<?php JHTML::_('JForms.General.version'); ?>