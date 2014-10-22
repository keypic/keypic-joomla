<?php
/**
* @package Keypic Plugin
* @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link     http://www.techjoomla.com
*/

defined('JPATH_BASE') or die;

class PlgSystemKeypic extends JPlugin
{
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	public function onAfterInitialise()
	{
		$app = JFactory::getApplication();

		if( $app->isAdmin() && $app->input->get('option') == 'com_users' && $app->input->get('view') == 'users')
		{
			JToolBarHelper::custom('keypic.rspam', '', '',JText::_('PLG_SYSTEM_KEYPIC_BUTTON'), false);
		}
	}
	
	//Check for wrong Form ID
	public function onExtensionAfterSave()
	{
		require_once(JPATH_SITE.'/plugins/captcha/keypic/class/keypic.php');
		
		$input = JFactory::getApplication()->input;
		
		// Get the form data 
		$formData = new JRegistry( $input->get('jform', '', 'array')  );
		
		if( !$formData->get('params')->formid) return;
		
		//Check if Form ID is valid
		$isvalid = Keypic::checkFormID( $formData->get('params')->formid );

		if($isvalid['status'] == 'error')
		{
			JError::raiseWarning(500, 'Wrong Form ID used ! Keypic will not work, you need to give valid Form ID.');
			return false;
		}
	}
}
