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

class MyjspaceViewMyjspace extends JViewLegacy
{
	function display($tpl = null)
	{
		require_once JPATH_COMPONENT_SITE.'/helpers/version.php';

		// Config
		$pparams = JComponentHelper::getParams('com_myjspace');
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();

		// The Version information (without the N° for safety !)
		$this->version = BS_Helper_version::get_information();

		// Web page title
		if ($pparams->get('pagetitle', 1) == 1) {
			$title = JText::_('COM_MYJSPACE_TITLE');
			if (empty($title)) {
				$title = $app->get('sitename');
			} elseif ($app->get('sitename_pagetitles', 0) == 1) {
				$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
			} elseif ($app->get('sitename_pagetitles', 0) == 2) {
				$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
			}
			if ($title)
				$document->setTitle($title);
		}

		// Breadcrumbs
		$pathway = $app->getPathway();
		$pathway->addItem(JText::_('COM_MYJSPACE_TITLE'), '');

		parent::display($tpl);
	}
}
