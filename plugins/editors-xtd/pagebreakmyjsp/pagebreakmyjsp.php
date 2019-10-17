<?php
/**
* @version $Id: pagebreakmyjsp.php $
* @version		3.0.0 29/06/2019
* @package		plg_xtd_pagebreakmyjsp
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2015 - 2019 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

/**
 * Editor Pagebreak button
 *
 */
class PlgButtonPagebreakMyjsp extends JPlugin
{
	protected $autoloadLanguage = true;

	/**
	 * Display the button
	 *
	 * @param   string  $name  The name of the button to add
	 *
	 * @return array A two element array of (imageName, textToInsert)
	 */
	public function onDisplay($name)
	{
		$jinput = JFactory::getApplication()->input;

		if ($jinput->get('option', '', 'STRING') == 'com_myjspace') {

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
			$link = 'index.php?option=com_myjspace&amp;view='.$view.'&amp;layout=pagebreak&amp;tmpl=component&amp;id='.$id.'&amp;e_name='.$name;

			$button = new JObject;
			$button->modal = true;
			$button->class = 'btn'; // Mandatory for J3! et editor = none
			$button->link  = $link;
			$button->text  = JText::_('PLG_MYJSPACE_XTD_PAGEBREAK_BUTTON');
			$button->name  = 'copy';
			$button->iconSVG = '<svg viewBox="0 0 32 32" width="24" height="24"><path d="M26 8h-6v-2l-6-6h-14v24h12v8h20v-18l-6-6zM26 10.828l3.172 3'
								. '.172h-3.172v-3.172zM14 2.828l3.172 3.172h-3.172v-3.172zM2 2h10v6h6v14h-16v-20zM30 30h-16v-6h6v-14h4v6h6v14z"></pa'
								. 'th></svg>';

			if (version_compare(JVERSION, '3.99.99', 'gt')) { 
				$button->options = [
					'height'     => '250px',
					'width'      => '280px',
					'bodyHeight' => '25',
					'modalWidth' => '40',
					];
			} else {
				$button->options = "{handler: 'iframe', size: {x: 280, y: 250}}";
			}

			return $button;
		} else {
			return false;
		}
	}
}
