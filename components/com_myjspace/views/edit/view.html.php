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

class MyjspaceViewEdit extends JViewLegacy
{
	function display($tpl = null)
	{
		require_once JPATH_COMPONENT_SITE.'/helpers/user.php';
		require_once JPATH_COMPONENT_SITE.'/helpers/user_event.php';
		require_once JPATH_COMPONENT_SITE.'/helpers/util.php';

		// Config
		$pparams = JComponentHelper::getParams('com_myjspace');
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$params = $app->getParams();
		$jinput = JFactory::getApplication()->input;

		// Params
		$layout = $jinput->get('layout', '', 'STRING');

		$id = $jinput->get('id', 0, 'INT');
		$link_folder = $pparams->get('link_folder', 1);

		$this->mjs_editable = 'mjs_editable';
		$this->e_name = $jinput->get('e_name', 'mjs_editable', 'STRING');

		// isAdmin
		if (version_compare(JVERSION, '3.7.0', 'lt'))
			$this->isAdmin = $app->isAdmin();
		else
			$this->isAdmin = $app->isClient('administrator');

		// User info
		$user = JFactory::getUser();
		$this->user_page = New BSHelperUser();

		if ($layout == '') {
			$this->return = $jinput->get('return', '', 'STRING');
			$catid = $jinput->get('catid', $params->get('catid', 0), 'INT');

			if ($id > 0 && $catid != 0)
				$catid = 0;

			// Catid url
			if ($catid != 0)
				$catid_url = '&catid='.$catid;
			else
				$catid_url = '';

			$cid = $jinput->get('cid', 0, 'INT');
			if ($id == 0)
				$id = $cid;

			if ($id < 0 && $cid > 0) { // New page to create with $cid page id as model
				$model_copy = $cid;
				$this->user_page->id = $model_copy;
				$this->user_page->loadPageInfoOnly();
				$catid = $this->user_page->catid;
				unset($this->user_page);
				$this->user_page = New BSHelperUser();
			} else {
				$model_copy = 0;
			}

			// Retrieve user page ID if pagename defined
			$pagename = $jinput->get('pagename', '', 'STRING');
			if ($pagename != '') {
				$this->user_page->pagename = $pagename;
				$this->user_page->loadPageInfo(1, 1);
				$id = $this->user_page->id;
			}

			// Page id - check
			if ($pparams->get('share_page', 0) != 0)
				$access = $user->getAuthorisedViewLevels();
			else
				$access = null;
			$list_page_tab = $this->user_page->getListPageId($user->id, $id, $catid, $access);
			$nb_page = count($list_page_tab);

			if ($id <= 0 || $nb_page != 1) {
				if ($id < 0 && $nb_page >= (int)$pparams->get('nb_max_page', 1)) { // New page KO
					$app->enqueueMessage(JText::_('COM_MYJSPACE_MAXREACHED'), 'error');
					$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages&lview=edit'.$catid_url, false));
					return;
				} else if ($id < 0 || ($nb_page == 0 && $id <= 0)) { // New page
					$id = 0;
				} else if ($nb_page == 1) { // id=0 => Display the page
					$id = $list_page_tab[0]['id'];
				} else { // Display Pages list
					$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages&lview=edit'.$catid_url, false));
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
				$this->user_page->loadPageInfo();
			}

			// Check if foldername exists => Alert Admin
			if (!BSHelperUser::IsDirW(BSHelperUser::getRootFoldername()) && $link_folder == 1) {
				$app->enqueueMessage(JText::_('COM_MYJSPACE_ALERTYOURADMIN'), 'error');
				$app->redirect('index.php');

				return;
			}

			// Create automatically page if none, if option 'auto_create_page' is activated & max 1 model
			$auto_create_page = $pparams->get('auto_create_page', 3);
			$model_page_list = ($model_copy > 0) ? array() : BSUserEvent::model_pagename_list($catid); // Model page list
			if ($this->user_page->pagename == null && ($auto_create_page == 2 || $auto_create_page == 3) && count($model_page_list) <= 2) {
				$blockview = $pparams->get('user_mode_view_default', 1);

				if ($model_copy > 0) {
					$model_page = $model_copy;
				} else {
					$model_page = 0;
					foreach ($model_page_list as $k => $v) { // Take the last one
						if ($k != 0 && $model_page_list[$k]['catid'] > 0)
							$catid = $model_page_list[$k]['catid'];
						$model_page = $model_page_list[$k]['pagename'];
					}
				}

				$pagename = $this->user_page->getPagenameFree(basename($pparams->get('auto_pagename_rule', '#username')), $user, $catid);

				$id = BSUserEvent::adm_save_page_conf(0, $user->id, $pagename, $blockview, '', 0, '', '', '', '', $model_page, $catid, null, '', null, null, 'site_create');

				$this->user_page->id = $id;
				$this->user_page->loadPageInfo(); // Reload the user data
			}

			// Catid url (if catid change)
			if ($catid != 0)
				$catid_url = '&catid='.$catid;

			$jinput->set('id', $id); // Used for upload from editor (necessary when the view is call using 'pagename')

			if ($this->user_page->pagename == '') { // Page not found => Go to create it
				$app->redirect(JRoute::_('index.php?option=com_myjspace&view=config'.$catid_url, false));

				return;
			}

			if ($this->user_page->blockedit == 1)
				$this->msg = JText::_('COM_MYJSPACE_EDITBLOCKED');
			else if ($this->user_page->blockedit == 2)
				$this->msg = JText::_('COM_MYJSPACE_EDITLOCKED');
			else
				$this->msg = null;

			// Editor selection
			$this->editor_selection = $pparams->get('editor_selection', 'tinymce');
			if (BS_Util::check_editor_selection($this->editor_selection) == false || $this->editor_selection == '-') // Use the Joomla! default editor
				$this->editor_selection = null;

			// Editor buttons to hide
			if ($pparams->get('allow_editor_button', 1) == 1) {
				$this->editor_button = array('readmore', 'article', 'image', 'pagebreak', 'contact', 'menu', 'module');
			} else {
				$this->editor_button = false;
			}

			// Editor 'windows' size
			$this->edit_x = $pparams->get('user_edit_x', '100%');
			$this->edit_y = $pparams->get('user_edit_y', '600px');

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
		} else if ($layout == 'tags') {
			// Tags button
			$this->allow_tag_myjsp_iframe = $pparams->get('allow_tag_myjsp_iframe', 1);
			$this->allow_tag_myjsp_include = $pparams->get('allow_tag_myjsp_include', 1);
			$this->share_page = $pparams->get('share_page', 0);
			$this->language_filter = $pparams->get('language_filter', 0);
			$this->show_tags = $pparams->get('show_tags', 1);
		} else if ($layout == 'upload') {
			$this->view = 'edit'; // Source view

			// Info about page page (directory ...)
			if ($id <= 0)
				return;

			$this->user_page->id = $id;
			$this->user_page->loadPageInfoOnly();

			// Drag & drop url for task call
			$this->drag_drop_upload = "\nvar urlupload = 'index.php?option=com_myjspace&task=upload_file&tmpl=component&upload_id=".$this->user_page->id.'&'.JSession::getFormToken()."=1';\n";

			// For 'old' editor 'myjsp' compatibility
			$this->editor_selection = $pparams->get('editor_selection', 'tinymce');
			if (BS_Util::check_editor_selection($this->editor_selection) == false || $this->editor_selection == '-') { // Use the Joomla! default editor
				$this->editor_selection = $app->get('editor');
			}
			$this->type = $jinput->get('type', 'undefined', 'STRING'); // media, image, file, undefined
			$this->uploadimg = $pparams->get('uploadimg', 1);
			$this->uploadmedia = $pparams->get('uploadmedia', 1);
			$this->uploaded = $jinput->get('uploaded', '', 'STRING');
			// Automatic configuration :-)
			if ($this->user_page->pagename == '' || $link_folder == 0) {
				$this->uploadimg = 0;
				$this->uploadmedia = 0;
			}

			// Uploaded file
			$this->uploaded = $jinput->get('uploaded', '', 'STRING');

			// Files list
			$this->tab_list_file = null;
			if ($this->type == 'image')
				$allowed_types = array('png', 'jpg', 'gif');
			else
				$allowed_types = array('*');
			if ($this->uploadimg > 0 || $this->uploadmedia > 0)
				$this->tab_list_file = BS_Util::list_file_dir(JPATH_SITE.'/'.$this->user_page->foldername.'/'.$this->user_page->pagename, $allowed_types, 1);

			$this->file_max_size = (int)$pparams->get('file_max_size', 5242880);
			$this->file_max_size_txt2 = BS_Util::convertSize($this->file_max_size);
		}

		parent::display($tpl);
	}
}
