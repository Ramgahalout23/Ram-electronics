<?php
/**
* @version $Id: myjspace.php $ 
* @version		3.0.0 23/06/2019
* @package		plg_system_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2019 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

JLoader::register('PrivacyPlugin', JPATH_ADMINISTRATOR . '/components/com_privacy/helpers/plugin.php');

/**
 * Privacy plugin managing Joomla user BS MyJspace data
 *
 * @since  3.9.0
 */
class PlgPrivacyMyJspace extends PrivacyPlugin
{
	/**
	 * Processes an export request for Joomla core user BS MyJspace data
	 *
	 * This event will collect data for the BS MyJspace core table
	 *
	 * - BS MyJspace custom fields
	 *
	 * @param   PrivacyTableRequest  $request  The request record being processed
	 * @param   JUser                $user     The user account associated with this request if available
	 *
	 * @return  PrivacyExportDomain[]
	 *
	 * @since   3.9.0
	 */
	public function onPrivacyExportRequest(PrivacyTableRequest $request, JUser $user = null)
	{
		if (!$user) {
			return array();
		}

		// Do not load if BS MyJspace not installed
		if (!JComponentHelper::isEnabled('com_myjspace')) {
			return array();
		}

		$domains = array();
		$domain = $this->createDomain('user_myjspace', 'joomla_user_myjspace_data');
		$domains[] = $domain;

		$query = $this->db->getQuery(true)
			->select('*')
			->from($this->db->quoteName('#__myjspace'))
			->where($this->db->quoteName('userid').' = '.(int)$user->id)
			->order($this->db->quoteName('pagename').' ASC');

		$items = $this->db->setQuery($query)->loadObjectList();

		foreach ($items as $item) {
			$domain->addItem($this->createItemFromArray((array) $item));
		}

		$domains[] = $this->createCustomFieldsDomain('com_myjspace.see', $items);

		return $domains;
	}
}
