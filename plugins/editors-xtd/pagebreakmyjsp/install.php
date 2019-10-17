<?php
/**
* @version $Id: install.php $
* @version		3.0.0 04/08/2019
* @package		plg_xtd_pagebreakmyjsp
* @author       Bernard Saulm�
* @copyright	Copyright (C) 2015 - 2019 Bernard Saulm�
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

/*
 * Enable, the plugin after install >= J1.6
 */

class plgeditorsxtdpagebreakmyjspInstallerScript
{
	function postflight($type, $parent)
	{
		// Get this plugin group, element
		$group = 'editors-xtd';
		$element = 'pagebreakmyjsp';

		if ($type == 'install' || $type = 'update') { // Enable plugin
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
