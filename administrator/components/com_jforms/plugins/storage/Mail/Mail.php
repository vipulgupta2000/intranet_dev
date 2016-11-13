<?php 
/**
* Mail storage  plugin
*
* @version		$Id: Mail.php 379 2010-04-22 12:06:58Z dr_drsh $
* @package		Joomla
* @subpackage	JForms.Plugins
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.mail.helper' );

/**
 * Button Element plugin 
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
 */
class JFormSPluginMail extends JFormSPlugin{


	function _translateMessage( $fields, $data, $messages, $attach, &$mailer ){
		
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
		
		$translatedMessages = array();


		foreach( $fields as $key => $field ){
			
			if(!array_key_exists('label', $field->parameters))continue;
			
			//matches strings like {field:FieldLabel.segment}
			//Segments are useful to return a specific part of the stored data
			//For example {field:juser.username} will be translated into the username only rather than the whole data of the juser element
			
			$fieldPlaceholder     = "\\{field:".JString::strtolower($field->parameters['label'])."(\\.\\w+){0,1}\\}";
			//{field.hash} can now be used, this can help resolve many conflicts
			//Support for label placeholders will be dropped in the next release
			$fieldPlaceholderHash = "\\{field:".JString::strtolower($field->parameters['hash']). "(\\.\\w+){0,1}\\}";

			//TODO: Use a more generic system that works for all "File system requiring objects"
			if( $field->type == 'file' && $attach ){
				foreach( $messages as $messageKey => $message ){
					if( preg_match( "/$fieldPlaceholder/i", $messages[$messageKey] )){
							$d = $pManager->invokeMethod('element','translate',array($field->type), array( $field, $data[$key],'object' ) );
							if($d == '' || $d == null)continue;
							$mailer->AddAttachment(JFORMS_FS_PATH.DS.$d->path);
					}
					if( preg_match( "/$fieldPlaceholderHash/i", $messages[$messageKey] )){
							$d = $pManager->invokeMethod('element','translate',array($field->type), array( $field, $data[$key],'object' ) );
							if($d == '' || $d == null)continue;
							$mailer->AddAttachment(JFORMS_FS_PATH.DS.$d->path);
					}
				}
				continue;
			}

			
			foreach( $messages as $messageKey => $message ){
				$matches = array();
				if( preg_match( "/$fieldPlaceholder/i", $message,$matches )){
				
					$segment = '';
					//Did user specify a segment?
					if( count($matches) > 1 ){
						$segment = trim($matches[1],'.');
					}
					$d = $pManager->invokeMethod('element','translate',array($field->type), array( $field, $data[$key], 'html', $segment ) );
					$messages[$messageKey] = preg_replace( "/$fieldPlaceholder/i", $d, $messages[$messageKey]  );
				}

				$matches = array();
				if( preg_match( "/$fieldPlaceholderHash/i", $message,$matches )){
				
					$segment = '';
					//Did user specify a segment?
					if( count($matches) > 1 ){
						$segment = trim($matches[1],'.');
					}
					$d = $pManager->invokeMethod('element','translate',array($field->type), array( $field, $data[$key], 'html', $segment ) );
					$messages[$messageKey] = preg_replace( "/$fieldPlaceholderHash/i", $d, $messages[$messageKey]  );
				}
			
			}
		}
		return $messages;
	}
	
	function _processNewEmailFields($form, $dataText, $fields, $data ){

		foreach( $fields as $key => $field ){
			
			if( $field->type == 'email' ){
				
				if( $field->parameters['uinput'] ){
					if( $field->parameters['minput'] )
						$incomingEmails = explode(',', $data[$key]);
					else
						$incomingEmails = array( $data[$key] );
				} else {
					$incomingEmails = array();
				}
				
				
				$attach = intval($field->parameters['attachFiles']);
				
				
				$mail = JFactory::getMailer();
				$mail->IsHTML( true );
				
				$cfg = new JConfig();
				$mail->setSender(array($cfg->mailfrom,$cfg->fromname));
				
				//This is to avoid damaging the parameter when the translation output has commas
				$field->parameters['emails'] = str_replace(',',"\n",$field->parameters['emails']);
				$messages = array($field->parameters['subject'],$field->parameters['replyto'],$field->parameters['message'],$field->parameters['emails']);
				
				list($field->parameters['subject'], $field->parameters['replyto'], $field->parameters['message'],$field->parameters['emails'])
				=JFormSPluginMail::_translateMessage( $fields, $data, $messages, $attach, $mail);				

				$incomingEmails = array_merge( $incomingEmails, explode( "\n", $field->parameters['emails']));
				
				foreach($incomingEmails as $address )
					if(JMailHelper::isEmailAddress($address))$mail->AddRecipient( $address );
				
				if(JMailHelper::isEmailAddress($field->parameters['replyto']))
							$mail->AddReplyTo( array($field->parameters['replyto'],'') );
				
				$field->parameters['message'] = preg_replace('/{FORM_NAME}/i',$form->title,$field->parameters['message']);
				$field->parameters['subject'] = preg_replace('/{FORM_NAME}/i',$form->title,$field->parameters['subject']);
				$field->parameters['message'] = preg_replace('/{ENTRY_DATA}/i',$dataText,$field->parameters['message']);
				$field->parameters['message'] = str_replace("\r\n", "\n"    , $field->parameters['message'] );
				$field->parameters['message'] = str_replace("\n"  , "<br />", $field->parameters['message'] );
				
				$mail->SetSubject($field->parameters['subject']);
				$mail->SetBody($field->parameters['message']);
				$mail->Send();
			}
		}
	}
	
	function saveRecord( $form, $data ){
			
		$pManager =& JFormsGetPluginManager();
		$pManager->loadPlugins('element');
			
		//Send out an E-mail to Administrators and User (Based on Form settings)
		$dataText = "<br />";
		$formName = $form->title;
			
		$pluginSettings  = $form->storagePluginParameters['Mail'];
			
		$fields = indexByHash( $form->fields );
		
				
		$adminMessage = $pluginSettings['AdminText'];
		$userMessage  = $pluginSettings['ConfrimText'];
			
		foreach($fields as $key => $f ){
				
			//If field has no storage requirments
			if( !count($pManager->settings['element'][$f->type]->storage) ){
				//Ignore it
				continue;
			}

			
			
				
			if( array_key_exists( $key, $data ) ){
				
				//Translate Raw data into readable format
				$d = $pManager->invokeMethod('element','translate',array($f->type), array( $f, $data[$key] ) );
				$dataText .= $f->parameters['label'] ." : ".$d."<br />";	
				
				$fieldPlaceholderA = '{FIELD='.JString::strtoupper($f->parameters['label']).'}';
				$fieldPlaceholderB = '{field:'.JString::strtolower($f->parameters['label']).'}';
				$adminMessage = str_replace( array($fieldPlaceholderA,$fieldPlaceholderB), $d, $adminMessage );
				$userMessage  = str_replace( array($fieldPlaceholderA,$fieldPlaceholderB), $d, $userMessage  );	
			}			
		}
		
		JFormSPluginMail::_processNewEmailFields($form,$dataText, $fields, $data);
	
		//Look for user E-mail field
		$userEmail = '';
		foreach($form->fields as $f){
				
			if( array_key_exists('isUserEmail',$f->parameters) && $f->parameters['isUserEmail'] == true ){
				$hash = $f->parameters['hash'];
				//Grab it from current record
				$userEmail = $data[$hash];
				break;
			}					
		}
			
		if( $pluginSettings['SendAdmin'] ){
			
			$AdminMails = explode(',',$pluginSettings['AdminMail']);
				
			$adminMessage = preg_replace('/{FORM_NAME}/i' ,$form->title,$adminMessage);
			$adminMessage = preg_replace('/{ENTRY_DATA}/i',$dataText  ,$adminMessage);
			$adminMessage = str_replace("\r\n", "\n"    , $adminMessage );
			$adminMessage = str_replace("\n"  , "<br />", $adminMessage );
			
			$mail = JFactory::getMailer();
			$mail->IsHTML( true );
			
			$cfg = new JConfig();
			$mail->setSender(array($cfg->mailfrom,$cfg->fromname));
				
			foreach($AdminMails as $address ){
				$mail->AddRecipient( $address );
			}
				
			if( $userEmail ){
				$mail->AddReplyTo( array($userEmail,'') );
			}
			
			//$mail->setSender( array( $email, $name ) );
			$mail->SetSubject(JText::_('New entry added'));
			$mail->SetBody( $adminMessage );
			$mail->Send();
		
		}
		if( $pluginSettings['SendUser'] ){
			
			if( JMailHelper::isEmailAddress( $userEmail ) ){
			
				$userMessage = preg_replace('/{FORM_NAME}/i' ,$form->title,$userMessage);
				$userMessage = preg_replace('/{ENTRY_DATA}/i',$dataText   ,$userMessage);
		
				$userMessage = str_replace("\r\n", "\n"    , $userMessage );
				$userMessage = str_replace("\n"  , "<br />", $userMessage );
		
				$mail = JFactory::getMailer();
				$mail->IsHTML( true );
				
				$cfg = new JConfig();
				$mail->setSender(array($cfg->mailfrom,$cfg->fromname));
				
				$mail->AddRecipient( $userEmail );
				
				//$mail->setSender( array( $email, $name ) );
				$mail->SetSubject(JText::_('Your entry has been received'));
				$mail->SetBody($userMessage);
				$mail->Send();
			}
		}
	}
}