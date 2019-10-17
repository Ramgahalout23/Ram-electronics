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

require_once JPATH_COMPONENT_SITE.'/helpers/user.php';

class MyjspaceViewCategories extends JViewLegacy
{
	function display($tpl = null)
	{
		// Config
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$jinput = JFactory::getApplication()->input;

		$option = $jinput->get('option', '', 'STRING');

		if ($params->get('categorie_language', 0)) {
			$lang = JFactory::getLanguage();
			$language = $lang->getTag();
		} else {
			$language = null;
		}

		if ($params->get('categorie_title', 0))
			$this->categories_page_title = JText::_('COM_MYJSPACE_TITLECATEGORIES');
		else
			$this->categories_page_title = '';

		$this->categorie_count = $params->get('categorie_count', 1);

		if ($params->get('categorie_user_filter', 0)) {
			$user = JFactory::getUser();
			$this->userid = $user->id;
		} else {
			$this->userid = 0;
		}

		// Limits & pagination
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
		$total = $this->categories = BSHelperUser::getCategoriesCountPages($this->userid, $language, true);
		$this->pagination = new JPagination($total, $limitstart, $limit);

		$this->categories = BSHelperUser::getCategoriesCountPages($this->userid, $language, false, $this->pagination->limitstart, $this->pagination->limit);

		// Breadcrumbs
		$pathway = $app->getPathway();
		$pathway->addItem(JText::_('COM_MYJSPACE_TITLECATEGORIES'), '');

		parent::display($tpl);
	}
}
