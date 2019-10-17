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

class MyjspaceViewTools extends JViewLegacy
{
	/**
	 * display method of BSbanner view
	 * @return void
	 **/
	function display($tpl = null)
	{
		// BS MyJspace tools Plugins
		JPluginHelper::importPlugin('myjspace'); // Import & call the constructor
		$this->POtherTools = JFactory::getApplication()->triggerEvent('onMyJspaceTools', array(null, 0)); // List of labels 		

		// Config
		$pparams = JComponentHelper::getParams('com_myjspace');
		$this->link_folder = $pparams->get('link_folder', 1);

		// Warning - backup !
		JFactory::getApplication()->enqueueMessage(JText::_('COM_MYJSPACE_TOOLS_WARNING'), 'warning');

		// Side bar
		if (version_compare(JVERSION, '3.99.99', 'lt'))
			$this->sidebar = JHtmlSidebar::render();

		$this->addToolbar();

		parent::display($tpl);
	}

	protected function addToolbar()
	{
		// Menu bar
		JToolBarHelper::title(JText::_('COM_MYJSPACE_HOME').JText::_('COM_MYJSPACE_2POINTS').JText::_('COM_MYJSPACE_TOOLS'), 'equalizer');

		JToolbarHelper::custom('myjspace_plugin', 'power-cord', 'power-cord', 'COM_MYJSPACE_PLUGINS', false);

		// To display config Options (on right)
		JToolBarHelper::preferences('com_myjspace');

		// To display Help website (on right)
		require_once JPATH_COMPONENT_SITE.'/helpers/version.php';
		JToolBarHelper::help(JText::_('COM_MYJSPACE_HELP'), false, BS_Helper_version::getXmlParam('com_myjspace', 'authorUrl'));
	}
}
