<?php
/**
* @version $Id: view.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2010 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

require_once JPATH_COMPONENT_SITE.'/helpers/search_fct.php';
require_once JPATH_COMPONENT_SITE.'/helpers/user.php';

class MyjspaceViewPages extends JViewLegacy
{
	function display($tpl = null)
	{
		// Config
		$user = JFactory::getUser();
		$db	= JFactory::getDBO();
		$app = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;

		// Component options
		$pparams = JComponentHelper::getParams('com_myjspace');
		$this->new_page_rview = $pparams->get('new_page_rview', 'config');
		$this->copy_page_rview = $pparams->get('copy_page_rview', 'config');
		$this->nb_max_page = (int)$pparams->get('nb_max_page', 1);
		$share_page = $pparams->get('share_page', 0);

		// View param
		$this->lview = $jinput->get('lview', 'see', 'STRING');
		$this->uid = $jinput->get('uid', $jinput->get('id', 0, 'INT'), 'INT');
		$this->association = $jinput->get('association', '', 'STRING'); // Association language id
		$this->tmpl = $jinput->get('tmpl', '', 'STRING');
		$this->layout = $jinput->get('layout', '', 'STRING');

		// Only 'my' (or uid) pages)
		if ($share_page != 0 && $this->uid <= 0 && $user->id != 0) { // List with shared pages with me
			$extra_query = ' AND (m.userid = '.$db->q($user->id).' OR m.access IN ('.implode(',', $user->getAuthorisedViewLevels()).'))';
			$uid_list = $user->id;
		} else {
			if ($this->uid > 0) {
				$extra_query = ' AND m.userid = '.$this->uid;
				$uid_list = $this->uid;
			} else {
				$extra_query = ' AND m.userid = '.$db->q($user->id);
				$uid_list = $user->id;
			}
		}

		if ($this->association != '') { // For the association pop-up
			$extra_query .= ' AND m.language = '.$db->q($this->association);
			$search_aff_add_default = 1+4+8+512+1024+2048; // Association
			$use_param_search = false; // Change columns is no allowed
		} else {
			$search_aff_add_default = 5805;
			$use_param_search = true;
		}

		// Prepare display (retrieve data). If catid != 0 catid add criteria is added into the query into BSHelperViewSearch::pre_display()
		$this->total = BSHelperViewSearch::pre_display($this, $search_aff_add_default, $use_param_search, 0, 0, 0, array(), 0, 0, $extra_query, 0);

		// User name for title info
		$table = JUser::getTable();
		if ($table->load($uid_list)) { // Test if user exists before to retrieve info
			$this->user_title = JFactory::getUser($uid_list);
		} else { // User no longer exists !
			$this->user_title = new stdClass();
			$this->user_title->id = 0;
			$this->user_title->username = ' '; // '' to do NOT display a page with no user
			$this->user_title->name = '';
		}

		// Current user
		$this->current_user = $user;

		// Number of pages for the category, for current user : limit reach ?
		$this->user_limit_page_this_cat_reached = false;			
		if ($this->current_user->id && $pparams->get('nb_max_page_category', 0)) {
			$user_page = New BSHelperUser();
			$user_page->userid = $this->current_user->id;

			if ($user_page->countUserPageCategory($this->catid) >= $pparams->get('nb_max_page_category', 0))
				$this->user_limit_page_this_cat_reached = true;
		}

		// Breadcrumbs
		if ($this->lview == 'config')
			$sub_title = JText::_('COM_MYJSPACE_TITLECONFIG1');
		else if ($this->lview == 'edit')
			$sub_title = JText::_('COM_MYJSPACE_TITLEEDIT1');
		else if ($this->lview == 'delete')
			$sub_title = JText::_('COM_MYJSPACE_DELETE');
		else
			$sub_title = JText::_('COM_MYJSPACE_TITLESEE1');

		$pathway = $app->getPathway();
		$pathway->addItem($sub_title, '');

		parent::display($tpl);
	}

	// Transform the page data to data to be displayed (options dependent)
	protected function transform_fields($inst, $i = 0, $separ = 0, $lview = 'see') {
		return BSHelperViewSearch::transform_fields($inst, $i, $separ, $lview);
	}
}
