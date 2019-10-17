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

class MyjspaceViewPage extends JViewLegacy
{
	function display($tpl = null)
	{
		require_once JPATH_COMPONENT_SITE.'/helpers/user.php';
		require_once JPATH_COMPONENT_SITE.'/helpers/util.php';
		require_once JPATH_COMPONENT_SITE.'/helpers/util_acl.php';

		// Config
		$pparams = JComponentHelper::getParams('com_myjspace');
		$app = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$db	= JFactory::getDBO();
		$nullDate = $db->getNullDate();

		$this->mjs_editable = 'mjs_editable';
		$this->e_name = $jinput->get('e_name', 'mjs_editable', 'STRING');

		$this->link_folder = $pparams->get('link_folder', 1);
		$link_folder_print = $pparams->get('link_folder_print', 0);
		$language_filter = $pparams->get('language_filter', 0);
		$this->default_catid = $pparams->get('default_catid', 0);
		$this->name_page_size_max = $pparams->get('name_page_size_max', 20);

		// Language
		if ($language_filter > 0)
			$this->language_list = BSHelperUser::get_language_list();
		else
			$this->language_list = array();

		// Upload layout
		$this->type = $jinput->get('type', 'undefined', 'STRING'); // media, image, file, undefined
		$layout = $jinput->get('layout', '', 'STRING');

		$id = $jinput->get('id', -1, 'INT');
		if ($id < 0) {	
			$pageid_tab = $jinput->get('cid', array(0), 'ARRAY');
			$id = (is_array($pageid_tab) && isset($pageid_tab[0])) ? intval($pageid_tab[0]) : 0;	
			// Redirect to have complete url displayed, no obligation but better for Options usage
			$app->redirect(JRoute::_('index.php?option=com_myjspace&view=page&id='.$id, false));	
			return;
		}

		// Personal page info
		$this->user_page = New BSHelperUser();
		$this->user_page->id = $id;		
		$this->user_page->loadPageInfo();

		if ($this->user_page->id <= 0) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_MYJSPACE_NOTVALIDPAGENAME'), 'warning');
			$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages', false));	
			return;
		}

		// User (for page) info
		$table = JUser::getTable();
		if ($table->load($this->user_page->userid)) { // Test if user exists before to retrieve info
			$this->user = JFactory::getUser($this->user_page->userid);
		} else { // User no longer exists !
			$this->user = new stdClass();
			$this->user->id = -1;
			$this->user->username = ' '; // '' to do NOT display a page with no user
			$this->user->name = '';
		}

		// Last update name
		if ($table->load($this->user_page->modified_by)) { // Test if user exists before to retrieve info
			$this->modified_by = JFactory::getUser($this->user_page->modified_by);
		} else { // User no longer exists !
			$this->modified_by = new stdClass();
			$this->modified_by->id = -1;
			$this->modified_by->username = ' ';
			$this->modified_by->name = '';
		}

		// Page link
		if ($this->user_page->pagename != '') {
			if ($link_folder_print == 1)
				$this->link = JURI::base().$this->user_page->foldername.'/'.$this->user_page->pagename;
			else
				$this->link = str_replace(JURI::base(true).'/', '', JURI::base()).JRoute::_('index.php?option=com_myjspace&view=see&pagename='.$this->user_page->pagename, false);
		} else 
			$this->link = null;
		$this->link = str_replace('/administrator', '', $this->link); 

		// Editor selection
		$editor_selection = $pparams->get('editor_selection', 'tinymce');
		if (BS_Util::check_editor_selection($editor_selection) == false || $editor_selection == '-') // Use the Joomla! default editor
			$editor_selection = null;

		// Editor 'windows' size
		$this->edit_x = $pparams->get('admin_edit_x', '100%');
		$this->edit_y = $pparams->get('admin_edit_y', '400px');

		// Editor buttons to hide
		if ($pparams->get('allow_editor_button', 1) == 1) {
			$this->editor_button = array('readmore', 'article', 'image', 'pagebreak', 'contact', 'menu', 'module');
		} else {
			$this->editor_button = false;
		}

		$this->uploadimg = $pparams->get('uploadimg', 1);
		$this->uploadmedia = $pparams->get('uploadmedia', 1);
		$this->publish_mode = $pparams->get('publish_mode', 2);
		$this->downloadimg = $pparams->get('downloadimg', 1);
		$this->file_max_size = (int)$pparams->get('file_max_size', 5242880);

		// Files upload
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
		$this->resize_x = (int)$pparams->get('resize_x', 800);
		$this->resize_y = (int)$pparams->get('resize_y', 600);
		if ($this->resize_x != 0 || $this->resize_y != 0) {
			if ($this->resize_x == 0)
				$this->resize_x = '&#8734';
			if ($this->resize_y == 0)
				$this->resize_y = '&#8734';
			if (function_exists('gd_info'))
				$this->file_max_size_txt .= JText::sprintf('COM_MYJSPACE_LABELUSAGE3', $this->resize_x, $this->resize_y);
			else
				$this->file_max_size_txt .= JText::_('COM_MYJSPACE_LABELUSAGE4');
		}

		// Files list
		$this->tab_list_file = null;
		if ($this->type == 'image')
			$allowed_types = array('png', 'jpg', 'gif');
		else
			$allowed_types = array('*');
		if ($this->uploadimg > 0 || $this->uploadmedia > 0)
			$this->tab_list_file = BS_Util::list_file_dir(JPATH_SITE.'/'.$this->user_page->foldername.'/'.$this->user_page->pagename, $allowed_types, 1);

		// Dates check if not set with interesting date
		$this->user_page->create_date = BS_Util::html_date_empty($this->user_page->create_date, JText::_('COM_MYJSPACE_DATE_FORMAT'));
		$this->user_page->last_update_date = BS_Util::html_date_empty($this->user_page->last_update_date, JText::_('COM_MYJSPACE_DATE_FORMAT'));
		$this->user_page->last_access_date = BS_Util::html_date_empty($this->user_page->last_access_date, JText::_('COM_MYJSPACE_DATE_FORMAT'));

		// Lock or not
		$lock_img = JURI::base().'components/com_myjspace/images/checked_out.png';
		$lock_img = str_replace('/administrator', '', $lock_img);
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

		// Automatic configuration :-)
		if ($this->user_page->pagename == '' || $this->link_folder == 0) {
			$this->uploadimg = 0;
			$this->uploadmedia = 0;
		}

		// Uploaded file
		$this->uploaded = $jinput->get('uploaded', '', 'STRING');

		// Categories
		$this->categories = BSHelperUser::getCategories(1, $this->user_page->language);
		if (count($this->categories)) {
			$new[0] = array('value' => 0, 'text' => '-', 'level' => 0, 'published' => 1); // Add for 'no category' to be set by the admin
			$this->categories = array_merge($new, $this->categories);

			if ($this->user_page->catid == 0 && $layout == '')
				$app->enqueueMessage(JText::_('COM_MYJSPACE_NOCATEGORY'), 'warning');
		}

		// Share edit
		if ($pparams->get('share_page', 0) != 0)
			$this->group_list = JHtml::_('access.assetgroups');
		else
			$this->group_list = null;

		// Blockview list
		$this->blockview_list = BS_UtilAcl::get_assetgroup_list();

		// Users access list => convert id to username
		$this->user_mode_view_userread = $pparams->get('user_mode_view_userread', 0);	
		$this->user_page->userread_name = '';
		if ($this->user_mode_view_userread && $this->user_page->userread != '') {
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

		// Association
		$this->associations = array();
		$count_association = 0;
		if ($language_filter == 2 && JLanguageAssociations::isEnabled() == 1) {
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

		// Editor for 'upload' layout
		$this->editor_selection = $pparams->get('editor_selection', 'tinymce');
		if (BS_Util::check_editor_selection($this->editor_selection) == false || $this->editor_selection == '-') // Use the Joomla! default editor
			$this->editor_selection = $app->get('editor');

		// Drag & drop url for task call
		$this->drag_drop_upload = "\nvar urlupload = 'index.php?option=com_myjspace&view=page&task=upload_file&tmpl=component&upload_id=".$this->user_page->id.'&'.JSession::getFormToken()."=1';\n";

		// Side bar
		if (version_compare(JVERSION, '3.99.99', 'lt'))
			$this->sidebar = JHtmlSidebar::render();

		if ($layout == 'tags') { // Tags buttons
			$this->e_name = $jinput->get('e_name', 'mjs_editable', 'STRING');
			$this->allow_tag_myjsp_iframe = $pparams->get('allow_tag_myjsp_iframe', 1);
			$this->allow_tag_myjsp_include = $pparams->get('allow_tag_myjsp_include', 1);
			$this->share_page = $pparams->get('share_page', 0);
			$this->language_filter = $pparams->get('language_filter', 0);
			$this->show_tags = $pparams->get('show_tags', 1); // J!3.1.4+
		}

		$this->addToolbar();

		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		// Menu bar
		JToolBarHelper::title(JText::_('COM_MYJSPACE_HOME').JText::_('COM_MYJSPACE_2POINTS').JText::_('COM_MYJSPACE_PAGE'), 'pencil-2');

		JToolBarHelper::apply('adm_save_page_all');
		JToolBarHelper::apply('adm_save_page', JText::_('COM_MYJSPACE_SAVE_DETAILS'));
		JToolBarHelper::apply('adm_save_page_content', JText::_('COM_MYJSPACE_SAVE_PAGE'));
		JToolbarHelper::cancel('page_cancel');

		// To display config Options (on right)
		JToolBarHelper::preferences('com_myjspace');

		// To display Help website (on right)
		require_once JPATH_COMPONENT_SITE.'/helpers/version.php';
		JToolBarHelper::help(JText::_('COM_MYJSPACE_HELP'), false, BS_Helper_version::getXmlParam('com_myjspace', 'authorUrl'));
	}
}
