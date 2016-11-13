<?php
defined('_JEXEC') or die( 'Restricted access' );

$basePath = dirname(__FILE__) . DS . '..' . DS;
require_once ($basePath . 'kernel' . DS . 'class.AriKernel.php');

require_once ($basePath . 'utils/constants.php');
require_once ($basePath . 'kernel/class.AriKernel.php');

AriKernel::import('Controllers.QuizController');
AriKernel::import('Controllers.ResultController');
AriKernel::import('Controllers.TextTemplateController');
AriKernel::import('Controllers.LicenseController');
AriKernel::import('Web.TaskManager');
AriKernel::import('Web.Controls.MultiplierControls');
AriKernel::import('Web.AdminQuizPageBase');
AriKernel::import('Web.UserQuizPageBase');
AriKernel::import('Date.Date');
AriKernel::import('Text.Text');
AriKernel::import('I18N.I18N');
AriKernel::import('Security.Security');
AriKernel::import('Entity.EntityFactory2');
AriKernel::import('Web.Utils.QuizWebHelper');
AriKernel::import('Web.Utils.Util');

class JElementQuiz extends JElement
{
	var	$_name = 'Quiz';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$qc = new AriQuizController();
		$quizzes = $qc->getQuizList(
			null,
			1
		);
		
		return JHTML::_(
			'select.genericlist', 
			$quizzes, 
			$control_name . '[' . $name . ']', 
			'class="inputbox"', 
			'QuizId', 
			'QuizName', 
			$value,
			$control_name . $name);
	}
}
