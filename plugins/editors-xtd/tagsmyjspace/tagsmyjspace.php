<?php
/**
* @version $Id: tagsmyjspace.php $
* @version		3.0.0 20/08/2019
* @package		plg_xtd_tags_myjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2012 - 2019 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

/**
 * Editor Tags MyJspace button
 *
 */
class plgButtonTagsMyjspace extends JPlugin
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

		if ($jinput->get('option', '', 'STRING') == 'com_myjspace' && $pparams->get('allow_user_content_var', 1) == 1) {

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
			$link = 'index.php?option=com_myjspace&amp;view='.$view.'&amp;layout=tags&amp;tmpl=component&amp;id='.$id.'&amp;e_name='.$name;

			$button = new JObject;
			$button->modal =  true;
			$button->link = $link;
			$button->class = 'btn';
			$button->text = JText::_('PLG_EDITORSXTD_MYJSPACE_BUTTON_TAGS');
			$button->name = 'tags';
			if (version_compare(JVERSION, '3.99.99', 'gt')) { 
				$button->iconSVG = 
'<svg viewBox="0 0 24 24" width="24" height="24">
	<path d="M18.748 11.717c.389.389.389 1.025 0 1.414l-4.949 4.95c-.389.389-1.025.389-1.414 0l-6.01-6.01c-.389-.389-.707-1.157-.707-1.707l-.001-4.364c0-.55.45-1 1-1h4.364c.55 0 1.318.318 1.707.707l6.01 6.01zm-10.644-4.261c-.579.576-.578 1.514-.001 2.093.578.577 1.516.577 2.095.001.576-.578.576-1.517 0-2.095-.581-.576-1.518-.577-2.094.001z"/>
</svg>';
				$button->options = [
					'height' => '200px',
					'width'  => '200px',
					'bodyHeight'  => '25',
					'modalWidth'  => '23',
					];
			} else{
				$button->options = "{handler: 'iframe', size: {x: 200, y: 200}}";
			}

			return $button;
		} else {
			return false;
		}
	}
}
