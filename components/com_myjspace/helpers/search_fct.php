<?php
/**
* @version $Id:	search_fct.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2014 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

require_once JPATH_COMPONENT_SITE.'/helpers/user.php';
require_once JPATH_COMPONENT_SITE.'/helpers/util.php';
require_once JPATH_COMPONENT_SITE.'/helpers/util_acl.php';

class BSHelperViewSearch
{
	// Retreive parameters & configuration & options from the view
	// inst (1)									: usually '$this' for call from view.html.php
	// search_aff_add_default (null) 			: columns to display (number or array)
	// use_param_search (true)					: allow to use view parameter to update the column list
	// separ_default (null) 					: display type (list as default = 0), raw (1), block (2), wall(4)
	//												if null use the global option else use the value
	// type_search (1)							: pages with content (1), all (0)
	// publish (1)								: only page published (1), all (0)
	// criteria	(null)							: criteria to be displayed (all = null), else array()
	// language_filter_default (null)			: use the language filter, (all page = 0)
	// rss_feed_default (null)					: display rss feed icon (0 = none)
	// extra_query (null)						: extra SQL query
	// list_blocked (1)							: display (0) or not(1) the locked pages

	public static function pre_display($inst = null, $search_aff_add_default = null, $use_param_search = true, $separ_default = null, $type_search = 1, $publish = 1, $aff_criteria_search_default = null, $language_filter_default = null, $rss_feed_default = null, $extra_query = null, $list_blocked = 1)
	{
		// Config
		$pparams = JComponentHelper::getParams('com_myjspace');
		$inst->user = JFactory::getUser();
		$inst->app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$params = $inst->app->getParams();
		$jinput = JFactory::getApplication()->input;

		// View parameters
		$def_sort = ($jinput->get('view', 'search', 'STRING') == 'search') ? 'pagename asc' : '';
		$inst->aff_sort = $jinput->get('sort', $params->get('sort', $def_sort), 'STRING'); // Sort order
		$inst->svalue = $jinput->get('svalue', $params->get('svalue', ''), 'STRING'); // Search key for search content value
		$inst->catid = $jinput->get('catid', $params->get('catid', 0), 'INT'); // Catid
		if ($jinput->get('catid', -1, 'INT') == -1 && $params->get('catid', 0) == 0)
			$inst->catid = -1;
		$layout = $jinput->get('layout', '', 'STRING');

		// View options
		$inst->separ = ($separ_default !== null) ? $separ_default : $params->get('separ', 0); // List-tab (0), raw (1), blocks (2), Wall (3) // Do not use the Option if parameter passed
		$inst->aff_select = $params->get('select', 1); // Print the search selector
		$inst->search_pagination = $params->get('search_pagination', 1);
		$search_max_line = intval($params->get('search_max_line', 200));
		$inst->search_image_type = $params->get('search_image_type', 2);
		$inst->search_image_effect_list = $params->get('search_image_effect_list', 2); // Effect on image click: Lytebox usage & preview, redirection
		$inst->title_limit = intval($params->get('title_limit', 30)); // Max num chars for title if used
		$inst->content_limit = intval($params->get('content_limit', 150)); // Max num chars for content if used
		$inst->description_limit = intval($params->get('description_limit', 45)); // Max num chars for description if used
		$inst->search_page_title = trim($params->get('search_page_title', '')); // Page title
		$inst->search_image_default = trim($params->get('search_image_default', 'components/com_myjspace/images/default.png')); // Default img name
		$search_block_width = intval($params->get('search_block_width', 150));
		$search_block_height = intval($params->get('search_block_height', 200));
		$search_block_width_min = intval($params->get('search_block_width_min', 150)); // Min img/block width
		$search_block_height_min = intval($params->get('search_block_height_min', 200)); // Min img/block height
		$inst->search_labels = intval($params->get('search_labels', 1));
		$inst->search_image_video = intval($params->get('search_image_video', 1));
		$search_image_effect = $params->get('search_image_effect', 3); // 0 non, 1 opacity, 2 zoom (for wall) (param to be added)
		$inst->search_sort_use = $params->get('search_sort_use', 1); // Sort using the column header
		$inst->search_user_name = $params->get('search_user_name', 0); // Display user name or user login name
		$search_restrict_acl = $params->get('search_restrict_acl', 0); // Restrict the search list to the page with same group than current user
		$inst->language_filter = $pparams->get('language_filter', 0);
		$inst->share_page = $pparams->get('share_page', 0);

		// Table ordering (using column header to sort or selector)
		$option = $jinput->get('option', '', 'STRING');
		$inst->lists['order'] = $inst->app->getUserStateFromRequest("$option.filter_order", 'filter_order', 'pagename', 'cmd');
		$inst->lists['order_Dir'] = $inst->app->getUserStateFromRequest("$option.filter_order_Dir", 'filter_order_Dir', 'asc', 'word');

		// Switch between order by selector or clic of the column header
		if ($inst->aff_sort != '') {
			$sort_list = explode(' ', $inst->aff_sort);
			if (isset($sort_list[0]) && isset($sort_list[1])) {
				$inst->lists['order'] = $sort_list[0];
				$inst->lists['order_Dir'] = $sort_list[1];
			}
		}

		// sort_list
		$bouton = $jinput->get('bouton', null, 'STRING'); // Button clicked

		if ($inst->separ <= 1) { // Only for display mode: list & row
			if ($inst->lists['order'] && $inst->lists['order_Dir'] && !$bouton) {
				// Check sort validity, for safety
				$allowed_order = array('pagename', 'userid', 'create_date', 'last_update_date', 'hits', 'catid', 'metakey', 'size', 'language', 'blockview', 'ctitle', 'uname', 'content');
				$allowed_order_dir = array('asc', 'desc');
				if (!in_array(strtolower($inst->lists['order']), $allowed_order) || !in_array(strtolower($inst->lists['order_Dir']), $allowed_order_dir))
					$inst->aff_sort = 'pagename asc' ; // Default
				else
					$inst->aff_sort = $inst->lists['order'].' '.$inst->lists['order_Dir'];
			} else {
				$inst->lists['order'] = null;
				$inst->lists['order_Dir'] = null;
			}
		}

		// Language restriction ?
		if ($language_filter_default === null) {
			$language_filter_default = $inst->language_filter;
		}

		if ($language_filter_default > 0) { // Restrict by language (to search only = 'current lenguage' or 'all')
			$lang = JFactory::getLanguage();
			$language = $lang->getTag();
		} else {
			$language = '';
		}
		$inst->languages = JLanguageHelper::getLanguages('lang_code'); // Languages list

		// Categories
		$inst->categories = BSHelperUser::getCategories(1);

		$limit = $inst->app->getUserStateFromRequest('global.list.limit', 'limit', $inst->app->get('list_limit'), 'int');
		$limitstart = $jinput->get('limitstart', 0, 'INT');
		if ($limit > $search_max_line || $limit == 0) {
			$limit = $search_max_line;
		}

		// Display checked columns
		if ($use_param_search)
			$inst->search_aff_add = $params->get('search_aff_add', $search_aff_add_default); // Take the parameter, as default, if nothing set into the menu configuration
		else if ($search_aff_add_default !== null)
			$inst->search_aff_add = $search_aff_add_default; // No column selection allowed
		else
			$inst->search_aff_add = array(1, 2, 64);

		if (is_array($inst->search_aff_add)) { // Selector
			$search_tmp = 0;
			foreach ($inst->search_aff_add as $i => $value) {
				$search_tmp |= $value;
			}
			$inst->search_aff_add = $search_tmp;
		} else { // EVOL simplifier pour J!3+ ?
			$inst->search_aff_add = intval($inst->search_aff_add);
		}

		// Usage dependency query/display
		if ($inst->search_aff_add & 64 && $inst->search_image_type > 1)
			$inst->search_aff_add_query = $inst->search_aff_add | 256;
		else
			$inst->search_aff_add_query = $inst->search_aff_add;

		// Folder root dir & url
		$inst->link_folder = $pparams->get('link_folder', 1);
		$inst->link_folder_print = ($inst->link_folder == 1) ? $pparams->get('link_folder_print', 0) : 0;

		// Selection for search criteria display & default checked
		$aff_criteria_search_default = ($aff_criteria_search_default !== null) ? $aff_criteria_search_default : array('name', 'content', 'description', 'category', 'sort');
		$aff_criteria_search =	$params->get('aff_criteria_search', $aff_criteria_search_default);
		$inst->aff_search_asso = array();
		foreach ($aff_criteria_search as $i => $value) {
			$inst->aff_search_asso[$value] = '1';
		}

		$check_search = $jinput->get('check_search', $params->get('check_search', array('name', 'content', 'description')), 'ARRAY');

		$inst->check_search_asso = array();
		foreach ($check_search as $i => $value) {
			$inst->check_search_asso[$value] = '1';
		}

		if ($pparams->get('show_tags', 1) == 0 && isset($inst->aff_search_asso['jtag']) && ($inst->search_aff_add & 8192)) { // If globally no J! tag to be used => no J! tag to be display!
			$inst->search_aff_add = $inst->search_aff_add - 8192;
		}

		if (!$inst->link_folder && ($inst->search_aff_add & 64)) { // Si pas de directory alors on n'affiche pas d'image !
			$inst->search_aff_add = $inst->search_aff_add - 64;
		}

		// Search criteria: J!3.1+ tags
		$metadata = $jinput->get('metadata', array(0), 'ARRAY');
		$inst->jtag = (array_key_exists('tags', $metadata)) ? $metadata['tags'] : array();

		// To be able to display tags as criteria. Search for Joomla! tags, J!3.1+
		if ($pparams->get('show_tags', 1) == 1 && isset($inst->aff_search_asso['jtag'])) {
			$pathToMyXMLFile = JPATH_COMPONENT_SITE.'/models/forms/myjspace.xml';
			$inst->form = JForm::getInstance('myform', $pathToMyXMLFile);
		} else {
			$inst->form = null;
		}

		// xml sitemap
		if ($layout == 'sitemap') { // All content out of $inst->catid filter
			$inst->aff_sort = 'last_update_date desc'; // Order by creation date
			$limit = $search_max_line;
			$inst->check_search_asso = null;
			$inst->svalue = '';
			$inst->search_aff_add_query = 1+2+16+64;
			$limitstart = 0;
			$language = '';
		}

		// User name used as search criteria
		if (isset($inst->aff_search_asso['username'])&& $inst->svalue != '') {
			$username = $inst->svalue;
		} else {
			$username = null;
		}

		// Restriction to ACL ?
		if ($search_restrict_acl == 0) {
			$search_restrict_acl = -1;
		}

		// Authorization & search
		$total = 0;
		if ($limit >= 0) {
			if ($layout != 'sitemap') {
				$total = BSHelperUser::loadPagename($inst->aff_sort, 0, $search_restrict_acl, $publish, $type_search, $inst->check_search_asso, $inst->svalue, $inst->search_aff_add_query, 0, true, $inst->catid, $language, $extra_query, $username, $list_blocked);
				$inst->pagination = new JPagination($total, $limitstart, $limit);
			} else {
				$inst->pagination = new stdClass();
			}

			$inst->result = BSHelperUser::loadPagename($inst->aff_sort, $limit, $search_restrict_acl, $publish, $type_search, $inst->check_search_asso, $inst->svalue, $inst->search_aff_add_query, $limitstart, false, $inst->catid, $language, $extra_query, $username, $list_blocked);

			// Restrict the result to the selected tags
			if (count($inst->jtag)) {
				list($inst->result, $total) = self::loadPagename_add_jtag_criteria($inst->result, $inst->jtag);
				if ($layout != 'sitemap')
					$inst->pagination = new JPagination($total, $limitstart, $limit);
			}
		} else {
			$inst->result = array();
			$inst->aff_select = 0;
			$inst->search_page_title = '';
			$inst->pagination = new stdClass();
		}

		// User Access (ACL)
		$inst->user_mode_view_acl = $pparams->get('user_mode_view_acl', 0);
		if ($inst->user_mode_view_acl == 1)
			$inst->access = $inst->user->getAuthorisedViewLevels();
		else
			$inst->access = array();

		// RSS feed
		$inst->url_rss_feed = '';
		$rss_feed_default = ($rss_feed_default !== null) ? $rss_feed_default : 1;
		$rss_feed = $params->get('rss_feed', $rss_feed_default);
		if ($rss_feed > 0) {
			$url = 'index.php?option=com_myjspace&view=search&format=feed';
			if ($inst->catid != 0 && !is_array($inst->catid))
				$url .= '&catid='.$inst->catid;
			if ($inst->svalue != '')
				$url .= '&svalue='.$inst->svalue;
			$url .= '&type=rss';
			$inst->url_rss_feed = '<a href="'.JRoute::_($url, false).'"><img src="components/com_myjspace/images/rss.gif" alt="rss" title="rss" /></a>';
			$document->addHeadLink(JRoute::_($url, false), 'alternate', 'rel', array('type' => 'application/rss+xml', 'title' => 'RSS 1.0'));
		}

		// Mode block style
		if ($inst->separ == 2) {
			$search_image_width = intval($search_block_width - min($search_block_width * 0.2, 30));
			$search_image_height = intval($search_image_width * 0.75);

			$inst->style_str = "
.myjsp-blocks div.icon a {
	max-width: {$search_block_width}px;
	max-height: {$search_block_height}px;
	min-width: {$search_block_width_min}px;
	min-height: {$search_block_height_min}px;
}

.myjsp-spanimg {
 	width: {$search_block_width}px;
	height: {$search_image_height}px;
	line-height: {$search_image_height}px;
	text-align: center;
}

.myjsp-blocks img {
	vertical-align: middle;
	max-width: 90%;
	max-height: 90%;
}
";
			$inst->chaine_ie = "
<!--[if lte IE 7]>
<style type=\"text/css\">
.myjsp-blocks img {
	height: {$search_block_width}px;
}
</style>
<![endif]-->
";
			} else if ($inst->separ == 3) {
				$search_block_width_grow = $search_block_width * 2;
				$search_block_height_grow = $search_block_height * 2;
				$search_block_width_min_grow = $search_block_width_min * 2;
				$search_block_height_min_grow = $search_block_height_min * 2;

				$inst->style_str = "
.myjsp-blocks2 a:hover { background-color: transparent; }

.myjsp-blocks2 .pic {
	max-width: {$search_block_width}px;
	max-height: {$search_block_height}px;
	min-width: {$search_block_width_min}px;
	min-height: {$search_block_height_min}px;

	overflow: hidden;
	float: left;

	opacity: 1;
	filter: alpha(opacity=100);
}

.myjsp-blocks2 .grow img {
	max-width: {$search_block_width}px;
	max-height: {$search_block_height}px;
	min-width: {$search_block_width_min}px;
	min-height: {$search_block_height_min}px;

	-webkit-transition: all 1s ease;
	-moz-transition: all 1s ease;
	-o-transition: all 1s ease;
	-ms-transition: all 1s ease;
	transition: all 1s ease;
}

.myjsp-blocks2 .grow img:hover {
	 background-color: transparent;
";
				if ($search_image_effect & 1) {
					$inst->style_str .= "
	max-width: {$search_block_width_grow}px;
	max-height: {$search_block_height_grow}px;
	min-width: {$search_block_width_min_grow}px;
	min-height: {$search_block_height_min_grow}px;
	";
				}
				if ($search_image_effect & 2) {
					$inst->style_str .= "
	opacity: 0.3;
	filter: alpha(opacity=30);
	";
				}
				$inst->style_str .= "
}
";
		$inst->chaine_ie = "
<!--[if lte IE 7]>
<style type=\"text/css\">
.myjsp-blocks img {
	height: {$search_block_width}px;
}
</style>
<![endif]-->
";
			} else {
				$inst->style_str = '';
			}

		return $total;
	}

	// Review the data with the Jtag criteria
	static function loadPagename_add_jtag_criteria($result = null, $jtag = null)
	{
		if (count($jtag) == 0) // Safety control
			return array($result, count($result));

		$new_result = array();

		foreach ($result as $key => $value) {
			$tags = new JHelperTags;
			$tags->getItemTags('com_myjspace.see', $value['id']);
			$trouve = false;
			foreach($tags->itemTags as $key2 => $value2) {
				if (in_array($tags->itemTags[$key2]->tag_id, $jtag))
					$trouve = true;
			}
			if ($trouve == true)
				$new_result[] = $value;
		}

		unset($result);

		return array($new_result, count($new_result));
	}

	// Transform the page data to data to be displayed (options dependent)
	public static function transform_fields($inst, $i = 0, $separ = 0, $lview = 'see')
	{
		$pparams = JComponentHelper::getParams('com_myjspace');
		$link_pre = "components/com_myjspace/images/";

		// Set default return variables
		$aff = new stdClass();
		$aff->id = 0;
		$aff->userid = 0;
		$aff->pagename = ($inst->search_aff_add & 1) ? true : false;
		$aff->username = ($inst->search_aff_add & 2) ? true : false;
		$aff->description = ($inst->search_aff_add & 4) ? true : false;
		$aff->create_date = ($inst->search_aff_add & 8) ? true : false;
		$aff->update_date = ($inst->search_aff_add & 16) ? true : false;
		$aff->hits = ($inst->search_aff_add & 32) ? true : false;
		$aff->image = ($inst->search_aff_add & 64) ? true : false;
		$aff->category = ($inst->search_aff_add & 128) ? true : false;
		$aff->content = ($inst->search_aff_add & 256) ? true : false;
		$aff->size = ($inst->search_aff_add & 512) ? true : false;
		$aff->blockview = ($inst->search_aff_add & 1024) ? true : false;
		$aff->language = ($inst->search_aff_add & 2048) ? true : false;
		$aff->lang = ($inst->search_aff_add & 2048) ? true : false;
		$aff->share_page = '';
		$aff->blockview_alt = '';
		$aff->page_url = '';
		$aff->title = '';
		$aff->text = '';
		$aff->local_folder = null;
		$aff->select = '';
		$aff->jtag = ($inst->search_aff_add & 8192) ? true : false;

		// Id
		$aff->id = $inst->result[$i]['id'];
		// Userid
		$aff->userid = $inst->result[$i]['userid'];

		// Select
		$aff->select = '<input type="radio" id="cb'.$i.'" name="cid" value="'.$inst->result[$i]['id'].'" />';

		// Title
		if (isset($inst->result[$i]['title']))
			$aff->title = BS_Util::clean_text($inst->result[$i]['title'], $inst->title_limit);

		// Url
		if (!in_array($lview, array('config', 'delete', 'edit', 'see'))) // Check for valid $lview only
			$lview = 'see';

		if ($inst->link_folder_print && $lview == 'see')
			$aff->page_url = JURI::base(true).'/'.$inst->result[$i]['foldername'].'/'.$inst->result[$i]['pagename'].'/';
		else if ($lview == 'see' || ($pparams->get('pagename_full_num', 0) == 1))
			$aff->page_url = JRoute::_('index.php?option=com_myjspace&view='.$lview.'&pagename='.$inst->result[$i]['pagename']);
		else
			$aff->page_url = JRoute::_('index.php?option=com_myjspace&view='.$lview.'&pagename='.$inst->result[$i]['id']);

		if ($inst->search_aff_add & 1 || $inst->separ >= 2) // Pagename (1)
			$aff->pagename = true;

		if ($inst->search_aff_add & 64) { // Image (64)
			if ($inst->search_aff_add_query & 256) {
				if ($inst->result[$i]['content'])
					$aff->text = $inst->result[$i]['content'];
			} else {
				$aff->text = '';
			}

			if ($inst->link_folder == 1)
				$aff->local_folder = $inst->result[$i]['foldername'].'/'.$inst->result[$i]['pagename'];
			else
				$aff->local_folder = null;

			if ($inst->separ != 3 && isset($inst->result[$i]['title']))
				$aff->image = BS_Util::exist_image_html($aff->local_folder, JPATH_SITE, 'img-preview', $inst->search_image_effect_list, $inst->result[$i]['title'], 'preview.jpg', $inst->search_image_default, $inst->search_image_type, $aff->text, $inst->search_image_video, $aff->page_url);
		}

		if ($inst->search_aff_add & 2) { // Username (2)

			$table = JUser::getTable();
			if ($table->load($inst->result[$i]['userid'])) { // Test if user exists before retrieving info
				$inst->user = JFactory::getUser($inst->result[$i]['userid']);
			} else { // User does not exist any more !
				$inst->user = new stdClass();
				$inst->user->id = 0;
				$inst->user->username = ' ';
				$inst->user->name = ' ';
			}

			if ($inst->search_user_name == 0)
				$aff->username = $inst->result[$i]['username'];
			else
				$aff->username = $inst->result[$i]['uname'];

			if (!isset($aff->username))
				$aff->username = ' ';
		}

		if ($inst->search_aff_add & 8 && isset($inst->result[$i]['create_date'])) { // Date created (8)
			$aff->create_date = date(JText::_('COM_MYJSPACE_DATE_FORMAT2'), strtotime($inst->result[$i]['create_date']));
		}

		if ($inst->search_aff_add & 16 && isset($inst->result[$i]['last_update_date'])) { // Date updated (16)
			$aff->update_date = date(JText::_('COM_MYJSPACE_DATE_FORMAT2'), strtotime($inst->result[$i]['last_update_date']));
		}

		if ($inst->search_aff_add & 32 && isset($inst->result[$i]['hits'])) { // Hits (32)
			$aff->hits = $inst->result[$i]['hits'];
		}

		if ($inst->search_aff_add & 128) {
			$aff->category = $inst->result[$i]['ctitle'];
		}

		// ACL
		if (isset($inst->result[$i]['blockview']))
			$blockview = $inst->result[$i]['blockview'];
		else
			$blockview = 0;
		$check_acl = ($blockview >= 2 && (($inst->user_mode_view_acl == 0 && $inst->user->id <= 0) || ($inst->user_mode_view_acl == 1 && !in_array($blockview, $inst->access))));

		if ($inst->search_aff_add & 4) { // Description (4)
			if ($check_acl && $inst->user->id != $inst->result[$i]['userid']) {
				$aff->description = ' ';
			} else if (isset($inst->result[$i]['metakey'])) {
				$aff->description = BS_Util::clean_text($inst->result[$i]['metakey'], $inst->description_limit).' ';
			}
		}

		if ($inst->search_aff_add & 256) { // Content (256) with no HTML & only some characters
			if ($check_acl && $inst->user->id != $inst->result[$i]['userid']) {
				$aff->blockview_alt = BS_UtilAcl::get_assetgroup_label($blockview);
				$aff->content = '<img src="components/com_myjspace/images/publish_y.png" class="icon16" alt="blockview" title="'.$aff->blockview_alt.'" />';
			} else if (isset($inst->result[$i]['content'])) {
				$aff->content = BS_Util::clean_html_text($inst->result[$i]['content'], $inst->content_limit).' ';
			}
		}

		if ($inst->search_aff_add & 512 && isset($inst->result[$i]['size'])) { // Size (512)
			$aff->size = BS_Util::convertSize($inst->result[$i]['size']);
		}

		if ($inst->search_aff_add & 1024) { // Access Level (blockview) (1024)
			if ($blockview == 1)
				$blockview_img = "publish_g.png";
			else if ($blockview == 0)
				$blockview_img = "publish_r.png";
			else if ($blockview == 2)
				$blockview_img = "publish_y.png";
			else
				$blockview_img = "publish_x.png";
			$aff->blockview_alt = BS_UtilAcl::get_assetgroup_label($blockview);

			$aff->blockview = '<img src="'.$link_pre.$blockview_img.'" class="icon16" alt="blockview" title="'.$aff->blockview_alt.'" />';
		}

		if ($inst->search_aff_add & 2048 && isset($inst->result[$i]['language'])) { // Language & association (2048)
			if (isset($inst->languages[$inst->result[$i]['language']]->title))
				$aff->language = $inst->languages[$inst->result[$i]['language']]->title;
			else if ($inst->result[$i]['language'] == '*')
				$aff->language = JText::_('COM_MYJSPACE_LANGUAGE_ALL');
			else
				$inst->result[$i]['language']; // Default

			$aff->lang = $inst->result[$i]['language']; // Language tag

			if ($inst->separ <= 1) {
				if (isset($inst->languages[$inst->result[$i]['language']])) { // Language
					$sef = $inst->languages[$inst->result[$i]['language']]->sef;
					$aff->language = JHtml::_('image', 'mod_languages/'.$sef.'.gif', $sef, array('title' => $inst->languages[$inst->result[$i]['language']]->title), true);

					if ($inst->language_filter == 2 && JLanguageAssociations::isEnabled() == 1) { // Association J!3.0.3+
						if (BSHelperUser::countAssociations($inst->result[$i]['id']) > 0) {
							$aff->language .= '<img src="'.$link_pre.'association.png" class="icon12" alt="association" title="'.JText::_('COM_MYJSPACE_LABELASSOCIATION').'" />';
						}
					}
				} else {
					$aff->language = $inst->result[$i]['language'];
					if ($aff->language == '*')
						$aff->language = JText::_('COM_MYJSPACE_LANGUAGE_ALL');
				}
			}
		}

		if ($inst->search_aff_add & 4096 && isset($inst->result[$i]['access'])) { // Share page (4096)
			if ($inst->result[$i]['access'] > 0) {
				if ($inst->result[$i]['userid'] != $inst->user->id && $inst->user->id != 0) {
					$table = JUser::getTable();
					if ($table->load($inst->result[$i]['userid'])) {
						$inst->user = JFactory::getUser($inst->result[$i]['userid']);
					} else {
						$inst->user = new stdClass();
						$inst->user->username = '-';
					}

					if ($inst->share_page) {
						$aff->share_page = ' <img src="'.$link_pre.'share.png" class="icon12" alt="share" title="'.JText::_('COM_MYJSPACE_LABELUSERNAME').JText::_('COM_MYJSPACE_2POINTS').$inst->user->username.'" />';
					}
				} else if ($inst->result[$i]['userid'] == $inst->user->id && $inst->result[$i]['userid'] > 0) {
					if ($inst->share_page) {
						$aff->share_page = ' <img src="'.$link_pre.'share_nb.png" class="icon12" alt="access" title="'.JText::_('COM_MYJSPACE_TITLESHAREEDIT').JText::_('COM_MYJSPACE_2POINTS').BS_UtilAcl::get_assetgroup_label($inst->result[$i]['access'], true).'" />';
					}
				}
			}
		}

		if ($inst->search_aff_add & 8192) { // J!3.4.4+ tags (8192)
			$tagLayout = new JLayoutFile('joomla.content.tags');
			$tags = new JHelperTags;
			$tags->getItemTags('com_myjspace.see', $inst->result[$i]['id']);
			if (!empty($tags) && count($tags->itemTags) > 0) {
				if ($separ > 1) {
					$chaine_tag = '';
					foreach($tags->itemTags as $key => $value) {
						$chaine_tag .= ' '.$tags->itemTags[$key]->title;
					}
					$aff->jtag = $chaine_tag;
				} else {
					$aff->jtag = $tagLayout->render($tags->itemTags);
				}
			}
		}

		return $aff;
	}

	public static function title($inst)
	{
		// Config
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();

		// Component
		$pparams = JComponentHelper::getParams('com_myjspace');

		// Web page title
		if ($pparams->get('pagetitle', 1) == 1) {
			if ($inst->search_page_title)
				$title = $inst->search_page_title;
			else
				$title = JText::_('COM_MYJSPACE_TITLESEARCH');
			if (empty($title)) {
				$title = $app->get('sitename');
			} elseif ($app->get('sitename_pagetitles', 0) == 1) {
				$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
			} elseif ($app->get('sitename_pagetitles', 0) == 2) {
				$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
			}
			if ($title)
				$document->setTitle($title);
		}
	}
}
