<?php
/**
* @version $Id:	router.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2010 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

require_once JPATH_ROOT.'/components/com_myjspace/helpers/util.php';

/**
 * Routing class from com_myjspace
 *
 * @since  3.3
 * For compatibility from J!3.4 (J!3.3+) to J!4 use JComponentRouterBase instead of RouterBase (J!4)
 * EVOL have a specific router for J!4
 */

class MyJspaceRouter extends JComponentRouterBase
{ 
	/**
	 * Build the route for the com_myspace component
	 * @param	array  &$query  An array of URL arguments
	 * @return	array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   J!3.3
	 */
	public function build(&$query)
	{
		$segments = array();

//		if (version_compare(JVERSION, '3.99.99', 'gt')) { // AFAIRE revoir router pour J!4
//			return $segments;
//		}

		$db = JFactory::getDbo();

		$active_itemid = (isset($this->menu->getActive()->id)) ? (int)$this->menu->getActive()->id : 0;
		$itemid = (isset($query['Itemid'])) ? (int)$query['Itemid'] : $active_itemid;
		$catid = 0;

		if (isset($query['view'])) {
			$view = $query['view'];

			if ($view != 'see') {  // To return $segment = array() for view without menu
				$itemid = BS_Util::get_menu_itemid('index.php?option=com_myjspace&view='.$view, $itemid, $catid);
				if ($itemid) {
					$query['Itemid'] = $itemid;
				} else {
					return $segments;
				}
			}

			if ($view != 'see' && $view != 'pages' && $view != 'search') {
				$segments[] = $view;
			}

			unset($query['view']);
		} else {
			return $segments; // We need a view
		}

		// For 'see' view, we need more details ... continue !
		if (isset($query['pagename'])) {
			$where = $db->qn('pagename').' = '.$db->q($query['pagename']);			

			$segments[] = $query['pagename'];

			unset($query['pagename']);
			if (isset($query['id'])) {
				unset($query['id']);
			}
		}

		if (isset($query['id'])) {
			$where = $db->qn('id').' = '.$db->q($query['id']);			

			$segments[] = $query['id'];
			unset($query['id']);
		}

		if (isset($query['uid'])) {
			$segments[] = $query['uid'];
			unset($query['uid']);
		}

		if (isset($view) && $view == 'see' && isset($where)) {
			$requete = 'SELECT '.$db->qn('catid').' FROM '.$db->qn('#__myjspace').' WHERE '.$where;
			$db->setQuery($requete);
			$catid = $db->loadResult();
		} else {
			$catid = (isset($query['catid'])) ? $query['catid'] : 0;
		}

		$itemid = (isset($query['Itemid'])) ? (int)$query['Itemid'] : $active_itemid;

		$itemid = BS_Util::get_menu_itemid('index.php?option=com_myjspace&view='.$view, $itemid, $catid);
		if ($itemid) {
			$query['Itemid'] = $itemid; // AFAIRE revoir en J!4alpha11 pas pris en compte vue 'search par example'
		}

		return $segments;
	}

/**
 * @param	array	A named array
 * @param	array
 *
 * Formats:
 *
 * index.php?/menuseealias/alias
 * index.php?/menuseealias/pages/uid
 * index.php?/menuseealias/id
 * index.php?/menuseealias/pagename
 */
	public function parse(&$segments)
	{
		$vars = array();
		$pparams = JComponentHelper::getParams('com_myjspace');

		$count = count($segments);

		// Standard routing for BS MyJspace

		if ($count && $segments[0] == 'component') {
			$count--;
			$segment = array_shift($segments);
		}

		if ($count && in_array($segments[0], array('config', 'delete', 'edit', 'myjspace', 'pages', 'search', 'see', 'categories'))) { // Set view
			$vars['view'] = $segments[0];
			$count--;
			$segment = array_shift($segments);
		}

		if ($count && isset($vars['view']) && $vars['view'] == 'pages') { // View 'pages'
			$count--;
			$segment = array_shift($segments);

			if (is_numeric($segment))
				$vars['uid'] = $segment;
		}

		if ($count) { // Supposed to be page ID (or pagename)
			if (!array_key_exists('view', $vars)) // Default view
				$vars['view'] = 'see';
			$segment = array_shift($segments);
			if (is_numeric($segment) && $pparams->get('pagename_full_num', 0) == 0)
				$vars['id'] = $segment;
			else
				$vars['pagename'] = $segment;
		}

		return $vars;
	}

	/**
	 * Content router functions
	 *
	 * These functions are proxys for the new router interface
	 * for old SEF extensions.
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @deprecated  4.0  Use Class based routers instead
	 */
/*
// AFAIRE asupp ?
	function contentBuildRoute(&$query)
	{
		$router = new ContentRouter;

		return $router->build($query);
	}
*/
	/**
	 * Parse the segments of a URL.
	 *
	 * This function is a proxy for the new router interface
	 * for old SEF extensions.
	 *
	 * @param   array  $segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   3.3
	 * @deprecated  4.0  Use Class based routers instead
	 */
/*
// AFAIRE asupp ?
	function contentParseRoute($segments)
	{
		$router = new ContentRouter;

		return $router->parse($segments);
	}
*/
}
