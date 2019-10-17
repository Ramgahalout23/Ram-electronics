<?php
/**
* @version $Id: install.php $
* @version		3.0.0 29/06/2019
* @package		plg_pagebreakmyjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2011 - 2019 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

/*
 * Enable, the plugin after install >= J!1.6
 */

class plgContentpagebreakmyjspaceInstallerScript
{
	protected $minimumPHPVersion = '5.3.1';
	protected $minimumJoomlaVersion = '3.4.0';
	protected $componentName = 'pagebreakmyjspace';

	// Method for pre-install
	public function preflight($type, $parent)
	{
		if (version_compare(JVERSION, $this->minimumJoomlaVersion, 'lt')) { // Check Joomla! version for component compatibility
			JFactory::getApplication()->enqueueMessage('You need at least Joomla! '.$this->minimumJoomlaVersion.' to install this '.$this->componentName.' version', 'error');
			return false;
		}

		if (version_compare(PHP_VERSION, $this->minimumPHPVersion, 'lt')) { // Check PHP version for component compatibility
			JFactory::getApplication()->enqueueMessage('You need at least PHP '.$this->minimumPHPVersion.' to install this '.$this->componentName.' version', 'error');
			return false;
		}

		return true;
	}

	function postflight($type, $parent)
	{
		// Get this plugin group, element
		$group = 'content';
		$element = 'pagebreakmyjspace';

		if ($type == 'install') { // Enable plugin
			$this->enablePlugin($group, $element);
		}
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
}
