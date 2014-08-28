<?php
/**
* @package Keypic Plugin
* @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link     http://www.techjoomla.com
*/

defined('_JEXEC') or die;
jimport( 'joomla.filesystem.file' );

//Require class file
require_once( dirname(__FILE__).'/class/keypic.php' ); 

//Load style
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'plugins/captcha/keypic/style/keypic.css');

//Class
class PlgCaptchaKeypic extends JPlugin
{
	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}	

	//Initialise Keypic
	public function onInit($id = 'keypic_image')
	{
	}

	//Display Keypic banner
	public function onDisplay($name, $id = 'keypic_image', $class = '')
	{
		//Check if Form ID is valid
		$isvalid = Keypic::checkFormID( $this->params->get('formid') );
		$image_token = '';

		if( !$this->params->get('formid') )
			$image_token = '<span class="errormessage">'.JText::_('PLG_KEYPIC_NO_FORM_ID').'</span>';

		else if($isvalid['status'] == 'error')
			$image_token = '<span class="errormessage">'.JText::_('PLG_KEYPIC_INVALID_FORM_ID').'</span>';

		else
		{		
			//Ger banner size from param
			$kepic_button_type = $this->params->get('button_type');
			//Generate new token and keep it in hidden field
			$image_token .= '<input type="hidden" name="'.$name.'" id="addtoken" value="'.Keypic::getToken('', '').'" />';
			$image_token .= Keypic::getIt('getScript', $kepic_button_type);
		}
		
		return $image_token;
	}

	//Check spam percentage and register user
	public function onCheckAnswer($code)
	{
		// Required objects 
		$input = JFactory::getApplication()->input;

		// Get the form data 
		$formData = new JRegistry( $input->get('jform', '', 'array') );
		
		//Get generated token
		$token = $formData->get('captcha','','');

		//Check for keypic token
		if ( !$token )
		{
			$this->_subject->setError(JText::_('PLG_KEYPIC_ERROR_NO_TOKEN'));
			return false;
		}
		
		//Get spam percentage from Keypic API
		$percent = Keypic::isSpam( $formData->get('captcha','',''), $formData->get('email','',''));
		
		$input->set('percent', $percent, '');
		
		//Flag it as spam
		if( $percent >= $this->params->get('spam', '70') )
		{
			$this->_subject->setError( str_replace( $percent, $percent.'%', JText::sprintf('PLG_KEYPIC_SPAM', $percent) ) );
			return false;
		}
		else return true;
	}
}
