<?php
define( '_JEXEC', 1 );
define( '_VALID_MOS', 1 );
//JPATH_BASE should point to Joomla!'s root directory
define( 'JPATH_BASE', realpath(dirname(__FILE__) .'/../../../' ) );
define( 'DS', DIRECTORY_SEPARATOR );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
$mainframe =& JFactory::getApplication('site');
$mainframe->initialise();
$user =& JFactory::getUser();
$session =& JFactory::getSession();

$dispatcher =& JDispatcher::getInstance();
$dispatcher	= JDispatcher::getInstance();
JPluginHelper::importPlugin('system');
$dispatcher->trigger('reportSpam', array(JRequest::getVar("token")));
$userid = JRequest::getInt('id'); // getting user id from url
$instance = JUser::getInstance($userid);
if($userid){
	if($instance->delete()){
		//return true;
	}
}

JFactory::getApplication()->enqueueMessage('Keypic token reported as SPAM and user deleted.');

header("Location:" . $_SERVER["HTTP_REFERER"]);