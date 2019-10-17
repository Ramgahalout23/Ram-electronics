<?php
/**
* @version $Id:	user.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2010 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

class JTableMyjspace extends JTable // For J!3.1 tags usage (for the moment)
{
	var $id = 0; // V 2.0.0 - Page id
	var $userid = 0; // User page id
	var $catid = 0; // V 2.0.1 - Category id
	var $modified_by = 0; // V 2.0.1 - modified by: user id
	var $access = 0; // V 2.0.1 - shared page access: group id
	var $title = null; // V 2.2.0 - Page title
	var $pagename = null; // Page name (= title alias)
	var $content = null; // Page content
	var $blockedit = 0; // Is the page locked by admin for owner editing (0:no, 1: yes edit suspended, 2: lock = edit & admin suspended)
	var $blockview = 1; // See view access; 0:lock, 1:public, 2:registered
	var $foldername = null; // Page folder name
	var $create_date = null;
	var $last_access_date = null;
	var $last_update_date = null;
	var $last_access_ip = 0; // crc32b last access IP value
	var $hits = 0; // Hits number
	var $publish_up = null;
	var $publish_down = null;
	var $metakey = null; // Page description
	var $template = null; // J! template
	var $language = '*'; // Language
	var $index_format_version = 11.0; // V 3.0.0 (can be numeric, but only the integer part is used for the version checking)
	var $newTags = null; // V 2.2.0 - J!3.1.4 tags
	protected $tagsHelper = null; // V 2.2.0 - J!3.1 tags
	var $userread = null; // V 2.5.5

	// Pour les Tags
	function __construct(&$db)
	{
		parent::__construct('#__myjspace', 'id', $db);

		$this->tagsHelper = new JHelperTags(); // J!3.1.4+
		$this->tagsHelper->typeAlias = 'com_myjspace.see';
	}

	public function store($updateNulls = false)
	{
		$this->tagsHelper->preStoreProcess($this); // J!3.1.4+

		if (!$this->tagsHelper->postStoreProcess($this)) {
			return false; // Tags not saved !
		} else {
			return true;
		}
	}

	// Delete uucm_ content & tags
	// pk : primary key
	public function delete($pk = null)
	{
		if ($this->id == 0)
			$this->id = $pk;
//		$result = parent::delete($pk);
		$result = $this->tagsHelper->unTagItem($pk, $this);

		return $result && $this->tagsHelper->deleteTagData($this, $pk);
	}

	// Get object (param) values and set to the current object
	// $user_page : BSHelperUser object (user page infos)
	public function get_row_BSHelperUser($user_page = null)
	{
		foreach($user_page as $key => $value) {
			if (isset($user_page->$key))
				$this->$key = $user_page->$key;
		}
	}
}

// -----------------------------------------------------------------------------

class BSHelperUser
{
	var $id = 0; // V 2.0.0
	var $userid = 0;
	var $catid = 0; // V 2.0.1
	var $modified_by = 0; // V 2.0.1
	var $access = 0; // V 2.0.1
	var $title = null; // // V 2.2.0 - Page title
	var $pagename = null; // Page name (= title alias)
	var $content = null;
	var $blockedit = 0; // Is the page locked by admin for owner editing (0:no, 1:yes edit suspended, 2:lock = edit & admin suspended)
	var $blockview = 1; // V 2.0.2 = 1 (0:lock, 1:public, 2:registered)
	var $foldername = null;
	var $create_date = null;
	var $last_access_date = null;
	var $last_update_date = null;
	var $last_access_ip = 0; // crc32b value
	var $hits = 0;
	var $publish_up = null;
	var $publish_down = null;
	var $metakey = null;
	var $template = null; // J! template
	var $language = '*';
	var $index_format_version = 11.0; // V 3.0.0
	var $metadata = null; // V 2.2.0
	var $userread = null; // V 2.5.5

// Constructor
	function __construct()
	{
		$this->foldername = self::getRootFoldername(); // Default, regular method
	}

// Error
	function setError($e = '')
	{
		$pparams = JComponentHelper::getParams('com_myjspace');

		if ($pparams->get('debug', 0)) {
			$msg = JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage());
			JFactory::getApplication()->enqueueMessage($msg, 'error');
		}
	}

// Get variable. Use for the User Action Logs
	public function get($id_holder)
	{
		if (isset($this->$id_holder))
			return $this->$id_holder;
		else
			return null;
	}

// DB: Page new create 'empty' content for the current user
//		return the page id
	function createPage($pagename = '', $catid = 0)
	{
		if (!$this->title)
			$this->title = $this->pagename;

		$this->setFoldername(); // Set foldername before creating

		$db	= JFactory::getDBO();

		$columns = array('userid', 'title', 'pagename', 'foldername', 'content', 'blockedit', 'blockview', 'metakey', 'template', 'catid', 'modified_by', 'access');
		$values = $db->q(intval($this->userid)).', '.$db->q($this->title).', '.$db->q($pagename).', '.$db->q($this->foldername).", '', '0', '1', '', '', ".$db->q(intval($catid)).', '.$db->q(intval($this->userid)).", '0'";

		$query = $db->getQuery(true)
			->insert('#__myjspace')
			->columns($columns)
			->values($values);

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			if (JDEBUG)
				JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');

			return false;
		}

		$this->id = $db->insertid();

		return $this->id;
	}

// DB: Set conf parameter: title, pagename, foldername, blockview, blockedit, publish_up, publish_down ... (for a page id) for a page
	function setConfPage($choice = 255)
	{
		$choice = intval($choice);

		$this->setFoldername(); // Set foldername before updating

		$db = JFactory::getDBO();

		$query = 'UPDATE '.$db->qn('#__myjspace').' SET ';
		$query .= $db->qn('last_update_date').' = CURRENT_TIMESTAMP,';
		$query .= $db->qn('foldername').' = '.$db->q($this->foldername).',';

		if ($choice & 1) {
			$query .= $db->qn('title').' = '.$db->q($this->title).',';
			$query .= $db->qn('pagename').' = '.$db->q($this->pagename).',';
		}
		if ($choice & 2)
			$query .= $db->qn('blockview').' = '.$db->q(intval($this->blockview)).',';
		if ($choice & 4)
			$query .= $db->qn('blockedit').' = '.$db->q(intval($this->blockedit)).',';
		if ($choice & 8 && ($this->publish_up))
			$query .= $db->qn('publish_up').' = '.$db->q($this->publish_up).',';
		if ($choice & 16 && ($this->publish_down))
			$query .= $db->qn('publish_down').' = '.$db->q($this->publish_down).',';
		if ($choice & 32)
			$query .= $db->qn('metakey').' = '.$db->q($this->metakey).',';
		if ($choice & 64)
			$query .= $db->qn('template').' = '.$db->q($this->template).',';
		if ($choice & 128)
			$query .= $db->qn('catid').' = '.$db->q(intval($this->catid)).',';
		if ($choice & 256)
			$query .= $db->qn('userid').' = '.$db->q(intval($this->userid)).',';
		if ($choice & 512)
			$query .= $db->qn('access').' = '.$db->q(intval($this->access)).',';
		if ($choice & 1024)
			$query .= $db->qn('modified_by').' = '.$db->q(intval($this->modified_by)).',';
		if ($choice & 2048)
			$query .= $db->qn('language').' = '.$db->q($this->language).',';
		if ($this->userread != '')
			$query .= $db->qn('userread').' = '.$db->q($this->userread).',';

		$query = substr($query, 0, -1); // Remove the last comma ...
		$query .= ' WHERE '.$db->qn('id').' = '.$db->q(intval($this->id));

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			if (JDEBUG)
				JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');

			return 0;
		}

		return 1;
	}

// DB & FS: Delete page & folder content
	function deletePage($link_folder = 1, $forced = 1)
	{
		if (!($this->pagename) || (!($this->foldername) && $link_folder == 1))
			return 0;

		if ($link_folder == 1) {
			$filedir = JPATH_SITE.'/'.$this->foldername.'/'.$this->pagename;

			$oldfolder = getcwd();
			if (!@chdir($filedir)) // Workaround for Windows & right access specifities
				return 0;

			// Delete all files from the folder
			$projectsListIgnore = array('.', '..'); // Safety
			$handle = @opendir('.');
			while (false !== ($file = @readdir($handle))) {
				if (!@is_dir($file) && !in_array($file, $projectsListIgnore)) {
					if ($forced == 0 && $file != 'index.php')
						return 0;

					if ($file != 'index.php' && !@unlink($file)) {
						@chdir($oldfolder);
						return 0;
					}
				}
			}
			if (!@unlink('index.php')) {
				@chdir($oldfolder);
				return 0;
			}

			@closedir($handle);
			@chdir(JPATH_SITE.'/'.$this->foldername);

			if (!(@rmdir($filedir) || @rename($filedir, JPATH_SITE.'/'.$this->foldername.'/#garbage'))) {
				@chdir($oldfolder);
				return 0;
			}
		}

		$db	= JFactory::getDBO();
		$query = 'DELETE FROM '.$db->qn('#__myjspace').' WHERE '.$db->qn('id').' = '.$db->q(intval($this->id));
		$db->setQuery($query);

		if ($link_folder == 1 && isset($oldfolder))
			@chdir($oldfolder);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			if (JDEBUG)
				JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');

			return 0;
		}

		return 1;
	}

// DB: Load all user page info (with content)
// $this->id or $this->pagename need to be set before call
// Choice=0 use id, choix=1 use pagename
// getcontent = 1 to load the content

	function loadPageInfo($choix = 0, $getcontent = 1)
	{
		$this->userid = 0;
		$this->title = null;
		$this->foldername = '';
		$this->content = null;
		$this->blockedit = 0;
		$this->blockview = 1;
		$this->create_date = null;
		$this->last_access_date = null;
		$this->last_update_date = null;
		$this->last_access_ip = 0;
		$this->hits = 0;
		$this->publish_up = null;
		$this->publish_down = null;
		$this->metakey = null;
		$this->template = null;
		$this->catid = 0;
		$this->access = 0;
		$this->modified_by = 0;
		$this->language = '*';
		$this->userread = '';

		if (($this->id > 0 && $choix == 0) || ($this->pagename != '' && $choix == 1)) {
			$db = JFactory::getDBO();

			if ($choix == 1)
				$where = $db->qn('pagename').' = '.$db->q($this->pagename);
			else
				$where = $db->qn('id').' = '.$db->q(intval($this->id));

			$select = array('id', 'userid', 'title', 'pagename', 'foldername', 'blockedit', 'blockview', 'create_date', 'last_update_date', 'last_access_date', 'last_access_ip', 'hits', 'publish_up', 'publish_down', 'metakey', 'template', 'catid', 'access', 'modified_by', 'language', 'userread');
			if ($getcontent)
				array_push($select, 'content');

			$query = $db->getQuery(true);
			$query->select($select);
			$query->from('#__myjspace');
			$query->where($where);

			$db->setQuery($query);
			$result_set = $db->loadObjectList();

			$this->id = 0;
			$this->pagename = null;
			if (isset($result_set[0])) {
				foreach($result_set[0] as $key => $value) {
					$this->$key = $result_set[0]->$key;
				}
				return 1;
			}
		}

		return 0;
	}

// DB: Load user info (without content)
	function loadPageInfoOnly($choix = 0)
	{
		$this->loadPageInfo($choix, 0);
	}

// DB: Update content (= personal page)
	function updateUserContent()
	{
		$db	= JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->update('#__myjspace');
		$query->set($db->qn('content').' = '.$db->q($this->content).', '.$db->qn('modified_by').' = '.$db->q($this->modified_by).', '.$db->qn('last_update_date').' = CURRENT_TIMESTAMP');
		$query->where($db->qn('id').' = '.$db->q(intval($this->id)));

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			if (JDEBUG)
				JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');

			return 0;
		}

		return 1;
	}

// DB: Update current date and hit for the last access if not same ip addr compare to the last (too simple but efficient)
	function updateLastAccess($last_access_ip = '')
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->update('#__myjspace');
		$query->set($db->qn('last_access_date').' = CURRENT_TIMESTAMP, '.$db->qn('last_access_ip').' = '.$db->q($last_access_ip).', '.$db->qn('hits').' = '.$db->qn('hits').' + 1');
		$query->where($db->qn('id').' = '.$db->q(intval($this->id)).' AND '.$db->qn('last_access_ip').' <> '.$db->q($last_access_ip));

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			if (JDEBUG)
				JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');

			return 0;
		}

		return 1;
	}

// DB: Reset Hits & Update Date
	function resetLastAccess()
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->update('#__myjspace');
		$query->set($db->qn('last_access_date').' = DEFAULT, '.$db->qn('last_access_ip').' = 0, '.$db->qn('hits').' = 0');
		$query->where($db->qn('id').' = '.$db->q(intval($this->id)));

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			if (JDEBUG)
				JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');

			return 0;
		}

		return 1;
	}

// DB: Check if pagename already exists by name, return id
	public static function ifExistPageName($pagename = '')
	{
		if ($pagename == self::getRootFoldername()) // To avoid page with name = rootfoldername
			return -1;

		$db = JFactory::getDBO();

		$query = $db->getQuery(true)
			->select('id')
			->from('#__myjspace')
			->where($db->qn('pagename').' = '.$db->q($pagename));

		$db->setQuery($query);

		return $db->loadResult();
	}

// DB: Select a specific content by Page id
	function getContentPageId($id = 0)
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true)
			->select('content')
			->from('#__myjspace')
			->where($db->qn('id').' = '.$db->q($id));

		$db->setQuery($query);

		return $db->loadResult();
	}

// DB: Get the list of page id, pagename, title for a specific user
//		If id specified, select only the concerned page (if owned by the user)
// 		If array() $access specified include share pages for the user list group
	function getListPageId($userid = 0, $id = 0, $catid = 0, $access = null)
	{
		$db = JFactory::getDBO();

		$where = '';
		$select = array('id', 'pagename', 'foldername', 'title');

		if ($access == null)
			$where .= $db->qn('userid').' = '.$db->q($userid);
		else
			$where .= ' ( '.$db->qn('userid').' = '.$db->q($userid).' OR '.$db->qn('access').' IN ('.implode(',', $access).') )';
		if ($id > 0)
			$where .= ' AND '.$db->qn('id').' = '.$db->q($id);
		if ($catid > 0)
			$where .= ' AND '.$db->qn('catid').' = '.$db->q($catid);

		$query = $db->getQuery(true);
		$query->select($select);
		$query->from('#__myjspace');
		$query->where($where);

		$db->setQuery($query);

		return $db->loadAssocList();
	}

// DB: Count the number of pages for a category for a user
// 		$this->userid need to be set before call
	public function countUserPageCategory($catid = 0)
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from('#__myjspace')
			->where($db->qn('userid').' = '.$db->q($this->userid).' AND '.$db->qn('catid').' = '.$db->q($catid));

		$db->setQuery($query);

		return $db->loadResult();
	}

// DB: Count the number of pages for a category for a user
	public static function countPageCategory($catid = 0)
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from('#__myjspace')
			->where($db->qn('catid').' = '.$db->q($catid));

		$db->setQuery($query);

		return $db->loadResult();
	}

// DB: Get categories list
	public static function getCategories($published = null, $language = null)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT a.id AS value, a.title AS text, a.level, a.published FROM '.$db->qn('#__categories').' AS a';
		$query .= ' LEFT JOIN '.$db->qn('#__categories').' AS b ON a.lft > b.lft';
		$query .= ' AND a.rgt < b.rgt';
		$query .= ' WHERE a.extension = '.$db->q('com_myjspace');

		if ($published != null)
			$query .= ' AND a.published = '.$db->q($published);
		else
			$query .= ' AND a.published IN (0, 1)';

		if ($language != null)
			$query .= ' AND a.language IN ('.$db->q('*').', '.$db->q($language).')';

		$query .= ' GROUP BY a.id, a.title, a.level, a.lft, a.rgt, a.extension, a.parent_id, a.published';
		$query .= ' ORDER BY a.lft, a.level ASC';
		$db->setQuery($query);

		return $db->loadAssocList();
	}

// DB: Get published categories list & count usage on pages
//		optional: for a specific user & language
	public static function getCategoriesCountPages($userid = 0, $language = null, $count = false, $limitstart = 0, $affmax = 0)
	{
		$db = JFactory::getDBO();
		$nullDate = $db->getNullDate();

		if ($affmax < 0)
			return null;

		if ($count == true)
			$query = 'SELECT COUNT(distinct c.id)';
		else
			$query = 'SELECT c.title AS title, c.id AS catid, c.level AS level, count(m.id) AS nb';

		$query .= ' FROM '.$db->qn('#__categories').' AS c';

		$query .= ' LEFT JOIN '.$db->qn('#__myjspace').' AS m';
		$query .= ' ON c.id = m.catid';
		$query .= ' WHERE c.extension = '.$db->q('com_myjspace');
		$query .= ' AND c.published = 1';
		$query .= ' AND m.blockview != 0';
		$query .= ' AND m.publish_up < CURRENT_TIMESTAMP';
		$query .= ' AND (m.publish_down >= CURRENT_TIMESTAMP OR m.publish_down = '.$db->q($nullDate).')';

		if ($userid)
			$query .= ' AND m.userid = '.$db->q($userid);

		if ($language != null)
			$query .= ' AND c.language IN ('.$db->q('*').', '.$db->q($language).')';

		if ($count == false) {
			$query .= ' GROUP BY c.id';
			$query .= ' ORDER BY c.title ASC';
		}

		$db->setQuery($query, $limitstart, $affmax);

		if ($count == true)
			$row = $db->loadResult();
		else
			$row = $db->loadAssocList();

		return $row;
	}

// DB: Get category label for a specific published category
	public static function getCategoryLabel($catid = 0)
	{
		if ($catid == 0)
			return '';

		$db = JFactory::getDBO();

		$query = $db->getQuery(true)
			->select('title')
			->from('#__categories')
			->where($db->qn('extension').' = '.$db->q('com_myjspace').' AND '.$db->qn('id').' = '.$db->q($catid).' AND '.$db->qn('published').' = 1');

		$db->setQuery($query);

		return $db->loadResult();
	}

// DB: count the number of pages with old alias (with upercase or _) for page created before BS MyJspace 2.6.5 (MySQL only)
	public static function countOldAlias()
	{
		$db = JFactory::getDBO();

		if (!in_array($db->name, array('mysql', 'mysqli'))) // If not MySQL, never had 'pagename' field with Upercase ...
			return 0;

		$query = 'SELECT COUNT(*) FROM '.$db->qn('#__myjspace').' WHERE BINARY '.$db->qn('pagename').' <> BINARY LOWER('.$db->qn('pagename').') OR '.$db->qn('pagename').' LIKE '.$db->q('%\_%');
		$db	= JFactory::getDBO();
		$db->setQuery($query);

		return $db->loadResult();
	}

// DB: Get user page(s) URL: Page URL if one & page list URL if more than one
//		if only one : can choose if display link as folder
// Return: Array of pages id, url

	public static function getUserUrl($userid = 0, $link_folder_print = 0, $xhtml = true, $force_list = 0)
	{
		require_once JPATH_ROOT.'/components/com_myjspace/helpers/util.php';

		$url = '';
		$user_page = New BSHelperUser();
		$list_page_tab = $user_page->getListPageId($userid);
		$nb_page = count($list_page_tab);

		if ($nb_page > 1 || $force_list == 1) {
			$url = JRoute::_('index.php?option=com_myjspace&view=pages&uid='.$userid, $xhtml);
		} else if ($nb_page == 1) {
			if ($link_folder_print == 1) {
				$url = JURI::base(true).'/'.$list_page_tab[0]['foldername'].'/'.$list_page_tab[0]['pagename'].'/';
			} else {
				$pparams = JComponentHelper::getParams('com_myjspace');
				if ($pparams->get('pagename_full_num', 0) == 0)
					$url = JRoute::_('index.php?option=com_myjspace&view=see&id='.$list_page_tab[0]['id'], $xhtml);
				else
					$url = JRoute::_('index.php?option=com_myjspace&view=see&pagename='.$list_page_tab[0]['pagename'], $xhtml);
			}
		}

		return (array($list_page_tab, $url));
	}

// DB: Get a new free pagename with a number as suffix
//		$prefix: pagename prefix, accepts tags: '#username', '#name', '#userid', '#category', '#catid'
//		$fin: max number of try to find a name (10000 !)
	function getPagenameFree($prefix = '#username', $user = null, $catid = 0, $fin = 10000)
	{
		// Prefix not alowed (front-end view name)
		$forbidden = array('config', 'delete', 'edit', 'myjspace', 'pages', 'search', 'see', 'categories');
		foreach ($forbidden as &$value) {
			if ($value == $prefix) {
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_MYJSPACE_PAGENAMENOTALLOWED', $prefix), 'notice');

				if ($user)
					$prefix = $user->username;
				else
					$prefix = '';

				break;
			}
		}

		// Replace the prefix tags & prefix cleaning
		if ($user) { // User
			$search = array('#username', '#name', '#userid');
			$replace = array($user->username, $user->name, $user->id);
			$prefix = str_replace($search, $replace, $prefix);
		}

		$pparams = JComponentHelper::getParams('com_myjspace');
		$catid = ($catid > 0) ? $catid : $pparams->get('default_catid', 0);
		if ($catid > 0) { // Category
			$search = array('#category', '#catid');
			$replace = array(self::getCategoryLabel($catid), $catid);
		} else {
			$search = array('#category', '#catid');
			$replace = array('', '');
		}
		$prefix_title = str_replace($search, $replace, $prefix);
		$prefix = self::stringURLSafe($prefix_title);

		$db = JFactory::getDBO();

		if (in_array($db->name, array('mysql', 'mysqli'))) { // MySQL only
			$searchEscaped = '('.$db->q('^'.$db->escape($prefix, true).'[0-9]*$').')';
			$query = 'SELECT '.$db->qn('pagename').' FROM '.$db->qn('#__myjspace').' WHERE BINARY '.$db->qn('pagename').' RLIKE '.$searchEscaped;
		} else { // More simple
			$searchEscaped = '('.$db->q($prefix.'%', true).')';
			$query = 'SELECT '.$db->qn('pagename').' FROM '.$db->qn('#__myjspace').' WHERE '.$db->qn('pagename').' LIKE '.$searchEscaped;
		}

		$db->setQuery($query);
		$list_pages = $db->loadAssocList();

		$nb_list = count($list_pages);

		// If no page with this $prefix use the prefix as pagename else find a suffix
		if ($nb_list != 0) {
			// To do not have suffix = 1 if a pagename = $prefix exists
			$debut = 1;
			for ($j = 0; $j < $nb_list; $j++) {
				if ($list_pages[$j]['pagename'] == $prefix) {
					$debut = 2;
					break;
				}
			}

			for ($i = $debut; $i <= $fin; $i++) {
				$ok = true;
				for ($j = 0; $j < $nb_list; $j++) {
					if ($list_pages[$j]['pagename'] == $prefix.$i) {
						$ok = false;
						break;
					}
				}

				if ($ok == true) {
					if (self::stringURLSafe($prefix_title.$i) == $prefix.$i)
						return $prefix_title.$i;
					else
						return $prefix.$i;
				}
			}
		}

		// No page with the prefix ($nb_list == 0) or too many existing pages with numbers ... choose it yourself
		return $prefix;
	}

// DB: List of pagename (if $resultmode = 1 add metakey)
//		or count the number of line for the same criteria
	public static function loadPagename($triemode = -1, $affmax = 0, $blocked = 0, $publish = 0, $content = 0, $check_search = null, $scontent = '', $resultmode = 0, $limitstart = 0, $count = false, $catid = -1, $language = null, $extra_query = null, $username = null, $list_blocked = 1)
	{
		$db = JFactory::getDBO();
		$nullDate = $db->getNullDate();

		// Safety
		$resultmode = intval($resultmode);

		if ($affmax < 0)
			return null;

		if ($count == true)
			$query = 'SELECT COUNT(*)';
		else {
			// Columns to 'display'
			$query = 'SELECT m.id AS id, m.userid AS userid, m.title AS title, m.pagename AS pagename, m.foldername AS foldername, m.blockview AS blockview, m.hits AS hits, m.create_date AS create_date, m.last_update_date AS last_update_date'; // id(username) = 1, pagename = 2 for display (search)

			if ($resultmode & 2) {
				$query .= ', u.name AS uname';
				$query .= ', u.name AS username';
			}
			if ($resultmode & 4)
				$query .= ', m.metakey AS metakey';
			// 64 for image (search)
			if ($resultmode & 128) {
				$query .= ', m.catid AS catid';
				$query .= ', c.title AS ctitle';
			}
			if ($resultmode & 256)
				$query .= ', m.content AS content';
			if ($resultmode & 512)
				$query .= ', LENGTH(m.content) AS size';
			// 1024 blockview
			if ($resultmode & 2048) // Language
				$query .= ', m.language AS language';
			if ($resultmode & 4096) // Share group
				$query .= ', m.access AS access';
		}

		$query .= ' FROM '.$db->qn('#__myjspace').' AS m';

		if ($username  || ($resultmode & 2))
			$query .= ' LEFT JOIN '.$db->qn('#__users').' AS u ON u.id = m.userid';

		if ($resultmode & 128)
			$query .= ' LEFT JOIN '.$db->qn('#__categories').' AS c ON m.catid = c.id';

		$query .= ' WHERE 1=1 ';

		// Criteria

		if ($blocked) { // Acces levels criteria
			if ($list_blocked == 1)
				$query .= ' AND '.$db->qn('blockview').' != 0';

			if ($blocked > 0) { // Only pages from my ACL and my pages
				$user_actual = JFactory::getUser();
				if ($user_actual->id == 0)
					$query .= ' AND m.blockview IN ('.implode(',', $user_actual->getAuthorisedViewLevels()).')';
				else
					$query .= ' AND m.blockview IN ('.implode(',', $user_actual->getAuthorisedViewLevels()).') OR m.userid = '.$user_actual->id.')';
			}
		}

		if ($publish)
			$query .= ' AND m.publish_up < CURRENT_TIMESTAMP AND (m.publish_down >= CURRENT_TIMESTAMP OR m.publish_down = '.$db->q($nullDate).')';

		if ($content == 1)
			$query .= ' AND m.content != '.$db->q('');

		if ($content == -1)
			$query .= ' AND m.content = '.$db->q('');

		if ($language)
			$query .= ' AND m.language IN ('.$db->q('*').', '.$db->q($language).')';

		if (is_array($catid)) {
			$nb_catid = count($catid); // Values need to be 'quoted' into the array !
			for ($i = 0; $i < $nb_catid; $i++)
				$catid[$i] = $db->q($catid[$i]);
			$query .= ' AND m.catid IN ('.implode(',', $catid).')';
		} else if ($catid > -1) {
			$query .= ' AND m.catid = '.$db->q($catid);
		}

		if ($check_search != null && count($check_search) > 0 && $scontent != '') {
			$query .= ' AND ( 1=0 ';

			$pparams = JComponentHelper::getParams('com_myjspace');
			if ($pparams->get('search_html', 1)) // Search into HTML content
				$scontent = htmlentities($scontent, ENT_QUOTES, 'UTF-8');

			$tab_scontent = explode(' ', $scontent);
			if (count($tab_scontent)) {
				$scontent = '';
				foreach ($tab_scontent as $word) {
					$scontent .= '%'.$db->escape($word, true);
				}
				$scontent .= '%';
			}

			if (isset($check_search['name']))
				$query .= ' OR m.pagename LIKE '.$db->q($scontent, false);

			if (isset($check_search['description']))
				$query .= ' OR m.metakey LIKE '.$db->q($scontent, false);

			if (isset($check_search['content']))
				$query .= ' OR m.content LIKE '.$db->q($scontent, false);

			if ($username) {
				$username = '%'.$username.'%';
				$query .= ' OR u.name LIKE '.$db->q($username, false).' OR u.username LIKE '.$db->q($username, false);
			}

			$query .= ' ) ';
		}

		// Extra query
		if ($extra_query)
			$query .= $extra_query;

		// Sort order
		if ($count == false) {
			if (is_numeric($triemode)) {
				if (JDEBUG)
					JLog::add(sprintf('%s sort order using numeric param is deprecated (%). Use char instead ', $triemode, __METHOD__), JLog::WARNING, 'deprecated'); // EVOL to be deleted with numeric trimode code after confirmation with the code part
				if ($triemode == 0)
					$query .= ' ORDER BY m.pagename ASC';
				else if ($triemode == 1)
					$query .= ' ORDER BY m.pagename DESC';
				else if ($triemode == 3)
					$query .= ' ORDER BY m.create_date DESC';
				else if ($triemode == 4)
					$query .= ' ORDER BY m.last_update_date DESC';
				else if ($triemode == 5)
					$query .= ' ORDER BY m.hits DESC';
				else
					$query .= ' ORDER BY m.pagename ASC';
			} else if (is_string($triemode) && $triemode != '') {
				$query .= ' ORDER BY '.$triemode;
			}
		}

		// Query
		$db->setQuery($query, $limitstart, $affmax);

		if ($count == true)
			$row = $db->loadResult();
		else
			$row = $db->loadAssocList();

		return $row;
	}

// DB: Count the total number of pages
	public static function myjsp_count_nb_page()
	{
		$db = JFactory::getDBO();
		$query = 'SELECT COUNT(*) FROM '.$db->qn('#__myjspace');
		$db->setQuery($query);

		return $db->loadResult();
	}

// DB: Count the number of distinct users
	public static function myjsp_count_nb_user()
	{
		$db = JFactory::getDBO();
		$query = 'SELECT COUNT(DISTINCT '.$db->qn('userid').') FROM '.$db->qn('#__myjspace');
		$db->setQuery($query);

		return $db->loadResult();
	}

// DB: Find the max. number of pages per user
	public static function myjsp_max_page_per_user()
	{
		$db = JFactory::getDBO();
		$query = 'SELECT MAX('.$db->qn('mycol').') FROM (SELECT COUNT('.$db->qn('userid').') AS mycol FROM '.$db->qn('#__myjspace').' GROUP BY '.$db->qn('userid').') AS compteur';
		$db->setQuery($query);

		return $db->loadResult();
	}

// DB: Find the max. number of pages per categories per user
	public static function myjsp_max_cat_per_user()
	{
		$db = JFactory::getDBO();
		$query = 'SELECT MAX(count) FROM (SELECT COUNT(*) as count, userid, catid FROM '.$db->qn('#__myjspace').' WHERE catid > 0 GROUP BY userid, catid) AS compteur';
		$db->setQuery($query);

		return $db->loadResult();
	}

// DB: Find pages association J!3.0.3+
	public static function getAssociations($id = 0)
	{
		$associations = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->from('#__myjspace AS c');
		$query->innerJoin('#__associations AS a ON a.id = c.id AND a.context = '.$db->q('com_myjspace.item'));
		$query->innerJoin('#__associations AS a2 ON a.key = a2.key');
		$query->innerJoin('#__myjspace AS c2 ON a2.id = c2.id');
		$query->where('c.id ='.intval($id));
		$select = array('c2.language', 'c2.id', 'c2.pagename');
		$query->select($select);
		$db->setQuery($query);
		$contactitems = $db->loadObjectList('language');

		foreach ($contactitems as $tag => $item) {
			$associations[$tag] = $item;
		}

		return $associations;
	}

// DB: Count the number of associations for a page J!3.0.3+
	public static function countAssociations($id = 0)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT COUNT('.$db->qn('id').') FROM '.$db->qn('#__associations').' WHERE '.$db->qn('context').' = '.$db->q('com_myjspace.item').' AND '.$db->qn('id').' = '.$db->q($id);
		$db->setQuery($query);

		return $db->loadResult();
	}

// DB: Set pages association ($associations = page id list) J!3.0.3+
	public static function setAssociations($associations)
	{
		foreach ($associations as $tag => $id) { // Clean
			$associations[$tag] = intval($id);
			if ($associations[$tag] <= 0)
				unset($associations[$tag]);
		}

		if (count($associations) == 0) // Safety check
			return false;

		foreach ($associations as $tag => $id) { // No association for 'all' language!
			if ($tag == '*')
				return -1;
		}

		// Deleting old association for these items
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__associations');
		$query->where($db->qn('context').' = '.$db->q('com_myjspace.item'));
		$query->where($db->qn('id').' IN ('.implode(',', $associations).')');
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			if (JDEBUG)
				JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');

			return false;
		}

		// Adding new association for these items
		if (count($associations) > 1) {
			$key = md5(json_encode($associations));
			$query->clear();
			$query->insert('#__associations');
			foreach ($associations as $tag => $id) {
				$query->values($id.','.$db->q('com_myjspace.item').','.$db->q($key));
			}
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				if (JDEBUG)
					JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');

				return false;
			}
		}

		return true;
	}

// DB: Del pages association (J!3.0.3+). Note that for Joomla! article ... no association row are deleted!
	public static function delAssociations($id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->delete('#__associations');
		$query->where($db->qn('context').' = '.$db->q('com_myjspace.item'));
		$query->where($db->qn('id').' = '.$db->q($id));
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			if (JDEBUG)
				JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');

			return false;
		}
/*
// EVOL ?
// Other algorithm (delete all the related associations)
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Get the correct key to identify the association to be deleted
		$query->select($db->qn('key'));
		$query->from('#__associations');
		$query->where($db->qn('context').' = '.$db->q('com_myjspace.item'));
		$query->where($db->qn('id').' = '.$db->q($id));
		$db->setQuery($query);
		$association = $db->loadColumn();

		// Deleting the associations linked with the page
		if (count($association)) {
			$query->clear();
			$query->delete('#__associations');
			$query->where($db->qn('context').' = '.$db->q('com_myjspace.item'));
			$query->where($db->qn('key').' IN (\''.str_replace(",", "','",implode(',', $association)).'\')');
			$db->setQuery($query);
			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				if (JDEBUG)
					JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');

				return false;
			}
		}
*/
		return true;
	}

// DB: Return the list of available languages (tag + label), including * for all
	public static function get_language_list()
	{
		$db	= JFactory::getDBO();
		$query = 'SELECT '.$db->qn('lang_code').', '.$db->qn('title').', '.$db->qn('sef').' FROM '.$db->qn('#__languages').' ORDER BY '.$db->qn('lang_code').' ASC';
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public static function get_language_add_all($language_list)
	{
		$language_list0[0] = new stdClass();
		$language_list0[0]->lang_code = '*';
		$language_list0[0]->title = JText::_('COM_MYJSPACE_LANGUAGE_ALL');
		$language_list0[0]->sef = '';
		$language_list = array_merge($language_list0, $language_list);

		return $language_list;
	}

// DB: Return the language title
	public static function get_language_title($lang_code = '*')
	{
		$db	= JFactory::getDBO();
		$query = 'SELECT '.$db->qn('title_native').' FROM '.$db->qn('#__languages').' WHERE '.$db->qn('lang_code').' = '.$db->q($lang_code);
		$db->setQuery($query);
		$result = $db->loadResult();

		if ($result)
			return $result;
		else
			return JText::_('COM_MYJSPACE_LANGUAGE_ALL');
	}

// FS: Create subdir(s) if not already exists, start 'after' $rootdir under JPATH_SITE
	public static function createSubdir($withsubdir = '', $rootdir = '')
	{
		if ($withsubdir != $rootdir) { // Si il y a lieu !
			JPluginHelper::importPlugin('myjspace', 'foldername'); // Import & call the constructor (if exists)
			JFactory::getApplication()->triggerEvent('onMyJspaceCreateSubdir', array($withsubdir, $rootdir)); // List of labels
		}
	}

// FS: Delete subdir(s) if exists, start 'after' $rootdir under JPATH_SITE
	public static function deleteSubdir($withsubdir = '', $rootdir = '')
	{
		if ($withsubdir != $rootdir) { // Si il y a lieu !
			JPluginHelper::importPlugin('myjspace', 'foldername'); // Import & call the constructor (if exists)
			JFactory::getApplication()->triggerEvent('onMyJspaceDeleteSubdir', array($withsubdir, $rootdir)); // List of labels
		}
	}

// FS: Page Create Folder & file to redirect
	function createDirFilePage($pagename = '', $choix = 1, $id = 0)
	{
		self::createSubdir($this->foldername, self::getRootFoldername()); // Create subdir if necessary (for 'complex' naming method)

		$filedir = JPATH_SITE.'/'.$this->foldername.'/'.$pagename;

		if ($id != 0)
			$page_id = $id;
		else
			$page_id = $this->id;

		if ($choix == 1)
			$content_id = 'pagename='.$pagename;
		else
			$content_id = 'id='.$page_id;

		$url = JURI::root().'index.php?option=com_myjspace&view=see&'.$content_id;
		$protocol = empty($_SERVER['HTTPS']) ? '': ($_SERVER['HTTPS'] == 'on') ? 'https' : 'http';

$content = "<?php
// com_myjspace
// Format:".$this->index_format_version."
// Pagename:".$pagename."
// Id:".$page_id."
// Protocol:".$protocol."
// ?>
<html>
<head>
<meta http-equiv=\"refresh\" content=\"0; URL=$url\">
</head>
<body>
<script>window.location=\"$url\"</script>
</body>
</html>
";

		// Folder (may already exists)
		@mkdir($filedir, 0755);

		// File index.php
		$file = $filedir.'/index.php';
		$handle = @fopen($file, 'w');
		if ($handle) {
			@fwrite($handle, $content);
			@chmod($file, 0755);

			return 1;
		}

		return 0;
	}

// FS: Retrieve the format version and protocol for the index file
	public static function versionIndexPage($pagename = '', $foldername = '')
	{
		$file_index = JPATH_SITE.'/'.$foldername.'/'.$pagename.'/index.php';

		$contenu = @fread(@fopen($file_index, 'r'), 120); // Check only the 120 first chars

		// Format
		$sortie = null;
		preg_match('#// Format:(.*)\n#Us', $contenu, $sortie);

		if (isset($sortie[1]))
			$version = trim($sortie[1]);
		else
			$version = 0;

		// Protocol
		$sortie = null;
		preg_match('#// Protocol:(.*)\n#Us', $contenu, $sortie);

		if (isset($sortie[1]))
			$protocol = trim($sortie[1]);
		else
			$protocol = '';

		return array($version, $protocol);
	}

// Check the number of index page with NOT the actual format (int version of the format) and protocol for all pages or only the oldest
// Return the number of page with index.hp no ) to current version ('false' if no page)
	public static function checkversionIndexPage($only_oldest = true)
	{
		$nb_index_ko = -1;
		$pparams = JComponentHelper::getParams('com_myjspace');

		if ($pparams->get('link_folder', 1) == 1) {
			$user_page = New BSHelperUser();
			$current_version = $user_page->index_format_version; // Check from Class

			$current_protocol = empty($_SERVER['HTTPS']) ? '': ($_SERVER['HTTPS'] == 'on') ? 'https' : 'http';

			$db = JFactory::getDBO();

			$query = 'SELECT id, '.$db->qn('pagename').', '.$db->qn('foldername').' FROM '.$db->qn('#__myjspace');

			if ($only_oldest == true)
				$query .= ' WHERE '.$db->qn('create_date').' IN (SELECT MIN('.$db->qn('create_date').') FROM '.$db->qn('#__myjspace').' )';

			$db->setQuery($query);

			$userpage_list = $db->loadAssocList();

			$nb_page = count($userpage_list);

			if ($nb_page == 0)
				return false;

			$nb_index_ko = 0;
			if ($nb_page > 0) {
				for ($i = 0; $i < $nb_page; $i++) {
					list($version, $protocol) = self::versionIndexPage($userpage_list[$i]['pagename'], $userpage_list[$i]['foldername']);

					if ((int)$version != (int)$current_version || $protocol != $current_protocol)
						$nb_index_ko = $nb_index_ko + 1;
				}
			}
		}

		return $nb_index_ko;
	}

// FOLDERNAME

// Set the user foldername value
	function setFoldername()
	{
		$foldername = self::getRootFoldername();
		$reponse = '';

		JPluginHelper::importPlugin('myjspace', 'foldername'); // Import & call the constructor (if exists)
		JFactory::getApplication()->triggerEvent('onMyJspaceSetFoldername', array($this, &$reponse));

		$foldername .= $reponse;

		if (strlen($foldername) > 150) // Control
			JFactory::getApplication()->enqueueMessage(JText::_('COM_MYJSPACE_FOLDERNAMETRUNCATE'), 'warning');

		$this->foldername = substr($foldername, 0, 150); // Limit
	}

// CFG: Get root foldername
	public static function getRootFoldername()
	{
		$pparams = JComponentHelper::getParams('com_myjspace');
		$foldername = $pparams->get('foldername', 'media/myjsp');

		return $foldername;
	}

// CFG: Set/Update root foldername
	public static function setRootFoldername($foldername = '')
	{
		require_once JPATH_ROOT.'/components/com_myjspace/helpers/version.php';

		$foldername_old = BSHelperUser::getRootFoldername();

		if ($foldername != '' && $foldername_old != $foldername) { // If update only
			$pparams = JComponentHelper::getParams('com_myjspace');
			$pparams->set('foldername', $foldername);
			self::save_parameters($pparams, 'com_myjspace'); // Re-save all parameters
			return 1;
		}

		return 0;
	}

// DB: Save parameters from memory to DB
	public static function save_parameters($pparams = null, $component = null)
	{
		$data = $pparams->toString('JSON');

		$db	= JFactory::getDBO();
		$query = 'UPDATE '.$db->qn('#__extensions').' SET '.$db->qn('params').' = '.$db->q($data).' WHERE '.$db->qn('element').' = '.$db->q($component).' AND '.$db->qn('type').' = '.$db->q('component');
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			if (JDEBUG)
				JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');
		}
	}

// FS: Test if the 'real' foldername exists ans is writable
	public static function IsDirW($foldername = '', $absolute = true)
	{
		if ($absolute)
			$dir = JPATH_SITE.'/'.$foldername;
		else
			$dir = $foldername;

		if (@is_dir($dir) && @is_writable($dir))
			return true;
		else
			return false;
	}

// FS & CFG: Create or update page ROOT folder name
// $retour = 0, incorrect name
	public static function updateRootFoldername($foldername = '', $link_folder = 1, $keep = 0)
	{
		$foldername_old = BSHelperUser::getRootFoldername();

		if ($foldername_old != $foldername) {
			if ($link_folder == 1) {
				if ($keep == 0)
					@rename(JPATH_SITE.'/'.$foldername_old, JPATH_SITE.'/'.$foldername);
				else
					@mkdir(JPATH_SITE.'/'.$foldername, 0755);
			}

			if (BSHelperUser::IsDirW($foldername)) { // Verify the directory access
				self::setRootFoldername($foldername); // Save the new rootfolder into component parameters
			} else {
				return 0;
			}
		}

		$file = JPATH_SITE.'/'.$foldername.'/index.html';
		if (!@file_exists($file)) {
			@copy(JPATH_SITE.'/components/com_myjspace/index.html', $file);
		}

		return 1;
	}

// Check foldername characters. Only characters numbers / _ -
	public static function checkFoldername($foldername = '', $allowed = '#^[a-zA-Z0-9/_/-]+$#')
	{
		if (preg_match($allowed, $foldername))
			return 1;

		return 0;
	}

// Provide alias for page name compatible with url
	public static function stringURLSafe($string)
	{
		$separ = '-';

		// Remove any '_' ($separ) from the string since they will be used as concatenate
		$str = str_replace($separ, ' ', $string);

		// Language transliteration
		$str = JLanguageTransliterate::utf8_latin_to_ascii($str); // Transliterate, this new version ok and not language dependant

		// Trim white spaces at beginning and end of alias
		$str = trim($str);

		// Lowercase
		$str = strtolower($str);

		// Remove any duplicate 'separator' and ensure all characters are alphanumeric or -
		$rule = '/(\s|[^A-Za-z0-9'."\\".$separ.'])+/';
		$str = preg_replace($rule, $separ, $str);

		// Trim ($separ) at beginning and end of alias
		$str = trim($str, $separ);

		return $str;
	}

// PAGE CONTENT fct

// Substitute #tags with they contents
// Reserved words: #userid, #name, #username, #title, #pagename, #id, #access, #acces_edit', #lastupdate, #lastaccess, #createdate, #fileslist, #hits ... and a specific one #bsmyjspace :-)
// pos = 0 for page content, 1 for prefix, 2 for suffix
	function traite_prefsuf($atraiter = '', $user = null, $page_increment = 0, $date_fmt = 'Y-m-d H:i:s', $chaine_files = '', $top_bottom = false, $jtag = null)
	{
		if ($atraiter == null || $atraiter == '')
			return '';

		require_once JPATH_ROOT.'/components/com_myjspace/helpers/util_acl.php';
		$pparams = JComponentHelper::getParams('com_myjspace');

		// 'Complex' tag: myjsp iframe
		if ($top_bottom && $pparams->get('allow_tag_myjsp_iframe', 1) >= 1 || $pparams->get('allow_tag_myjsp_iframe', 1) == 1) {
			// Tag {myjsp iframe URL}
			$chaine_iframe = '<iframe src="$1" id="myjsp-iframe" frameborder="0" ></iframe>';
			$atraiter = preg_replace('!{myjsp iframe (.+)\}!isU', $chaine_iframe, $atraiter);
		}

		// 'Complex' tag: myjsp include
		if ($top_bottom && $pparams->get('allow_tag_myjsp_include', 1) >= 1 || $top_bottom && $pparams->get('allow_tag_myjsp_include', 1) == 1) {
			// Tag {myjsp include URL} (only the first url will be taking into account: to be used once per page + head + foot)
			if (preg_match('!{myjsp include (.+)\}!isU', $atraiter, $sortie)) {
				if (count($sortie) >= 2) {
					$fichier_sortie = @file_get_contents(trim($sortie[1]));
					preg_match('#<body(.*)>(.*)</body>#Us', $fichier_sortie, $tab_sortie);
					if (count($tab_sortie) >= 3)
						$atraiter = preg_replace('!{myjsp include (.+)\}!isU', '<div>'.$tab_sortie[2].'</div>', $atraiter);
				}
			}
		}

		// CB
		$chaine_cb = '<iframe src="'.JRoute::_('index.php?option=com_comprofiler&task=userProfile&user='.$user->id.'&tmpl=component', false).'" id="cbprofile" frameborder="0" ></iframe>';

		// Joomsocial
		$chaine_jsocial_profile = '<iframe src="'.JRoute::_('index.php?option=com_community&view=profile&userid='.$user->id.'&tmpl=component', false).'" id="jomsocial-profile" frameborder="0" ></iframe>';
		$chaine_jsocial_photos = '<iframe src="'.JRoute::_('index.php?option=com_community&view=photos&task=myphotos&userid='.$user->id.'&tmpl=component', false).'" id="jomsocial-photos" frameborder="0" ></iframe>';

		// BS MyJspace label & url
		$chaine_bsmyjspace = '<span class="bsfooter"><a href="'.JRoute::_('index.php?option=com_myjspace&amp;view=myjspace', false).'">BS MyJspace</a></span>';

		// Reserved words to replace
		$search = array('#userid', '#name', '#username', '#id', '#title', '#pagename', '#access', '#lastupdate', '#lastaccess', '#createdate', '#description', '#category', '#bsmyjspace', '#fileslist', '#cbprofile', '#jomsocial-profile', '#jomsocial-photos', '#jtag');
		$replace = array($user->id,
						$user->name,
						$user->username,
						$this->id,
						$this->title,
						$this->pagename,
						BS_UtilAcl::get_assetgroup_label($this->blockview),
						date($date_fmt, strtotime($this->last_update_date)),
						date($date_fmt, strtotime($this->last_access_date)),
						date($date_fmt, strtotime($this->create_date)),
						$this->metakey,
						self::getCategoryLabel($this->catid),
						$chaine_bsmyjspace,
						$chaine_files,
						$chaine_cb,
						$chaine_jsocial_profile,
						$chaine_jsocial_photos,
						str_replace(array('<div ', '</div>'), array('<span ', '</span>'), $jtag)
						);

		if ($pparams->get('share_page', 0) != 0) {
			$search[] = '#shareedit';
			$replace[] = BS_UtilAcl::get_assetgroup_label($this->access);

			$table = JUser::getTable();
			if ($table->load($this->modified_by)) { // Check if user exists before to retrieve info
				$modified_by = JFactory::getUser($this->modified_by);
			} else { // User do not exists any more !
				$modified_by = new stdClass();
				$modified_by->username = ' ';
			}
			$search[] = '#modifiedby';
			$replace[] = $modified_by->username;
		}

		if ($pparams->get('language_filter', 0) != 0) {
			$search[] = '#language';
			$replace[] = self::get_language_title($this->language);
		}

		if ($page_increment == 1) {
			$search[] = '#hits';
			$replace[] = $this->hits;
		}

		// Replace
		$atraiter = str_replace($search, $replace, $atraiter);

		return $atraiter;
	}

	// Function to have 'API' for component & plugins

	// Return the user pagename content if exist (with all tags replaced)
	// Usage examples into plugins: jsmyjspace, cb.myjspacetab

	public static function mjsp_exist_page_content($id = null, $pagebreak = 0) {
		$retour = '';

		// User & component
		$pparams = JComponentHelper::getParams('com_myjspace');
		$user_actual = JFactory::getUser();

		$db	= JFactory::getDBO();
		$nullDate = $db->getNullDate();

		// Personal page info
		if (intval($id) != 0) {
			$user_page = New BSHelperUser(); // For simple call from outside
			$user_page->id = $id;
			$user_page->loadPageInfo();
		} else if (isset($id->id)) {
			$user_page = $id;
		} else
			return '';

		$user = JFactory::getUser($user_page->userid);

		// Content & complete with prefix & suffix and replacing # tags
		$page_increment = $pparams->get('page_increment', 1);

		// Content
		$uploadimg = $pparams->get('uploadimg', 1);
		$tag_mysp_file_separ = $pparams->get('tag_mysp_file_separ', ' ');
		$chaine_files = '';
		if ($uploadimg == 1) { // May be add optional in the future
			require_once JPATH_ROOT.'/components/com_myjspace/helpers/util.php';
			$tab_list_file = BS_Util::list_file_dir(JPATH_SITE.'/'.$user_page->foldername.'/'.$user_page->pagename, '*', 1);
			$nb = count($tab_list_file);
			for ($i = 0 ; $i < $nb ; ++$i)
				$chaine_files .= '<a href="'.JURI::base().$user_page->foldername.'/'.$user_page->pagename.'/'.$tab_list_file[$i].'">'.$tab_list_file[$i].'</a>'.$tag_mysp_file_separ;
		}

		if ($pparams->get('allow_user_content_var', 1))
			$content = $user_page->traite_prefsuf($user_page->content, $user, $page_increment, JText::_('COM_MYJSPACE_DATE_FORMAT'), $chaine_files, false);
		else
			$content = $user_page->content;

		// [register]
		if ($pparams->get('editor_bbcode_register', 0) == 1) { // Allow to use the dynamic tag [register]
			$uri = JURI::getInstance();
			$return = $uri->toString();

			if ($pparams->get('url_login_redirect', ''))
				$url = $pparams->get('url_login_redirect', '');
			else
				$url = 'index.php?option=com_users&view=login';
			$url .= '&return='.base64_encode($return); // To return to the call page
			$url = JRoute::_($url, false);

			if ($user_actual->id != 0)// If not registered
				$content = preg_replace('!\[register\](.+)\[/register\]!isU', '$1', $content);
			else // If registered
				$content = preg_replace('!\[register\](.+)\[/register\]!isU', JText::sprintf('COM_MYJSPACE_REGISTER', $url), $content);
		}

		$prefix = '';
		$suffix = '';

		// Force default dates
		if ($pparams->get('publish_mode', 2) == 0) { // Do not take into account the dates
			$user_page->publish_up = $nullDate;
			$user_page->publish_down = $nullDate;
		}
		if ($user_page->publish_down == $nullDate)
			$user_page->publish_down = date('Y-m-d 00:00:00',strtotime('+1 day'));

		// Specific context
		$aujourdhui = time();
		if ($user_page->blockview == null) { // Page not found
			$content = '';
		} else if ($user_page->blockview == 0 && $user_actual->id != $user_page->userid) { // Page block
			$content = '';
		} else if ($user_page->blockview == 2 && $user_actual->username == '') { // Page reserved
			$content = '';
		} else if ($user_page->content == null) { // Page empty
			$content = '';
		} else if (strtotime($user_page->publish_up) > $aujourdhui || strtotime($user_page->publish_down) <= $aujourdhui) { // Page unpublished
			$content = '';
		} else { // Top and bottom
			if ($pparams->get('page_prefix', ''))
				$prefix = '<span class="top_myjspace">'.$user_page->traite_prefsuf($pparams->get('page_prefix', ''), $user, $page_increment, JText::_('COM_MYJSPACE_DATE_FORMAT'), $chaine_files, true).'</span><br />';
			if ($pparams->get('page_suffix', '#bsmyjspace'))
				$suffix = '<span class="bottom_myjspace">'.$user_page->traite_prefsuf($pparams->get('page_suffix', '#bsmyjspace'), $user, $page_increment, JText::_('COM_MYJSPACE_DATE_FORMAT'), $chaine_files, true).'</span><br />';
		}

		if ($pagebreak == 0) {
			$regex = '#<hr([^>]*?)class=(\"|\')system-pagebreak(\"|\')([^>]*?)\/*>#iU';
			$content = preg_replace($regex, '<br />', $content);
		}

		if ($content)
			$retour = '<div class="myjspace-prefix">'.$prefix.'</div><div class="myjspace-content"></div>'.$content.'<div class="myjspace-suffix">'.$suffix.'</div>';

		return $retour;
	}
}
