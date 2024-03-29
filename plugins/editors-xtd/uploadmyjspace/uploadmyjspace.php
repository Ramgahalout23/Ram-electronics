<?php
/**
* @version $Id: uploadmyjspace.php $
* @version		3.0.0 09/07/2019
* @package		plg_xtd_upload_myjspace
* @author       Bernard Saulm�
* @copyright	Copyright (C) 2016 - 2019 Bernard Saulm�
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

/**
 * Editor Tags MyJspace button
 *
 */
class plgButtonUploadMyjspace extends JPlugin
{
	protected $autoloadLanguage = true;

	/**
	 * Display the button
	 *
	 * @return array A two element array of (imageName, textToInsert)
	 */
	public function onDisplay($name)
	{
		$pparams = JComponentHelper::getParams('com_myjspace');
		$jinput = JFactory::getApplication()->input;

		$uploadmedia = $pparams->get('uploadmedia', 1);
		$link_folder  = $pparams->get('link_folder', 1);

		if ($jinput->get('option', '', 'STRING') == 'com_myjspace' && $link_folder == 1 && $uploadmedia > 0) {

			if (version_compare(JVERSION, '3.5.0', 'lt'))
				JHtml::_('behavior.modal');

			$id = $jinput->get('id', 0, 'INT');
			if ($id == 0)
				$id = $jinput->get('mjs_id', 0, 'INT');

			$app = JFactory::getApplication();
			if (version_compare(JVERSION, '3.7.0', 'lt'))
				$isAdmin = $app->isAdmin();
			else
				$isAdmin = $app->isClient('administrator');

			$view = $isAdmin ? 'page' : 'edit';
			$link = 'index.php?option=com_myjspace&amp;view='.$view.'&amp;layout=upload&amp;tmpl=component&amp;type=undefined&amp;id='.$id.'&amp;e_name='.$name;

			$button = new JObject;
			$button->modal = true;
			$button->link = $link;
			$button->class = 'btn';
			$button->text = JText::_('PLG_EDITORSXTD_MYJSPACE_BUTTON_UPLOAD');
			if (version_compare(JVERSION, '3.99.99', 'gt')) { 
				$button->name = 'link';
				$button->options = [
					'height' => '290px',
					'width'  => '600px',
					'bodyHeight'  => '35',
					'modalWidth'  => '35'
					];
			} else {
				$button->name = 'folder';
				$button->options = "{handler: 'iframe', size: {x: 600, y: 290}}";
			}

			return $button;
		} else {
			return false;
		}
	}
}
