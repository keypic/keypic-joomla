<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.utilities.date');

$helper_file = dirname(__FILE__) . DS . "helper" . DS . "keypic.php";
require_once($helper_file);

/**
 * An example custom profile plugin.
 *
 * @package		Joomla.Plugin
 * @subpackage	User.profile
 * @version		1.6
 */
class plgSystemKeypic extends JPlugin
{
	private static $keypic_host;
	private static $keypic_port;
	private static $enable_debug;
	private static $form_id;
	private static $user_agent;
	private static $token;
	
	private $kepic_registration_enable;
	private $kepic_registration_button_type;
	private $kepic_registration_display_as;
	
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
		JFormHelper::addFieldPath(dirname(__FILE__) . '/fields');
		
		Keypic::setFormID($this->params->get('plg_kepic_form_id'));
		
		$this->kepic_registration_enable = $this->params->get('plg_kepic_registration_enable');
		$this->kepic_registration_button_type = $this->params->get('plg_kepic_registration_button_type');
		$this->kepic_registration_display_as = $this->params->get('plg_kepic_registration_display_as');
		
		$this->kepic_login_enable = $this->params->get('plg_kepic_login_enable');
		$this->kepic_login_button_type = $this->params->get('plg_kepic_login_button_type');
		$this->kepic_login_display_as = $this->params->get('plg_kepic_login_display_as');
	}

	/**
	 * @param	string	$context	The context for the data
	 * @param	int		$data		The user id
	 * @param	object
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	function onContentPrepareData($context, $data)
	{
		return true;
	}

	/**
	 * @param	JForm	$form	The form to be altered.
	 * @param	array	$data	The associated data for the form.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	function onContentPrepareForm($form, $data)
	{
		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}
		
		$name = $form->getName();
		if (!in_array($name, array('com_users.registration')))
		{
			return true;
		}
		
		if($name == 'com_users.registration'){
			if(!$this->kepic_registration_enable) return true;
			
			JRequest::setVar("kepic_button_type", $this->kepic_registration_button_type);
			JRequest::setVar("kepic_display_as", $this->kepic_registration_display_as);
		}

		// Add the registration fields to the form.
		JForm::addFormPath(dirname(__FILE__) . '/keypic');
		$form->loadFile('keypic', false);
		
		return true;
	}

	function onAfterStoreUser($data, $isNew, $result, $error)
	{
		$userId	= JArrayHelper::getValue($data, 'id', 0, 'int');

		if($this->kepic_registration_enable){
			//save token into the database
			$db =& JFactory::getDBO();
			$ndata =  new stdClass();
			$ndata->id = $userId;
			$ndata->spam_per = Keypic::isSpam(JRequest::getVar("keypic_token"), $data["email"], $data["username"]);
			$ndata->spam_token =  JRequest::getVar("keypic_token");
			
			if($ndata->spam_per == "error") return false;
			
			$db->updateObject( '#__users', $ndata, 'id', false);
		}
		
		return true;
	}
	
	static function getKeypicDisplay(){		
		$kepic_button_type = JRequest::getVar("kepic_button_type");
		$kepic_display_as =	JRequest::getVar("kepic_display_as");
		$token = Keypic::getToken(null);
		
		$input_hidden = "<input type=\"hidden\" name=\"keypic_token\" value=\"$token\"/>";
		
		if($kepic_display_as == "image"){
			return Keypic::getIt("getClick", $kepic_button_type) . $input_hidden;
		}else{
			return Keypic::getIt("getiFrame", $kepic_button_type) . $input_hidden;
		}
	}
	
	function onRenderKeypicLoginFormValidator(){
		if(!$this->kepic_login_enable) return true;
		
		JRequest::setVar("kepic_button_type", $this->kepic_login_button_type);
		JRequest::setVar("kepic_display_as", $this->kepic_login_display_as);
		echo plgSystemKeypic::getKeypicDisplay();
		
		return true;
	}
	
	function reportSpam($token){		
		Keypic::reportSpam($token);
	}
}
