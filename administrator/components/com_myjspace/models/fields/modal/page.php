<?php
/**
* @version $Id: page.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

/**
 * Supports a modal article picker.
 *
 * @since  1.6
 */
class JFormFieldModal_Page extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   1.6
	 */
	protected $type = 'Modal_Page';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 *
	 * @since   1.6
	 */
	protected function getInput()
	{
		$allowClear = ((string)$this->element['clear'] != 'false');

		// Load language
		JFactory::getLanguage()->load('com_myjspace', JPATH_ADMINISTRATOR);

		// Build the script
		$script = array();

		// Select button script
		$script[] = '	function jSelectMyjsp_jform_modal_b(id, title, lang) {';
		$script[] = '		document.getElementById("'.$this->id.'_id").value = id;';
		$script[] = '		document.getElementById("'.$this->id.'_name").value = title;';

		if ($allowClear) {
			$script[] = '		jQuery("#'.$this->id.'_clear").removeClass("hidden");';
		}

		$script[] = '		jQuery("#modalArticle'.$this->id.'").modal("hide");';
		$script[] = '	}';
		
		// Clear button script
		static $scriptClear;

		if ($allowClear && !$scriptClear) {
			$scriptClear = true;

			$script[] = '	function jClearArticle(id) {';
			$script[] = '		document.getElementById(id + "_id").value = "";';
			$script[] = '		document.getElementById(id + "_name").value = "'.
				htmlspecialchars(JText::_('COM_MYJSPACE_SELECT_A_PAGE', true), ENT_COMPAT, 'UTF-8').'";';
			$script[] = '		jQuery("#"+id + "_clear").addClass("hidden");';
			$script[] = '		if (document.getElementById(id + "_edit")) {';
			$script[] = '			jQuery("#"+id + "_edit").addClass("hidden");';
			$script[] = '		}';
			$script[] = '		return false;';
			$script[] = '	}';
		}

		// Add the script to the document head
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Setup variables for display
		$html = array();
		$link = 'index.php?option=com_myjspace&amp;view=pages&amp;layout=modal&amp;tmpl=component&amp;modal_fct=jSelectMyjsp_jform_modal_b';

		if (isset($this->element['language'])) {
			$link .= '&amp;forcedLanguage=' . $this->element['language'];
		}

		if ((int)$this->value > 0) {
			$db	= JFactory::getDbo();
			$query = $db->getQuery(true)
				->select($db->qn('title'))
				->from($db->qn('#__myjspace'))
				->where($db->qn('id').' = '.(int)$this->value);
			$db->setQuery($query);

			try
			{
				$title = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				if (JDEBUG)
					JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()), 'error');
			}
		}

		if (empty($title)) {
			$title = JText::_('COM_MYJSPACE_SELECT_A_PAGE');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The active article id field.
		if (0 == (int)$this->value) {
			$value = '';
		} else {
			$value = (int)$this->value;
		}

		$url = $link.'&amp;'.JSession::getFormToken().'=1';
		// The current article display field
		$html[] = '<span class="input-append input-group">';
		$html[] = '<input type="text" class="input-medium form-control" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" />';
		$html[] = '<a href="#modalArticle'.$this->id.'" class="btn btn-primary hasTooltip" role="button"  data-toggle="modal" title="'
			.JHtml::tooltipText('COM_MYJSPACE_CHANGE_PAGE').'">'
			.'<span class="icon-file"></span> '
			.JText::_('JSELECT').'</a>';

		// Clear article button
		if ($allowClear && (int)$this->value > 0) {
			$html[] = '<button id="'.$this->id.'_clear" class="btn btn-secondary'.($value ? '' : ' hidden').'" onclick="return jClearArticle(\'' .
				$this->id.'\')"><span class="icon-remove"></span>'.JText::_('JCLEAR').'</button>';
		}

		$html[] = '</span>';

		// The class='required' for client side validation
		$class = '';

		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

		$html[] = JHtml::_(
			'bootstrap.renderModal',
			'modalArticle'.$this->id,
			array(
				'url' => $url,
				'title' => JText::_('COM_MYJSPACE_CHANGE_PAGE'),
				'width' => '800px',
				'height' => '300px',
				'footer' => '<button class="btn" data-dismiss="modal" aria-hidden="true">'
					.JText::_("JLIB_HTML_BEHAVIOR_CLOSE").'</button>'
			)
		);

		return implode("\n", $html);
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   3.4
	 */
	protected function getLabel()
	{
		return str_replace($this->id, $this->id.'_id', parent::getLabel());
	}
}
