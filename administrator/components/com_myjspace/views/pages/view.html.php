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

class MyjspaceViewPages extends JViewLegacy
{
	function display($tpl = null)
	{
		require_once JPATH_COMPONENT_SITE.'/helpers/user.php';
		require_once JPATH_COMPONENT_SITE.'/helpers/util_acl.php';

		$db	= JFactory::getDBO();
		$app = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
	
		$option = $jinput->get('option', '', 'STRING');

		$this->association = $jinput->get('association', '', 'STRING');
		$this->layout = $jinput->get('layout', '', 'STRING');
		$this->tmpl = $jinput->get('tmpl', '', 'STRING');
		$this->modal_fct = $jinput->get('modal_fct', 'jSelectMyjsp_jform_modal', 'STRING');

		$pparams = JComponentHelper::getParams('com_myjspace');
		$this->share_page = $pparams->get('share_page', 0);

		// Language & Association
		$this->language_filter = $pparams->get('language_filter', 0);
		if ($this->language_filter == 2 && JLanguageAssociations::isEnabled() == 0)
			$this->language_filter = 1;

		$this->languages = JLanguageHelper::getLanguages('lang_code'); // Languages list

		$filter_order = $app->getUserStateFromRequest("$option.filter_order", 'filter_order', 'a.title', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest("$option.filter_order_Dir", 'filter_order_Dir', '', 'word');

		$filter_type = $app->getUserStateFromRequest("$option.filter_type", 'filter_type', 0, 'int');
		$filter_logged = $app->getUserStateFromRequest("$option.filter_logged", 'filter_logged', -1, 'int');
		$filter_category = $app->getUserStateFromRequest("$option.filter_category", 'filter_category', -1, 'int');
		$search	= $app->getUserStateFromRequest("$option.search", 'search', '', 'string');
		if (strpos($search, '"') !== false)
			$search = str_replace(array('=', '<'), '', $search);
		$search = strtolower($search);

		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');

		$where = array();
		if (isset($search) && $search != '') {
			$searchEscaped = $db->q('%'.$db->escape($search, true).'%', false);
			$where[] = 'a.title LIKE '.$searchEscaped.' OR b.username LIKE '.$searchEscaped;
		}

		// Filters
		if (isset($filter_type) && $filter_type != 0)
			$where[] = ' a.blockedit = '.($filter_type-1).' ';

		if (isset($filter_logged) && $filter_logged > -1)
			$where[] = ' a.blockview = '.$filter_logged.' ';

		if (isset($filter_category) && $filter_category > -1)
			$where[] = ' a.catid = '.$filter_category.' ';

		if ($this->association)
			$where[] = ' a.language = '.$db->q($this->association);

		$where = (count($where) ? ' WHERE ('.implode(') AND (', $where).')' : '');

		$query = 'SELECT COUNT(a.id) FROM '.$db->qn('#__myjspace').' AS a LEFT JOIN '.$db->qn('#__users').' AS b ON a.userid = b.id'.$where;

		$db->setQuery($query);
		$total = $db->loadResult();

		$this->pagination = new JPagination($total, $limitstart, $limit);

		$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		if ($this->language_filter == 2) {
			$select_association = ', COUNT(asso2.id)>1 as association';
			$join_association = ' LEFT JOIN '.$db->qn('#__associations').' AS asso ON asso.id = a.id AND asso.context = "com_myjspace.item" LEFT JOIN '.$db->qn('#__associations').' AS asso2 ON asso2.key = asso.key';
			$group_association = ' GROUP BY a.id';
		} else {
			$select_association = '';
			$join_association = '';
			$group_association = '';
		}

		$query = 'SELECT a.id, a.userid, a.title, a.blockedit, a.blockview, b.username, a.publish_up, a.publish_down, b.name, b.block, a.hits, a.create_date, a.access, a.language, a.catid, LENGTH(content) AS size'
			.$select_association
			.' FROM '.$db->qn('#__myjspace').' AS a LEFT JOIN '.$db->qn('#__users').' AS b ON a.userid = b.id'
			.$join_association
			.$where
			.$group_association
			.$orderby
		;

		$db->setQuery($query, $this->pagination->limitstart, $this->pagination->limit);
		$this->items = $db->loadObjectList();

		// Get list of 'edit' mode for dropdown filter
		$types = array();
		$types[] = JHtml::_('select.option', 0, '- '.JText::_('COM_MYJSPACE_TITLEMODEEDIT').' -');
		$types[] = JHtml::_('select.option', 1, JText::_('COM_MYJSPACE_TITLEMODEEDIT0'));
		$types[] = JHtml::_('select.option', 2, JText::_('COM_MYJSPACE_TITLEMODEEDIT1'));
		$this->lists['type'] = JHtml::_('select.genericlist', $types, 'filter_type', 'class="custom-select myjsp_pages" onchange="document.adminForm.submit();"', 'value', 'text', "$filter_type");

		// Get list of 'State' for dropdown filter
		$logged = array();
		$logged[] = JHtml::_('select.option', -1, '- '.JText::_('COM_MYJSPACE_TITLEMODEVIEW').' -');
		$group_list = BS_UtilAcl::get_assetgroup_list();
		for ($i = 0 ; $i < count($group_list) ; $i++) {
			$logged[] = JHtml::_('select.option', $group_list[$i]->value, $group_list[$i]->text);
		}
		$this->lists['logged'] = JHtml::_('select.genericlist', $logged, 'filter_logged', 'class="custom-select myjsp_pages" onchange="document.adminForm.submit();"', 'value', 'text', "$filter_logged");

		// Get list of 'Categories' for dropdown filter
		$category_list = BSHelperUser::getCategories();
		$this->lists['category'] = null;

		if (count($category_list)) {
			$category = array();
			$category[] = JHtml::_('select.option', -1, '- '.JText::_('COM_MYJSPACE_LABELCATEGORY').' -'); // Header (all pages)

			if (BSHelperUser::countPageCategory(0))
				$category[] = JHtml::_('select.option', 0, '-'); // Pages with non category (id = 0)

			foreach ($category_list as $i => $value){
				$category[] = JHtml::_('select.option', $category_list[$i]['value'], str_repeat('-', max(0, $category_list[$i]['level']-1)).' '.$category_list[$i]['text']);
			}
			$this->lists['category'] = JHtml::_('select.genericlist', $category, 'filter_category', 'class="custom-select myjsp_pages" onchange="document.adminForm.submit();"', 'value', 'text', "$filter_category");
		}

		// Table ordering
		$this->lists['order_Dir'] = $filter_order_Dir;
		$this->lists['order'] = $filter_order;

		// Search filter
		$this->lists['search'] = $search;

		// Side bar
		if (version_compare(JVERSION, '3.99.99', 'lt') && $this->layout != 'modal')
			$this->sidebar = JHtmlSidebar::render();

		$this->addToolbar();

		parent::display($tpl);
	}

	protected function is_img_lock($publish_up = null, $publish_down = null)
	{
		$db	= JFactory::getDBO();

		// Lock or not
		$lock_img = JURI::base().'components/com_myjspace/images/checked_out.png';
		$lock_img = str_replace('/administrator', '', $lock_img);
		$html_lock = '<img src="'.$lock_img.'" alt="unpublished" title="'.JText::_('COM_MYJSPACE_PAGEUNPLUBLISHED').'" />';
		$aujourdhui = time();

		$img_lock = '';

		if (strtotime($publish_up) >= $aujourdhui)
			$img_lock = $html_lock;

		if ($publish_down != $db->getNullDate() && $publish_down != null && strtotime($publish_down) < $aujourdhui)
			$img_lock = $html_lock;

		return $img_lock;
	}

	protected function convertSize($bytes = 0)
	{
		require_once JPATH_COMPONENT_SITE.'/helpers/util.php';

		return BS_Util::convertSize($bytes);
	}

	protected function addToolbar()
	{
		// Menu bar
		JToolBarHelper::title(JText::_('COM_MYJSPACE_HOME').JText::_('COM_MYJSPACE_2POINTS').JText::_('COM_MYJSPACE_PAGES'), 'stack');

		JToolBarHelper::addNew('createpage');
		JToolBarHelper::editList('page');
		JToolbarHelper::custom('remove', 'delete.png', 'delete_f2.png', 'JTOOLBAR_DELETE', true);

		// To display config Options (on right)
		JToolBarHelper::preferences('com_myjspace');

		// To display Help website (on right)
		require_once JPATH_COMPONENT_SITE.'/helpers/version.php';
		JToolBarHelper::help(JText::_('COM_MYJSPACE_HELP'), false, BS_Helper_version::getXmlParam('com_myjspace', 'authorUrl'));
	}
}
