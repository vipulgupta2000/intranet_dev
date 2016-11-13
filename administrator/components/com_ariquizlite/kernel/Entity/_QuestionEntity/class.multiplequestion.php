<?php
/*
 * ARI Quiz Lite
 *
 * @package		ARI Quiz Lite
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

define ('ARI_QUIZ_MQ_DOC_TAG', 'answers');
define ('ARI_QUIZ_MQ_ITEM_TAG', 'answer');
define ('ARI_QUIZ_MQ_CORRECT_ATTR', 'correct');
define ('ARI_QUIZ_MQ_ID_ATTR', 'id');

AriKernel::import('Entity._QuestionEntity.QuestionBase');
AriKernel::import('Entity._QuestionEntity._Templates.QuestionTemplates');

class MultipleQuestion extends QuestionBase 
{ 	
	function getDataFromXml($xml, $htmlSpecialChars = TRUE)
	{
		$data = null;
		if (!empty($xml))
		{
			$xmlHandler =& JFactory::getXMLParser('Simple');
			$xmlHandler->loadString($xml);
			$childs = $xmlHandler->document->children();
			if (!empty($childs))
			{
				$data = array();
				foreach ($childs as $child)
				{
					$answer = $child->data();
					$data[] = array(
						'tbxAnswer' => $htmlSpecialChars
							? AriQuizWebHelper::htmlSpecialChars($answer) 
							: $answer,
						'hidQueId' => $child->attributes(ARI_QUIZ_MQ_ID_ATTR),
						'cbCorrect' => $child->attributes(ARI_QUIZ_MQ_CORRECT_ATTR));
				}
			}
		}

		return $data;
	}
	
	function getFrontXml()
	{
		$selectedAnswers = JRequest::getVar('selectedAnswers', array());
		$xmlHandler =& JFactory::getXMLParser('Simple');
		$xmlHandler->loadString(sprintf(ARI_QT_TEMPLATE_XML, ARI_QUIZ_DB_CHARSET, ARI_QUIZ_MQ_DOC_TAG));
		$xmlDoc = $xmlHandler->document; 
		if (!is_array($selectedAnswers))
		{
			$selectedAnswers = array($selectedAnswers);
		}
		
		foreach ($selectedAnswers as $answerId)
		{
			$answerId = trim($answerId);
			if (!empty($answerId))
			{
				$xmlItem =& $xmlDoc->addChild(ARI_QUIZ_MQ_ITEM_TAG);
				$xmlItem->addAttribute(ARI_QUIZ_MQ_ID_ATTR, $answerId);
			}
		}
		
		return $xmlDoc->toString();
	}
	
	function isCorrect($xml, $baseXml)
	{
		$isCorrect = false;
		if (!empty($xml) && !empty($baseXml))
		{
			$data = $this->getDataFromXml($baseXml);
			$correctIdList = array();
			if (!empty($data))
			{
				foreach ($data as $dataItem)
				{
					if (!empty($dataItem['cbCorrect']))
					{
						$correctIdList[] = $dataItem['hidQueId'];
					}
				}
			}

			if (count($correctIdList) > 0)
			{
				$xData = $this->getDataFromXml($xml);
				$selIdList = array();
				foreach ($xData as $dataItem)
				{
					$selIdList[] = $dataItem['hidQueId'];
				}

				if (count($correctIdList) == count($selIdList))
				{
					$diff = array_diff($correctIdList, $selIdList);
					if (empty($diff) || count($diff) == 0)
					{
						$isCorrect = true;
					}
				}
			}
		}
		
		return $isCorrect;
	}

	function getXml()
	{
		$answers = WebControls_MultiplierControls::getData('tblQueContainer', array('tbxAnswer', 'cbCorrect', 'hidQueId'), null, true);
		$xmlStr = null;
		if (!empty($answers))
		{
			$xmlHandler =& JFactory::getXMLParser('Simple');
			$xmlHandler->loadString(sprintf(ARI_QT_TEMPLATE_XML, ARI_QUIZ_DB_CHARSET, ARI_QUIZ_MQ_DOC_TAG));
			$xmlDoc = $xmlHandler->document;
			foreach ($answers as $answerItem)
			{
				$answer = trim($answerItem['tbxAnswer']);
				if (strlen($answer))
				{
					if (empty($answer)) $answer .= ' ';
					$xmlItem =& $xmlDoc->addChild(ARI_QUIZ_MQ_ITEM_TAG);
					$xmlItem->setData($answer);
					
					$correct = isset($answerItem['cbCorrect']);
					if ($correct)
					{
						$xmlItem->addAttribute(ARI_QUIZ_MQ_CORRECT_ATTR, 'true');
					}
					
					$id = isset($answerItem['hidQueId']) && !empty($answerItem['hidQueId']) 
						? $answerItem['hidQueId'] 
						: uniqid('', TRUE);
					$xmlItem->addAttribute(ARI_QUIZ_MQ_ID_ATTR, $id);
				}
			}

			$xmlStr = $xmlDoc->toString();
		}

		return $xmlStr;
	}
}
?>	