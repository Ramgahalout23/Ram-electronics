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
?>
<h2><?php echo JText::_('COM_MYJSPACE_TITLEDELETE').JText::_('COM_MYJSPACE_2POINTS').$this->user_page->title; ?></h2>
<div class="myjspace">
	<br />
	<fieldset class="adminform front">
	<legend><?php echo  JText::_('COM_MYJSPACE_AREYOUSURE'); ?></legend>
		<form method="post" action="<?php echo JRoute::_('index.php'); ?>">
			<input type="hidden" name="option" value="com_myjspace" />
			<input type="hidden" name="task" value="del_page" />
			<input type="hidden" name="id" value="<?php echo $this->user_page->id; ?>" />
			<button class="btn btn-danger">
				<span class="icon-trash"></span>
				<?php echo JText::_('COM_MYJSPACE_DELETE') ?>
			</button>

			<?php echo JHtml::_('form.token'); ?>
		</form>
	</fieldset>
</div>
