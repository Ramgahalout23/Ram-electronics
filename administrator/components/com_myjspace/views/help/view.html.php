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

class MyjspaceViewHelp extends JViewLegacy
{
	/**
	 * display method of BSbanner view
	 * @return void
	 **/
	function display($tpl = null)
	{
		require_once JPATH_COMPONENT_SITE.'/helpers/user.php';
		require_once JPATH_COMPONENT_SITE.'/helpers/util.php';
		require_once JPATH_COMPONENT_SITE.'/helpers/user_event.php';

		// Config
		$pparams = JComponentHelper::getParams('com_myjspace');
		$file_max_size = (int)$pparams->get('file_max_size', 5242880);
		$editor_selection = $pparams->get('editor_selection', 'tinymce');
		$link_folder = $pparams->get('link_folder', 1);
		$nb_max_page = (int)$pparams->get('nb_max_page', 1);
		$nb_max_page_category = (int)$pparams->get('nb_max_page_category', 0);
		$default_catid = $pparams->get('default_catid', 0);
		$model_pagename = $pparams->get('model_pagename', '');

		// Page root folder
		if (BSHelperUser::IsDirW(BSHelperUser::getRootFoldername()))
			$iswritable = 1;
		else
			$iswritable = 0;

		// Check model page
		list($error_model, $warning_model) = BSUserEvent::model_pagename_valid();

		// Check page index version
		$nb_index_ko = BSHelperUser::checkversionIndexPage();

		// Help configuration report
		$this->report = BS_Util::configuration_report();

		// BS MyJspace config report
		$this->report .= ' [quote]';
		$this->report .= '[b]Editor selection:[/b] '.$editor_selection;
		$this->report .= ' | [b]Index Format:[/b] ';
		if ($nb_index_ko == 0)
			$this->report .= ' ok';
		else
			$this->report .= ' ko';

		$this->report .= ' | [b]Link as folder:[/b] ';			
		if ($link_folder == 1)
			$this->report .= ' yes';
		else
			$this->report .= ' no';

		$this->report .= ' | [b]Root Page dir:[/b] '.BSHelperUser::getRootFoldername();

		$this->report .= ' | [b]Root Page dir writable:[/b] ';
		if ($iswritable == 1)
			$this->report .= ' ok';
		else
			$this->report .= ' ko';

		$this->report .= ' | [b]Max. pages per user:[/b] '.$nb_max_page;

		if ($model_pagename) {
			$this->report .= ' | [b]Model page(s):[/b]'.$model_pagename;
			if ($error_model != '')
				$this->report .= ' | [b]Model page(s) check:[/b]'.$error_model;
		}
		$this->report .= '[/quote][confidential]';
		$this->report .= '[b]Nb. pages:[/b] '.BSHelperUser::myjsp_count_nb_page();
		$this->report .= ' | [b]Nb. users:[/b] '.BSHelperUser::myjsp_count_nb_user();
		$this->report .= '[/confidential]';		

		// GD
		if (function_exists('gd_info'))
			$gd_support = true;
		else
			$gd_support = false;

		// Check the max. page per user (configuration) compared to the real usage
		$nb_max_page_per_user = BSHelperUser::myjsp_max_page_per_user();
		// Check the max. cat per user (configuration) compared to the real usage
		$nb_max_cat_per_user = BSHelperUser::myjsp_max_cat_per_user();

		// --- Display HELP information ---

		$this->data_help = array();
		$l = -1;
		$flag_ok = '<i class="icon-publish"></i> ';
		$flag_ko = '<i class="icon-unpublish"></i> ';	
		$flag_i = '<i class="icon-info"></i> ';

		// file_uploads = On/Off allows to allow or not files upload.
		$l++;
		$this->data_help[$l] = array();
		$this->data_help[$l]['setting'] = 'PHP file_uploads';
		if (ini_get('file_uploads') != 1 && ($pparams->get('uploadimg', 1) || $pparams->get('uploadmedia', 1)))
			$this->data_help[$l]['value'] = $flag_ko.ini_get('file_uploads');
		else
			$this->data_help[$l]['value'] = $flag_ok;

		// upload_max_filesize = 2M. Sets the maximum size allowed for the file. If this limit is exceeded, the server send an error code.
		$l++;
		$this->data_help[$l] = array();
		$this->data_help[$l]['setting'] = 'PHP upload_max_filesize';
		if (BS_Util::convertBytes(ini_get('upload_max_filesize')) < $file_max_size)
			$this->data_help[$l]['value'] = $flag_ko.JText::sprintf('COM_MYJSPACE_ADMIN_UPLOAD_MAXFILESIZE', BS_Util::convertBytes(ini_get('upload_max_filesize')), $file_max_size);
		else
			$this->data_help[$l]['value'] = $flag_ok;

		// post_max_size is the maximum size of data sent by a form. This directive overrides upload_max_filesize, so make sure you have post_max_size greater than upload_max_filesize
		$l++;
		$this->data_help[$l] = array();
		$this->data_help[$l]['setting'] = 'PHP post_max_size';
		if (BS_Util::convertBytes(ini_get('post_max_size')) < $file_max_size) 
			$this->data_help[$l]['value'] = $flag_ko.JText::sprintf('COM_MYJSPACE_ADMIN_UPLOAD_MAXFILESIZE', BS_Util::convertBytes(ini_get('upload_max_filesize')), $file_max_size);
		else
			$this->data_help[$l]['value'] = $flag_ok;

		// --- Display 'others' information ---

		$this->data_others = array();

		// Editor
		$l++;
		$this->data_others[$l] = array();
		$this->data_others[$l]['setting'] = JText::_('COM_MYJSPACE_ADMIN_EDITOR');

		$check_editor = BS_Util::check_editor_selection($editor_selection);
		if ($editor_selection == 'myjsp' && version_compare(JVERSION, '3.5.0', 'ge')) // Check if 'old' editor usage on J!3.6+
			$this->data_others[$l]['value'] = $flag_ko.$editor_selection.'. '.JText::_('COM_MYJSPACE_ADMIN_EDITOR_CHECK');
		else if ($check_editor == false) // Use the Joomla! default editor
			$this->data_others[$l]['value'] = $flag_ko.$editor_selection.'. '.JText::_('COM_MYJSPACE_ADMIN_EDITOR_SELECTION');
		else
			$this->data_others[$l]['value'] = $flag_ok.$flag_i.$editor_selection;

		// Editor upload button plugins enable?
		$l++;
		$this->data_others[$l] = array();
		$this->data_others[$l]['setting'] = JText::_('COM_MYJSPACE_ADMIN_UPLOAD_PLUGIN_LABEL');
		if (!JPluginHelper::isEnabled('editors-xtd', 'uploadmyjspace') || !JPluginHelper::isEnabled('editors-xtd', 'imagemyjspace'))
			$this->data_others[$l]['value'] = $flag_i.JText::_('COM_MYJSPACE_ADMIN_UPLOAD_PLUGIN');
		else
			$this->data_others[$l]['value'] = $flag_ok;

		// GD
		$l++;
		$this->data_others[$l] = array();
		$this->data_others[$l]['setting'] = JText::_('COM_MYJSPACE_ADMIN_GD');
		if ($gd_support == true)
			$this->data_others[$l]['value'] = $flag_ok;
		else
			$this->data_others[$l]['value'] = $flag_ko.JText::_('COM_MYJSPACE_LABELUSAGE4');

		// At least one 'see' view
		$l++;
		$this->data_others[$l] = array();
		$this->data_others[$l]['setting'] = JText::_('COM_MYJSPACE_ATLEASTONEMENUSEE0');
		if (BS_Util::get_menu_itemid('index.php?option=com_myjspace&view=see') != 0)
			$this->data_others[$l]['value'] = $flag_ok;
		else
			$this->data_others[$l]['value'] = $flag_ko.JText::_('COM_MYJSPACE_ATLEASTONEMENUSEE1');

		// Contextual checks

		// Max. page(s) par user (config) compare to real
		if ($nb_max_page_per_user > $nb_max_page) {
			$l++;
			$this->data_others[$l] = array();
			$this->data_others[$l]['setting'] = JText::_('COM_MYJSPACE_NB_MAX_PAGE_PER_USER_LABEL');
			$this->data_others[$l]['value'] = $flag_ko.JText::sprintf('COM_MYJSPACE_NB_MAX_PAGE_PER_USER', $nb_max_page_per_user, $nb_max_page);
		}

		// Max. categories par page(s) par user (config) compare to real
		if ($nb_max_page_category && $nb_max_cat_per_user > $nb_max_page_category) {
			$l++;
			$this->data_others[$l] = array();
			$this->data_others[$l]['setting'] = JText::_('COM_MYJSPACE_NB_MAX_CAT_PER_USER_LABEL');
			$this->data_others[$l]['value'] = $flag_ko.JText::sprintf('COM_MYJSPACE_NB_MAX_CAT_PER_USER', $nb_max_cat_per_user, $nb_max_page_category);
		}

		// Check if default catid still valid (exists) and different than 0
		if (($default_catid) && BSHelperUser::getCategoryLabel($default_catid) == '') {
			$l++;
			$this->data_others[$l] = array();
			$this->data_others[$l]['setting'] = JText::_('COM_MYJSPACE_ADMIN_DEFAULT_CATID_LABEL');
			$this->data_others[$l]['value'] = $flag_ko.JText::_('COM_MYJSPACE_ADMIN_DEFAULT_CATID_NOEXISTS_LABEL');	
		}

		// Pages root folder
		if ($link_folder == 1) {
			$l++;
			$this->data_others[$l] = array();
			// Root folder
			$this->data_others[$l]['setting'] = JText::_('COM_MYJSPACE_ADMIN_FOLDER_LABEL');
			if ($iswritable)
				$this->data_others[$l]['value'] = $flag_ok;
			else
				$this->data_others[$l]['value'] = $flag_ko.JText::_('COM_MYJSPACE_ADMIN_FOLDER_KO');

			// Index
			$l++;
			$this->data_others[$l] = array();
			if ($nb_index_ko >= 0) {
				$this->data_others[$l]['setting'] = str_replace('TOKEN', JSession::getFormToken(), JText::_('COM_MYJSPACE_ADMIN_INDEX_FORMAT_LABEL'));
				if ($nb_index_ko == 0)
					$this->data_others[$l]['value'] = $flag_ok;
				else
					$this->data_others[$l]['value'] = $flag_ko.JText::sprintf('COM_MYJSPACE_ADMIN_INDEX_FORMAT_KO', 'index.php?option=com_myjspace&amp;task=adm_create_folder&amp;'.JSession::getFormToken().'=1');
			}
		}

		// ACL (user.pages) Migration to Myjspace 2.0.0
		$db	= JFactory::getDBO();
		$query = $db->getQuery(true)
			->select('COUNT('.$db->qn('rules').')')
			->from('#__assets')
			->where($db->qn('title')." = 'com_myjspace' AND ".$db->qn('name')." = 'com_myjspace' AND ".$db->qn('rules')." LIKE '%user.pages%'");

		$db->setQuery($query);
		$count = $db->loadResult();

		if ($nb_max_page > 1 && $count == 0) {
			$l++;
			$this->data_others[$l] = array();
			$this->data_others[$l]['setting'] = JText::_('COM_MYJSPACE_ADMIN_ACL_LABEL');
			$this->data_others[$l]['value'] = $flag_ko.JText::_('COM_MYJSPACE_ADMIN_ACL_MSG');
		}

		// Model checks
		if ($error_model != '' || $warning_model != '') {
			$l++;
			$this->data_others[$l] = array();
			$this->data_others[$l]['setting'] = JText::_('COM_MYJSPACE_TITLEMODEL');
			if ($error_model != '')
				$this->data_others[$l]['value'] = $flag_ko.'<span class="myjsp-alert">'.$error_model.'</span>';
			if ($warning_model != '')
				$this->data_others[$l]['value'] = $flag_ko.' <span class="myjsp-warning">'.$warning_model.'</span>';
		}

		// If template(s) usage configurated, the plugin system_myjsptemplateset need to be installed & enabled
		if (trim($pparams->get('template_list', '')) != '' && !JPluginHelper::isEnabled('system', 'myjsptemplateset')) {
			$l++;
			$this->data_others[$l] = array();
			$this->data_others[$l]['setting'] = JText::_('COM_MYJSPACE_PLUGINTEMPLATESET_LABEL');
			$this->data_others[$l]['value'] = $flag_ko.JText::_('COM_MYJSPACE_PLUGINTEMPLATESET');
		}

		// Ensure alias are into lowercase and no _ (old method for oldest BS MyJspace < 2.6.5 using MySQL)
		if (BSHelperUser::countOldAlias()) {
			$l++;
			$this->data_others[$l] = array();
			$this->data_others[$l]['setting'] = JText::_('COM_MYJSPACE_PAGENAME_KO_LABEL');
			$this->data_others[$l]['value'] = $flag_ko.str_replace('TOKEN', JSession::getFormToken(), JText::_('COM_MYJSPACE_PAGENAME_KO'));
			// Warning - backup !
			JFactory::getApplication()->enqueueMessage(JText::_('COM_MYJSPACE_TOOLS_WARNING'), 'warning');
		}

		// If last feature(s) are configured
		if (!BSUserEvent::check_last_feature()) {
			$l++;
			$this->data_others[$l] = array();
			$this->data_others[$l]['setting'] = JText::_('COM_MYJSPACE_LASTFEATURE_LABEL');
			$this->data_others[$l]['value'] = $flag_ko.JText::_('COM_MYJSPACE_LASTFEATURE');
		}

		// Side bar
		if (version_compare(JVERSION, '3.99.99', 'lt'))
			$this->sidebar = JHtmlSidebar::render();

		$this->addToolbar();

		parent::display($tpl);
	}

	function aff_tabinfo($the_tab)
	{
		$k = 0;

		echo '<table class="adminlist table table-striped">'."\n";
		foreach ($the_tab as $value) {
			if (is_array($value)) { // 2 columns
				echo '<tr class="row'.$k.'">'."\n";
				if (isset($value['setting']))
					echo '<td><strong>'.$value['setting']."</strong></td>\n";
				if (isset($value['value']))
					echo '<td>'.$value['value']."</td>\n";
				echo "</tr>\n";
				$k++;
			} else { // 1 column
				echo '<tr class="row'.$k.'">'."\n";
				echo '<td>'.$value."</td>\n";
				echo "</tr>\n";
				$k++;
			}
		}
		echo "</table>\n";
	}

	protected function addToolbar()
	{
		// Menu bar
		JToolBarHelper::title(JText::_('COM_MYJSPACE_HOME').JText::_('COM_MYJSPACE_2POINTS').JText::_('COM_MYJSPACE_HELP'), 'help');

		// To display config Options (on right)
		JToolBarHelper::preferences('com_myjspace');

		// To display Help website (on right)
		require_once JPATH_COMPONENT_SITE.'/helpers/version.php';
		JToolBarHelper::help(JText::_('COM_MYJSPACE_HELP'), false, BS_Helper_version::getXmlParam('com_myjspace', 'authorUrl'));
	}
}
