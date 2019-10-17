<?php
/**
* @version $Id:	route.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2018 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

require_once JPATH_ROOT.'/components/com_myjspace/helpers/util.php';

class MyJspaceHelperRoute
{
	/**
	 * Get the page route
	 *
	 * @param	integer	$id			The route of the content item
	 * @param	integer	$catid		The category ID
	 * @param	integer	$language	The language code
	 * @param	string	$layout		The layout value
	 *
	 * @return  string  The page route
	 *
	 */
	public static function getPageRoute($id, $catid = 0, $language = 0, $layout = null)
	{
		$id_tab = explode(':', $id); // From Jtag url
		if (count($id_tab) == 2) {
			if ($id_tab[1] == '')
				$id = $id_tab[0];
			else
				$id = $id_tab[1];
		}

		if (is_string($id))
			$link = 'index.php?option=com_myjspace&view=see&pagename='.$id;
		else
			$link = 'index.php?option=com_myjspace&view=see&id='.$id;

		return $link;
	}

	/**
	 * Get the category route
	 *
	 * @param   integer  $catid     The category ID
	 * @param   integer  $language  The language code
	 * @param   string   $layout    The layout value
	 *
	 * @return  string  The article route
	 *
	 */
	public static function getCategoryRoute($catid, $language = 0, $layout = null)
	{
		if ($catid instanceof CategoryNode) {
			$id = $catid->id;
		} else {
			$id = (int) $catid;
		}

		if ($id < 1) {
			return '';
		}

		$link = 'index.php?option=com_myjspace&view=search&catid='.$id;

		if ($language && $language !== '*' && JLanguageMultilang::isEnabled()) {
			$link .= '&lang='.$language;
		}

		if ($layout) {
			$link .= '&layout='.$layout;
		}

		return $link;
	}
}
