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

/**
 * HTML View pour la page 
 * @package myjspace
 */

class MyjspaceViewMyjspace extends JViewLegacy
{
	function display($tpl = null)
	{
		require_once JPATH_COMPONENT_SITE.'/helpers/version.php';
		require_once JPATH_COMPONENT_SITE.'/helpers/user.php';

		// Content to display
		$this->version_information = BS_Helper_version::get_information('com_myjspace.manage');

		// New version ?
		$pparams = JComponentHelper::getParams('com_myjspace');
		$check_version =  $pparams->get('check_version', '0.0.0');
		$current_version = BS_Helper_version::getXmlParam('com_myjspace', 'version');

		if (version_compare($check_version, $current_version, 'gt'))
			$this->version_new = $check_version;
		else
			$this->version_new = '';

		// Nb pages
		$this->nb_pages_total = BSHelperUser::myjsp_count_nb_page();

		// Nb distinct users
		$this->nb_distinct_users = BSHelperUser::myjsp_count_nb_user();

		// Side bar
		if (version_compare(JVERSION, '3.99.99', 'lt'))
			$this->sidebar = JHtmlSidebar::render();

		$this->addToolbar();

		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		// Menu bar
		JToolBarHelper::title(JText::_('COM_MYJSPACE_HOME'), 'info-2');

		// To display config Options (on right)
		JToolBarHelper::preferences('com_myjspace');

		// To display Help website (on right)
		require_once JPATH_COMPONENT_SITE.'/helpers/version.php';
		JToolBarHelper::help(JText::_('COM_MYJSPACE_HELP'), false, BS_Helper_version::getXmlParam('com_myjspace', 'authorUrl'));
	}
}
