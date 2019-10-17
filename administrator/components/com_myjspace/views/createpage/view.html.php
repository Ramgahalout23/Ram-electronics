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

require_once JPATH_COMPONENT_SITE.'/helpers/user_event.php';

class MyjspaceViewCreatepage extends JViewLegacy
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$pparams = JComponentHelper::getParams('com_myjspace');

		// Test prerequisite to create ! This is a precaution
		if ($pparams->get('link_folder', 1) == 1 && !BSHelperUser::IsDirW(BSHelperUser::getRootFoldername())) { // No 'myjsp' directory ?
			$app->redirect(JRoute::_('index.php?option=com_myjspace&view=url', false)); // Go to create the missing directory
			return;
		}

		$this->name_page_size_max = $pparams->get('name_page_size_max', 20);

		// Model page list
		$this->model_page_list = BSUserEvent::model_pagename_list();

		// Categories
		$this->default_catid = $pparams->get('default_catid', 0);
		$this->categories = BSHelperUser::getCategories(1);
		if (count($this->categories)) {
			$new[0] = array('value' => 0, 'text' => '-', 'level' => 0, 'published' => 1); // Add for 'no category' to be set by the admin
			$this->categories = array_merge($new, $this->categories);
		}

		// Side bar
		if (version_compare(JVERSION, '3.99.99', 'lt'))
			$this->sidebar = JHtmlSidebar::render();

		$this->addToolbar();

		parent::display($tpl);
	}

	protected function addToolbar()
	{
		// Menu bar
		JToolBarHelper::title(JText::_('COM_MYJSPACE_HOME').JText::_('COM_MYJSPACE_2POINTS').JText::_('COM_MYJSPACE_CREATEPAGE'), 'pencil-2');

		JToolBarHelper::apply('adm_create_page');	
		JToolbarHelper::cancel('page_cancel');

		// To display config Options (on right)
		JToolBarHelper::preferences('com_myjspace');

		// To display Help website (on right)
		require_once JPATH_COMPONENT_SITE.'/helpers/version.php';
		JToolBarHelper::help(JText::_('COM_MYJSPACE_HELP'), false, BS_Helper_version::getXmlParam('com_myjspace', 'authorUrl'));
	}
}
