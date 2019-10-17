<?php
/**
* @version $Id: default.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2010 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

JHtml::_('stylesheet', 'components/com_myjspace/assets/myjspace.min.css');
JHtml::_('script', 'media/system/js/core.js');
?>
<h2><?php echo JText::_('COM_MYJSPACE_TITLEEDIT'); ?></h2>
<div class="myjspace">
<?php
	if (!$this->msg) {
?>
	<form method="post" name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php'); ?>">
		<div class="mjp-form-button">
			<input type="hidden" name="option" id="option" value="com_myjspace" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="view" id="view" value="edit" />
			<input type="hidden" name="id" id="id" value="<?php echo $this->user_page->id; ?>" />
			<input type="hidden" name="pagename" value="<?php echo $this->user_page->pagename; ?>" />
			<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
			<button class="btn btn-primary mjp-config">
				<span class="icon-save"></span> <?php echo JText::_('COM_MYJSPACE_SAVE'); ?>
			</button>

			<input type="reset" class="btn btn-secondary mjp-config" value="<?php echo JText::_('COM_MYJSPACE_CLEAR'); ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
<?php
	$editor = JEditor::getInstance($this->editor_selection);
	echo $editor->display($this->mjs_editable, $this->user_page->content, $this->edit_x, $this->edit_y, null, null, $this->editor_button);
?>
	</form>
	<br />
<?php
	} else echo $this->msg; ?>

</div>
