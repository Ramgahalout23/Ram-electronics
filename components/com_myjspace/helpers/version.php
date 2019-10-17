<?php
/**
* @version $Id:	version.php $
* @version		26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2010 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

require_once JPATH_ROOT.'/components/com_myjspace/helpers/user.php';

class BS_Helper_version extends JPlugin
{
	// Get component information (with or without, when public acess, the version)
	public static function get_information($droits = null)
	{
		// Configuration file name and xml info extract
		$retour = null;
		$user = JFactory::getUser();
		$app = JFactory::getApplication();

		$retour = self::getXmlParam(null, 'name').' | '.self::getXmlParam(null, 'author');
		$retour .= ' | <a href="'.self::getXmlParam(null, 'authorUrl').'">'.self::getXmlParam(null, 'authorUrl').'</a><br />';
		$retour .= self::getXmlParam(null, 'copyright').' | '.self::getXmlParam(null, 'license').'<br />';

		if (version_compare(JVERSION, '3.7.0', 'lt'))
			$isAdmin = $app->isAdmin();
		else
			$isAdmin = $app->isClient('administrator');

		// For admin & back-end only
		if ($isAdmin) {
			$retour_version = self::getXmlParam(null, 'version').' | '.self::getXmlParam(null, 'build').' | '.self::getXmlParam(null, 'creationDate');

			if ($user->authorise($droits))
				$retour .= $retour_version.'<br />';
		}

		return $retour;
	}

	// Get last version checked from com_installer update
	public static function get_updates_version($component = null, $type = 'component')
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true)
			->select('version')
			->from('#__updates')
			->where($db->qn('element').' = '.$db->q($component).' AND '.$db->qn('type').' = '.$db->q($type));

		$db->setQuery($query);
		return $db->loadResult();
	}

	// Get a variable from the manifest file
	public static function getXmlParam($component = null, $item = null)
	{
		// Configuration file name and XML info extract
		$jinput = JFactory::getApplication()->input;
		$option = $jinput->get('option', '', 'PATH');

		$retour = '';
		if ($option || $component) {
			if ($component)
				$my_componant = substr($component, 4, strlen($component) - 4);
			else
				$my_componant = substr($option, 4, strlen($option) - 4);

			$file = JPATH_ROOT.'/administrator/components/com_'.$my_componant.'/'.$my_componant.'.xml';

			libxml_use_internal_errors(true);
			$xml = @simplexml_load_file($file);

			if ($xml == null)
				return '';

			if (isset($xml->{$item}))
				$retour = (string)$xml->{$item};
			else
				$retour = '';
		}

		return $retour;
	}

	// Check if new version or from Joomla com_installer test or directly from the update server
	public static function get_newversion($component = null)
	{
		if (!$component)
			return null;

		$pparams = JComponentHelper::getParams($component);

		$current_version = BS_Helper_version::getXmlParam('com_myjspace', 'version');
		$check_allowcheckversion = $pparams->get('check_allowcheckversion', 1);
		$check_version = $pparams->get('check_version', '0.0.0');
		$check_lastdate = (int)$pparams->get('check_lastdate', 0);
		$check_period = (int)$pparams->get('check_period', 604800); // Check weekly as default

		$old = false;
		if (abs(time() - $check_lastdate) > $check_period) // Time to check on update server ?
			$old = true;

		$version = self::get_updates_version('com_myjspace', 'component'); // Get data from Joomla com_installer last check

		if (!$version && $check_allowcheckversion == 1 && $old) // Get data directly from update server if !$version & if allowed
			$version = self::get_updateservers_version($component);

		if ($check_version != $version && version_compare($version, $current_version, 'gt')) {
			$pparams->set('check_lastdate', strval(time()));
			$pparams->set('check_version', $version);
			BSHelperUser::save_parameters($pparams, 'com_myjspace');
		} else if ($old) { // In case of old information (may be obsolete & wrong) allow to reset
			$pparams->set('check_lastdate', strval(time()));
			$pparams->set('check_version', $current_version);
			BSHelperUser::save_parameters($pparams, 'com_myjspace');
		}

		return $version;
	}

	// Check the last version from the update server
	public static function get_updateservers_version($component = null, $type_tmp = 'component')
	{
		// Current manifest info
		$my_componant = substr($component, 4, strlen($component) - 4);
		$file = JPATH_ROOT.'/administrator/components/com_'.$my_componant.'/'.$my_componant.'.xml';

		libxml_use_internal_errors(true);
		$xml = @simplexml_load_file($file);

		if (!isset($xml) || !isset($xml->version) || !isset($xml->build) || !isset($xml->updateservers->server))
			return '';

		$actual_version = (string)$xml->version;
		$actual_build = (string)$xml->build;
		$datalink = (string)$xml->updateservers->server;

		// Search for the last version
		$version = '';

		// Update server file retrieve
		if ($datalink) { // If update link
			$mon_serveur = JURI::root();
			$url = $datalink.'&type='.$type_tmp.'&name='.$component.'&version='.$actual_version.'&b='.$actual_build.'&joomla='.JVERSION.'&server='.$mon_serveur.'&php='.PHP_VERSION;

			$xml = @simplexml_load_file($url);

			if (!isset($xml) || !isset($xml->update))
				return '';

			$pparams = JComponentHelper::getParams('com_installer'); // Get from the com_installer component !
			$minimum_stability = $pparams->get('minimum_stability', JUpdater::STABILITY_STABLE); // Between 0 (dev) to 4 (stable) JUpdater::STABILITY_STABLE

			$version_update = self::filterListByPlatform($xml->update, JVERSION, $minimum_stability);

			if (isset($version_update->version))
				$version = (string)$version_update->version;
		}

		return $version;
	}

	public static function filterListByPlatform($updates, $jVersion = null, $minimum_stability = JUpdater::STABILITY_STABLE)
	{
		// Get the target platform
		if (is_null($jVersion)) {
			$jVersion = JVERSION;
		}

		$versionParts = explode('.', $jVersion, 4);
		$platformVersionMajor = $versionParts[0];
		$platformVersionMinor = (count($versionParts) > 1) ? $platformVersionMajor.'.'.$versionParts[1] : $platformVersionMajor;
//		$platformVersionNormal = (count($versionParts) > 2) ? $platformVersionMinor.'.'.$versionParts[2] : $platformVersionMinor;
//		$platformVersionFull = (count($versionParts) > 3) ? $platformVersionNormal.'.'.$versionParts[3] : $platformVersionNormal;

		$pickedExtension = null;

		foreach ($updates as $update) {
			$stability = self::stabilityTagToInteger((string)$update->tags->tag);
			$targetPlatform = (string)$update->targetplatform['version'];

			if ($stability >= $minimum_stability && ($jVersion == $targetPlatform || preg_match("/".$targetPlatform."/i", $platformVersionMinor))) { // Check minimum stability (prend le dernier avec le minimum severity ok)
				$pickedExtension = $update;
			}
		}

		return $pickedExtension;
	}

	/**
	 * Converts a tag to numeric stability representation. If the tag doesn't represent a known stability level (one of
	 * dev, alpha, beta, rc, stable) it is ignored
	 *
	 * @param	string  $tag  The tag string, e.g. dev, alpha, beta, rc, stable
	 *
	 * @return	integer
	 *
	 * @since	3.4
	 */
	public static function stabilityTagToInteger($tag)
	{
		$constant = 'JUpdater::STABILITY_'.strtoupper($tag);

		if (defined($constant)) {
			return constant($constant);
		}

		return JUpdater::STABILITY_STABLE;
	}
}
