<?php
/**
* @version $Id:	script.myjspace.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2012 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

class com_myjspaceInstallerScript
{
	protected $minimumPHPVersion = '5.3.1';
	protected $minimumJoomlaVersion = '3.4.0';
	protected $componentName = 'BS MyJspace';
	protected $component = 'com_myjspace';
	protected $release_old = '999.0.0';

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

		if ($type == 'update') {
			$this->release_old = $this->getParam('version', $this->component);
		}

		return true;
	}

	// Method for postinstall
	public function postflight($type, $parent)
	{
		if ($type == 'uninstall')
			return true;

		$this->header_install(); // Display beginning of install message

		$pparams = JComponentHelper::getParams('com_myjspace');
		$db	= JFactory::getDBO();

		if ($type = 'update') {

			// *** Begin - Old config if saved ***

			// Get old foldername, to resave it into param (for version <= BS MyJspace 3.0.0)
			$query = $db->getQuery(true)
				->select('*')
				->from('#__myjspace_cfg');

			$db->setQuery($query);

			try {
				$row = $db->loadAssoc();
			}
			catch (RuntimeException $e) { // Table doesn't exists or query error
			}

			$foldername = '';
			if (isset($row['foldername'])) { // If migrate from old version < 3.0.0 & old MySQL table myjspace_cfg
				$foldername = $row['foldername'];

				$query = 'DROP TABLE IF EXISTS '.$db->qn('#__myjspace_cfg');
				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');
				}

				$query = 'CREATE TABLE IF NOT EXISTS '.$db->qn('#__myjspace_cfg').' ( ';
				$query .= $db->qn('id').' int(10) unsigned NOT NULL AUTO_INCREMENT,';
				$query .= $db->qn('params').' text NOT NULL,';
				$query .= 'PRIMARY KEY ('.$db->qn('id').')';
				$query .= ' ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci';
				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');
				}
			}

			if (isset($row['params'])) { // If migrate from version >= 3.0.0
				$params = json_decode($row['params'], true); // Get param from a previous install ?

				if (isset($params['foldername']))
					$foldername = $params['foldername']; // BS MyJspace 3.0.0 +
			}

			if ($foldername != '') { // To save content into parameters
				require_once JPATH_ROOT.'/components/com_myjspace/helpers/user.php';

				$pparams->set('foldername', $foldername);
				BSHelperUser::save_parameters($pparams, 'com_myjspace');
			}

			// *** End - Old config if saved ***
		}

		// Create the default BS MyJspace pages root folder, if not exists
		$foldername = $pparams->get('foldername', 'media/myjsp');
		if (!@is_dir(JPATH_SITE.'/'.$foldername)) {
			if (!@mkdir(JPATH_SITE.'/'.$foldername, 0755)) {
				return 0;
			}
		}

		// Migration from myjspace 1.2, BS MyJspace 1.5 to BS MyJspace 2.6.0 (previous install)
		if ($type == 'update' && version_compare($this->release_old, '2.6.0', 'le') && in_array($db->name, array('mysql', 'mysqli'))) { // Check to avoid to call for nothing (even if compatible)
			echo "Migrate from BS MyJspace ".$this->release_old."<br>";
			require_once __DIR__ .'/updateold.myjspace.php';
			mjsp_old_postflight();
		}

		// Model update : columns name in lowercase mandatory to keep it simple for PostgreSQL => same model for everybody :-)
		if ($type == 'update' && version_compare($this->release_old, '3.0.0', 'lt') && in_array($db->name, array('mysql', 'mysqli'))) { // Only for old version & MySQL
			$query = 'ALTER TABLE '.$db->qn('#__myjspace').' CHANGE '.$db->qn('blockEdit').' '.$db->qn('blockedit').' INT(10) unsigned NOT NULL DEFAULT 0';
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');
			}

			$query = 'ALTER TABLE '.$db->qn('#__myjspace').' CHANGE '.$db->qn('blockView').' '.$db->qn('blockview').' INT(10) unsigned NOT NULL DEFAULT 1';
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');
			}
		}

		if ($type == 'install' || $type == 'update') {

			// Add default ACL for the component
			$asset = JTable::getInstance('asset');

			if (!$asset->loadByName('com_myjspace')) {
				$root = JTable::getInstance('asset');
				$root->loadByName('root.1');
				$asset->name = 'com_myjspace';
				$asset->title = 'com_myjspace';
				$asset->setLocation($root->id, 'last-child');
			}

			$v26x_acl_rules = '{"core.admin":{"7":1},"core.options":{"7":1},"core.manage":{"6":1},"user.config":{"6":1,"2":1},"user.delete":{"6":1,"2":1},"user.edit":{"6":1,"2":1},"user.myjspace":{"1":1},"user.search":{"1":1,"2":1},"user.see":{"1":1,"2":1},"user.pages":{"1":1,"2":1}}';
			$acl_rules_updates = false;

			// Component first Install or update from an identified version with no ACL update
			if ($asset->rules == '' || $asset->rules == '{}' || $asset->rules == $v26x_acl_rules) { // No Rules => Store the default ACL rules into the database
				$asset->rules = '{"core.admin":{"7":1},"core.options":{"7":1},"core.manage":{"6":1},"user.config":{"6":1,"2":1},"user.delete":{"6":1,"2":1},"user.edit":{"6":1,"2":1},"user.myjspace":{"1":1},"user.search":{"1":1,"2":1},"user.see":{"1":1,"2":1},"user.pages":{"1":1,"2":1},"user.categories":{"1":1,"2":1}}';

				if (!$asset->check() || !$asset->store()) {
					JFactory::getApplication()->enqueueMessage('Error updating ACL', 'error');
					return false;
				}
				$acl_rules_updates = true;
			}

			// Component update
			$rules_tab = json_decode($asset->rules, true);
			$maj = false;

			if (!isset($rules_tab['users.pages']) || count($rules_tab['users.pages']) == 0) { // Migration to BS Myjspace 2.0.0 from older version with ACL but not with 'user.pages'
				$rules_tab['user.pages'] = array('1' => 1, '2' => 1);
				$maj = true;
			}

			if (!isset($rules_tab['core.admin']) || count($rules_tab['core.admin']) == 0) { // Migration Joomla! 3.4.5 => ACL updates
				$rules_tab['core.admin'] = array('7' => 1);
				$maj = true;
			}

			if (!isset($rules_tab['core.options']) || count($rules_tab['core.options']) == 0) { // Migration Joomla! 3.4.5 => ACL updates
				$rules_tab['core.options'] = array('7' => 1);
				$maj = true;
			}

			if (!isset($rules_tab['core.manage']) || count($rules_tab['core.manage']) == 0) { // Migration Joomla! 3.4.5 => ACL updates
				$rules_tab['core.manage'] = array('6' => 1);
				$maj = true;
			}

			// Note: access rights for 'Page administration', 'Delete page', 'Page Edit' may be added to 'Manager' & 'Administrator' group if necessary (but theses profile are 'registered' also ... !

			if ($maj) { // Save the updates
				$asset->rules = json_encode($rules_tab);
				if (!$asset->check() || !$asset->store()) {
					JFactory::getApplication()->enqueueMessage('Error updating ACL', 'error');
					return false;
				}
			}

			// Tags for J!3.1.4+ => add BS MyJspace into content_type
			$query = 'SELECT COUNT('.$db->qn('type_alias').') FROM '.$db->qn('#__content_types').' WHERE '.$db->qn('type_alias')." IN ('com_myjspace.see', 'com_myjspace.category')";
			$db->setQuery($query);
			$count = $db->loadResult();	
			if (!isset($count))
				return false;

			if ($count == 0) { // Install the config for the first time
				$query = 'INSERT INTO '.$db->qn('#__content_types').
				' ('.$db->qn('type_title').
				', '.$db->qn('type_alias').
				', '.$db->qn('table').
				', '.$db->qn('rules').
				', '.$db->qn('field_mappings').
				', '.$db->qn('router').') VALUES ';
				// Minimum ok to save tags => {"common":{"core_content_item_id":"id","core_title":"title","core_state":"state","core_alias":"alias","core_created_time":"null","core_modified_time":"null","core_body":"null", "core_hits":"null","core_publish_up":"null","core_publish_down":"null","core_access":"null", "core_params":"null", "core_featured":"null", "core_metadata":"null", "core_language":"null", "core_images":"null", "core_urls":"null", "core_version":"null", "core_ordering":"null", "core_metakey":"null", "core_metadesc":"null", "core_catid":"null", "core_xreference":"null", "asset_id":"null"}, "special":{"fulltext":"null"}}
				$query .= "('MyJspace Page', 'com_myjspace.see', '{\"special\":{\"dbtable\":\"#__myjspace\",\"key\":\"id\",\"type\":\"Content\",\"prefix\":\"JTable\",\"config\":\"array()\"},\"common\":{\"dbtable\":\"#__ucm_content\",\"key\":\"ucm_id\",\"type\":\"Corecontent\",\"prefix\":\"JTable\",\"config\":\"array()\"}}', '', '{\"common\":{\"core_content_item_id\":\"id\",\"core_title\":\"title\",\"core_state\":\"blockview\",\"core_alias\":\"pagename\",\"core_created_time\":\"created_date\",\"core_modified_time\":\"last_update_date\",\"core_body\":\"content\", \"core_hits\":\"hits\", \"core_publish_up\":\"null\",\"core_publish_down\":\"null\",\"core_access\":\"blockview\", \"core_params\":\"null\", \"core_featured\":\"null\", \"core_metadata\":\"metadata\", \"core_language\":\"language\", \"core_images\":\"null\", \"core_urls\":\"null\", \"core_version\":\"null\", \"core_ordering\":\"null\", \"core_metakey\":\"metakey\", \"core_metadesc\":\"null\", \"core_catid\":\"catid\", \"core_xreference\":\"null\", \"asset_id\":\"null\"}, \"special\": {}}', 'MyJspaceHelperRoute::getPageRoute'),";
				$query .= "('MyJspace Category', 'com_myjspace.category', '{\"special\":{\"dbtable\":\"#__categories\",\"key\":\"id\",\"type\":\"Category\",\"prefix\":\"JTable\",\"config\":\"array()\"},\"common\":{\"dbtable\":\"#__ucm_content\",\"key\":\"ucm_id\",\"type\":\"Corecontent\",\"prefix\":\"JTable\",\"config\":\"array()\"}}', '', '{\"common\":{\"core_content_item_id\":\"id\",\"core_title\":\"title\",\"core_state\":\"published\",\"core_alias\":\"alias\",\"core_created_time\":\"created_time\",\"core_modified_time\":\"modified_time\",\"core_body\":\"description\", \"core_hits\":\"hits\",\"core_publish_up\":\"null\",\"core_publish_down\":\"null\",\"core_access\":\"access\", \"core_params\":\"params\", \"core_featured\":\"null\", \"core_metadata\":\"metadata\", \"core_language\":\"language\", \"core_images\":\"null\", \"core_urls\":\"null\", \"core_version\":\"version\", \"core_ordering\":\"null\", \"core_metakey\":\"metakey\", \"core_metadesc\":\"metadesc\", \"core_catid\":\"parent_id\", \"core_xreference\":\"null\", \"asset_id\":\"asset_id\"}, \"special\": {\"parent_id\":\"parent_id\",\"lft\":\"lft\",\"rgt\":\"rgt\",\"level\":\"level\",\"path\":\"path\",\"extension\":\"extension\",\"note\":\"note\"}}', 'MyJspaceHelperRoute::getCategoryRoute')";
				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');
				}
			} else { // Update for previous install
				// Update for previous install < BS MyJspace 3.0.0. Update the 'router' naming
				$router = 'MyJspaceHelperRoute::getPageRoute';
				$query = 'UPDATE '.$db->qn('#__content_types').' SET '.$db->qn('router').' = '.$db->q($router).' WHERE '.$db->qn('type_alias')." = 'com_myjspace.see' AND ".$db->qn('router').' != '.$db->q($router);
				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');
				}

				// Check & update for core_publish_up & core_publish_down not null : usage changed for J!4 (so update for J!3 & J!4)
				// EVOL : Format into BS MyJspace may be review in the future if this usage is requested for the 2 fields publish_up & publish_down

				if (in_array($db->name, array('mysql', 'mysqli')))
					$binary = ' BINARY ';
				else
					$binary = '';

				$field_mappings = "{\"common\":{\"core_content_item_id\":\"id\",\"core_title\":\"title\",\"core_state\":\"blockview\",\"core_alias\":\"pagename\",\"core_created_time\":\"created_date\",\"core_modified_time\":\"last_update_date\",\"core_body\":\"content\", \"core_hits\":\"hits\", \"core_publish_up\":\"null\",\"core_publish_down\":\"null\",\"core_access\":\"blockview\", \"core_params\":\"null\", \"core_featured\":\"null\", \"core_metadata\":\"metadata\", \"core_language\":\"language\", \"core_images\":\"null\", \"core_urls\":\"null\", \"core_version\":\"null\", \"core_ordering\":\"null\", \"core_metakey\":\"metakey\", \"core_metadesc\":\"null\", \"core_catid\":\"catid\", \"core_xreference\":\"null\", \"asset_id\":\"null\"}, \"special\": {}}";
				$query = 'UPDATE '.$db->qn('#__content_types').' SET '.$db->qn('field_mappings').' = '.$db->q($field_mappings).' WHERE '.$db->qn('type_alias').' = '.$db->q('com_myjspace.see').' AND '.$binary.$db->qn('field_mappings').' != '.$binary.$db->q($field_mappings);
				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');
				}

				// End of update for previous install < BS MyJspace 3.0.0
			}

			// User Actions Log configuration
			if (version_compare(JVERSION, '3.9.0', 'ge')) {
				// Add the extension to the table (#__action_logs_extensions) so that it will appear in the configuration of User Actions Log
				$query = 'SELECT COUNT(*) FROM '.$db->qn('#__action_logs_extensions').' WHERE '.$db->qn('extension').' = '.$db->q($this->component);
				$db->setQuery($query);
				$count = $db->loadResult();	
				if (!isset($count))
					return false;

				if ($count == 0) {
					$query = 'INSERT INTO '.$db->qn('#__action_logs_extensions').' ('.$db->qn('extension').') VALUES ('.$db->q($this->component).')';
					$db->setQuery($query);

					try 
					{
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');
					}
				}

				// Add the extension configuration to the table (#__action_log_config) so that your actions data will be captured
				// For create, udpate, delete page
				$query = 'SELECT COUNT(*) FROM '.$db->qn('#__action_log_config').' WHERE '.$db->qn('type_alias').' = '.$db->q($this->component.'.see');
				$db->setQuery($query);
				$count = $db->loadResult();	
				if (!isset($count))
					return false;

				if ($count == 0) {
					$logConf = new stdClass();
					$logConf->type_title = 'page';
					$logConf->type_alias = $this->component.'.see';
					$logConf->id_holder = 'id';
					$logConf->title_holder = 'title';
					$logConf->table_name = '#__myjspace';
					$logConf->text_prefix = 'COM_MYJSPACE_TRANSACTION';

					try {
						// If it fails, it will throw a RuntimeException
						// Insert the object into the table
						\Joomla\CMS\Factory::getDbo()->insertObject('#__action_log_config', $logConf);
					} catch (RuntimeException $e) {
						if (JDEBUG)
							JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');

						return false;
					}
				}

				// Add configuration for media upload
				$query = 'SELECT COUNT(*) FROM '.$db->qn('#__action_log_config').' WHERE '.$db->qn('type_alias').' = '.$db->q($this->component.'.media');
				$db->setQuery($query);
				$count = $db->loadResult();	
				if (!isset($count))
					return false;

				if ($count == 0) {
					$logConf = new stdClass();
					$logConf->type_title = 'media';
					$logConf->type_alias = $this->component.'.media';
					$logConf->id_holder = 'id';
					$logConf->title_holder = 'title';
					$logConf->table_name = '#__myjspace';
					$logConf->text_prefix = 'COM_MYJSPACE_TRANSACTION';

					try {
						// If it fails, it will throw a RuntimeException
						// Insert the object into the table
						\Joomla\CMS\Factory::getDbo()->insertObject('#__action_log_config', $logConf);
					} catch (RuntimeException $e) {
						if (JDEBUG)
							JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');

						return false;
					}
				}
			}

			// Post install messages

			// Get component id (JComponentHelper::getComponent('com_myjspace')->id not yet ok during install ...)
			$query = 'SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('type').' = '.$db->q('component').' AND '.$db->qn('name').' = '.$db->q($this->component);
			$db->setQuery($query);
			$com_id = $db->loadResult();	

			$com_id = (isset($com_id) && $com_id) ? $com_id : 700;

			if ($type == 'install') {
				$post_install = array();
				$post_install['extension_id'] = $com_id;
				$post_install['title_key'] = 'COM_BS_MYJSPACE_FOLDER_TITLE';
				$post_install['description_key'] = 'COM_BS_MYJSPACE_FOLDER_MESSAGE';
				$post_install['action_key'] = '';
				$post_install['action_file'] = '';
				$post_install['action'] = '';
				$post_install['condition_file'] = '';
				$post_install['condition_method'] = '';
				$post_install['language_extension'] = 'com_myjspace';
				$post_install['language_client_id'] = 1;
				$post_install['type'] = 'message';
				$post_install['version_introduced'] = '3.0.0';
				$this->add_postinstall_message($post_install);
			}

			if ($type == 'update') {

				if (version_compare($this->release_old, '2.4.0', 'lt')) { // If migrate for BS MyJspace version older than 2.4.0
					$post_install = array();
					$post_install['extension_id'] = $com_id;
					$post_install['title_key'] = 'COM_BS_MYJSPACE_PRE_24_TITLE';
					$post_install['description_key'] = 'COM_BS_MYJSPACE_PRE_24_MESSAGE';
					$post_install['action_key'] = '';
					$post_install['action_file'] = '';
					$post_install['action'] = '';
					$post_install['condition_file'] = '';
					$post_install['condition_method'] = '';
					$post_install['language_extension'] = 'com_myjspace';
					$post_install['language_client_id'] = 1;
					$post_install['type'] = 'message';
					$post_install['version_introduced'] = '3.0.0';
					$this->add_postinstall_message($post_install);
				}

				if (version_compare($this->release_old, '3.0.0', 'lt') && $acl_rules_updates == false) { // If migrate for BS MyJspace version older than 3.0.0
					$post_install = array();
					$post_install['extension_id'] = $com_id;
					$post_install['title_key'] = 'COM_BS_MYJSPACE_CATEGORIES_ACL_TITLE';
					$post_install['description_key'] = 'COM_BS_MYJSPACE_CATEGORIES_ACL_MESSAGE';
					$post_install['action_key'] = '';
					$post_install['action_file'] = '';
					$post_install['action'] = '';
					$post_install['condition_file'] = '';
					$post_install['condition_method'] = '';
					$post_install['language_extension'] = 'com_myjspace';
					$post_install['language_client_id'] = 1;
					$post_install['type'] = 'message';
					$post_install['version_introduced'] = '3.0.0';
					$this->add_postinstall_message($post_install);
				}

				// Recreate index ?
				if ($this->release_old != $this->getParam('version', $this->component)) { // Un case of update with the same version do not add
					$post_install = array();
					$post_install['extension_id'] = $com_id;
					$post_install['title_key'] = 'COM_BS_MYJSPACE_INDEX_TITLE';
					$post_install['description_key'] = 'COM_BS_MYJSPACE_INDEX_MESSAGE';
					$post_install['action_key'] = '';
					$post_install['action_file'] = '';
					$post_install['action'] = '';
					$post_install['condition_file'] = '';
					$post_install['condition_method'] = '';
					$post_install['language_extension'] = 'com_myjspace';
					$post_install['language_client_id'] = 1;
					$post_install['type'] = 'message';
					$post_install['version_introduced'] = '3.0.0';
					$this->add_postinstall_message($post_install, true);
				}
			}

			// Cleanup data, not necessary any more, from previous install
			if ($type == 'update') {
				// Delete the old site update url (http://) from previous install
				$query = 'SELECT '.$db->qn('update_site_id').' FROM '.$db->qn('#__update_sites').' WHERE '.$db->qn('location')." LIKE 'http://%myjspace-update.xml'";
				$db->setQuery($query);
				$update_site_id = $db->loadResult();

				if ($update_site_id > 0) { // Previous install with 'http' to be deleted
					$db	= JFactory::getDBO();
					$query = 'DELETE FROM '.$db->qn('#__update_sites').' WHERE '.$db->qn('update_site_id').' = '.$db->q(intval($update_site_id));
					$db->setQuery($query);

					try
					{
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');
					}

					$db	= JFactory::getDBO();
					$query = 'DELETE FROM '.$db->qn('#__update_sites_extensions').' WHERE '.$db->qn('update_site_id').' = '.$db->q(intval($update_site_id));
					$db->setQuery($query);

					try
					{
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');
					}
				}

				// --- Delete old files unused, if still exists from old install (< BS MyJSpace 3.0.0)

				$files = array(
					'/administrator/components/com_myjspace/_myjspace.xml',
					'/administrator/components/com_myjspace/sql/updates/2.5.5.sql',
					'/administrator/components/com_myjspace/sql/updates/2.6.0.sql',
					'/administrator/components/com_myjspace/sql/install/install.mysql.utf8.sql',
					'/administrator/components/com_myjspace/sql/install/install.sqlazure.utf8.sql',
					'/administrator/components/com_myjspace/sql/install/index.html',
					'/components/com_myjspace/helpers/legacy.php',
					'/components/com_myjspace/helpers/util_legacy.php',
				);

				$folders = array(
					'/administrator/components/com_myjspace/sql/install',
				);

				foreach ($files as $file) {
					if (@file_exists(JPATH_ROOT.$file) && !@unlink(JPATH_ROOT.$file)) {
						JFactory::getApplication()->enqueueMessage(JText::sprintf('FILES_JOOMLA_ERROR_FILE_FOLDER', $file), 'error');
					}
				}

				foreach ($folders as $folder) {
					if (@is_dir(JPATH_ROOT.$folder) && !@rmdir(JPATH_ROOT.$folder)) {
						JFactory::getApplication()->enqueueMessage( JText::sprintf('FILES_JOOMLA_ERROR_FILE_FOLDER', $folder), 'error');
					}
				}

				// --- End delete old files unused, if still exists from old install (< BS MyJSpace 3.0.0)
			}
		}

		$this->footer_install(); // Display end of install message

		return true;
	}

	// Method to un-install the component
	function uninstall($parent)
	{
		$pparams = JComponentHelper::getParams('com_myjspace');
		$db	= JFactory::getDBO();

		if ($pparams->get('uninstall_tables', 0)) { // Drop tables & content
			// Delete the BS MyJspace J!Tags contents: J!3.1.4+
			$query = 'DELETE FROM '.$db->qn('#__content_types').' WHERE '.$db->qn('type_alias')." = 'com_myjspace.see'";
			$db->setQuery($query);
			$db->execute();

			$query = 'DELETE FROM '.$db->qn('#__content_types').' WHERE '.$db->qn('type_alias')." = 'com_myjspace.category'";
			$db->setQuery($query);
			$db->execute();

			$query = 'DELETE FROM '.$db->qn('#__contentitem_tag_map').' WHERE '.$db->qn('type_alias')." = 'com_myjspace.see'";
			$db->setQuery($query);
			$db->execute();

			$query = 'DELETE FROM '.$db->qn('#__contentitem_tag_map').' WHERE '.$db->qn('type_alias')." = 'com_myjspace.category'";
			$db->setQuery($query);
			$db->execute();

			$query = 'DELETE FROM '.$db->qn('#__ucm_base').' WHERE '.$db->qn('ucm_id').' IN (SELECT '.$db->qn('core_content_id').' FROM '.$db->qn('#__ucm_content').' WHERE '.$db->qn('core_type_alias')." = 'com_myjspace.see')";
			$db->setQuery($query);
			$db->execute();

			$query = 'DELETE FROM '.$db->qn('#__ucm_content').' WHERE '.$db->qn('core_type_alias')." = 'com_myjspace.see'";
			$db->setQuery($query);
			$db->execute();

			if (version_compare(JVERSION, '3.9.0', 'ge')) {
				// User actions Log
				$query = 'DELETE FROM '.$db->qn('#__action_logs_extensions').' WHERE '.$db->qn('extension')." = 'com_myjspace'";
				$db->setQuery($query);
				$db->execute();

				$query = 'DELETE FROM '.$db->qn('#__action_log_config').' WHERE '.$db->qn('type_alias')." = 'com_myjspace.see' OR ".$db->qn('type_alias')." = 'com_myjspace.media'";
				$db->setQuery($query);
				$db->execute();
			}

			echo "<p>Deleted BS myJspace data from Joomla! tables</p>";

			$query = 'DROP TABLE IF EXISTS '.$db->qn('#__myjspace');
			$db->setQuery($query);
			$db->execute();

			$query = 'DROP TABLE IF EXISTS '.$db->qn('#__myjspace_cfg');
			$db->setQuery($query);
			$db->execute();

			echo "<p>Dropped the BS MyJSpace pages table</p>";

			if ($pparams->get('link_folder', 1))
				echo '<p>Please delete <span style="color:red;">manually</span> the pages root folders (files and sub-folfers): '.$pparams->get('foldername', 'media/myjsp').'</p>';

		} else {
			echo "<p>Updating #__myjspace_cfg table to keep in 'mind' the root folder name ...</p>";

			$query = 'DELETE FROM '.$db->qn('#__myjspace_cfg');
			$db->setQuery($query);
			$db->execute();

			$query = 'INSERT INTO '.$db->qn('#__myjspace_cfg').' ('.$db->qn('params').') VALUES ('.$db->q($pparams->toString('JSON')).')';
			$db->setQuery($query);
			$db->execute();
?>
			<p>
				<div>BS MyJspace table(s), some config., user's data and folders were not deleted during this uninstall process.</div>
				<div>Set the option 'Uninstall tables and content' to 'yes' (after reinstall!) to delete the tables.</div>
			</p>
<?php			
		}

		// Delete all post-install message(s) for the component
		$this->del_postinstall_messages();

	}

	private function header_install() {

		// BS MyJspace version & text from Manifest file
		$version_str = '';
		$file = JPATH_ROOT.'/administrator/components/com_myjspace/myjspace.xml';
		if (@file_exists($file)) {
			libxml_use_internal_errors(true);
			$xml = @simplexml_load_file($file);

			if (isset($xml) && isset($xml->version) && isset($xml->build) && isset($xml->creationDate))
				$version_str = (string)$xml->version.' - '.(string)$xml->build.' - '.(string)$xml->creationDate;
		}

		// Message
		$msg = '		
	<span><img src="'.JURI::base().'components/com_myjspace/images/myjspace.png" alt="BS MyJspace" /> BS Myjspace component '.$version_str.'</span>
	<br />
	';
		echo $msg;
	}

	private function footer_install() {
		$msg = '
	<br />
	<div style="text-align: left;">
		<div class="button2-left">
			<div class="blank">
				<a class="modal-button btn btn-primary" href="index.php?option=com_myjspace&amp;view=help">Configuration and Check</a>
			</div>
		</div>
	</div>
	<br />
	';
		echo $msg;
	}

	// Add a post install message (if not exists = if 'title_key' not exists = BS choice !)
	private function add_postinstall_message($values = array(), $force = false) {

		// Test if key 'title_key' exists
		if (!array_key_exists('title_key', $values) || !array_key_exists('extension_id', $values))
			return;

		$db	= JFactory::getDBO();
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from('#__postinstall_messages')
			->where($db->qn('title_key').' = '.$db->q($values['title_key']).' AND extension_id = '.$db->q($values['extension_id']));

		$db->setQuery($query);
		$nb = $db->loadResult();

		// Message exists ?

		if ($nb > 0 && $force == false)
			return;

		if ($nb > 0 && $force == true) {
			$query = 'DELETE FROM '.$db->qn('#__postinstall_messages').' WHERE title_key = '.$db->q($values['title_key']).' AND extension_id = '.$db->q($values['extension_id']);
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
			}
		}

		// Add new message
		$column_str = '';
		$value_str = '';
		foreach ($values as $key => $value) {
			$column_str .= $db->qn($key).',';
			if (is_string($value))
				$value_str .= $db->q($value).',';
			else
				$value_str .= $value.',';
		}

		$column_str = trim($column_str, ',');
		$value_str = trim($value_str, ',');

		$db = JFactory::getDbo();
		$query = 'INSERT INTO '.$db->qn('#__postinstall_messages').' ('.trim($column_str, ',').') VALUES ('.$value_str.')';

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (Exception $e)
		{
			if (JDEBUG)
				JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');

			return;
		}
	}

	// Delete post install message(s)
	private	function del_postinstall_messages() {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->delete($db->qn('#__postinstall_messages'))
			->where($db->qn('language_extension').' = '.$db->q('com_myjspace'));

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (Exception $e)
		{
			if (JDEBUG)
				JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');

			return;
		}		
	}

	// Get a variable from the manifest (actually, from the manifest cache)
	function getParam($name, $component = null, $type = 'component') {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('manifest_cache')
			->from('#__extensions')
			->where($db->qn('name').' = '.$db->q($component).' AND '.$db->qn('type').' = '.$db->q($type));

		$db->setQuery($query, null, 1);
		$manifest = json_decode($db->loadResult(), true);

		if (isset($manifest[$name]))
			return $manifest[$name];
		else
			return null;
	}
}
