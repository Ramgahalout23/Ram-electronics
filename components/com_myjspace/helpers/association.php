<?php
/**
* @version $Id:	association.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2010 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

JLoader::register('CategoryHelperAssociation', JPATH_ADMINISTRATOR.'/components/com_categories/helpers/association.php');
require_once JPATH_ROOT.'/components/com_myjspace/helpers/user.php';
require_once JPATH_ROOT.'/components/com_myjspace/helpers/util.php';

abstract class MyjspaceHelperAssociation extends CategoryHelperAssociation
{
	/**
	 * Method to get the associations for a given item
	 *
	 * @param	integer	$id		Id of the item
	 * @param	string	$view	Name of the view
	 *
	 * @return  array   Array of associations for the item
	 *
	 * @since  3.0
	 */

	public static function getAssociations($id = 0, $view = null)
	{
		$jinput = JFactory::getApplication()->input;

		$id = $jinput->getInt('id', 0);
		$view = $jinput->get('view', '');
		$pagename = $jinput->get('pagename', '');

		if ($view == 'see') {
			if ($id || $pagename) {
				if ($id <= 0) { // Get page ID
					$user_page = New BSHelperUser();
					$user_page->pagename = $pagename;
					$user_page->loadPageInfo(1);
					$id = $user_page->id;
				}
				$associations = BSHelperUser::getAssociations($id);

				$return = array();

				foreach ($associations as $tag => $item) {
					$return[$tag] = MyjspaceHelperAssociation::getMyJspaceRoute($item->id, $item->pagename, $item->language);
				}

				return $return;
			}
		}

		return array();
	}

	static function getMyJspaceRoute($id = 0, $pagename = '', $language = 0)
	{
		$pparams = JComponentHelper::getParams('com_myjspace');
		$link_folder_print = $pparams->get('link_folder_print', 0);

		// Create the link
		if ($link_folder_print == 1) {
			$user_page = New BSHelperUser();
			$user_page->id = $id;		
			$user_page->loadPageInfo();
			$link = JURI::base().$user_page->foldername.'/'.$pagename.'/';
		} else {
			$link = 'index.php?option=com_myjspace&view=see';

			if ($pagename != '')
				$link .= '&pagename='.$pagename;
			else 
				$link .= '&id='.$id;
		}
/* 
// EVOL ?
		if ($language && $language != "*" && JLanguageMultilang::isEnabled()) {
			$db	= JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('a.sef AS sef');
			$query->select('a.lang_code AS lang_code');
			$query->from('#__languages AS a');
				$db->setQuery($query);
			$langs = $db->loadObjectList();
			foreach ($langs as $lang) {
//				if ($language == $lang->lang_code && $link_folder_print == 1)
////					$link .= '?lang='.$lang->sef;
//					$link .= '?l=1'; // More improvement to be found ...
//				else if ($language == $lang->lang_code)
//					$link .= '&lang='.$lang->sef;
			}
		}
*/

		return $link;
	}
}
