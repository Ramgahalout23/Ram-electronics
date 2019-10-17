<?php
/*
* @version $Id: helper.php $
* @version		3.0.0 24/07/2019
* @package		mod_viewmyjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010 - 2019 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

class modViewMyJspaceHelper {

	// Retrieve pagename & id & content
	public static function getListPage($triemode = 0, $affmax = 0, $emptymode = 0, $search_restrict_acl = 0, $publish = 1, $resultmode = 0, $catid_list = array(), $language = '')
	{
		$db	= JFactory::getDBO();
		$nullDate = $db->getNullDate();
		$result	= null;

		$query = 'SELECT DISTINCT mjs.id, mjs.userid, jos.userid AS connect, mjs.title, mjs.pagename, mjs.foldername, mjs.last_update_date, mjs.blockview';
		// id (username) = 1, pagename = 2, last_update_date = 16 for display (search)			
		if ($resultmode & 4)
			$query .= ', mjs.metakey';
		if ($resultmode & 8)
			$query .= ', mjs.create_date';
		if ($resultmode & 32)
			$query .= ', mjs.hits';
		if ($resultmode & 256)
			$query .= ', mjs.content';
		// 64 for image (search)
		$query .= ' FROM '.$db->qn('#__myjspace').' mjs LEFT JOIN '.$db->qn('#__session').' jos ON mjs.userid=jos.userid WHERE 1=1 ';

		if ($emptymode == 0)
			$query .= ' AND mjs.content != '.$db->q('');

		$query .= ' AND '.$db->qn('blockview').' != 0';
		if ($search_restrict_acl > 0) { // Only pages from my ACL and my pages
			$user_actual = JFactory::getuser();
			if ($user_actual->id == 0)
				$query .= ' AND '.$db->qn('blockview').' IN ('.implode(',', $user_actual->getAuthorisedViewLevels()).')';
			else
				$query .= ' AND ('.$db->qn('blockview').' IN ('.implode(',', $user_actual->getAuthorisedViewLevels()).') OR mjs.userid = '.$user_actual->id.')';
		}

		if ($publish == 1)
			$query .= ' AND mjs.publish_up < NOW() AND (mjs.publish_down >= NOW() OR mjs.publish_down = '.$db->q($nullDate).')';

		if (is_array($catid_list)) {
			$nb_catid = count($catid_list);
			for ($i = 0; $i < $nb_catid; $i++)
				$catid_list[$i] = $db->q($catid_list[$i]);
			$query .= ' AND '.$db->qn('catid').' IN ('.implode(',', $catid_list).')';
		}

		if ($language)
			$query .= ' AND '.$db->qn('language').' IN ('.$db->q('*').','.$db->q($language).')';

		if ($triemode == 0) {
			$query .= ' ORDER BY mjs.pagename ASC';
		} else if ($triemode == 1) {
			$query .= ' ORDER BY mjs.pagename DESC';
		} else if ($triemode == 2 && !in_array($db->name, array('postgresql'))) {
			$query .= ' ORDER BY RAND()';
		} else if ($triemode == 3) {
			$query .= ' ORDER BY mjs.create_date DESC';
		} else if ($triemode == 4) {
			$query .= ' ORDER BY mjs.last_update_date DESC';
		} else if ($triemode == 5) {
			$query .= ' ORDER BY mjs.hits DESC';
		}

		if ($affmax != 0)
			$query .= ' LIMIT '.$affmax;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	// Retrieve page number
	public static function getNbPage($emptymode = 0, $search_restrict_acl = 0, $publish = 1, $catid_list = '', $language = '')
	{
		$db = JFactory::getDBO();
		$nullDate = $db->getNullDate();
		$result	= null;

		// Select page
		$query = 'SELECT COUNT(*) FROM '.$db->qn('#__myjspace').' WHERE 1=1 ';

		if ($emptymode == 0)
			$query .= ' AND '.$db->qn('content').' != '.$db->q('').' ';

		$query .= ' AND '.$db->qn('blockview').' != 0';
		if ($search_restrict_acl > 0) { // Only pages from my ACL and my pages
			$user_actual = JFactory::getuser();
			if ($user_actual->id == 0)
				$query .= ' AND '.$db->qn('blockview').' IN ('.implode(',', $user_actual->getAuthorisedViewLevels()).')';
			else
				$query .= ' AND ('.$db->qn('blockview').' IN ('.implode(',', $user_actual->getAuthorisedViewLevels()).') OR '.$db->qn('userid').' = '.$user_actual->id.')';
		}

		if ($publish == 1)
			$query .= ' AND '.$db->qn('publish_up').' < NOW() AND ('.$db->qn('publish_down').' >= NOW() OR '.$db->qn('publish_down').' = '.$db->q($nullDate).')';

		if ($catid_list != '') {
			$nb_catid = count($catid_list);
			for ($i = 0; $i < $nb_catid; $i++)
				$catid_list[$i] = $db->q($catid_list[$i]);
			$query .= ' AND '.$db->qn('catid').' IN ('.implode(',', $catid_list).')';
		}

		if ($language)
			$query .= ' AND '.$db->qn('language').' IN ('.$db->q('*').','.$db->q($language).')';

		$db->setQuery($query);
		$result = $db->loadResult();

		if ($result === null)
			$result = 0;

		return $result;
	}

	// Image connected/not connected or updated since 'delay'
	public static function aff_img($connecte = 0, $dateupdate = null, $delais = 0, $affimgcon = 0)
	{
		if ($affimgcon != 0) {
			$link_pre = 'modules/mod_viewmyjspace/images/';

			if ($connecte) // Connected
				$retour = '<img src="'.$link_pre.'tick.png" style="width:10px; border:none; margin-left:3px;margin-right:3px;" alt="" />';
			else
				$retour = '<img src="'.$link_pre.'rating_star_blank.png" style="width:10px; border:none; margin-left:3px;margin-right:3px;" alt="" />';

			// If option for page updated since 'delay'
			if ($delais != 0 && (time() - strtotime($dateupdate)) < $delais && ($connecte))
				$retour = '<img src="'.$link_pre.'rating_star_green.png" style="width:10px; border:none; margin-left:3px;margin-right:3px;" alt="" />';
			if ($delais != 0 && (time() - strtotime($dateupdate)) < $delais && $delais != 0 && !($connecte))
				$retour = '<img src="'.$link_pre.'rating_star.png" style="width:10px; border:none; margin-left:3px;margin-right:3px;" alt="" />';
		} else {
			$retour = '';
		}

		return $retour;
	}

}
