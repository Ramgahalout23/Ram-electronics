<?php
/**
* @version $Id: script.BSMyJspace.php $
* @version		2.7.0 01/07/2018
* @package		BSMyJspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2018 - 2019 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die();

class Pkg_BSMyJspaceInstallerScript
{
	protected $minimumPHPVersion = '5.3.10';
	protected $minimumJoomlaVersion = '3.4.0';
	protected $currentMyJspaceVersion = '';
	protected $componentName = 'BS MyJspace pack';
	protected $extra_plugin_zip_list = array('plg_privacy_myjspace_3.0.0b2.zip', 'plg_system_myjspace_3.0.0b1.zip'); // Add plugins
	protected $extra_plugin_list = array(array('plugin', 'privacy', 'myjspace'), array('plugin', 'system', 'myjspace')); // Delete plugins

	/**
	 * Joomla! pre-flight event. This runs before Joomla! installs or updates the package. This is our last chance to
	 * tell Joomla! if it should abort the installation.
	 *
	 * @param   string                     $type    Installation type (install, update, discover_install)
	 * @param   \JInstallerAdapterPackage  $parent  Parent object
	 *
	 * @return  boolean  True to let the installation proceed, false to halt the installation
	 */
	public function preflight($type, $parent)
	{
		// Get the Package version
		$file = $parent->getParent()->getPath('source').'/pkg_BSMyJspace.xml';
		libxml_use_internal_errors(true);
		if (@file_exists($file)) {
			$xml = @simplexml_load_file($file);

			if (isset($xml) && isset($xml->version))
				$this->currentMyJspaceVersion = (string)$xml->version;
		}

		if (version_compare(JVERSION, $this->minimumJoomlaVersion, 'lt')) { // Check Joomla! version for component compatibility
			JFactory::getApplication()->enqueueMessage('You need at least Joomla! '.$this->minimumJoomlaVersion.' to install this '.$this->componentName.' version', 'error');
			return false;
		}

		if (version_compare(PHP_VERSION, $this->minimumPHPVersion, 'lt')) { // Check PHP version for component compatibility
			JFactory::getApplication()->enqueueMessage('You need at least PHP '.$this->minimumPHPVersion.' to install this '.$this->componentName.' version', 'error');
			return false;
		}

		return true;
	}

	/**
	 * Runs after install, update or discover_update. In other words, it executes after Joomla! has finished installing
	 * or updating your component. This is the last chance you've got to perform any additional installations, clean-up,
	 * database updates and similar housekeeping functions.
	 *
	 * @param   string                       $type   install, uninstall, update or discover_update
	 * @param   \JInstallerAdapterComponent  $parent Parent object
	 */
	public function postflight($type, $parent)
	{
		if ($type == 'install' || $type == 'update') {
			if (version_compare(JVERSION, '3.9.0', 'ge')) { // Install privacy for J!3.9+, for example ...
				foreach ($this->extra_plugin_zip_list as $extra_plugin) {
					$this->installOrUpdatePack($parent, $extra_plugin);
				}
			}

			$msg = '
			<div style="text-align: left;">
				<span><img src="components/com_myjspace/images/myjspace.png" alt="BS MyJspace" /> BS MyJspace '.$this->currentMyJspaceVersion.' pack - manage personal pages system for your users</span>
				<br /><br />
				<div class="button2-left">
					<div class="blank">
						<a class="modal-button btn btn-primary" href="index.php?option=com_myjspace&amp;view=help">Configuration and Check</a>
					</div>
				</div>
			</div>
			';
			JFactory::getApplication()->enqueueMessage($msg, 'notice');
		}

		return true;
	}

	/**
	 * Tuns on installation (but not on upgrade). This happens in install and discover_install installation routes.
	 *
	 * @param   \JInstallerAdapterPackage  $parent  Parent object
	 *
	 * @return  bool
	 */
	public function install($parent)
	{
		return true;
	}

	/**
	 * Runs on uninstallation
	 *
	 * @param   \JInstallerAdapterPackage  $parent  Parent object
	 *
	 * @return  bool
	 */
	public function uninstall($parent)
	{
		if (version_compare(JVERSION, '3.9.0', 'ge')) { // Delete privacy for J!3.9+, for example ...
			foreach ($this->extra_plugin_list as $extra_plugin) {
				$this->DeletePack($parent, $extra_plugin[0], $extra_plugin[1], $extra_plugin[2]);
			}
		}

		if (!$this->exists_table('myjspace')) {
			$msg = "
			<p>
				<div><strong>Bye bye :-(</strong></div>
				<div>The MyJspace content table(s) and table have been removed during this uninstall process.</div>
				<div>You need to delete the 'myjsp' folder, subfolders &amp; files <strong>manually</strong>, if any.</div>
			</p>
			";
		} else {
			$msg = "
			<p>
				<div><strong>Bye bye :-(</strong></div>
				<div>The MyJspace table(s) content, tables, folders and files <strong>are not</strong> removed during this uninstall process.</div>
				<div>Set the option 'Uninstall table and content' to 'yes' (after reinstall &amp; new uninstall!) to delete the table(s) and tables(s) content.</div>
				<div>You need to delete the 'myjsp' folder, subfolders &amp; files <strong>manually</strong>, if any.</div>
			</p>
			";
		}

		JFactory::getApplication()->enqueueMessage($msg, 'notice');

		return true;
	}

	// ------------------- extra fct -------------------

	/**
	 * Check if a table exists
	 *
	 * @param 		$tablename  example: 'myjspace'
	 *
	 * @return  bool
	 */
	private function exists_table($tablename = null) 
	{
		if (!$tablename)
			return false;

		$config = new JConfig();

		$ma_table = $config->dbprefix.$tablename;
		$liste_tables = JFactory::getDbo()->getTableList();

		if (in_array($ma_table, $liste_tables)) {
			return true;
		}

		return false;
	}

	/**
	 * Install, update extra package/file
	 *
	 * @param   \JInstallerAdapterPackage  $parent,
	 *                                     $zipfile
	 *                                     $install (true: install or update)
	 */
	private function installOrUpdatePack($parent, $zipfile = '')
	{
		// Get the path to the file
		$sourcePath = $parent->getParent()->getPath('source');
		$sourcePackage = $sourcePath.'/'.$zipfile;

		// Extract and install the file
		$package = JInstallerHelper::unpack($sourcePackage);

		$myInstaller = new JInstaller;

		try {
			$myInstaller->install($package['dir']);
		}
		catch (\Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Delete plugin, ...
	 */	
	private function DeletePack($parent, $type = '', $folder = '', $element = '')
	{
		$db = $parent->getParent()->getDbo();

		$query = $db->getQuery(true)
		            ->select('extension_id')
		            ->from('#__extensions')
		            ->where($db->qn('type').' = '.$db->q($type))
		            ->where($db->qn('element').' = '.$db->q($element))
		            ->where($db->qn('folder').' = '.$db->q($folder));

		$db->setQuery($query);
		$id = $db->loadResult();

		if (!$id) {
			return;
		}

		$myInstaller = new JInstaller;

		try {
			$myInstaller->uninstall($type, $id);
		}
		catch (\Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}
}
