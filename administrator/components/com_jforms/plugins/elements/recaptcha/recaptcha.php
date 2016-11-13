<?php
/**
* ReCaptcha Element plugin
*
* @version		$Id: recaptcha.php 362 2010-02-20 06:50:23Z dr_drsh $
* @package		Joomla
* @subpackage	JForms
* @copyright	Copyright (C) 2008 Mostafa Muhammad. All rights reserved.
* @license		GNU/GPL
*
* Slightly modified version from the original file written by my mentor "Jui-Yu Tsai"
*
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Get a key from http://recaptcha.net/api/getkey
require_once('recaptchalib.php');

/**
 * reCaptcha Element Plugin
 *
 * @package    Joomla
 * @subpackage JForms.Plugins
*/
class JFormEPluginRecaptcha extends JFormEPlugin{
	
	function _constructCustomLang( $string ){
		
		$keys   = array('visual_challenge','audio_challenge','refresh_btn','instructions_visual','instructions_audio','help_btn','play_again','cant_hear_this','incorrect_try_again');
		$values = explode('\n', $string);
		$badChars = array("\"","'","\\","\n","\r","\t");

		$output = array();
		for($i=0;$i<count($values);$i++){
			$key   = $keys[$i];
			$value = $values[$i];
			$key   = str_replace($badChars,'',$key);
			$value = str_replace($badChars,'',$value);
			$output[] = _line("$key:'$value'",2);			
		}
		return '{'.implode(',', $output).'}';
	}
	
	function render( $elementData ){
	    
		$p = JArrayHelper::toObject($elementData->parameters);

		# reCaptcha Initial
		$publickey  = $p->publickey;
		$privatekey = $p->privatekey;
		$lang       = $p->lang;
		$clang	    = $p->custom_lang;
		$theme	    = $p->theme;
		# the response from reCAPTCHA
		$resp       = null;
		# the error code from reCAPTCHA, if any
		$error      = null;
		
		$error   = isset($elementData->validationError)?$elementData->validationError:'';
		
		$htmlId = $p->hash.'_'.$elementData->id;
		
		$isCustomLang = ($p->lang == '__');
		
		
		$output  = '';
		$output .= _line("<div class='error-message' id='{$htmlId}_error'>$error</div>",2	);
		$output .= _line("<script type='text/javascript'>",2);
		$output .= _line("var RecaptchaOptions = {",3);
		$output .= _line("theme : '$p->theme',",4);
		if($isCustomLang){
			$customLang   = JFormEPluginRecaptcha::_constructCustomLang( $clang );
			$output .= _line("custom_translations : $customLang",4);
		} else {
			$output .= _line("lang : '$p->lang'",4);
		}
		$output .= _line("};",3);
		$output .= _line("</script>",2);
		$output .= _line(recaptcha_get_html($publickey, $error),2);
		$output .= _line('<div class="clear"></div>',2);

		return $output;

	}
	
	function validate( $elementData, $input ){
		
		$p = JArrayHelper::toObject($elementData->parameters);
		
		# reCaptcha Initial
		$publickey  = $p->publickey;
		$privatekey = $p->privatekey;
		# the response from reCAPTCHA
		$resp       = null;
		# the error code from reCAPTCHA, if any
		$error      = null;
		
		if (isset($_POST["recaptcha_response_field"])) {
		    $resp = recaptcha_check_answer ($privatekey,
                                            $_SERVER["REMOTE_ADDR"],
                                            JRequest::getString('recaptcha_challenge_field', '', 'POST'),
                                            JRequest::getString('recaptcha_response_field', '', 'POST'));

            if ($resp->is_valid) {
            } else {
                # set the error code so that we can display it
                $error = $resp->error;
				return JText::_($resp->error);
            }
			return "";
        } else {
		    return JText::_("Field Required");
		}
		
	}
}