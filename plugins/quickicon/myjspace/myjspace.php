<?php
/**
* @version $Id: myjspace.php $
* @version		3.0.0 19/08/2019
* @package		plg_quickiconmyjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2012 - 2019 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

if (file_exists(JPATH_ROOT.'/components/com_myjspace/helpers/version.php')) {
	require_once JPATH_ROOT.'/components/com_myjspace/helpers/version.php';
}

class plgQuickiconMyjspace extends JPlugin
{
	protected $autoloadLanguage = true;
	protected $link = 1;

	public function __construct(& $subject, $config)
	{
		// Do not load if component MyJspace is not installed
		if (!file_exists(JPATH_ROOT.'/components/com_myjspace/helpers/version.php')) 
			return false;

		parent::__construct($subject, $config);

		$this->link = $this->params->get('link', 1);
		
		return true;
	}

	/**
	 * Display BS MyJspace back-end icon in J!3+
	 *
	 * @param string $context
	 */
	public function onGetIcons($context)
	{
		if ($context !== $this->params->get('context', 'mod_quickicon') || !JFactory::getUser()->authorise('core.manage', 'com_myjspace')) {
			return array();
		}

		// Check for 'new ?' version
		BS_Helper_version::get_newversion('com_myjspace');

		// Is it New version ?
		$pparams = JComponentHelper::getParams('com_myjspace');
		$check_version =  $pparams->get('check_version', '0.0.0');
		$current_version = BS_Helper_version::getXmlParam('com_myjspace', 'version');

		if (version_compare($check_version, $current_version, 'gt')) {
			if (version_compare(JVERSION, '3.99.99', 'lt'))
				$version_new = ' <span class="label label-important">'.$check_version.'</span>';
			else
				$version_new = ' <span class="badge badge-warning">'.$check_version.'</span>';
		} else {
			$version_new = '';
		}

		// Url to display
		if ($version_new != '')
			$link = 'index.php?option=com_myjspace&view=myjspace';
		else if ($this->link == 1)
			$link = 'index.php?option=com_myjspace&view=pages';
		else if ($this->link == 2)
			$link = 'index.php?option=com_myjspace&view=createpage';
		else
			$link = 'index.php?option=com_myjspace&view=myjspace';

		if (version_compare(JVERSION, '3.99.99', 'lt')) {
			JHtml::_('stylesheet', 'plugins/quickicon/myjspace/myjspace.css');

			return array(array(
				'link'  => $link,
				'image' => 'myjspace',
				'icon'  => 'myjspace/../../../administrator/components/com_myjspace/images/myjspace.png',
				'text'  => JText::_('PLG_QUICKICON_MYJSPACE_LABEL').$version_new,
				'id'    => 'plg_quickicon_myjspace',
				'group' => 'MOD_QUICKICON_EXTENSIONS'
			));
		} else {
			return array(
				array(
					'link'  => $link,
					'image' => 'fa fa-pen-square',
					'icon'  => '',
					'text'  => JText::_('PLG_QUICKICON_MYJSPACE_LABEL').$version_new,
					'id'    => 'plg_quickicon_myjspace',
					'group' => 'MOD_QUICKICON_EXTENSIONS',
					'linkadd' => 'index.php?option=com_myjspace&view=createpage',
					'name' => 'PLG_QUICKICON_MYJSPACE_LABEL2'
				)
			);
		}
	}
}
