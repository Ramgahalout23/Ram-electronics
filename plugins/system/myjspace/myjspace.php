<?php
/**
* @version $Id: myjspace.php $ 
* @version		3.0.0 29/06/2019
* @package		plg_system_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2019 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Class plgSystemMyJspace
 * @since MyJspace
 */
class plgSystemMyJspace extends \Joomla\CMS\Plugin\CMSPlugin
{
	protected $autoloadLanguage = true;
	protected $extension = 'com_myjspace';

	/**
	 * @param   object $subject Subject
	 * @param   array  $config  Config
	 *
	 * @throws Exception
	 * @since MyJspace
	 */
	public function __construct(&$subject, $config)
	{
		// Do not load if BS MyJspace not installed
		if (JComponentHelper::isEnabled($this->extension) === false) {
			return;
		}

		parent::__construct($subject, $config);
	}

	/**
	 * Adds the MyJspace Privacy Information to Joomla Privacy plugin.
	 *
	 * @return array
	 *
	 * @since BS MyJspace 3.0.0
	 */
	public function onPrivacyCollectAdminCapabilities()
	{
		$this->loadLanguage();

		return(array(
			Text::_('PLG_SYSTEM_MYJSPACE_PRIVACY') => array(
				Text::_('PLG_SYSTEM_MYJSPACE_PRIVACY_CAPABILITY_USERPROFILE'),
				Text::_('PLG_SYSTEM_MYJSPACE_PRIVACY_CAPABILITY_PAGES'),
				Text::_('PLG_SYSTEM_MYJSPACE_PRIVACY_CAPABILITY_MORE')
			),
		));
	}
}
