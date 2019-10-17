<?php
/**
* @version $Id: controller.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2010 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

require_once JPATH_COMPONENT_SITE.'/helpers/user.php';
require_once JPATH_COMPONENT_SITE.'/helpers/user_event.php';
require_once JPATH_COMPONENT_SITE.'/helpers/version.php';

class MyjspaceController extends JControllerLegacy
{
// Displays a view
	public function display($cachable = false, $urlparams = array())
	{
		// Load & add the menu
		require_once JPATH_COMPONENT.'/helpers/myjspace.php';

		$jinput = JFactory::getApplication()->input;
		MyJspaceHelper::addSubmenu($jinput->get('view', 'myjspace', 'STRING'));

		switch ($this->getTask())
		{
			case 'createpage' : // Clic en button 'new' page, from 'pages' view
			{
				$jinput->set('view', 'createpage'); // to redirect
			}	break;

			case 'page' : // Clic en button 'edit' page, from 'pages' view
			{
				$jinput->set('view', 'page'); // to redirect
			}
		}

		// Check main mandatory config & alert message
		BSUserEvent::check_mandatory_msg();

		// Check version
		BS_Helper_version::get_newversion('com_myjspace');

		parent::display();
	}

// Create an empty page or a page with a model
	function adm_create_page()
	{
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		$pparams = JComponentHelper::getParams('com_myjspace');
		$jinput = JFactory::getApplication()->input;

		$pagename = $jinput->get('mjs_pagename', '', 'STRING');
		$user_id = $jinput->get('mjs_userid', 0, 'INT');
		$catid = $jinput->get('mjs_categories', 0, 'INT');
		$mjs_model_page = $jinput->get('mjs_model_page', 0, 'INT');

		if ($user_id > 0)
			$user = JFactory::getUser($user_id);
		else
			$user = JFactory::getUser(); // Current user

		$user_page = New BSHelperUser();

		// Model page
		$model_page = 0;
		if ($mjs_model_page != 0) {
			$model_pagename_tab = BSUserEvent::model_pagename_list();
			if (array_key_exists($mjs_model_page, $model_pagename_tab)) {
				$catid = $model_pagename_tab[$mjs_model_page]['catid'];
				$model_page = $model_pagename_tab[$mjs_model_page]['pagename'];				
			}
		}

		// Page name identification
		if ($pagename == '') {
			$pagename = $user_page->getPagenameFree(basename($pparams->get('auto_pagename_rule', '#username')), $user, $catid);
		} else {
			$pagename_check = $user_page->getPagenameFree($pagename, $user, $catid);
			if (BSHelperUser::stringURLSafe($pagename) != $pagename_check)
				$pagename = $pagename_check; // New page name already exists, so we use the automatic proposal
		}

		if (($user) && $pagename != '') {
			$list_page_tab = $user_page->getListPageId($user->id);
			if (count($list_page_tab) >= (int)$pparams->get('nb_max_page', 1)) { // Is it ok for the use to have a new page ?
				$this->setRedirect(JRoute::_('index.php?option=com_myjspace&view=pages', false), JText::_('COM_MYJSPACE_MAXREACHED'), 'error');			
			} else {
				$id = BSUserEvent::adm_save_page_conf(0, $user->id, $pagename, $pparams->get('user_mode_view_default', 1), '', $pparams->get('user_mode_edit_default', 0), '', '', '', '', $model_page, $catid, null, '', null, null, 'admin');

				if ($id > 0)
					$this->setRedirect(JRoute::_('index.php?option=com_myjspace&view=page&task=edit&id='.$id, false));

				return;
			}
		} else // User do no exist
			$this->setRedirect(JRoute::_('index.php?option=com_myjspace&view=pages', false), JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
	}

// Remove the personal page record from the database and folder & files from disk
	function remove()
	{
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		$jinput = JFactory::getApplication()->input;

		$pageid_tab = $jinput->get('cid', array(0), 'ARRAY');

		BSUserEvent::adm_page_remove($pageid_tab, JRoute::_('index.php?option=com_myjspace&view=pages', false), 'admin');
	}

// Save content & details
	function adm_save_page_all()
	{
		$this->adm_save_page_content(null, false); // Save content
		$this->adm_save_page(); // Save details
	}

// Save (update) page details 'only'
	function adm_save_page()
	{
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		$pparams = JComponentHelper::getParams('com_myjspace');
		$jinput = JFactory::getApplication()->input;

		$id = $jinput->get('mjs_id', 0, 'INT');
		$pagename = $jinput->get('mjs_pagename', '', 'STRING');
		$blockview = $jinput->get('mjs_mode_view', $pparams->get('user_mode_view_default', 1), 'STRING');
		$blockedit = $jinput->get('mjs_mode_edit', 0, 'STRING');
		$resethits = $jinput->get('resethits', 'no', 'STRING');
		$publish_up = $jinput->get('jform_publish_up', '', 'STRING');
		$publish_down = $jinput->get('jform_publish_down', '', 'STRING');
		$metakey = $jinput->get('mjs_metakey', '', 'STRING');
		$mjs_template = $jinput->get('mjs_template', '', 'STRING');
		$mjs_categories = $jinput->get('mjs_categories', 0, 'INT');
		$user_id = $jinput->get('mjs_userid', 0, 'INT');
		$mjs_userread = $jinput->get('mjs_userread', '', 'STRING');

		if ($pparams->get('share_page', 0) != 0)
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

		$url = 'index.php?option=com_myjspace&view=page&id='.$id;

		if ($resethits != 'yes') {
			BSUserEvent::adm_save_page_conf($id, $user_id, $pagename, $blockview, $mjs_userread, $blockedit, $publish_up, $publish_down, $metakey, $mjs_template, 0, $mjs_categories, $mjsp_share, $mjs_language, $tags, JRoute::_($url, false), 'admin2');
		} else {
			BSUserEvent::adm_reset_page_access($id, JRoute::_($url, false), 'admin');
		}
	}

// Upload file for user page
	function upload_file()
	{
		(JSession::checkToken('post') || JSession::checkToken('get')) or jexit(JText::_('JINVALID_TOKEN'));

		$jinput = JFactory::getApplication()->input;

		$id = $jinput->get('upload_id', 0, 'INT');

		$layout = $jinput->get('layout', '', 'STRING');
		if ($layout != '')
			$layout = '&tmpl=component&layout='.$layout;

		$type = $jinput->get('type', 'undefined', 'STRING');
		if ($type != '')
			$type = '&type='.$type;

		if (!isset($_FILES['upload_file']))
			return;

		$FileObject = $_FILES['upload_file'];
		$uploaded='&uploaded='.$FileObject['name'];

		$url = JRoute::_('index.php?option=com_myjspace&view=page&id='.$id.$layout.$type.$uploaded, false);

		BSUserEvent::adm_upload_file($id, $FileObject, $url, 'admin');
	}

// Delete file from user page
	function delete_file()
	{
		(JSession::checkToken('post') || JSession::checkToken('get')) or jexit(JText::_('JINVALID_TOKEN'));

		$jinput = JFactory::getApplication()->input;

		$id = $jinput->get('delete_id', 0, 'INT');

		$layout = $jinput->get('layout', '', 'STRING');
		if ($layout != '')
			$layout = '&tmpl=component&layout='.$layout;

		$type = $jinput->get('type', 'undefined', 'STRING'); // On recupere pour renvoyer pour les futurs éventues upload
		if ($type != '')
			$type = '&type='.$type;

		$filmjs_editable = $jinput->get('delete_file', '', 'STRING');
		BSUserEvent::adm_delete_file($id, $filmjs_editable, JRoute::_('index.php?option=com_myjspace&view=page&id='.$id.$layout.$type, false), 'admin');
	}

// Save(update) page content 'only'
	function adm_save_page_content($url = '', $event = true)
	{
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		require_once JPATH_COMPONENT_SITE.'/helpers/util.php';

		$pparams = JComponentHelper::getParams('com_myjspace');
		$jinput = JFactory::getApplication()->input;

		$id = $jinput->get('mjs_id', 0, 'INT');

		$content = $jinput->get('mjs_editable', '@@vide@@', 'RAW');
		if ($content == '@@vide@@') { // To allow really empty page
			$this->setRedirect(JRoute::_('index.php'), JText::_('COM_MYJSPACE_ERRUPDATINGPAGE'), 'error');
			return;
		}

		if ($pparams->get('editor_bbcode', 1) == 1) {
			$error = 0;
			$content = BS_Util::bs_bbcode($content, (int)$pparams->get('editor_bbcode_width', 800), (int)$pparams->get('editor_bbcode_height', 0), $error);
			if ($error != 0)
				JFactory::getApplication()->enqueueMessage(JText::_('COM_MYJSPACE_ERROBBCODE'), 'notice');
		}

		if ($url !== null) {
			if ($url == '')
				$url = JRoute::_('index.php?option=com_myjspace&view=page&id='.$id, false);
			$url = JRoute::_($url, false);
		}

		BSUserEvent::adm_save_page_content($id, $content, (int)$pparams->get('name_page_max_size', 92160), $url, 'admin', $event);
	}

// Rename the personal pages alias and folder or sub-folders using the naming convention (for pages created with BS MyJspace < 3.0.0)
	function adm_ren_alias_folders()
	{
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

		set_time_limit(0);

		$total = BSUserEvent::adm_recompute_foldername();
		$url = JRoute::_('index.php?option=com_myjspace&view=help', false);

		BSUserEvent::user_actions_log_add_task('COM_MYJSPACE_TRANSACTION_UPDATE_TOOL', $url, 'adm_ren_alias_folders');

		$this->setRedirect($url, JText::sprintf('COM_MYJSPACE_PAGENAME_KO_FIXED', $total), 'message');
	}

// Rename/create/move the personal Root pages folder or sub-folders
	function adm_ren_rootfolder()
	{
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		$pparams = JComponentHelper::getParams('com_myjspace');
		$jinput = JFactory::getApplication()->input;

		$link_folder = $pparams->get('link_folder', 1);
		$keep = $jinput->get('keep', 0, 'INT');
		$foldername_new = trim(trim($jinput->get('mjs_foldername', '', 'STRING')), '/'); // Avoid ' ' and '/' at the beginning and end
		$foldername_old = BSHelperUser::getRootFoldername();

		$url = JRoute::_('index.php?option=com_myjspace&view=url', false); // Url de retour

		set_time_limit(0);

		// Pre-check to avoid some issues
		if ($pparams->get('precheck-rootfolder', 1)) {
			// Check if all pages alias naming are ok (to be fixed before if not ok)
			if (BSHelperUser::countOldAlias()) {
				$this->setRedirect($url, str_replace('TOKEN', JSession::getFormToken(), JText::_('COM_MYJSPACE_PAGENAME_KO')), 'error');
				return;
			}

			// Check forbidden naming directory (only $prefix = 'media' allowed for the Joomla! directories)
			$gauche = substr($foldername_new, 0, strpos($foldername_new, '/'));
			$forbidden = array('administrator', 'bin', 'cache', 'cli', 	'components', 'images', 'includes', 'language', 'layouts', 'libraries', 'modules', 'plugins', 'templates', 'tmp', 'myjspace', 'com_myjspace');
			if (in_array($gauche, $forbidden) || in_array(basename($foldername_new), $forbidden)) { // Forbidden naming
				$this->setRedirect($url, JText::_('COM_MYJSPACE_FOLDERNAMEUPDATED_3'), 'error');
				return;
			}

			// Check if all pages folders exists and OK (if check version OK => all directories exists and correct ...)
			$nb_index_ko = BSHelperUser::checkversionIndexPage(false); // Check for all pages
			if ($nb_index_ko) {
				$this->setRedirect($url, str_replace('TOKEN', JSession::getFormToken(), JText::sprintf('COM_MYJSPACE_ADMIN_CHECK_FOLDER_2', $nb_index_ko)), 'error');
				return;
			}

			// Check if basename($foldername_new) is an existing page, to avoid to merge the two !
			if ($foldername_new != $foldername_old && BSHelperUser::ifExistPageName(basename($foldername_new))) {
				$this->setRedirect($url, JText::_('COM_MYJSPACE_FOLDERNAMEUPDATED_3'), 'error');
				return;
			}

			// Naming checking
			$prefix = 'media';
			if ($foldername_old == $prefix)
				$keep = 1;

			// Check in case of JPATH_SITE not writable (like some preinstall Joomla! hosting) force to use JPATH_SITE./.'media'.'/'.bsename($foldername_new) instead
			if (!BSHelperUser::IsDirW(dirname($foldername_new)) && BSHelperUser::IsDirW($prefix)) { // Alternative ROOT folder
				$foldername_new = $prefix.'/'.basename($foldername_new); // As default $prefix = 'media'
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_MYJSPACE_FOLDERNAMEUPDATED_2', $gauche), 'warning');
			}
		}
		// End pre-checks

		if (BSHelperUser::checkFoldername($foldername_new)) { // Test if characters allowed
			if (BSHelperUser::updateRootFoldername($foldername_new, $link_folder, $keep)) { // Create folder if not exists
				if ($foldername_old != $foldername_new && $link_folder == 1) { // Rename folders inside all pages content !
					BSUserEvent::adm_recompute_foldername();
				}

				BSUserEvent::user_actions_log_add_task('COM_MYJSPACE_TRANSACTION_UPDATE_URL', $url, $foldername_new); // Event For user Action Logs

				$this->setRedirect($url, JText::_('COM_MYJSPACE_FOLDERNAMEUPDATED'), 'message');
			} else {
				$this->setRedirect($url, JText::_('COM_MYJSPACE_ERRUPDATINGFOLDERNAMEFILE'), 'error');
			}
		} else {
			$this->setRedirect($url, JText::_('COM_MYJSPACE_NOTVALIDFOLDERNAME'), 'error');
		}
	}

// Tools: Re-Create folders and link pages for all personal pages
	function adm_create_folder()
	{
		(JSession::checkToken('post') || JSession::checkToken('get')) or jexit(JText::_('JINVALID_TOKEN'));

		set_time_limit(0);

		list($retour, $msg) = BSUserEvent::adm_create_folder();

		$url = JRoute::_('index.php?option=com_myjspace&view=tools', false);
		if ($retour > 0) {
			$this->setRedirect($url, JText::_($msg), 'error');
		} else {
			BSUserEvent::user_actions_log_add_task('COM_MYJSPACE_TRANSACTION_UPDATE_TOOL', $url, 'adm_create_folder');
			$this->setRedirect($url, JText::_($msg), 'message');
		}
	}

// Tools: delete folders and link pages for all personal pages
	function adm_delete_folder()
	{
		(JSession::checkToken('post') || JSession::checkToken('get')) or jexit(JText::_('JINVALID_TOKEN'));

		set_time_limit(0);

		list($retour, $msg) = BSUserEvent::adm_delete_folder();

		$url = JRoute::_('index.php?option=com_myjspace&view=tools', false);
		if ($retour > 0) {
			$this->setRedirect($url, JText::_($msg), 'warning');
		} else {
			BSUserEvent::user_actions_log_add_task('COM_MYJSPACE_TRANSACTION_UPDATE_TOOL', $url, 'adm_delete_folder');
			$this->setRedirect($url, JText::_($msg), 'message');
		}
	}

// Tools: delete all empty pages (= content + folder empty)
	function adm_delete_empty_pages()
	{
		(JSession::checkToken('post') || JSession::checkToken('get')) or jexit(JText::_('JINVALID_TOKEN'));

		set_time_limit(0);

		list($retour, $msg) = BSUserEvent::adm_delete_empty_pages();

		$url = JRoute::_('index.php?option=com_myjspace&view=tools', false);
		if ($retour > 0) {
			$this->setRedirect($url, JText::_($msg), 'warning');
		} else {
			BSUserEvent::user_actions_log_add_task('COM_MYJSPACE_TRANSACTION_UPDATE_TOOL', $url, 'adm_delete_empty_pages');
			$this->setRedirect($url, JText::_($msg), 'message');
		}
	}

// Tools: check pages folder index version
	function adm_check_folder()
	{
		(JSession::checkToken('post') || JSession::checkToken('get')) or jexit(JText::_('JINVALID_TOKEN'));

		set_time_limit(0);

		$nb_index_ko = BSHelperUser::checkversionIndexPage(false); // Check for all pages

		$url = JRoute::_('index.php?option=com_myjspace&view=tools', false);
		if ($nb_index_ko === false)
			$this->setRedirect($url, JText::_('COM_MYJSPACE_ADMIN_CREATE_FOLDER_1'), 'message');
		else if ($nb_index_ko == 0)
			$this->setRedirect($url, JText::_('COM_MYJSPACE_ADMIN_CHECK_FOLDER_1'), 'message');
		else
			$this->setRedirect($url, JText::sprintf('COM_MYJSPACE_ADMIN_CHECK_FOLDER_2', $nb_index_ko), 'error');
	}

// Other tools
	function other_tools()
	{
		(JSession::checkToken('post') || JSession::checkToken('get')) or jexit(JText::_('JINVALID_TOKEN'));

		$jinput = JFactory::getApplication()->input;

		$id = $jinput->get('id', '', 'STRING');

		// BS MyJspace 'myjspace' Plugins
		JPluginHelper::importPlugin('myjspace'); // Import & call the constructor

		$url = JRoute::_('index.php?option=com_myjspace&view=tools', false);

		// Tools plugin
		JFactory::getApplication()->triggerEvent('onMyJspaceTools', array($id, 1)); // Function call 

		BSUserEvent::user_actions_log_add_task('COM_MYJSPACE_TRANSACTION_UPDATE_TOOL', $url, $id);

		$this->setRedirect($url);
	}

// Cancel button during page creation or editing 
	function page_cancel()
	{
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		$this->setRedirect(JRoute::_('index.php?option=com_myjspace&view=pages', false));
	}
	
// Redirect to the myjspace plugin list
	function myjspace_plugin()
	{
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		$this->setRedirect(JRoute::_('index.php?option=com_plugins&view=plugins&filter[folder]=myjspace', false));
	}
}
