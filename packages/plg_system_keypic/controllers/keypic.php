<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once(JPATH_SITE.'/plugins/captcha/keypic/class/keypic.php');

class UsersControllerKeypic extends JControllerForm
{
	public function rspam()
	{
		$id = $this->input->get('cid', array(), 'array');
		$id = $id[0];
		
		if (empty($id))
		{
			JError::raiseWarning(500, JText::_('COM_USERS_USERS_NO_ITEM_SELECTED'));
			
			//Redirect to users list
			$this->setRedirect('index.php?option=com_users&view=users');
			return;
		}

		$db = JFactory::getDBO();
		
		$plugin = JPluginHelper::getPlugin('captcha', 'keypic');
		$params = new JRegistry($plugin->params);

		//Set Keypic Form ID
		Keypic::setFormID( $params->get('formid','a47aa961ca2783208939687d9610c7593') );

		//Get token for each user
		$query = "SELECT body FROM #__user_notes WHERE user_id = {$id} AND subject = 'Token'";
		$db->setQuery($query);
		$token = $db->loadResult();

		//Checks based on token
		if( $token )
		{
			//Report user as spam based on token
			$mark = Keypic::reportSpam( strip_tags($token) );

			if( $mark['status'] == 'response')
			{
				$instance = JUser::getInstance( $id );

				if( $instance->delete() )
					$msg = 'Success !! This user is reported as spammer and deleted';//JText::_('COM_USERS_SPAM_SUCCESS');
				else $msg = 'Success !! This user is reported as spammer but not deleted';//JText::_('COM_USERS_SPAM_SUCCESS');
			}
			else if( $mark['status'] == 'error')
					$msg = 'This user is already reported as spammer !';//JText::_('COM_USERS_IS_SPAMMER');
		}	
		else
		{
			JError::raiseWarning(500, 'Keypic token not found for this user' );

			//Redirect to users list
			$this->setRedirect('index.php?option=com_users&view=users');
			return;
		} 			
		
		//Redirect to users list
		$this->setRedirect('index.php?option=com_users&view=users', $msg);
	}
}
