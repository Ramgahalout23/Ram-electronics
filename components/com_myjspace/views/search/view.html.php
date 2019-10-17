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

require_once JPATH_COMPONENT_SITE.'/helpers/search_fct.php';

class MyjspaceViewSearch extends JViewLegacy
{
	function display($tpl = null)
	{
		// Config
		$app = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;

		// Prepare display (retrieve data)
		BSHelperViewSearch::pre_display($this, array(1, 2, 64), true); // For sitemap the value is 1+2+16+32 into the function itself !

		// Web page title
		BSHelperViewSearch::title($this);

		// Sort order list (using only fields which are always selected into BSHelperUser::loadPagename()
		$this->sort_list = array();
		$this->sort_list[''] = '- '.JText::_('COM_MYJSPACE_SEARCHSORT').' -';
		$this->sort_list['pagename asc'] = JText::_('COM_MYJSPACE_SEARCHSORT10');
		$this->sort_list['pagename desc'] = JText::_('COM_MYJSPACE_SEARCHSORT11');
		$this->sort_list['create_date asc'] = JText::_('COM_MYJSPACE_SEARCHSORT12');
		$this->sort_list['create_date desc'] = JText::_('COM_MYJSPACE_SEARCHSORT13');
		$this->sort_list['last_update_date asc'] = JText::_('COM_MYJSPACE_SEARCHSORT14');
		$this->sort_list['last_update_date desc'] = JText::_('COM_MYJSPACE_SEARCHSORT15');
		$this->sort_list['hits asc'] = JText::_('COM_MYJSPACE_SEARCHSORT16');
		$this->sort_list['hits desc'] = JText::_('COM_MYJSPACE_SEARCHSORT17');

		// Breadcrumbs
		$pathway = $app->getPathway();
		$pathway->addItem(JText::_('COM_MYJSPACE_TITLESEARCH'), '');

		// Sitemap
		if ($jinput->get('layout', '', 'STRING') == 'sitemap') {
			$app = JFactory::getApplication();
			$params = $app->getParams();

			if ($params->get('sitemap_allow', 1) == 0) {
				$app->setHeader('Status', '403 Forbidden', 'true');
				exit;
			}

			$this->sitemap_freq = $params->get('sitemap_freq', 'weekly');

			parent::display('sitemap');
			exit;
		}

		parent::display($tpl);
	}

	// Transform the page data to data to be displayed (options dependent)
	protected function transform_fields($inst, $i = 0, $separ = 0)
	{
		return BSHelperViewSearch::transform_fields($inst, $i, $separ);
	}
}
