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

class MyjspaceViewDelete extends JViewLegacy
{
	function display($tpl = null)
	{
		require_once JPATH_COMPONENT_SITE.'/helpers/user.php';
		require_once JPATH_COMPONENT_SITE.'/helpers/util.php';

		// Config
		$pparams = JComponentHelper::getParams('com_myjspace');
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$params = $app->getParams();
		$jinput = JFactory::getApplication()->input;

		// Params
		$catid = $jinput->get('catid', $params->get('catid', 0), 'INT');

		$id = $jinput->get('id', 0, 'INT');
		if ($id == 0) {
			$id = $jinput->get('cid', 0, 'INT');
		}

		// User info
		$user = JFactory::getUser();
		$this->user_page = New BSHelperUser();

		// Retrieve user page ID if pagename defined
		$pagename = $jinput->get('pagename', '', 'STRING');
		if ($pagename != '') {
			$this->user_page->pagename = $pagename;
			$this->user_page->loadPageInfoOnly(1);
			$id = $this->user_page->id;
		}

		// Page id - check
		$list_page_tab = $this->user_page->getListPageId($user->id, $id, $catid);
		$nb_page = count($list_page_tab);
		if ($id <= 0 || $nb_page != 1) {
			if ($nb_page == 0 && $id > 0) {
				$app->enqueueMessage(JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
				$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages&lview=delete', false));
				return;
			} else if ($nb_page == 1) { // => the page
				$id = $list_page_tab[0]['id'];
			} else { // Display Pages list
				$app->redirect(JRoute::_('index.php?option=com_myjspace&view=pages&lview=delete', false));
				return;
			}
		} else if ($nb_page > 1) { // Error
			$app->enqueueMessage(JText::_('COM_MYJSPACE_NOTALLOWED'), 'error');
			$app->redirect('index.php');
			return;
		}

		// Retrieve user page info
		if ($this->user_page->pagename == '') {
			$this->user_page->id = $id;
			$this->user_page->loadPageInfoOnly();
		}

		// If no page
		if ($this->user_page->id == 0) {
			$app->redirect(JRoute::_('index.php?option=com_myjspace&view=see', false));
			return;
		}

		// If page locked (admin & edit | edit)
		if ($this->user_page->blockedit != 0) {
			$app->enqueueMessage(JText::_('COM_MYJSPACE_EDITLOCKED'), 'error');
			$app->redirect(JRoute::_('index.php'));
			return;
		}

		// Web page title
		if ($pparams->get('pagetitle',1) == 1) {
			$title = $this->user_page->title;
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
		$pathway->addItem($this->user_page->title, '');

		parent::display($tpl);
	}
}
