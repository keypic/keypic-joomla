<?php
/**
* @package Keypic Plugin
* @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link     http://www.techjoomla.com
*/

defined('_JEXEC') or die;
jimport( 'joomla.html.html' );

class PlgUserKeypic extends JPlugin
{
	protected $db;
	protected $app;
	
	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
	
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		if( $this->app->isAdmin() ) return;

		$input = JFactory::getApplication()->input;

		$note = new stdClass;

		$note->id				=	'';
		$note->user_id			=	$user['id'];
		$note->catid			=	$this->params->get('category');
		$note->subject			=	JText::_('PLG_USER_KEYPIC_SUBJECT_TOKEN');
		$note->body				=	'<p>'.$user['captcha'].'</p>';
		$note->state			=	1;
		$note->checked_out		=	'';
		$note->checked_out_time	=	'';
		$note->created_user_id	=	$this->params->get('userid');
		$note->created_time		=	date('Y-m-d H:i:s');
		$note->modified_user_id	=	'';
		$note->modified_time	=	'';
		$note->review_time		=	'';
		$note->publish_up		=	'';
		$note->publish_down		=	'';

		//Add note with token
		$this->db->insertObject('#__user_notes', $note, 'id');
		
		$spam = new stdClass;

		$spam->id				=	'';
		$spam->user_id			=	$user['id'];
		$spam->catid			=	$this->params->get('category');
		$spam->subject			=	JText::_('PLG_USER_KEYPIC_SUBJECT_SPAM_PERCENT');
		$spam->body				=	str_replace('sign', '%', JText::sprintf('PLG_USER_KEYPIC_BODY_SPAM_PERCENT', $input->get('percent')));
		$spam->state			=	1;
		$spam->checked_out		=	'';
		$spam->checked_out_time	=	'';
		$spam->created_user_id	=	$this->params->get('userid');
		$spam->created_time		=	date('Y-m-d H:i:s');
		$spam->modified_user_id	=	'';
		$spam->modified_time	=	'';
		$spam->review_time		=	'';
		$spam->publish_up		=	'';
		$spam->publish_down		=	'';

		//Add spam percentage
		$this->db->insertObject('#__user_notes', $spam, 'id');
	}
	
	//Delete notes when user is registered from front end and deleted from admin
	public function onUserAfterDelete($user, $success, $msg)
	{
		if (!$success) return false;

		$query = $this->db->getQuery(true)
			->delete($this->db->quoteName('#__user_notes'))
			->where($this->db->quoteName('user_id') . ' = ' . (int) $user['id']);

		$this->db->setQuery($query)->execute();

		return true;
	}
}
