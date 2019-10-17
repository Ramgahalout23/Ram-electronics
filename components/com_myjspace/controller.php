<?php
/**
* @version $Id:	controller.php $
* @version		3.0.0 26/08/2019
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2010 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

require_once JPATH_COMPONENT_SITE.'/helpers/user_event.php';
require_once JPATH_COMPONENT_SITE.'/helpers/user.php';
require_once JPATH_COMPONENT_SITE.'/helpers/util.php';

class MyjspaceController extends JControllerLegacy
{
	function display($cachable = false, $urlparams = false)
	{
		$user = JFactory::getUser();
		$pparams = JComponentHelper::getParams('com_myjspace');
		$app = JFactory::getApplication();		
		$params = $app->getParams();
		$jinput = JFactory::getApplication()->input;
		$acces_ok = true;

		if ($jinput->get('layout', '', 'STRING') == 'upload')
			$jinput->set('view', 'edit'); // To redirect

		$get_view = $jinput->get('view', '', 'STRING');

		// J! ACL
		if ($get_view != '' && !JFactory::getUser()->authorise('user.'.$get_view, 'com_myjspace'))
			$acces_ok = false;

		// If not connected => redirection to login page for 'admin' & 'delete', 'edit', 'see (if no page id and pagename)'
		$id = $jinput->get('id', $params->get('id', 0), 'INT');
		$cid = $jinput->get('cid', 0, 'INT');
		$pagename = $jinput->get('pagename', $params->get('pagename', ''), 'STRING');

		if (!isset($user->username) && ($get_view == 'config' || $get_view == 'delete' || $get_view == 'edit' || ($get_view == 'see' && $id == 0 && $cid == 0 && $pagename == ''))) {
			$acces_ok = false; // Login redirection
		}

		if ($acces_ok == false && !isset($user->username)) { // Redirect to login page
			$uri = JURI::getInstance();
			$return = $uri->toString();

			if ($pparams->get('url_login_redirect', ''))
				$url = $pparams->get('url_login_redirect', '');
			else
				$url = 'index.php?option=com_users&view=login';
			$url .= '&return='.base64_encode($return); // To return to the call page
			$url = JRoute::_($url, false);

			$this->setRedirect($url);
			return;
		} else if ($acces_ok == false && isset($user->username)) { // Not allowed
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
		}

		parent::display();
	}

// Save page content (view edit)
	function save()
	{
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		if (!JFactory::getUser()->authorise('user.edit', 'com_myjspace')) {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');			
			return;
		}

		$pparams = JComponentHelper::getParams('com_myjspace');
		$jinput = JFactory::getApplication()->input;

		$id = $jinput->get('id', 0, 'INT');
		$pagename = $jinput->get('pagename', '', 'STRING');
		$return = $jinput->get('return', '', 'STRING');

		$user = JFactory::getUser();
		$user_page = New BSHelperUser();
		if ($pparams->get('share_page', 0) != 0)
			$access = $user->getAuthorisedViewLevels();
		else
			$access = null;
		$list_page_tab = $user_page->getListPageId($user->id, $id, 0, $access);
		if (count($list_page_tab) != 1 || $pagename == '') { // For 'my' page
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
			return;
		}

		$content = $jinput->get('mjs_editable', '@@vide@@', 'RAW');
		if ($content == '@@vide@@') { // To allow really empty page
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_ERRUPDATINGPAGE'), 'error');
			return;
		}

		if ($pparams->get('editor_bbcode', 1) == 1) {
			$error = 0;
			$content = BS_Util::bs_bbcode($content, (int)$pparams->get('editor_bbcode_width', 800), (int)$pparams->get('editor_bbcode_height', 0), $error);
			if ($error != 0)
				JFactory::getApplication()->enqueueMessage(JText::_('COM_MYJSPACE_ERROBBCODE'), 'notice');
		}

		// Url
		$url = JRoute::_('index.php?option=com_myjspace&view=see&pagename='.$pagename, false);

		if ($return) {
			$return = base64_decode($return);
			if (JURI::isInternal($return))
				$url = $return;
		}

		BSUserEvent::adm_save_page_content($id, $content, (int)$pparams->get('name_page_max_size', 92160), $url, 'site');
	}

// Save page config (& create page if not exist)
	function save_config()
	{
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		if (!JFactory::getUser()->authorise('user.config', 'com_myjspace')) {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
			return;
		}

		$pparams = JComponentHelper::getParams('com_myjspace');
		$jinput = JFactory::getApplication()->input;

		$id = $jinput->get('id', 0, 'INT');
		$pagename = $jinput->get('mjs_pagename', '', 'STRING');
		$resethits = $jinput->get('resethits', 'no', 'STRING');
		$publish_up = $jinput->get('jform_publish_up', '', 'STRING');
		$publish_down = $jinput->get('jform_publish_down', '', 'STRING');
		$metakey = $jinput->get('mjs_metakey', '', 'STRING');
		$mjs_template = $jinput->get('mjs_template', '', 'STRING');
		$mjs_model_page = $jinput->get('mjs_model_page', 0, 'INT');
		$mjs_categories = $jinput->get('mjs_categories', 0, 'INT');
		$mjs_userread = $jinput->get('mjs_userread', '', 'STRING');

		$user = JFactory::getUser(); 
		$user_page = New BSHelperUser();
		$list_page_tab = $user_page->getListPageId($user->id, $id); // Do not filter on catid, because it can be an update
		if (count($list_page_tab) != 1) // For 'my' page
			$id = 0;

		if ($pparams->get('share_page', 0) == 2)
			$mjsp_share = $jinput->get('mjsp_share', 0, 'INT');
		else
			$mjsp_share = null;

		$mjs_language = $jinput->get('mjs_language', '', 'STRING');

		if ($pparams->get('language_filter', 0) == 2) { // Associations
			$associations = $jinput->get('associations', array(0), 'ARRAY');
			$associations[$mjs_language] = $id;

			if (BSHelperUser::setAssociations($associations) === -1)
				JFactory::getApplication()->enqueueMessage(JText::_('COM_MYJSPACE_ASSOCIATION_ALL_ERROR'), 'notice');
		}

		// Get J!3.1+ tags
		$metadata = $jinput->get('metadata', array(0), 'ARRAY');
		$tags = (array_key_exists('tags', $metadata)) ? $metadata['tags'] : array();

		// Model page
		$model_page = 0;
		if ($mjs_model_page != 0) {
			$model_pagename_tab = BSUserEvent::model_pagename_list();	
			if (array_key_exists($mjs_model_page, $model_pagename_tab)) {
				if ($model_pagename_tab[$mjs_model_page]['catid'] != 0)
					$mjs_categories = $model_pagename_tab[$mjs_model_page]['catid'];
				$model_page = $model_pagename_tab[$mjs_model_page]['pagename'];				
			}
		}

		if ($resethits == 'yes' && $id != 0) {
			BSUserEvent::adm_reset_page_access($id, JRoute::_('index.php?option=com_myjspace&view=config&id='.$id, false), 'site');		
		} else if ($resethits == 'yes' && $id == 0) {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
		} else {
			if ($pparams->get('user_mode_view', 1) == 0)
				$blockview = $pparams->get('user_mode_view_default', 1); // Do do take param in this case (safety)
			else
				$blockview = $jinput->get('mjs_mode_view', $pparams->get('user_mode_view_default', 1), 'STRING');

			BSUserEvent::adm_save_page_conf($id, $user->id, $pagename, $blockview, $mjs_userread, 0, $publish_up, $publish_down, $metakey, $mjs_template, $model_page, $mjs_categories, $mjsp_share, $mjs_language, $tags, JRoute::_('index.php?option=com_myjspace&view=config&id='.$id, false), 'site');
		}
	}

// Delete page
	function del_page()
	{
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		if (!JFactory::getUser()->authorise('user.delete', 'com_myjspace')) {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
			return;
		}

		$pparams = JComponentHelper::getParams('com_myjspace');
		$jinput = JFactory::getApplication()->input;

		$user = JFactory::getUser(); 
		$user_page = New BSHelperUser();
		$list_page_tab = $user_page->getListPageId($user->id, $jinput->get('id', 0, 'INT')); 

		if (count($list_page_tab) == 1) { // For 'my' page
			$pageid = $list_page_tab[0]['id'];
		} else {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
			return;
		}

		$auto_create_page = $pparams->get('auto_create_page', 3);

		if ($auto_create_page != 3 && $auto_create_page != 1)
			BSUserEvent::adm_page_remove($pageid, JRoute::_('index.php?option=com_myjspace&view=config', false));
		else
			BSUserEvent::adm_page_remove($pageid, JRoute::_('index.php?option=com_myjspace&view=pages', false));
	}

// Upload file
	function upload_file()
	{
		(JSession::checkToken('post') || JSession::checkToken('get')) or jexit(JText::_('JINVALID_TOKEN'));

		$jinput = JFactory::getApplication()->input;

		$view = $jinput->get('view', 0, 'STRING');
		if ($view == 'see' || $view == 'pages') // If original view = 'see' or 'page' but redirected to list, and then click on button ?
			$view = 'config';

		if (!JFactory::getUser()->authorise('user.config', 'com_myjspace') && !JFactory::getUser()->authorise('user.edit', 'com_myjspace')) {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
			return;
		}

		$layout = $jinput->get('layout', '', 'STRING');
		if ($layout != '')
			$layout = '&tmpl=component&layout='.$layout;

		$type = $jinput->get('type', 'undefined', 'STRING'); // Media, image, file, undefined
		if ($type != '')
			$type = '&type='.$type;

		$user = JFactory::getUser();
		$user_page = New BSHelperUser();
		$list_page_tab = $user_page->getListPageId($user->id, $jinput->get('upload_id', 0, 'INT')); 

		if (count($list_page_tab) == 1) { // For 'my' page
			$pageid = $list_page_tab[0]['id'];
		} else {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
			return;
		}

		if (!isset($_FILES['upload_file']))
			return;

		$FileObject = $_FILES['upload_file'];
		$uploaded='&uploaded='.$FileObject['name'];

		$url = JRoute::_('index.php?option=com_myjspace&view='.$view.'&id='.$pageid.$layout.$type.$uploaded, false);

		BSUserEvent::adm_upload_file($pageid, $FileObject, $url, 'site');	
	}

// Delete file from user page
	function delete_file()
	{
		(JSession::checkToken('post') || JSession::checkToken('get')) or jexit(JText::_('JINVALID_TOKEN'));

		if (!JFactory::getUser()->authorise('user.config', 'com_myjspace')) {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
			return;
		}

		$jinput = JFactory::getApplication()->input;

		$view = $jinput->get('view', 0, 'STRING');
		if ($view == 'see' || $view == 'pages')
			$view = 'config';

		$layout = $jinput->get('layout', '', 'STRING');
		if ($layout != '')
			$layout = '&tmpl=component&layout='.$layout;

		$type = $jinput->get('type', 'undefined', 'STRING');
		if ($type != '')
			$type = '&type='.$type;

		$user = JFactory::getUser(); 
		$user_page = New BSHelperUser();

		$list_page_tab = $user_page->getListPageId($user->id, $jinput->get('delete_id', 0, 'INT')); 

		if (count($list_page_tab) == 1) { // For 'my' page
			$pageid = $list_page_tab[0]['id'];
		} else {
			$this->setRedirect('index.php', JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
			return;
		}

		$filmjs_editable = $jinput->get('delete_file', '', 'STRING');		

		BSUserEvent::adm_delete_file($pageid, $filmjs_editable, JRoute::_('index.php?option=com_myjspace&view='.$view.'&id='.$pageid.$layout.$type, false), 'site');
	}

// Frontend tools plugin usage
	function front_tools()
	{
		(JSession::checkToken('post') || JSession::checkToken('get')) or jexit(JText::_('JINVALID_TOKEN'));

		$all_param = JFactory::getApplication()->input->getArray(); 

		// BS MyJspace 'myjspace' Plugins
		JPluginHelper::importPlugin('myjspace'); // Import & call the constructor

		// Frontend Tools plugin
		JFactory::getApplication()->triggerEvent('onMyJspaceFrontTools', array($all_param, 1)); // Function call
	}
}
