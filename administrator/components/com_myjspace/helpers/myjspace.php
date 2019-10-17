<?php
/**
* @version $Id: myjspace.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2012 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

class MyJspaceHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function addSubmenu($vName = 'myjspace')
	{
		if (version_compare(JVERSION, '3.99.99', 'lt')) {
			JHtmlSidebar::addEntry(JText::_('COM_MYJSPACE_HOME'), 'index.php?option=com_myjspace&view=myjspace', $vName == 'myjspace');
			JHtmlSidebar::addEntry(JText::_('COM_MYJSPACE_LINKS'), 'index.php?option=com_myjspace&view=url', $vName == 'url');
			JHtmlSidebar::addEntry(JText::_('COM_MYJSPACE_PAGES'), 'index.php?option=com_myjspace&view=pages', $vName == 'pages');
			JHtmlSidebar::addEntry(JText::_('COM_MYJSPACE_CATEGORIES'), 'index.php?option=com_categories&extension=com_myjspace', $vName == 'categories');
			JHtmlSidebar::addEntry(JText::_('COM_MYJSPACE_TOOLS'), 'index.php?option=com_myjspace&view=tools', $vName == 'tools');
			JHtmlSidebar::addEntry(JText::_('COM_MYJSPACE_HELP'), 'index.php?option=com_myjspace&view=help', $vName == 'help');	
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 * @return	JObject
	 */
	public static function getActions($categoryId = 0)
	{
	}

	/**
	 * For User Actions Log
	 */
	public static function getContentTypeLink($contentType = 'page', $id = 0)
	{
		return 'index.php?option=com_myjspace&view=page&id='.$id;	
	}
}
