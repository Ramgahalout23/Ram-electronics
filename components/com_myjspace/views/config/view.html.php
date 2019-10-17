<?php
/**
* @version $Id: view.html.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2010 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

class MyjspaceViewConfig extends JViewLegacy
{
	function display($tpl = null)
	{
		require_once JPATH_COMPONENT_SITE.'/helpers/user.php';
		require_once JPATH_COMPONENT_SITE.'/helpers/util.php';
		require_once JPATH_COMPONENT_SITE.'/helpers/util_acl.php';
		require_once JPATH_COMPONENT_SITE.'/helpers/user_event.php';

		// Config
		$pparams = JComponentHelper::getParams('com_myjspace');
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$params = $app->getParams();
		$jinput = JFactory::getApplication()->input;
		$db	= JFactory::getDBO();
		$nullDate = $db->getNullDate();

		$this->name_page_size_max = $pparams->get('name_page_size_max', 20);

		// Personal page info
		$this->user = JFactory::getUser();
		$this->user_page = New BSHelperUser();

		$id = $jinput->get('id', 0, 'INT');
		$this->catid = $jinput->get('catid', $params->get('catid', 0), 'INT');

		$cid = $jinput->get('cid', 0, 'INT'); // Page selected from pages lists
		if (is_array($cid) && isset($cid[0]))
			$cid = $cid[0];

		if ($id == 0)
			$id = $cid;

		if ($id < 0 && $cid > 0) { // New page to create with $cid page id as model
			$model_copy = $cid;
			$this->user_page->id = $model_copy;
			$this->user_page->loadPageInfoOnly();
			$this->catid = $this->user_page->catid;
			unset($this->user_page);
			$this->user_page = New BSHelperUser();
		} else {
			$model_copy = 0;
		}

		if ($id > 0 && $this->catid != 0)
			$this->catid = 0;

		// Parameters
		$this->default_catid = $pparams->get('default_catid', 0);
		$this->file_max_size = (int)$pparams->get('file_max_size', 5242880);
		$auto_create_page = $pparams->get('auto_create_page', 3);
		$this->nb_max_page = (int)$pparams->get('nb_max_page', 1);
		$language_filter = $pparams->get('language_filter', 0);
		$this->select_category = $pparams->get('select_category', 1);
		if ($this->catid > 0) // To prevent forced catid page (page name ...) to be changed or auto-create a new page :-( ...
			$this->select_category = 0;

		// Catid url
		if ($this->catid != 0)
			$this->catid_url = '&catid='.$this->catid;
		else
			$this->catid_url = '';

		// Language
		if ($language_filter > 0)
			$this->language_list = BSHelperUser::get_language_list();
		else
			$this->language_list = array();

		// Retrieve user page ID if pagename defined
		$pagename = $jinput->get('pagename', '', 'STRING');
		if ($pagename != '') {
			$this->user_page->pagename = $pagename;
			$this->user_page->loadPageInfoOnly(1);
			$id = $this->user_page->id;
		}

		// Page id - check
		$list_page_tab = $this->user_page->getListPageId($this->user->id, $id, $this->catid);
		$nb_page = count($list_page_tab);

		if ($id <= 0 || $nb_page != 1) {
			if ($id < 0 && $nb_page >= $this->nb_max_page) { // New page KO
				$app->enqueueMessage(JText::_('COM_MYJSPACE_MAXREACHED'), 'error');
				$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages&lview=config'.$this->catid_url, false));
				return;
			} else if ($nb_page == 0 && $id > 0 && $this->catid == 0) {
				$app->enqueueMessage(JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
				$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages&lview=config'.$this->catid_url, false));
				return;
			} else if ($id < 0 || ($nb_page == 0 && $id <= 0)) { // New page
				$id = 0;
			} else if ($nb_page == 1) { // id= 0 => Display the page
				$id = $list_page_tab[0]['id'];
			} else { // Display Pages list
				$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages&lview=config'.$this->catid_url, false));
				return;
			}
		} else if ($nb_page > 1) { // Error
			$app->enqueueMessage(JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
			$app->redirect('index.php');
			return;
		}

		// Retrieve user page info
		if ($this->user_page->pagename == '') {
			$this->user_page->id = $id;
			$this->user_page->loadPageInfoOnly();
		}

		// Last update name
		$table = JUser::getTable();
		if ($table->load($this->user_page->modified_by)) { // Test if user exists before to retrieve info
			$this->modified_by = JFactory::getUser($this->user_page->modified_by);
		} else { // User no longer exists !
			$this->modified_by = new stdClass();
			$this->modified_by->id = -1;
			$this->modified_by->username = ' ';
			$this->modified_by->name = '';
		}

		if ($this->user_page->pagename == '')
			$this->user_page->blockview = $pparams->get('user_mode_view_default', 1);

		// Links
		$this->link_folder = $pparams->get('link_folder', 1);
		$this->link_folder_print = $pparams->get('link_folder_print', 0);

		// Test if foldername exist => Admin
		$this->alert_root_page = 0;
		if (!BSHelperUser::IsDirW(BSHelperUser::getRootFoldername()) && $this->link_folder == 1) {
			$this->alert_root_page = 1;
		}

		// Create automatically page if none & if option 'auto_create_page' is activated & less or equal than 1 model
		$this->model_page_list = ($model_copy > 0) ? array() : BSUserEvent::model_pagename_list($this->catid); // Model page list
		if ($this->alert_root_page == 0 && $this->user_page->pagename == '' && ($auto_create_page == 1 || $auto_create_page == 3) && count($this->model_page_list) <= 2) {
			$blockview = $pparams->get('user_mode_view_default', 1);

			if ($model_copy > 0) {
				$model_page = $model_copy;
			} else {
				$model_page = 0;
				foreach ($this->model_page_list as $k => $v) { // take the last one
					if ($k != 0 && $this->model_page_list[$k]['catid'] > 0)
						$this->catid = $this->model_page_list[$k]['catid'];
					$model_page = $this->model_page_list[$k]['pagename'];
				}
			}

			// Define the pagename/title for a default new page name
			$pagename = $this->user_page->getPagenameFree(basename($pparams->get('auto_pagename_rule', '#username')), $this->user, $this->catid);
			$id = BSUserEvent::adm_save_page_conf(0, $this->user->id, $pagename, $blockview, '', 0, '', '', '', '', $model_page, $this->catid, null, '', null, null, 'site_create');
			// Add error msg if id = 0 ?
			$this->user_page->id = $id;
			$this->user_page->loadPageInfoOnly(); // Reload the user data
		}

		// Catid url (if catid change)
		if ($this->catid != 0)
			$this->catid_url = '&catid='.$this->catid;

		// Page link
		if ($this->user_page->pagename != '') { // Yet or not yes a page
			if ($this->link_folder_print == 1)
				$this->link = JURI::base().$this->user_page->foldername.'/'.$this->user_page->pagename.'/';
			else
				$this->link = str_replace(JURI::base(true).'/', '', JURI::base()).JRoute::_('index.php?option=com_myjspace&view=see&pagename='.$this->user_page->pagename, false);
		} else {
			$this->link = null;
		}

		$this->user_mode_view = $pparams->get('user_mode_view', 1);
		$this->page_increment = $pparams->get('page_increment', 1);
		$this->pagename_username = $pparams->get('pagename_username', 0);
		$this->uploadimg = $pparams->get('uploadimg', 1);
		$this->uploadmedia = $pparams->get('uploadmedia', 1);
		$this->publish_mode = $pparams->get('publish_mode', 2);

		// Files uploaded
		if ($this->link_folder == 1 && ($this->uploadimg > 0 || $this->uploadmedia > 0)) {
			list($this->page_number, $this->page_size) = BS_Util::dir_size(JPATH_SITE.'/'.$this->user_page->foldername.'/'.$this->user_page->pagename);
		} else {
			$this->page_size = 0;
			$this->page_number = 0;
		}
		$this->page_size = BS_Util::convertSize($this->page_size);
		$this->dir_max_size = BS_Util::convertSize((int)$pparams->get('dir_max_size', 52428800)); // Max upload (dir)
		$this->file_max_size_txt2 = BS_Util::convertSize($this->file_max_size);
		$this->file_max_size_txt = $this->file_max_size_txt2;
		$resize_x = (int)$pparams->get('resize_x', 800);
		$resize_y = (int)$pparams->get('resize_y', 600);
		if ($resize_x != 0 || $resize_y != 0) {
			if ($resize_x == 0)
				$resize_x = '&#8734';
			if ($resize_y == 0)
				$resize_y = '&#8734';
			if (function_exists("gd_info"))
				$this->file_max_size_txt .= JText::sprintf('COM_MYJSPACE_LABELUSAGE3', $resize_x, $resize_y);
			else
				$this->file_max_size_txt .= JText::_('COM_MYJSPACE_LABELUSAGE4');
		}

		// Files list (all types: '*')
		if ($this->uploadimg > 0 || $this->uploadmedia > 0)
			$this->tab_list_file = BS_Util::list_file_dir(JPATH_SITE.'/'.$this->user_page->foldername.'/'.$this->user_page->pagename, array('*'), 1);

		// Dates check if not set with interesting date
		$this->user_page->create_date = BS_Util::html_date_empty($this->user_page->create_date, JText::_('COM_MYJSPACE_DATE_FORMAT'));
		$this->user_page->last_update_date = BS_Util::html_date_empty($this->user_page->last_update_date, JText::_('COM_MYJSPACE_DATE_FORMAT'));
		$this->user_page->last_access_date = BS_Util::html_date_empty($this->user_page->last_access_date, JText::_('COM_MYJSPACE_DATE_FORMAT'));

		// Lock or not
		$lock_img = JURI::base().'components/com_myjspace/images/checked_out.png';
		$aujourdhui = time();

		if (strtotime($this->user_page->publish_up) >= $aujourdhui)
			$this->img_publish_up = '<img src="'.$lock_img.'" alt="lock" />';
		else
			$this->img_publish_up = '';

		if ($this->user_page->publish_down != $nullDate && $this->user_page->publish_down != null && strtotime($this->user_page->publish_down) < $aujourdhui)
			$this->img_publish_down = '<img src="'.$lock_img.'" alt="lock" />';
		else
			$this->img_publish_down = '';

		$this->user_page->publish_up = BS_Util::html_date_empty($this->user_page->publish_up, JText::_('COM_MYJSPACE_DATE_CALENDAR'));
		$this->user_page->publish_down = BS_Util::html_date_empty($this->user_page->publish_down, JText::_('COM_MYJSPACE_DATE_CALENDAR'));

		// Automatic configuration :-) for new page (auto-create = 0)
		if ($this->user_page->pagename == '' || $this->link_folder == 0) {
			$this->uploadimg = 0;
			$this->uploadmedia = 0;
		}

		// New page with auto-create = 0
		if ($this->user_page->pagename != '') {
			$this->msg_tmp = '';
		} else {
			$this->user_page->title = $this->user_page->getPagenameFree(basename($pparams->get('auto_pagename_rule', '#username')), $this->user, $this->catid);
			$this->msg_tmp = ' <i class="icon-info"></i> '.JText::_('COM_MYJSPACE_NEWPAGEINFO');
		}

		// Web page title
		if ($pparams->get('pagetitle', 1) == 1) {
			$title = $this->user_page->title;
			if (empty($title)) {
				$title = $app->get('sitename');
			} elseif ($app->get('sitename_pagetitles', 0) == 1) {
				$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
			} elseif ($app->get('sitename_pagetitles', 0) == 2) {
				$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
			}
			if ($title)
				$document->setTitle($title);
		}

		// Breadcrumbs
		$pathway = $app->getPathway();
		$pathway->addItem($this->user_page->title, '');

		// Templates list proposed for user selection
		$template_list = trim($pparams->get('template_list', ''));
		$this->tab_template = array();
		if ($template_list) {
			$template_list = explode(',', $template_list);
			foreach ($template_list as $value) {
				$value_tab = explode(':', $value);
				if (count($value_tab) == 2)
					$this->tab_template[trim(strtolower($value_tab[0]))] = trim($value_tab[1]);
				else
					$this->tab_template[trim(strtolower($value))] = trim($value);
			}
		}

		// Categories
		$this->categories = BSHelperUser::getCategories(1, $this->user_page->language);

		// Share edit
		$this->share_page = $pparams->get('share_page', 0);
		$this->group_list = JHtml::_('access.assetgroups');

		// Blockview list
		$this->blockview_list = BS_UtilAcl::get_assetgroup_list();

		// Users access list => convert id to username
		$this->user_mode_view_userread = $pparams->get('user_mode_view_userread', 0);
		$this->user_page->userread_name = '';
		if ($pparams->get('user_mode_view_userread', 0) && $this->user_page->userread != '') {
			$userread = json_decode($this->user_page->userread, true);
			foreach ($userread as &$value) {
				// User (for page) info
				$table = JUser::getTable();
				if ($table->load($value)) { // Test if user exists before to retrieve info
					$user_grant = JFactory::getUser($value);
					$this->user_page->userread_name .= $user_grant->username.',';
				}
			}
			$this->user_page->userread_name = trim($this->user_page->userread_name, ',');
		}

		// Show link admin
		$this->show_link_admin = $pparams->get('show_link_admin', 1);

		// Association
		$this->associations = array();
		$count_association = 0;

		if ($language_filter == 2 && JLanguageAssociations::isEnabled() == 1) { // Association J!3.0.3+
			$assoc_list = BSHelperUser::getAssociations($this->user_page->id);
			foreach ($this->language_list as $tag => $lang_code) {
				$lang_tmp = $this->language_list[$tag]->lang_code;
				if ($lang_tmp != $this->user_page->language) {
					$this->associations[$lang_tmp] = new stdClass();
					$this->associations[$lang_tmp]->language = $this->language_list[$tag]->title;
					if (isset($assoc_list[$lang_tmp])) {
						$this->associations[$lang_tmp]->pagename = $assoc_list[$lang_tmp]->pagename;
						$count_association++;
					} else {
						$this->associations[$lang_tmp]->pagename = '';
					}
				}
			}
		}

		// Add 'All' language '*'
		if ($count_association == 0 && count($this->language_list) > 0)
			$this->language_list = BSHelperUser::get_language_add_all($this->language_list);

		// Joomla! tags, J!3.1.4+
		if ($pparams->get('show_tags', 1) == 1) {
			$pathToMyXMLFile = JPATH_COMPONENT_SITE.'/models/forms/myjspace.xml';
			$this->form = JForm::getInstance('myform', $pathToMyXMLFile);
			// Default tag value
			$my_JHelperTags = new JHelperTags;
			$this->tags = $my_JHelperTags->getTagIds($this->user_page->id, 'com_myjspace.see');
		} else {
			$this->tags = null;
			$this->form = null;
		}

		// Drag & drop url for task call
		$this->drag_drop_upload = "\nvar urlupload = 'index.php?option=com_myjspace&task=upload_file&tmpl=component&upload_id=".$this->user_page->id.'&'.JSession::getFormToken()."=1';\n";

		parent::display($tpl);
	}
}
