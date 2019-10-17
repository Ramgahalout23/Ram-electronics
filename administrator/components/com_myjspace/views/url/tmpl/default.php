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

if (version_compare(JVERSION, '3.99.99', 'gt')) {
	JHtml::_('stylesheet', 'media/system/css/fields/switcher.min.css');
}
?>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
<div class="myjspace <?php if (version_compare(JVERSION, '3.99.99', 'lt')) {echo '';} else {echo 'row';} ?>">
<?php if (isset($this->sidebar)) { ?>
	<div id="j-sidebar-container" class="col-md-2">
		<?php echo $this->sidebar; ?>
	</div>
<?php } ?>
	<div id="j-main-container" class="j-main-container col-md-12">
		<fieldset class="adminform">
			<legend class="title-back"><?php echo JText::_('COM_MYJSPACE_FOLDERNAME');?></legend>
<?php if ($this->link_folder || $this->link_folder_print) { ?>
			<table class="adminlist table table-striped">
				<tr>
					<td>
						<span title="<?php echo JText::_('COM_MYJSPACE_FOLDERNAMEINFO');?>" ><?php echo JText::_('COM_MYJSPACE_FOLDERNAME'); ?></span> <span class="icon-folder"></span>
					</td>
					<td>
						<input type="text" name="mjs_foldername" class="form-control" value="<?php echo $this->link; ?>" placeholder="<?php echo JText::_('COM_MYJSPACE_FOLDERNAME'); ?>" />
					</td>
				</tr>
<?php 		if ($this->link_folder) { ?>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_FOLDERNAME_KEEP'); ?>
					</td>
					<td>
<?php
if (version_compare(JVERSION, '3.99.99', 'le')) {
?>
						<div class="controls">
							<fieldset id="jform_keep" class="btn-group btn-group-yesno btn-group-reversed radio">
								<input type="radio" id="jform_keep0" name="keep" value="1" />
								<label for="jform_keep0" ><?php echo JText::_('COM_MYJSPACE_ADMIN_YES'); ?></label>
								<input type="radio" id="jform_keep1" name="keep" value="0" checked="checked" />
								<label for="jform_keep1" ><?php echo JText::_('COM_MYJSPACE_ADMIN_NO'); ?></label>
							</fieldset>
						</div>
<?php } else { ?>
						<div class="switcher" role="switch">
							<input type="radio" id="jform_keep1" name="keep" value="1" >
							<label for="jform_keep1"><?php echo JText::_('COM_MYJSPACE_ADMIN_YES'); ?></label>
							<input type="radio" id="jform_keep0" name="keep" value="0" checked="checked" class="active">
							<label for="jform_keep0"><?php echo JText::_('COM_MYJSPACE_ADMIN_NO'); ?></label>
							<span class="toggle-outside"><span class="toggle-inside"></span></span>
						</div>
<?php } ?>
					</td>
				</tr>
<?php 		} ?>
			</table>
<?php } else {
		echo JText::_('COM_MYJSPACE_FOLDERNAME_NOTACTIVATED');
	}
?>
			<input type="hidden" name="option" value="com_myjspace" />
			<input type="hidden" name="task" value="adm_ren_rootfolder" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</div>
</div>
</form>