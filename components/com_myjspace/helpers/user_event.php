<?php
/**
* @version $Id:	user_event.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2010 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

// For AddLog()
use Joomla\CMS\MVC\Model\BaseDatabaseModel; // For J!3+
use Joomla\Component\Actionlogs\Administrator\Model\ActionlogModel; // For J!4

// JPATH_ROOT to allow to call out of the component, for some methods only
require_once JPATH_ROOT.'/components/com_myjspace/helpers/user.php';
require_once JPATH_ROOT.'/components/com_myjspace/helpers/util.php';

// -----------------------------------------------------------------------------

// Theses function are here because they can be call from user or admin interface

class BSUserEvent
{
	protected static $component = 'com_myjspace';
// Constructor - none

// Remove the personal page(s) record(s) from the database and files
// $tab_id can be a single page (numeric) or array of pages id
 	public static function adm_page_remove($tab_id = null, $url_redirect = 'index.php', $caller = 'site')
	{
		$app = JFactory::getApplication();

		if (!is_array($tab_id) || !isset($tab_id[0])) {
			$valeur = $tab_id;
			$tab_id = array();
			$tab_id[0] = $valeur;
		}

		$pparams = JComponentHelper::getParams('com_myjspace');
		$max_page = (int)$pparams->get('max_page_delete', 20);
		if (count($tab_id) > $max_page) { // Safety control ... not too much delete
			$app->enqueueMessage(JText::sprintf('COM_MYJSPACE_PAGEDELETETOOMUCH', $max_page), 'error');
			$app->redirect($url_redirect);

			return;
		}

		$total = 0;
		JPluginHelper::importPlugin('content');

		foreach ($tab_id as &$id) {
			$user_page = New BSHelperUser();
			$user_page->id = $id; // To set page id
			$user_page->loadPageInfoOnly(); // To get pagename & foldername

			// If page locked (admin & edit | edit) - Front-end
			if ($user_page->blockedit != 0 && $caller == 'site') {
				$app->enqueueMessage(JText::_('COM_MYJSPACE_EDITLOCKED'), 'error');
				$app->redirect(JRoute::_('index.php?option=com_myjspace&view=config', false));

				return;
			}

			// Joomla! 3.1.4+ tags
			$db	= JFactory::getDBO();
			$user_JTableMyjspace = New JTableMyjspace($db);
			$user_JTableMyjspace->delete($id); // Delete tag & ucm_ tables content

			// Del associations, if any
			BSHelperUser::delAssociations($id);

			if (!$user_page->deletePage($pparams->get('link_folder', 1))) { // Delete
				$app->enqueueMessage(JText::sprintf('COM_MYJSPACE_ERRDELETINGPAGE', $user_page->title), 'error');
			} else {
				// Del empty subfolder if any
				BSHelperUser::deleteSubdir($user_page->foldername, BSHelperUser::getRootFoldername()); // Del old subdir if necessary (for complex fooldername only)
				$total = $total + 1;

				// Event
				$app->triggerEvent('onContentAfterDelete', array('com_myjspace.see', $user_page));
			}
		}

		$app->enqueueMessage(JText::sprintf('COM_MYJSPACE_PAGEDELETEDS', $total), 'notice');
		$app->redirect($url_redirect);
	}

// Save (=update) page content
	public static function adm_save_page_content($id = 0, &$content = null, $name_page_max_size = 0, $url_redirect = 'index.php', $caller = 'site', $event = true)
	{
		$app = JFactory::getApplication();

		// Size test
		if ($name_page_max_size > 0 && strlen($content) > $name_page_max_size) {
			if ($url_redirect != null) {
				$app->enqueueMessage(JText::_('COM_MYJSPACE_ERRCREATEPAGESIZE').' '.$name_page_max_size, 'error');
				$app->redirect($url_redirect);
			}
			return;
		}

		// Param
		$pparams = JComponentHelper::getParams('com_myjspace');
		$user = JFactory::getUser();
		$email_user = $pparams->get('email_user', 0);
		$email_admin = $pparams->get('email_admin', 0);
		$email_admin_from = $pparams->get('email_admin_from', '');

		$user_page = New BSHelperUser();
		$user_page->id = $id; // To set pageid
		$user_page->loadPageInfoOnly(); // Get info (for pagename)
		$user_page->modified_by = $user->id;

		// If page locked (admin & edit)
		if ($user_page->blockedit == 2 && $caller == 'site') {
			$app->enqueueMessage(JText::_('COM_MYJSPACE_EDITLOCKED'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_myjspace&view=config', false));
			return;
		}

		// Plugin event
		JPluginHelper::importPlugin('content');

		$user_page->content = $content; // To set content
		if ($user_page->updateUserContent()) {
			if ($pparams->get('show_tags', 1) == 1 ) { // Joomla! 3.1.4+ tags
				$my_JHelperTags = new JHelperTags;
				$tags = $my_JHelperTags->getTagIds($user_page->id, 'com_myjspace.see'); // Get tags for this page
				$tags = ($tags) ? explode(',', $tags) : array();
				if (count($tags)) {
					$db	= JFactory::getDBO();
					$user_JTableMyjspace = New JTableMyjspace($db);
					$user_JTableMyjspace->get_row_BSHelperUser($user_page);
					$user_JTableMyjspace->newTags = $tags;
					$user_JTableMyjspace->store(); // Store updated content in ucm table + tags
				}
			}

			if ($pparams->get('lock_page_after_update', 0) == 1) { // Force to 'lock' after update
				$user_page->blockview = 0;
				$user_page->setConfPage(2); // Update blockview table

				if ($email_admin == 1) { // Send email to Admin
					$subject = JText::sprintf('COM_MYJSPACE_EMAIL_SUBJECT2', $user_page->pagename);
					$site_msg = str_replace('administrator/', '', JURI::base());
					$body = JText::sprintf('COM_MYJSPACE_EMAIL_CONTENT2', $user_page->pagename, $site_msg);
					BS_Util::send_mail('', $email_admin_from, $subject, $body);
				}
			}

			// Event
			if ($event)
				$app->triggerEvent('onContentAfterSave', array('com_myjspace.see', $user_page, false));

			if ($email_user == 1 && $caller == 'admin') { // Send email to user
				$subject = JText::sprintf('COM_MYJSPACE_EMAIL_SUBJECT2', $user_page->pagename);
				$site_msg = str_replace('administrator/', '', JURI::base());
				$body = JText::sprintf('COM_MYJSPACE_EMAIL_CONTENT2', $user_page->pagename, $site_msg);
				$user = JFactory::getUser($user_page->userid);
				BS_Util::send_mail($email_admin_from, $user->email, $subject, $body);
			}

			if ($url_redirect != null) {
				$app->enqueueMessage(JText::_('COM_MYJSPACE_EUPDATINGPAGE'), 'message');
				$app->redirect($url_redirect);
			}
		} else if ($url_redirect != null) {
			$app->enqueueMessage(JText::_('COM_MYJSPACE_ERRUPDATINGPAGE'), 'error');
			$app->redirect($url_redirect);
		}
	}

// Save (=update) page (out of content)
	public static function adm_save_page_conf($id = 0, $userid = 0, $pagename = null, $blockview = 1, $userread_name = '', $blockedit = 0, $publish_up = '', $publish_down = '', $metakey = '', $template = '', $mjs_model_page = 0, $catid = 0, $access = null, $language = '', $tags = null, $url_redirect = null, $caller = 'site', $redirect = true)
	{
		$app = JFactory::getApplication();

		$user_page = New BSHelperUser();
		$creation = 0;

		// Param
		$pparams = JComponentHelper::getParams('com_myjspace');
		$link_folder = $pparams->get('link_folder', 1);
		$name_page_size_min = (int)$pparams->get('name_page_size_min', 0);
		$name_page_size_max = (int)$pparams->get('name_page_size_max', 30);
		$pagename_username = $pparams->get('pagename_username', 0);
		$email_admin = $pparams->get('email_admin', 0);
		$email_user = $pparams->get('email_user', 0);
		$email_admin_from = $pparams->get('email_admin_from', '');
		$email_admin_to = $pparams->get('email_admin_to', '');
		$publish_mode = $pparams->get('publish_mode', 2);
		$user_mode_view	= $pparams->get('user_mode_view', 1);
		$nb_max_page_category = (int)$pparams->get('nb_max_page_category', 0);

		// Category checks
		$category_label = BSHelperUser::getCategoryLabel($catid);
		if ($catid && ($category_label)) { // If catid specified & exists
			$default_catid = $catid;
		} else { // Use default catid
			$default_catid = $pparams->get('default_catid', 0);

			$categories = BSHelperUser::getCategories(1);
			if ($default_catid == 0 && count($categories) && $caller == 'site_create')
				$default_catid = $categories[0]['value'];
			if (!$category_label) // Incorrect catid => default catid
				$catid = $default_catid;
		}

		if (version_compare(JVERSION, '3.7.0', 'lt'))
			$isAdmin = $app->isAdmin();
		else
			$isAdmin = $app->isClient('administrator');

		// Default url to redirect => from front-end or back-end ?
		if ($url_redirect == null) {
			if ($isAdmin)
				$url_redirect = JRoute::_('index.php?option=com_myjspace&view=page&id='.$id, false); // id=0 (new page ko) will redirect to view=pages (from view=page)
			else
				$url_redirect = JRoute::_('index.php?option=com_myjspace&view=config&id='.$id, false);
		}

		if ($pagename_username == 0 && (strlen($pagename) < $name_page_size_min || strlen($pagename) > $name_page_size_max)) { // Check pagename length
			if ($redirect) {
				$app->enqueueMessage(JText::sprintf('COM_MYJSPACE_ADMIN_NAME_PAGE_SIZE_ERROR', $name_page_size_min, $name_page_size_max), 'error');
				$app->redirect($url_redirect);
			}
			return 0;
		}

		// Plugin event
		JPluginHelper::importPlugin('content');

		// Charge page info if page (id) exists
		$user_page->id = $id;
		$user_page->loadPageInfo();

		// Store old/previous data
		$foldername_old = $user_page->foldername;
		$pagename_old = $user_page->pagename;

		// Pagename & title
		$title = trim($pagename);
		$pagename = BSHelperUser::stringURLSafe($pagename); // Create title alias for the pagename

		if ($pagename == '' || $pagename == '0' || (is_numeric($pagename) && $pparams->get('pagename_full_num', 0) == 0)) { // Check naming (no empty & no numeric)
			if ($redirect) {
				$app->enqueueMessage(JText::_('COM_MYJSPACE_NOTVALIDPAGENAME'), 'error');
				$app->redirect($url_redirect);
			}
			return 0;
		}

		$id_recup = $user_page->id;
		if ($userid != 0)
			$user_page->userid = $userid; // In case if page do not already exists

		if ($nb_max_page_category > 0) { // If nb page(s) per user per category limited
			$count_thiscatid = $user_page->countUserPageCategory($catid);
			if ($count_thiscatid >= $nb_max_page_category && $user_page->catid != $catid) {
				if ($caller == 'site' || $caller == 'site_create')
					$view = 'config';
				else
					$view = 'page&id='.$user_page->id;
				if ($redirect) {
					$app->enqueueMessage(JText::sprintf('COM_MYJSPACE_MAXCATEGORYREACHED', $nb_max_page_category, BSHelperUser::getCategoryLabel($catid)), 'error');
					$app->redirect(JRoute::_('index.php?option=com_myjspace&view='.$view, false));
				}
				return 0;
			}
		}

		// If page locked (admin & edit)
		if ($user_page->blockedit == 2 && $caller == 'site') {
			if ($redirect) {
				$app->enqueueMessage(JText::_('COM_MYJSPACE_EDITLOCKED'), 'error');
				$app->redirect(JRoute::_('index.php?option=com_myjspace&view=config', false));
			}
			return 0;
		}

		if ($user_page->pagename != $pagename) { // Test if pagename change (or new page)
			if (BSHelperUser::ifExistPageName($pagename)) {
				if ($redirect) {
					$app->enqueueMessage(JText::_('COM_MYJSPACE_PAGEEXISTS'), 'error');
					$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages', false));
				}
				return 0;
			}

			if ($user_page->pagename == '') { // Page Creation
				$creation = 1;

				if ($userid == 0) {
					$userid = JFactory::getUser()->id;
					$user_page->userid = $userid;
				}
				$user_page->pagename = $pagename;

				if ($nb_max_page_category > 0) { // If nb page(s) per user per category limited
					$count_thiscatid = $user_page->countUserPageCategory($default_catid);
					if ($count_thiscatid >= $nb_max_page_category) {
						if ($caller == 'site' || $caller == 'site_create')
							$view = 'config';
						else
							$view = 'createpage';

						if ($redirect) {
							$app->enqueueMessage(JText::sprintf('COM_MYJSPACE_MAXCATEGORYREACHED', $nb_max_page_category, BSHelperUser::getCategoryLabel($default_catid)), 'error');
							$app->redirect(JRoute::_('index.php?option=com_myjspace&view='.$view, false));
						}
						return 0;
					}
				}

				// Page creation (DB (sthis et/update the doldername name) & directory & file, if page with directory configured)
				if (!($id_recup) && (!($user_page->id = $user_page->createPage($pagename, $default_catid)) || ($link_folder == 1 && $user_page->createDirFilePage($pagename, $pparams->get('index_pagename_id', 1)) == 0))) { // A completer en cas d'erreur de l'un ou de l'autre seulement ?
					if ($redirect) {
						$app->enqueueMessage(JText::_('COM_MYJSPACE_ERRCREATEPAGE'), 'error');
						$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages', false));
					}
					// Clean-up to be made, in case ?
					return $user_page->id;
				}

				// Model Page(s) ?
				$mjs_model_page = self::model_pagename_id($mjs_model_page); // Select the model page id (or file name) to be used
				if ($mjs_model_page) { // If model page to use
					if (intval($mjs_model_page) != 0) { // If it's a number != 0, it's a page id
						$user_page->content = $user_page->getContentPageId($mjs_model_page);
					} else { // File content to upload
						$user_page->content = @file_get_contents($mjs_model_page);
						if (strstr($user_page->content, '<body>') && preg_match('#<body(.*)>(.*)</body>#Us', $user_page->content, $sortie)) {
							if (count($sortie) >= 3)
								$user_page->content = $sortie[2];
						}
					}

					if ($user_page->content)
						$user_page->updateUserContent();
				}

//				if (count(self::model_pagename_list($catid))) {
					// Non SEF
					$url_redirect = str_replace('&id=0', '&id='.$user_page->id, $url_redirect);
					// SEF
					$url_redirect .= '#####';
					$url_redirect = str_replace('/0#####', '/'.$user_page->id, $url_redirect);
//				}
				if ($email_admin == 1) { // Send Email to admin
					$subject = JText::sprintf('COM_MYJSPACE_EMAIL_SUBJECT1', $pagename);
					$body = JText::sprintf('COM_MYJSPACE_EMAIL_CONTENT1', $pagename, str_replace('administrator/', '', JURI::base()));
					BS_Util::send_mail($email_admin_from, $email_admin_to, $subject, $body);
				}
			}
		}

		if ($caller == 'site_create') // Act (redirect) now as admin for site & auto-create
			$caller = 'admin';

		// Update with param received & keep the old one if none received
		$user_page->title = $title;
		$user_page->pagename = $pagename;

		if ($access !== null)
			$user_page->access = $access;

		if ($creation == 1)
			 $blockview = $pparams->get('user_mode_view_default', 1);
		$user_page->blockview = $blockview;

		$user_page->blockedit = $blockedit;

		if ($language != '')
			$user_page->language = $language;

		if ($userread_name != '') { // User access list
			$userread_name_tab = array_unique(explode(',', $userread_name));

			$user_page->userread = '[';
			foreach ($userread_name_tab as &$value) {
				$user_grant = JFactory::getUser(trim($value));
				if ($user_grant->id > 0 && (strlen($user_page->userread) + strlen($user_grant->id)) < 100) { // Max 100 characters
					$user_page->userread .= $user_grant->id.',';
				}
			}
			$user_page->userread = trim($user_page->userread, ',');
			$user_page->userread .= ']';
		}

		// Metakey (comment)
		$user_page->metakey = trim(substr($metakey, 0, 150)); // Max. 150 characters

		// Template
		$user_page->template = trim(substr($template, 0, 50)); // Max. 50 characters

		// Catid
		$user_page->catid = $catid;

		// Publish dates (with the right format & valid)
		$user_page->publish_up = BS_Util::valid_date(trim($publish_up), JText::_('COM_MYJSPACE_DATE_CALENDAR'));
		$user_page->publish_down = BS_Util::valid_date(trim($publish_down), JText::_('COM_MYJSPACE_DATE_CALENDAR'));

		// Right selection (all $droits= 31) to avoid use to change unauthorised with some kind of direct url access ...
		$droits = 0;
		if ($pagename_username == 0 || $caller == 'admin2' || $caller == 'admin' || $creation == 1)
			$droits += 1;
		if ($user_mode_view == 1 || $caller == 'admin2' || $creation == 1)
			$droits += 2;
		if ($caller == 'admin2' || $caller == 'admin')
			$droits += 4; // blockedit
		if ($publish_mode == 2 || ($publish_mode == 1 && $caller == 'admin2')) {
			if ($user_page->publish_up)
				$droits += 8;
			if ($user_page->publish_down)
				$droits += 16;
		}
		$droits += 32; // metakey
		$droits += 64; // template
		if ($pparams->get('select_category', 1) == 1 || $caller == 'admin2' || $caller == 'admin')
			$droits += 128;	// catid
		if ($access !== null)
			$droits += 512;	// access
		if ($userid != 0 || $creation == 1)
			$droits += 256;
		if ($language != '') // language
			$droits += 2048;

		if ($user_page->setConfPage($droits)) { // Update page config
			// Update Page fodder or subfolder & content containt pagename & foldername
			if ($creation == 0 && $link_folder == 1 && ($pagename_old != $user_page->pagename || $foldername_old != $user_page->foldername)) {
				BSHelperUser::createSubdir($user_page->foldername, BSHelperUser::getRootFoldername()); // Add subdir if necessary (for complex fooldername only) & rule change

				if ($pagename_old != $user_page->pagename) // Rename
					@rename(JPATH_SITE.'/'.$foldername_old.'/'.$pagename_old, JPATH_SITE.'/'.$user_page->foldername.'/'.$user_page->pagename);
				$user_page->createDirFilePage($pagename, $pparams->get('index_pagename_id', 1)); // If not already exists, create (cases options (uploadimg, ...) changed)

				BSHelperUser::deleteSubdir($foldername_old, BSHelperUser::getRootFoldername()); // Del old subdir if necessary (for complex fooldername only)

				// Update url & image link (relative & absolute)
				$user_page->content = preg_replace('!src=(.*)'.$foldername_old.'/'.$pagename_old.'!isU', 'src=$1'.$user_page->foldername.'/'.$user_page->pagename.'', $user_page->content, -1, $nbsrc);
				$user_page->content = preg_replace('!href=(.*)'.$foldername_old.'/'.$pagename_old.'!isU', 'href=$1'.$user_page->foldername.'/'.$user_page->pagename.'', $user_page->content, -1, $nbhref);

				// Update modified content!
				if ($nbsrc+$nbhref)
					$user_page->updateUserContent();
			}

			// Joomla! tags J!3.1.4+
			if ($pparams->get('show_tags', 1) == 1) {
				if ($tags == null) // EVOL revoir l'appel de la fct(), mettre array() au lien de null, dont pour defaut
					$tags = array();

				if (($max_tags_to_save = (int)$pparams->get('max_tags_to_save', 0)) > 0) { // Max. number of tags to be saved
					$count_before = count($tags);
					$tags = array_slice($tags, 0, $max_tags_to_save, true);
					$count_after = count($tags);

					if ($count_before > $count_after)
						JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_MYJSPACE_JTAG_LIMITED', $max_tags_to_save), 'notice');
				}

				// Add J!tags
				$db	= JFactory::getDBO();
				$user_JTableMyjspace = New JTableMyjspace($db);
				$user_JTableMyjspace->get_row_BSHelperUser($user_page);
				$user_JTableMyjspace->newTags = $tags;
				$user_JTableMyjspace->store();
			}

			// Event
			$app->triggerEvent('onContentAfterSave', array('com_myjspace.see', $user_page, $creation));

			// Send email to user
			if ($email_user == 1 && $creation == 0 && $caller == 'admin2') {
				require_once JPATH_ROOT.'/components/com_myjspace/helpers/util_acl.php';
				$subject = JText::sprintf('COM_MYJSPACE_EMAIL_SUBJECT2', $pagename);
				$edit_msg = 'COM_MYJSPACE_TITLEMODEEDIT'.$blockedit;
				$site_msg = str_replace('administrator/', '', JURI::base());
				$body = JText::sprintf('COM_MYJSPACE_EMAIL_CONTENT2', $pagename, $site_msg);
				$body .= "\n ". JText::_('COM_MYJSPACE_TITLEMODEEDIT').JText::_('COM_MYJSPACE_2POINTS').JText::_($edit_msg);
				$body .= "\n ". JText::_('COM_MYJSPACE_TITLEMODEVIEW').JText::_('COM_MYJSPACE_2POINTS').BS_UtilAcl::get_assetgroup_label($user_page->blockview);
				$user = JFactory::getUser($user_page->userid);
				BS_Util::send_mail($email_admin_from, $user->email, $subject, $body);
			}

			if ($caller != 'admin' && $redirect) {
				$app->enqueueMessage(JText::_('COM_MYJSPACE_EUPDATINGPAGE'), 'message');
				$app->redirect($url_redirect);
			}

		} else if ($caller != 'admin' && $redirect) {
			$app->enqueueMessage(JText::_('COM_MYJSPACE_ERRUPDATINGPAGE'), 'error');
			$app->redirect($url_redirect);
		} else if ($redirect) {
			$app->enqueueMessage(JText::_('COM_MYJSPACE_ERRUPDATINGPAGE'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages', false));
		}

		return $user_page->id;
	}

// Reset page hit
	public static function adm_reset_page_access($id = 0, $url_redirect = 'index.php', $caller = 'site')
	{
		$app = JFactory::getApplication();

		// Plugin event
		JPluginHelper::importPlugin('content');

		$user_page = New BSHelperUser();
		$user_page->id = $id; // To set page id
		$user_page->loadPageInfoOnly();

		// If page locked (admin & edit)
		if ($user_page->blockedit == 2 && $caller == 'site') {
			$app->enqueueMessage(JText::_('COM_MYJSPACE_EDITLOCKED'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_myjspace&view=config',false));
			return;
		}

		if ($user_page->resetLastAccess()) { // Reset hit
			$app->triggerEvent('onContentAfterSave', array('com_myjspace.see', $user_page, false)); // Event

			$app->enqueueMessage(JText::_('COM_MYJSPACE_EUPDATINGPAGE'), 'message');
			$app->redirect($url_redirect);
		} else {
			$app->enqueueMessage(JText::_('COM_MYJSPACE_ERRUPDATINGPAGE'), 'error');
			$app->redirect($url_redirect);
		}
	}

// Delete the selected file for a user
	public static function adm_delete_file($id = 0, $filmjs_editable = '', $url_redirect = 'index.php', $caller = 'site') {
		$app = JFactory::getApplication();

		// Extra controls
		$filmjs_editable = basename($filmjs_editable);
		$forbiden_files = array('', '.', '..', '.htaccess');
		$forbiden_types = array('htm', 'html', 'php', 'js');
		$type_parts = strtolower(pathinfo($filmjs_editable, PATHINFO_EXTENSION));
		if (in_array($type_parts, $forbiden_types) || in_array(strtolower($filmjs_editable), $forbiden_files)) {
			if ($filmjs_editable == '')
				$filmjs_editable = JText::_('COM_MYJSPACE_UPLOADCHOOSE1');
			$app->enqueueMessage(JText::_('COM_MYJSPACE_UPLOADNOALLOWED').' '.JText::_('COM_MYJSPACE_UPLOADERROR11').$filmjs_editable, 'error');
			$app->redirect($url_redirect);
			return;
		}

		$user_page = New BSHelperUser();
		$user_page->id = $id; // To set page id
		$user_page->loadPageInfoOnly(); // To get pagename & foldername

		// If page locked (admin & edit)
		if ($user_page->blockedit == 2 && $caller == 'site') {
			$app->enqueueMessage(JText::_('COM_MYJSPACE_EDITLOCKED'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_myjspace&view=config',false));
			return;
		}

		if (@unlink(JPATH_SITE.'/'.$user_page->foldername.'/'.$user_page->pagename.'/'.utf8_decode($filmjs_editable))) {
			$user_page->setConfPage(0); // Page update date

			// Event
			$app->triggerEvent('onContentAfterDelete', array('com_myjspace.media', $user_page));

			$app->enqueueMessage(JText::_('COM_MYJSPACE_UPLOADERROR10').$filmjs_editable, 'message');
			$app->redirect($url_redirect);
		} else {
			$app->enqueueMessage(JText::_('COM_MYJSPACE_UPLOADERROR11').$filmjs_editable, 'error');
			$app->redirect($url_redirect);
		}
	}

// Upload the file for a user into his personal folder
	public static function adm_upload_file($id = 0, $FileObject = null, $url_redirect = 'index.php', $caller = 'site') {

		$app = JFactory::getApplication();
		$pparams = JComponentHelper::getParams('com_myjspace');

		// User
		$user_page = New BSHelperUser();
		$user_page->id = $id; // To set page id
		$user_page->loadPageInfoOnly(); // To get pagename & foldername

		// If page locked (admin & edit)
		if ($user_page->blockedit == 2 && $caller == 'site') {
			$app->enqueueMessage(JText::_('COM_MYJSPACE_EDITLOCKED'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_myjspace&view=config',false));
			return 0;
		}

		// Secure
		if ($user_page->pagename == '') {
			if ($url_redirect) {
				$app->enqueueMessage(JText::_('COM_MYJSPACE_UPLOADNOALLOWED'), 'error');
				$app->redirect($url_redirect);
			}
			return 0;
		}

		// Upload error mesg
		if ($FileObject['error'] == 1 || $FileObject['error'] == 2) { // File too big
			if ($url_redirect) {
				$app->enqueueMessage(JText::_('COM_MYJSPACE_UPLOADERROR4'), 'error');
				$app->redirect($url_redirect);
			}
			return 0;
		} else if ($FileObject['error'] > 2) { // Other error
			if ($url_redirect) {
				$app->enqueueMessage(JText::_('COM_MYJSPACE_UPLOADERROR12'), 'error');
				$app->redirect($url_redirect);
			}
			return 0;
		}

		// File with accent ? Add parameter 'file_accept_accent' = 1 in case of accent needed (via myjspace tool plugin, for example)
		$pattern = '#^[a-zA-Z0-9_.-]{1,}$#';
		if (!$pparams->get('file_accept_accent', 0) && !preg_match($pattern, $FileObject['name'])) { // File character not allowed
			if ($url_redirect) {
				$app->enqueueMessage(JText::_('COM_MYJSPACE_UPLOADERROR13'), 'error');
				$app->redirect($url_redirect);
			}
			return 0;
		}

		// 'Params'
		$pparams = JComponentHelper::getParams('com_myjspace');
		$DestPath = JPATH_SITE.'/'.$user_page->foldername.'/'.$user_page->pagename.'/';
		$resize_x = (int)$pparams->get('resize_x', 800);
		$resize_y = (int)$pparams->get('resize_y', 600);
		$uploadfile = strtolower(str_replace(' ', '', $pparams->get('uploadfile', '*'))); // Files suffixes
		$uploadimg = $pparams->get('uploadimg', 1);
		$uploadmedia = $pparams->get('uploadmedia', 0);

		$forbiden_files = array('', '.', '..', '.htaccess');
		$forbiden_types = array('htm', 'html', 'php', 'js');

		$allowed_types = array();
		if ($uploadimg == 1)
			$allowed_types = array_merge($allowed_types, array('jpg', 'png', 'gif'));

		$uploadfile_tab = array();
		$uploadfile_tab[0] = '*';
		if ($uploadmedia == 1) {
			$uploadfile = str_replace(array('|', ' '), array(',', ''), $uploadfile); // Compatibility with BS MyJspace < 1.7.7 and cleanup
			$uploadfile_tab = explode(',', $uploadfile);
			$allowed_types = array_merge($allowed_types, $uploadfile_tab);
		}

		$file_max_size = (int)$pparams->get('file_max_size', '5242880');
		$dir_max_size = (int)$pparams->get('dir_max_size', '52428800'); // Max upload (dir)
		$StatusMessage = '';
		$error = 1;

		list($rien, $dir_size_var) = BS_Util::dir_size($DestPath);
		$FileBasename = utf8_decode(basename($FileObject['name']));
		list($void_w, $void_h, $image_type) = @getimagesize($FileObject['tmp_name']);
		$type_parts = strtolower(pathinfo($FileObject['name'], PATHINFO_EXTENSION));

		if (!isset($FileObject) || $FileObject['size'] <= 0 || in_array($type_parts, $forbiden_types) || in_array(strtolower($FileBasename), $forbiden_files) || !($uploadfile_tab[0] == '*' || in_array($type_parts, $allowed_types))) {
			$StatusMessage = JText::_('COM_MYJSPACE_UPLOADERROR2');
		} else if ($image_type >= 1 && $image_type <= 3 && !in_array($type_parts, array('jpg', 'png', 'gif'))) { // Image not correctly suffixed
			$StatusMessage = JText::_('COM_MYJSPACE_UPLOADERROR2');
		} else {
			$ActualFileName = $DestPath.$FileBasename;	// Path & name to file
			$actual_filesize = intval(@filesize($ActualFileName));
			if ($actual_filesize > 0)					// If the file already exists, inform user
				$StatusMessage .= JText::_('COM_MYJSPACE_UPLOADERROR6');

			// If image it may be resized
			$StatusMessage_tmp = '';
			$ActualFileName_tmp = '';
			$FileResized = false;
			if ($resize_x != 0 || $resize_y != 0) { // Resize & gif | jpg | png
				$ActualFileName_tmp = tempnam(sys_get_temp_dir(), 'bs_');
				if (BS_Util::resize_image($FileObject['tmp_name'], $resize_x, $resize_y, $ActualFileName_tmp) == true) { // Resize if image
					$StatusMessage_tmp .= JText::_('COM_MYJSPACE_UPLOADERROR1');
					$FileObject_size = intval(@filesize($FileObject['tmp_name'])); // File uploaded
					$ActualFileName_size = intval(@filesize($ActualFileName_tmp)); // Size after resized
					if ($ActualFileName_size < $FileObject_size) { // Only smaller file :-)
						$FileResized = true; // The file to be used is ActualFileName_tmp
						$FileObject["size"] = $ActualFileName_size;
					}
				}
			}

			if ($FileObject["size"] > $file_max_size) { // File size limit
				$StatusMessage = JText::_('COM_MYJSPACE_UPLOADERROR4').BS_Util::convertSize($FileObject['size']).JText::_('COM_MYJSPACE_UPLOADERROR3').BS_Util::convertSize($file_max_size);
			} else if (($dir_size_var + $FileObject["size"] - $actual_filesize) > $dir_max_size) { // Folder size limit
				$StatusMessage = JText::_('COM_MYJSPACE_UPLOADERROR5').BS_Util::convertSize($FileObject['size']+$dir_size_var).JText::_('COM_MYJSPACE_UPLOADERROR3').BS_Util::convertSize($dir_max_size);
			} else { // Copy/Move file to user page
				if (($FileResized && @copy($ActualFileName_tmp, $ActualFileName)) || @move_uploaded_file($FileObject['tmp_name'], $ActualFileName)) {
					$StatusMessage .= $StatusMessage_tmp.JText::_('COM_MYJSPACE_UPLOADERROR9');
					$error = 0;
				} else {
					$StatusMessage .= JText::_('COM_MYJSPACE_UPLOADERROR12');
				}
				@chmod($ActualFileName, 0644);
			}
			if ($ActualFileName_tmp)
				@unlink($ActualFileName_tmp);
		}

		if ($error == 0) {
			if ($pparams->get('upload_display_path', 0)) { // Display path for the uploaded file
				$link = str_replace('/administrator', '', JURI::base(true));
				$StatusMessage .= JText::sprintf('COM_MYJSPACE_UPLOADERROR14', $link.'/'.$user_page->foldername.'/'.$user_page->pagename.'/'.basename($FileObject['name']));
			} else {
				$StatusMessage .= basename($FileObject['name']);
			}

			$user_page->setConfPage(0); // Page update date

			// Event
			$app->triggerEvent('onContentAfterSave', array('com_myjspace.media', $user_page, true)); // Event

			if ($url_redirect) {
				$app->enqueueMessage($StatusMessage, 'message');
				$app->redirect($url_redirect);
			}
		} else if ($url_redirect) {
			$app->enqueueMessage($StatusMessage, 'error');
			$app->redirect($url_redirect);
		}

		return !$error;
	}

// Tools: delete all folders and indexes file for a personal page (no uploaded file(s) or sub-folder deleted)
	public static function adm_delete_folder()
	{
		$userpage_list = BSHelperUser::loadPagename();

		$nb_page = count($userpage_list);
		if ($nb_page <= 0)
			return(array(0, JText::_('COM_MYJSPACE_ADMIN_CREATE_FOLDER_1')));

		$compte_dir_ok = 0;
		$compte_dir_ko = 0;
		$compte_ide_ok = 0;
		$compte_ide_ko = 0;

		for ($i = 0; $i < $nb_page; $i++) {
			if (@unlink(JPATH_SITE.'/'.$userpage_list[$i]['foldername'].'/'.$userpage_list[$i]['pagename'].'/index.php'))
				$compte_ide_ok = $compte_ide_ok +1;
			else
				$compte_ide_ko = $compte_ide_ko +1;

			if (@rmdir(JPATH_SITE.'/'.$userpage_list[$i]['foldername'].'/'.$userpage_list[$i]['pagename'])) {
				BSHelperUser::deleteSubdir($userpage_list[$i]['foldername'], BSHelperUser::getRootFoldername()); // Del old subdir if necessary (for complex foldername only)
				$compte_dir_ok = $compte_dir_ok +1;
			} else {
				$compte_dir_ko = $compte_dir_ko +1;
			}
		}

		// $compte_dir_ko & $compte_ide_ko not used any more into the message to make it more simple
		return(array($compte_ide_ko + $compte_dir_ko, JText::sprintf('COM_MYJSPACE_ADMIN_DELETE_FOLDER_1', $compte_dir_ok, $compte_ide_ok, $nb_page)));
	}

// Tools: create (or Recreate after delete) all pages folder and index for each personal pages
	public static function adm_create_folder()
	{
		$pparams = JComponentHelper::getParams('com_myjspace');
		$user_page = New BSHelperUser();
		$userpage_list = BSHelperUser::loadPagename();

		$nb_page = count($userpage_list);
		if ($nb_page <= 0)
			return(array(0, JText::_('COM_MYJSPACE_ADMIN_CREATE_FOLDER_1')));

		$retour_ok = 0;
		for ($i = 0; $i < $nb_page; $i++) {
			$user_page->foldername = $userpage_list[$i]['foldername'];

			if ($user_page->createDirFilePage($userpage_list[$i]['pagename'], $pparams->get('index_pagename_id', 1), $userpage_list[$i]['id']))
				$retour_ok = $retour_ok+1;
		}

		return(array($nb_page-$retour_ok, JText::sprintf('COM_MYJSPACE_ADMIN_CREATE_FOLDER_2', $retour_ok, $nb_page)));
	}

// Tools: delete all empty pages (= content & folders)
	public static function adm_delete_empty_pages()
	{
		$pparams = JComponentHelper::getParams('com_myjspace');
		$link_folder = $pparams->get('link_folder', 1);

		$userpage_list = BSHelperUser::loadPagename(-1, 0, 0, 0, -1); // Page List with empty content

		$nb_page = count($userpage_list);
		if ($nb_page <= 0)
			return(array(0, JText::_('COM_MYJSPACE_ADMIN_CREATE_FOLDER_1')));

		$compte_del_ok = 0;
		$compte_del_ko = 0;
		$user_page = New BSHelperUser();

		for ($i = 0; $i < $nb_page; $i++) {
			$user_page->id = $userpage_list[$i]['id'];
			$user_page->pagename = $userpage_list[$i]['pagename'];
			$user_page->foldername = $userpage_list[$i]['foldername'];

			if ($user_page->deletePage($link_folder, 0)) { // Delete but do not force to delete files
				BSHelperUser::deleteSubdir($user_page->foldername, BSHelperUser::getRootFoldername()); // Del old subdir if necessary (for complex foldername only)
				$compte_del_ok = $compte_del_ok + 1;
			} else {
				$compte_del_ko = $compte_del_ko + 1;
			}
		}

		return(array($compte_del_ko, JText::sprintf('COM_MYJSPACE_ADMIN_DELETE_EMPTY_PAGES_1', $compte_del_ok, $compte_del_ko)));
	}

// Recompute all foldernames and move pages to the new targets
	public static function adm_recompute_foldername()
	{
		$userpage_list = BSHelperUser::loadPagename(); // All pages list
		$nb_page = count($userpage_list);

		if ($nb_page <= 0)
			return 0;

		$user_page = New BSHelperUser();
		$nb_updated = 0;
		$nb_error = 0;
		$error_display = 5; // Display only the 5 first errors ...

		for ($i = 0; $i < $nb_page; $i++) {
			// User info
			$user_page->id = $userpage_list[$i]['id'];
			$user_page->loadPageInfo();
			$pagename_old = $user_page->pagename;
			$user_page->pagename = BSHelperUser::stringURLSafe($user_page->pagename); // In case rules changed, for older BS MyJspace version
			$foldername_old = $user_page->foldername;
			$user_page->setFoldername(); // Recompute foldername
			$foldername_new = $user_page->foldername;

			if ($foldername_new != $foldername_old || $user_page->pagename != $pagename_old ) { // Any change ?
				if (!$user_page->setConfPage()) { // Recalculate & set user page foldername (may change onwer if old one does not exists any more)
					if ($nb_error <= $error_display)
						JFactory::getApplication()->enqueueMessage(JText::_('COM_MYJSPACE_ERRUPDATINGPAGE').' '.$user_page->pagename.' (*)', 'error');
					$nb_error++;
				}

				// Update url & image link (relative & absolute)
				$user_page->content = preg_replace('!src=(.*)'.$foldername_old.'/(.*)'.$pagename_old.'!isU', 'src=$1'.$foldername_new.'/$2'.$user_page->pagename.'', $user_page->content, -1, $nbsrc);
				$user_page->content = preg_replace('!href=(.*)'.$foldername_old.'/(.*)'.$pagename_old.'!isU', 'href=$1'.$foldername_new.'/$2'.$user_page->pagename.'', $user_page->content, -1, $nbhref);

				// Save modified content
				if ($nbsrc + $nbhref) {
					if (!$user_page->updateUserContent()) {
						if ($nb_error <= $error_display)
							JFactory::getApplication()->enqueueMessage(JText::_('COM_MYJSPACE_ERRUPDATINGPAGE').' '.$user_page->pagename.' (**)', 'error');
						$nb_error++;
					}
				}

				// Create new subfolder if necessary (usually when using specific plugin for naming convention)
				BSHelperUser::createSubdir($foldername_new, BSHelperUser::getRootFoldername());

				// Move page sub-folder (can generate error if already ok. But no display with @)
				@rename(JPATH_SITE.'/'.$foldername_old.'/'.$pagename_old, JPATH_SITE.'/'.$foldername_new.'/'.$user_page->pagename);

				// Delete old new subfolder (usually when using specific plugin for naming convention)
				BSHelperUser::deleteSubdir($foldername_old);

				// Nb updated
				$nb_updated++;
			}
		}

		if ($nb_error > $error_display) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_MYJSPACE_ERRUPDATINGPAGE').' = '.$nb_error, 'error');
		}

		return $nb_updated;
	}

// List of model pages
// Return: tab of model, for a specify catid or all if 0
//		$tab[$i]['pagename'] = pagename, $tab[$i]['catid'] = catid, $tab[$i]['type'] = 0 page id, 1 pagename, 3 file
	public static function model_pagename_list($catid = 0)
	{
		$pparams = JComponentHelper::getParams('com_myjspace');
		$model_pagename = $pparams->get('model_pagename', '');
		if ($model_pagename == '')
			return array();
		$model_pagename_tab_init = array_merge(array(JText::_('COM_MYJSPACE_MODELTOBESELECTED')), explode(',', $model_pagename));

		$model_pagename_tab_count = count($model_pagename_tab_init);
		$user_page = New BSHelperUser();

		$model_pagename_tab = array();
		for ($i = 0; $i < $model_pagename_tab_count; $i++) { // Page check and find the name & catid
			$pagename_tmp = array();
			$model_pagename_tab[$i]['pagename'] = '';
			$model_pagename_tab[$i]['text'] = '';
			$model_pagename_tab[$i]['id'] = 0;
			$model_pagename_tab[$i]['catid'] = 0;
			$model_pagename_tab[$i]['category'] = '';
			$model_pagename_tab[$i]['file'] = '';
			$model_pagename_tab[$i]['type'] = 1; // 0 page id (does not exists replaced automatically with 1), 1 pagename, 3 file

			if (strstr($model_pagename_tab_init[$i], ':')) {
				$pagename_tmp = explode(':', $model_pagename_tab_init[$i]);
				$model_pagename_tab[$i]['pagename'] = trim($pagename_tmp[0]);
				if (array_key_exists(1, $pagename_tmp)) {
					$model_pagename_tab[$i]['catid'] = trim($pagename_tmp[1]);
					$model_pagename_tab[$i]['category'] = BSHelperUser::getCategoryLabel($model_pagename_tab[$i]['catid']);
				} else {
					$model_pagename_tab[$i]['catid'] = 0;
				}
			} else {
				$model_pagename_tab[$i]['pagename'] = trim($model_pagename_tab_init[$i]);
			}

			if (intval($model_pagename_tab[$i]['pagename']) == $model_pagename_tab[$i]['pagename'] && intval($model_pagename_tab[$i]['pagename']) != 0) { // number
				$user_page->id = $model_pagename_tab[$i]['pagename'];
				$user_page->loadPageInfoOnly(0);
				$model_pagename_tab[$i]['id'] = $user_page->id;
				$model_pagename_tab[$i]['pagename'] = $user_page->pagename; // Replace the id with the pagename
				$model_pagename_tab[$i]['text'] = $user_page->title;
			} else { // text
				// Check if pagename
				$user_page->id = 0;
				$user_page->pagename = $model_pagename_tab[$i]['pagename'];
				$user_page->loadPageInfoOnly(1);
				if ($user_page->pagename == null) { // Not an existing pagename => file to upload
					if (strncmp($model_pagename_tab[$i]['pagename'], 'http', 4) == 0) // URL
						$model_pagename_tab[$i]['file'] = $model_pagename_tab[$i]['pagename'];
					else if ($i != 0) // page
						$model_pagename_tab[$i]['file'] = JPATH_SITE.'/'.$model_pagename_tab[$i]['pagename'];
					$chaine_tab = explode('.', basename($model_pagename_tab[$i]['pagename']));
					$model_pagename_tab[$i]['pagename'] = $chaine_tab[0];
					$model_pagename_tab[$i]['type'] = 2;
					$model_pagename_tab[$i]['text'] = str_replace('_', ' ', $model_pagename_tab[$i]['pagename']);
				} else { // 'real' page
					$model_pagename_tab[$i]['id'] = $user_page->id;
					$model_pagename_tab[$i]['pagename'] = $user_page->pagename;
					$model_pagename_tab[$i]['text'] = $user_page->title;
				}
			}

			if ($catid == 0 && $model_pagename_tab[$i]['category'] != '') // Add Catid if not catid selection
				$model_pagename_tab[$i]['text'] .= JText::_('COM_MYJSPACE_2POINTS').$model_pagename_tab[$i]['category'];

			if ($i != 0 && $catid != 0) { // If catid filter, keep only the concerned catid
				if (array_key_exists(1, $pagename_tmp)) {
					if (trim($pagename_tmp[1]) == $catid)
						$model_pagename_tab[$i]['pagename'] = $pagename_tmp[0];
					else
						unset($model_pagename_tab[$i]);
				} else
						unset($model_pagename_tab[$i]);
			}
		}

		return $model_pagename_tab;
	}

// Check the validity for the model field option (error & warning)
	public static function model_pagename_valid()
	{
		// Get the models
		$model_page_list = self::model_pagename_list();

		$error = '';
		$warning = '';
		$count_id_non_zero = 0;

		// Checks
		foreach ($model_page_list as $key => $value) {
			if ($key != 0) {
				if ($model_page_list[$key]['catid'] > 0)
					$count_id_non_zero = $count_id_non_zero + 1;

				if ($model_page_list[$key]['category'] == '' && $model_page_list[$key]['catid'] > 0)
					$error .= JText::sprintf('COM_MYJSPACE_ADMIN_MODEL_CATID', $model_page_list[$key]['catid']);

				if ($model_page_list[$key]['type'] == 2 &&strlen(@file_get_contents($model_page_list[$key]['file'])) <= 0)
					$error .= JText::sprintf('COM_MYJSPACE_ADMIN_MODEL_PAGENAME', $model_page_list[$key]['pagename']);
			}
		}

		if ((count($model_page_list)-1) != $count_id_non_zero && $count_id_non_zero > 0)
			$warning .= JText::_('COM_MYJSPACE_ADMIN_MODEL_ERROR1');

		return array($error, $warning);
	}

// Transform the model page name or model page id into page id, if exists. Else keep the name
	public static function model_pagename_id($pagename = 0)
	{
		if (!$pagename)
			return 0;

		if ($pagename < 0)
			return $pagename * -1;

		$model_page_list = self::model_pagename_list();

		foreach ($model_page_list as $key => $value) {
			if ($model_page_list[$key]['pagename'] == $pagename) {
				if ($model_page_list[$key]['type'] == 2)
					return $model_page_list[$key]['file'];
				else
					return $model_page_list[$key]['id'];
			}
		}

		return $pagename; // This is not supposed to be reached
	}

// Check for the most usual mandatory actions to be made for the configuration
	public static function check_mandatory_msg()
	{
		$jinput = JFactory::getApplication()->input;
		$pparams = JComponentHelper::getParams('com_myjspace');

		$link = BSHelperUser::getRootFoldername();

		// Is the page root directory exists and writable ?
		if ($pparams->get('link_folder', 1) == 1 && !BSHelperUser::IsDirW($link))
			JFactory::getApplication()->enqueueMessage(JText::_('COM_MYJSPACE_FOLDERNAME_KO2'), 'error');

		// Is the 'see' menu added ?
		if (BS_Util::get_menu_itemid('index.php?option=com_myjspace&view=see') == 0 && $jinput->get('layout', '', 'STRING') != 'upload')
			JFactory::getApplication()->enqueueMessage(JText::_('COM_MYJSPACE_ATLEASTONEMENUSEE1'), 'notice');
	}

// Check if last BS MyJSpace feature(s) are available
// Check if the component need to be reinstall to have access to all the features available for this component
//		For example if the current BS version was installed into a Joomla version < 3.9.0 the component need to be reinstall to use the User Actions Logs
	public static function check_last_feature()
	{
		if (version_compare(JVERSION, '3.9.0', 'ge')) {
			$db	= JFactory::getDBO();

			// (#__action_logs_extensions) To have BS com_myjspace which appears in the configuration of User Action Logs
			$query = $db->getQuery(true)
				->select('COUNT(*)')
				->from('#__action_logs_extensions')
				->where($db->qn('extension').' = '.$db->q(self::$component));

			$db->setQuery($query);
			$count = $db->loadResult();
			if (!isset($count) || $count == 0)
				return false;

			// (#__action_log_config) To have your actions (create, save, update, delete) to be captured into User Action Logs
			$query = $db->getQuery(true)
				->select('COUNT(*)')
				->from('#__action_log_config')
				->where($db->qn('type_alias').' = '.$db->q(self::$component.'.see').' OR '.$db->qn('type_alias').' = '.$db->q(self::$component.'.media'));

			$db->setQuery($query);
			$count = $db->loadResult();
			if (!isset($count) || $count == 0)
				return false;
		}

		return true;
	}

	// Add an action (from tools ...) into User Actions Log
	public static function user_actions_log_add_task($messageLanguageKey = '', $itemlink = '', $task = '')
	{
		if (version_compare(JVERSION, '3.9.0', 'ge')) {
			$pparams = JComponentHelper::getParams('com_actionlogs');
			$loggable_extensions = $pparams->get('loggable_extensions', array());

			if (in_array('com_myjspace', $loggable_extensions)) { // Log only if the component is selected in com_actionslogs Options
				$user = JFactory::getUser();

				$message = [
					'action'			=> $task,
					'itemlink'			=> $itemlink,
					'username'			=> $user->username,
					'accountlink'		=> 'index.php?option=com_users&task=user.edit&id='.$user->id,
					'extension_name'	=> 'com_myjspace'
				];

				if (version_compare(JVERSION, '3.99.99', 'gt'))
					$model = new ActionlogModel;
				else
					$model = BaseDatabaseModel::getInstance('Actionlog', 'ActionlogsModel');

				$model->addLog([$message], strtoupper($messageLanguageKey), 'com_myjspace.see', $user->id);
			}
		}
	}
}
