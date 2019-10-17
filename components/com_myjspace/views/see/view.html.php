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

class MyjspaceViewSee extends JViewLegacy
{
	function display($tpl = null)
	{
		require_once JPATH_COMPONENT_SITE.'/helpers/user.php';
		require_once JPATH_COMPONENT_SITE.'/helpers/util.php';
		require_once JPATH_COMPONENT_SITE.'/helpers/util_acl.php';

		$this->access_ok = true;

		// Config
		$pparams = JComponentHelper::getParams('com_myjspace');
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$jinput = JFactory::getApplication()->input;
		$db	= JFactory::getDBO();
		$nullDate = $db->getNullDate();

		$params = $app->getParams(); // Use param ?
		$catid = $jinput->get('catid', $params->get('catid', 0), 'INT');

		$page_provided = true;

		if ($jinput->get('id', 0, 'INT') == 0 && $jinput->get('pagename', '', 'STRING') == '') { // May be pagename or id from the Menu Option
			$jinput->set('id', $params->get('id', 0)); // If set from 'id' (Menu)
			$jinput->set('pagename', $params->get('pagename', '')); // If set from 'pagename' (Menu) for compabitibility BS MyJspace < 3.0.0
			$page_provided = false;
		}

		// Params
		$id = $jinput->get('id', 0, 'INT');

		if ($id == 0) { // Call from 'pages' view & page select
			$id = $jinput->get('cid', 0, 'INT');
		}

		$return = $jinput->get('return', '', 'STRING');
		$icon = $jinput->get('icon', 1, 'INT');

		// User info
		$user_actual = JFactory::getUser();
		$this->user_page = New BSHelperUser();

		// Criteria: user info or pagename
		$pagename = $jinput->get('pagename', '', 'STRING');

		if ($id <= 0 && $pagename == '') { // No page => redirect to pages list
			// Page id - check
			$list_page_tab = $this->user_page->getListPageId($user_actual->id, $id, $catid);
			$nb_page = count($list_page_tab);
			if ($nb_page == 1) {
				$id = $list_page_tab[0]['id'];
			} else if ($nb_page > 1) {
				$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages', false)); // Default lview=see
				return;
			}
		}

		// Personal page info
		$this->user_page->id = $id;
		if ($pagename != '') {
			$this->user_page->pagename = $pagename;
			$this->user_page->loadPageInfo(1);
		} else {
			$this->user_page->loadPageInfo();
		}

		// User (for page) info
		$table = JUser::getTable();
		if ($table->load($this->user_page->userid)) { // Test if user exists before to retrieve info
			$user = JFactory::getUser($this->user_page->userid);
		} else { // User no longer exists !
			$user = new stdClass();
			$user->id = 0;
			$user->username = ' '; // '' to do NOT display a page with no user
			$user->name = '';
			$this->access_ok = false;
		}

		// Increment hits, if : not empty, not the owner, no block ... :-)
		$this->allow_plugin = $pparams->get('allow_plugin', 1);
		$page_increment = $pparams->get('page_increment', 1);
		if ($page_increment == 1 && $user_actual->id != $this->user_page->userid && $this->user_page->content != null && ($this->user_page->blockview == 1 || ($this->user_page->blockview == 2 && $user_actual->username != '')))
			$this->user_page->updateLastAccess(hash('crc32b', BS_Util::addr_ip()));

		// Joomla! Tags
		$this->contenu = new stdClass();
		$this->jtag = null;
		if ($pparams->get('show_tags', 1) == 1) {
			$this->contenu->tagLayout = new JLayoutFile('joomla.content.tags');
			$this->contenu->tags = new JHelperTags;
			$this->contenu->tags->getItemTags('com_myjspace.see', $this->user_page->id);
			if (!empty($this->contenu->tags))
				$this->jtag = $this->contenu->tagLayout->render($this->contenu->tags->itemTags);
		}

		// Content
		$uploadimg = $pparams->get('uploadimg', 1);
		$tag_mysp_file_separ = $pparams->get('tag_mysp_file_separ', ' ');
		$chaine_files = '';
		if ($uploadimg == 1) { // May be add optional in the future
			$tab_list_file = BS_Util::list_file_dir(JPATH_SITE.'/'.$this->user_page->foldername.'/'.$this->user_page->pagename, '*', 1);
			$nb = count($tab_list_file);
			for ($i = 0 ; $i < $nb ; ++$i)
				$chaine_files .= '<a href="'.JURI::base().$this->user_page->foldername.'/'.$this->user_page->pagename.'/'.$tab_list_file[$i].'">'.$tab_list_file[$i].'</a>'.$tag_mysp_file_separ;
		}

		if ($pparams->get('allow_user_content_var', 1))
			$content = $this->user_page->traite_prefsuf($this->user_page->content, $user, $page_increment, JText::_('COM_MYJSPACE_DATE_FORMAT'), $chaine_files, false, $this->jtag);
		else
			$content = $this->user_page->content;

		// [register]
		if ($pparams->get('editor_bbcode_register', 0) == 1 && strstr($content, '[register')) { // Allow to use the dynamic tag [register]
			$uri = JURI::getInstance();
			$return_here = $uri->toString();

			if ($pparams->get('url_login_redirect', ''))
				$url = $pparams->get('url_login_redirect', '');
			else
				$url = 'index.php?option=com_users&view=login';
			$url .= '&return='.base64_encode($return_here); // To return to the call page
			$url = JRoute::_($url, false);

			if ($user_actual->id != 0) // If registered
				$content = preg_replace('!\[register\](.+)\[/register\]!isU', '$1', $content);
			else // If not registered
				$content = preg_replace('!\[register\](.+)\[/register\]!isU', JText::sprintf('COM_MYJSPACE_REGISTER', $url), $content);

			if (preg_last_error() == PREG_BACKTRACK_LIMIT_ERROR)
				JFactory::getApplication()->enqueueMessage(JText::_('COM_MYJSPACE_ERROBBCODE'), 'notice');
		}

		// Force default dates
		if ($pparams->get('publish_mode', 2) == 0) { // Do not take into account the dates
			$this->user_page->publish_up = $nullDate;
			$this->user_page->publish_down = $nullDate;
		}
		if ($this->user_page->publish_down == $nullDate)
			$this->user_page->publish_down = date('Y-m-d 00:00:00', strtotime('+1 day'));

		// Specific context
		$prefix = '';
		$suffix = '';
		$aujourdhui = time();

		// If ACL filter for display this page
		$user_mode_view_acl = $pparams->get('user_mode_view_acl', 0);
		if ($user_mode_view_acl == 1)
			$access = $user_actual->getAuthorisedViewLevels();
		else
			$access = array();

		// If the current user is into the users list granted to display this page
		$user_grant = false;

		// If current user is the page owner
		if ($user_actual->id == $this->user_page->userid) {
			$user_grant = true;
		}

		if ($pparams->get('user_mode_view_userread', 0) && $this->user_page->userread) {
			$userread = json_decode($this->user_page->userread, true);

			if (in_array($user_actual->id, $userread))
				$user_grant = true;
		}

		if ($this->user_page->id == 0) {
			if ($page_provided == false && $user_actual->id)
				$content = JText::_('COM_MYJSPACE_PAGENOTFOUND_CREATE');
			else
				$content = JText::_('COM_MYJSPACE_PAGENOTFOUND');
			$this->allow_plugin = 0;
			$this->access_ok = false;
			$app->setHeader('Status', '404 Not Found', 'true');
		} else if ($this->user_page->blockview == 0 && $user_actual->id != $this->user_page->userid && !$user_grant) {
			$content = JText::_('COM_MYJSPACE_NOTALLOWED');
			$this->allow_plugin = 0;
			$this->access_ok = false;
			$app->setHeader('Status', '403 Forbidden', 'true');
		} else if ($this->user_page->blockview >= 2 && (($user_mode_view_acl == 0 && $user_actual->id <= 0) || ($user_mode_view_acl == 1 && !in_array($this->user_page->blockview, $access) && !$user_grant))) {
			$content = JText::sprintf('COM_MYJSPACE_PAGERESERVED', BS_UtilAcl::get_assetgroup_label($this->user_page->blockview));
			$this->allow_plugin = 0;
			$this->access_ok = false;
			$app->setHeader('Status', '403 Forbidden', 'true');
		} else if ($this->user_page->content == null) {
			$content = JText::_('COM_MYJSPACE_PAGEEMPTY');
			$this->allow_plugin = 0;
		} else if ((strtotime($this->user_page->publish_up) > $aujourdhui || strtotime($this->user_page->publish_down) <= $aujourdhui) && ($user_actual->id != $this->user_page->userid || $pparams->get('publish_mode', 2) == 1)) {
			$content = JText::_('COM_MYJSPACE_PAGEUNPLUBLISHED');
			$this->allow_plugin = 0;
			$this->access_ok = false;
			$app->setHeader('Status', '410 Gone', 'true');
		} else {
			// Top and bottom
			if ($pparams->get('page_prefix', ''))
				$prefix = '<span class="top_myjspace">'.$this->user_page->traite_prefsuf($pparams->get('page_prefix', ''), $user, $page_increment, JText::_('COM_MYJSPACE_DATE_FORMAT'), $chaine_files, true, $this->jtag).'</span><br />';

			$page_suffix = $pparams->get('page_suffix', '#bsmyjspace');
			if ($page_suffix)
				$suffix = '<span class="bottom_myjspace">'.$this->user_page->traite_prefsuf($page_suffix, $user, $page_increment, JText::_('COM_MYJSPACE_DATE_FORMAT'), $chaine_files, true, $this->jtag).'</span><br />';
		}
		$content = '<div class="myjspace-prefix">'.$prefix.'</div><div class="myjspace-content"></div>'.$content.'<div class="myjspace-suffix">'.$suffix.'</div>';

		// Set the pagename, for the case if the view page is call using id (to help for better Search engine ref with SEO in some cases: pagebreak ...)
		$jinput->set('pagename', $this->user_page->pagename);
		$jinput->set('id', '');

		// Lightbox usage
		$this->add_lightbox = $pparams->get('add_lightbox', 1);

		// Process the prepare content for plugins
		$this->contenu->text = $content;
		$this->contenu->toc = '';
		if ($pparams->get('pagetitle', 1)) { // Browser page title
			$this->contenu->page_title = JText::sprintf('COM_MYJSPACE_PAGETITLE', $document->getTitle(), $this->user_page->title);
		}
		$this->contenu->metadesc = $this->user_page->metakey; // HTML description

		// HTML content
		if ($pparams->get('pageauthor', 1) == 1) { // Meta data: author
			if ($app->get('MetaAuthor', 1) == '1')
				$document->setMetaData('author', $user->name);
		}

		if ($this->allow_plugin >= 1) { // Content plugin usage for the page content
			JPluginHelper::importPlugin('content');

			$this->contenu->id = $this->user_page->id; // To have a 'false' article id (can identify all page as same J! article ...)
			$this->contenu->catid = $this->user_page->catid;
			$this->contenu->title = $this->user_page->title;
			$this->contenu->alias = $this->user_page->pagename;
			$this->contenu->introtext = $content; // introtext = text
			$this->contenu->created_by = $this->user_page->userid; // Author id
			$this->contenu->publish_up = $this->user_page->publish_up;
			$this->contenu->publish_down = $this->user_page->publish_down;
			$this->contenu->created = $this->user_page->create_date;
			$this->contenu->modified = $this->user_page->last_update_date;
			$this->contenu->hits = $this->user_page->hits;
			$this->contenu->category_title = BSHelperUser::getCategoryLabel($this->user_page->catid);
			if ($this->user_page->blockview == 0)
				$this->contenu->state = 0;
			else
				$this->contenu->state = 1;

			$params = clone($app->getParams('com_content')); // To have all (false) 'regular content' default data if a plugin for content call it
			$limitstart = $jinput->get('limitstart', 0, 'INT');

			JFactory::getApplication()->triggerEvent('onContentPrepare', array('com_content.myjspace', &$this->contenu, &$params, &$limitstart, 0));

			if ($this->allow_plugin > 1) { // BS MyJspace 1.6+
				$this->contenu->event = new stdClass();
				$results = JFactory::getApplication()->triggerEvent('onContentAfterTitle', array('com_content.myjspace', &$this->contenu, &$params, &$limitstart, 0));
				$this->contenu->event->afterDisplayTitle = trim(implode("\n", $results));

				$results = JFactory::getApplication()->triggerEvent('onContentBeforeDisplay', array('com_content.myjspace', &$this->contenu, &$params, &$limitstart, 0));
				$this->contenu->event->beforeDisplayContent = trim(implode("\n", $results));

				$results = JFactory::getApplication()->triggerEvent('onContentAfterDisplay', array('com_content.myjspace', &$this->contenu, &$params, &$limitstart, 0));
				$this->contenu->event->afterDisplayContent = trim(implode("\n", $results));
			}
		}

		if ($this->contenu->page_title) // Browser page title
			$document->setTitle($this->contenu->page_title);

		if ($this->contenu->metadesc) // Description
			$document->setDescription($this->contenu->metadesc);

		// Page template specific
		if ($this->user_page->template) { // J!3.2.0+
			if (isset($_COOKIE['mjspTemplateSet']))
				$mjspTemplateSet = htmlspecialchars($_COOKIE['mjspTemplateSet']);
			else
				$mjspTemplateSet = '';

			if ($mjspTemplateSet != $this->user_page->template) {
				setcookie('mjspTemplateSet', $this->user_page->template, time()+86400, '/');
				$app->redirect(JRoute::_('index.php?option=com_myjspace&view=see&pagename='.$this->user_page->pagename, false));
				return;
			}
		} else if (isset($_COOKIE["mjspTemplateSet"])) { // 'Workaround' if the previous page have a template setted (out of default)
			setcookie('mjspTemplateSet', '');
			$app->redirect(JRoute::_('index.php?option=com_myjspace&view=see&pagename='.$this->user_page->pagename, false));
			return;
		}

		// Breadcrumbs
		$pathway = $app->getPathway();
		$pathway->addItem($this->user_page->title, '');

		// Background image
		$file_background = $this->user_page->foldername.'/'.$this->user_page->pagename.'/background.jpg';
		if (@file_exists($file_background))
			$this->css_background = "background-image:url('".$file_background."');";
		else
			$this->css_background = '';

		// Edit link icon
		$this->edit_icon = null;
		$icon_edit_view_see = $pparams->get('icon_edit_view_see', 2);
		if ($icon == 1 && $icon_edit_view_see > 0 && $user_actual->id != 0 && ($user_actual->id == $this->user_page->userid || $this->user_page->id == 0)) { // user connected & page owner
			$title_edit = JText::_('COM_MYJSPACE_TITLEEDIT1');
			$title_config = JText::_('COM_MYJSPACE_TITLECONFIG1');
			$return_url = '';
			$edit_icon_edit = '';
			$edit_icon_config = '';
			if ($return != '')
				$return_url = '&return='.$return;
			if ($catid != 0) // Catid url
				$catid_url = '&catid='.$catid;
			else
				$catid_url = '';
			$url_edit = JRoute::_('index.php?option=com_myjspace&view=edit&id='.$this->user_page->id.'&pagename='.$this->user_page->pagename.$return_url.$catid_url, false);
			$url_config = JRoute::_('index.php?option=com_myjspace&view=config&id='.$this->user_page->id.'&pagename='.$this->user_page->pagename.$return_url.$catid_url, false);

			if ($icon_edit_view_see == 2) { // J!1.6, 1.7, 2.5, 3.0 look ... or forced
				$url_icon_edit = 'components/com_myjspace/images/icon-16-edit.png';
				$url_icon_config = 'components/com_myjspace/images/icon-16-config.png';

				if (JFactory::getUser()->authorise('user.edit', 'com_myjspace') && $this->user_page->blockedit == 0)
					$edit_icon_edit	= "<span class=\"mjp-config\" title=\"$title_edit\"><a class=\"btn btn-secondary\" href=\"$url_edit\" ><img src=\"$url_icon_edit\" alt=\"edit\" /></a></span>";

				if (JFactory::getUser()->authorise('user.config', 'com_myjspace') && $this->user_page->blockedit != 2)
					$edit_icon_config = "<span class=\"mjp-config\" title=\"$title_config\"><a class=\"btn btn-secondary\" href=\"$url_config\" ><img src=\"$url_icon_config\" alt=\"config\" /></a></span>";

				if ($edit_icon_edit != '' || $edit_icon_config != '')
					$this->edit_icon = "
					<ul class=\"actions\">
						<li class=\"edit-iconX\">
							$edit_icon_edit
							$edit_icon_config
						</li>
					</ul>
					";
			} else { // >= J!3.5 look
				if (JFactory::getUser()->authorise('user.edit', 'com_myjspace') && $this->user_page->blockedit == 0)
					$edit_icon_edit = "<li class=\"edit-icon\"><a href=\"$url_edit\" ><span class=\"hasTip icon-edit tip\" title=\"$title_edit\"></span>&#160;$title_edit&#160;</a></li>";

				if (JFactory::getUser()->authorise('user.config', 'com_myjspace') && $this->user_page->blockedit != 2)
					$edit_icon_config = "<li class=\"edit-options\"><a href=\"$url_config\" ><span class=\"hasTip icon-options tip\" title=\"$title_config\"></span>&#160;$title_config&#160;</a></li>";

				if ($edit_icon_edit != '' || $edit_icon_config != '')
					$this->edit_icon = "
					<div class=\"btn-group pull-right\">
						<a class=\"btn btn-secondary dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\"> <span class=\"icon-cog\"></span> <span class=\"caret\"></span> </a>
							<div class=\"dropdown-menu dropdown-menu-right\">
								$edit_icon_edit
								$edit_icon_config
							</div>
					</div>
					";
			}
		}

		parent::display($tpl);
	}
}
