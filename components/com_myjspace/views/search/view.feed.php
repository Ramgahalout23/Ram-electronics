<?php
/**
* @version $Id: view.feed.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2010 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

class MyjspaceViewSearch extends JViewLegacy
{
	function display($tpl = null)
	{
		require_once JPATH_COMPONENT_SITE.'/helpers/user.php';

		// Config
		$pparams = JComponentHelper::getParams('com_myjspace');
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$params = $app->getParams();
		$jinput = JFactory::getApplication()->input;

		$rss_feed = (int)$params->get('rss_feed', 0);
		if ($rss_feed <= 0)
			return;

		// Param
		$aff_sort = $jinput->get('sort', $params->get('sort', 'last_update_date desc'), 'STRING'); // Sort order
		$svalue = $jinput->get('svalue', $params->get('svalue', ''), 'STRING'); // Search key for search content value
		$catid = $jinput->get('catid', $params->get('catid', -1), 'INT'); // Catid
		$check_search = $jinput->get('check_search', $params->get('check_search', array('name', 'content', 'description')), 'ARRAY');

		$check_search_asso = array();
		foreach ($check_search as $i => $value) {
			$check_search_asso[$value] = '1';
		}
		$search_restrict_acl = $params->get('search_restrict_acl', 0); // Restrict the search list to the page with same group than current user

		// Language
		$language_filter = $pparams->get('language_filter', 0);

		if ($language_filter > 0) { // Filter by language
			$lang = JFactory::getLanguage();
			$language = $lang->getTag();
		} else {
			$language = '';
		}

		// Limit
		$limit = (int)$params->get('search_max_line', 100);
		$limitstart = $jinput->get('limitstart', 0, 'INT');

		// Restriction to ACL ?
		if ($search_restrict_acl == 0)
			$search_restrict_acl = -1;

		// Authorization & search
		if ($limit >= 0)
			$result = BSHelperUser::loadPagename($aff_sort, $limit, $search_restrict_acl, 1, 1, $check_search_asso, $svalue, 4+16+128, $limitstart, false, $catid, $language);
		else
			$result = array();

		// The Feed itself
		$document->link = JRoute::_('index.php?option=com_myjspace&view=search&format=feed', false);
		$document->title = JText::_('COM_MYJSPACE_TITLEFEED');

		$nb = min(count($result), $rss_feed);
		for ($i = 0; $i < $nb; ++$i) {
			$item = new JFeedItem();
			$item->title = $result[$i]['title'];

			$item->link = JRoute::_('index.php?option=com_myjspace&view=see&pagename='.$result[$i]['pagename'], false);
			$item->date = date('D, d M Y H:i:s +0000', strtotime($result[$i]['last_update_date']));

			if (isset($result[$i]['ctitle']))
				$item->category = $result[$i]['ctitle'];
			else
				$item->category = '-';

			$table = JUser::getTable();
			if ($table->load($result[$i]['userid'])) { // Test if user exists before retrieving info
				$user = JFactory::getUser($result[$i]['userid']);
			} else { // User does not exist any more !
				$user = new stdClass();
				$user->username = '';
			}
			$item->author = $user->username;

			$item->description = '<div class="feed-description">'.$result[$i]['metakey'].'</div>';

			$document->addItem($item);
		}
	}
}
