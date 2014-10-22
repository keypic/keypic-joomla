<?php
/**
* @package Canva Image Plugin
* @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link     http://www.techjoomla.com
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class plguserkeypicInstallerScript
{
	public function postflight($type, $parent)
	{
		//Enable plugin when installed
		if ($type == 'install')
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			 
			// Fields to update.
			$fields = array(
				$db->quoteName('enabled') . ' = ' . 1
			);
			 
			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('keypic'),
				$db->quoteName('type') . ' = ' . $db->quote('plugin'),
				$db->quoteName('folder') . ' = ' . $db->quote('user'),

			);
			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$db->query();
		}

		return true;
	}
}
