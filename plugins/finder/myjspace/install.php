<?php
/**
* @version $Id: install.php $ 
* @version		3.0.0 04/08/2019
* @package		plg_finder_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2019 Bernard Saulmé
* @license      Usage and copie only validated by the author
*/

defined('_JEXEC') or die;

/*
 * Enable, the plugin after install // >= J!1.6
 */
 
class plgFinderMyJspaceInstallerScript {
	protected $minimumJoomlaVersion = '3.4.0';
	protected $component = 'com_myjspace';
	protected $minimumComponentVersion = '3.0.0';

	// Method for pre-install
	public function preflight($type, $parent)
	{
		if ($type == 'uninstall')
			return true;

		$componentVersion = $this->getParam('version', $this->component);

		if (version_compare($componentVersion, $this->minimumComponentVersion, 'lt')) { // Check for component compatibility
			JFactory::getApplication()->enqueueMessage('You need at least BS MyJspace '.$componentVersion.' to install this plugin', 'error');
			return false;
		}

		if (version_compare(JVERSION, $this->minimumJoomlaVersion, 'lt')) { // Check Joomla! version for component compatibility
			JFactory::getApplication()->enqueueMessage('You need at least Joomla! '.$this->minimumJoomlaVersion.' to install this plugin', 'error');
			return false;
		}

		return true;
	}

	function postflight($type, $parent) {

		// Get this plugin group, element
		$group = 'finder';
		$element = 'myjspace';

		if ($type == 'install') { // Enable plugin
			$this->enablePlugin($group, $element);
		}

/*
		// Test if the Joomla 'Content - Smart Search' plugin is enable
		$plugin = JTable::getInstance('extension');
		if ($plugin->load(array('type'=>'plugin', 'folder'=>'content', 'element'=>'finder'))) {
			if (!$plugin->enabled)
				JFactory::getApplication()->enqueueMessage('You need to enable the \'Content - Smart Search\' plugin to use the MyJspace finder plugin', 'warning');
		}
*/
   }
   
	function enablePlugin($group, $element)
	{
		$plugin = JTable::getInstance('extension');
		if (!$plugin->load(array('type'=>'plugin', 'folder'=>$group, 'element'=>$element))) {
			return false;
		}
		$plugin->enabled = 1;
		return $plugin->store();
	}

	/*
	 * Get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getParam($name, $component = null, $type = 'component') {
		$db = JFactory::getDbo();
		$query = 'SELECT '.$db->qn('manifest_cache').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('name').' = '.$db->q($component).' AND '.$db->qn('type').' = '.$db->q($type);
		$db->setQuery($query, null, 1);
		$manifest = json_decode($db->loadResult(), true);

		if (isset($manifest[$name]))
			return $manifest[$name];
		else
			return null;
	}
}
