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

use Joomla\CMS\HTML\HTMLHelper; // J!4+

JHtml::_('bootstrap.tooltip');
JHtml::_('stylesheet', 'components/com_myjspace/assets/myjspace.min.css');

if (version_compare(JVERSION, '3.99.99', 'gt')) { // >= J!4+
	HTMLHelper::_('webcomponent', 'system/fields/joomla-field-user.min.js', ['version' => 'auto', 'relative' => true]);
} else if (version_compare(JVERSION, '3.4.99', 'gt')) { // between J!3.5+ & < J!4
	JHtml::_('script', 'media/jui/js/fielduser.min.js');
	JHtml::_('bootstrap.renderModal', 'modal_jform_created_by_name');
} else { // Between J!3.4+ & < J3.5
	JHtml::_('behavior.modal', 'a.modal_jform_created_by_name');
	JHtml::_('script', 'media/system/js/mootools-more.js');
	JHtml::_('script', 'components/com_myjspace/assets/myjsp-create-user-j3.4.js');
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
		<legend class="title-back"><?php echo JText::_('COM_MYJSPACE_LABELUSERDETAILS'); ?></legend>
		<table class="adminlist table table-striped">
			<tr>
				<td>
					<?php echo JText::_('COM_MYJSPACE_TITLENAME'); ?>
				</td>
				<td>
					<input type="text" name="mjs_pagename" class="inputbox form-control" value="" placeholder="<?php echo JText::_('COM_MYJSPACE_SET_PAGENAME'); ?>" size="<?php echo $this->name_page_size_max; ?>" maxlength="<?php echo $this->name_page_size_max; ?>" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_MYJSPACE_LABELNAME'); ?>
				</td>
				<td>
<?php if (version_compare(JVERSION, '3.99.99', 'gt')) { // J!4+ ?>
	<div class="controls"><joomla-field-user class="field-user-wrapper"
	url="index.php?option=com_users&view=users&layout=modal&tmpl=component&required=0&field=jform_created_by"
	modal=".modal"
	modal-width="100%"
	modal-height="400px"
	input=".field-user-input"
	input-name=".mjs_username"
	button-select=".button-select">
	<div class="input-group">
		<input type="text" id="jform_created_by_name" class="form-control mjs_username" value="" placeholder="<?php echo JText::_('COM_MYJSPACE_SELECT_USER'); ?>" readonly>
		<span class="input-group-append">
			<a class="btn btn-primary button-select" title="<?php echo JText::_('COM_MYJSPACE_SELECT_USER'); ?>"><span class="fa fa-user icon-white" aria-hidden="true"></span></a>
			<div id="userModal_jform_created_by_name" role="dialog" tabindex="-1" class="joomla-modal modal fade" data-url="index.php?option=com_users&view=users&layout=modal&tmpl=component&required=0&field=jform_created_by_name" data-iframe="&lt;iframe class=&quot;iframe&quot; src=&quot;index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;required=0&amp;field=jform_created_by_name&quot; name=&quot;Select User&quot; height=&quot;100%&quot; width=&quot;100%&quot;&gt;&lt;/iframe&gt;">
			<div class="modal-dialog modal-lg jviewport-width80" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title"><?php echo JText::_('COM_MYJSPACE_SELECT_USER'); ?></h3>
				<button type="button" class="close novalidate" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body jviewport-height60"></div>
			<div class="modal-footer"><a type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo JText::_('COM_MYJSPACE_CANCEL'); ?></a></div>
			</div>
			</div>
			</div>
		</span>
	</div>
	<input type="hidden" id="mjs_userid" name="mjs_userid" value="0" class="field-user-input " data-onchange="">
	</joomla-field-user>
	</div>
<?php } else if (version_compare(JVERSION, '3.4.99', 'gt')) { ?>
		<div class="field-user-wrapper"
		data-url="index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;required=0&amp;field={field-user-id}&amp;ismoo=0"
		data-modal=".modal"
		data-modal-width="100%"
		data-modal-height="400px"
		data-input=".field-user-input"
		data-input-name=".mjs_username"
		data-button-select=".button-select"
		>
			<div class="input-append">
				<input type="text" id="jform_created_by_nammjs_editable" value="" placeholder="<?php echo JText::_('COM_MYJSPACE_SELECT_USER'); ?>" readonly disabled="disabled" class="mjs_username " />
				<a class="btn btn-primary button-select" title="<?php echo JText::_('COM_MYJSPACE_SELECT_USER'); ?>"><span class="icon-user"></span></a>
				<div id="modal_jform_created_by_name" tabindex="-1" class="modal hide fade">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">×</button>
						<h3><?php echo JText::_('COM_MYJSPACE_SELECT_USER'); ?></h3>
					</div>
					<div class="modal-body"></div>
					<div class="modal-footer"><button class="btn" data-dismiss="modal"><?php echo JText::_('COM_MYJSPACE_CANCEL'); ?></button></div>
				</div>
			</div>
			<input type="hidden" id="mjs_userid" name="mjs_userid" value="0" class="field-user-input " data-onchange=""/>
		</div>
<?php } else { ?>
		<div class="input-append">
			<input type="text" name="mjs_username2" id="mjs_username2" class="inputbox form-control" value="" placeholder="<?php echo JText::_('COM_MYJSPACE_SELECT_USER'); ?>" disabled="disabled" />
			<input type="hidden" name="mjs_userid" id="mjs_userid" value="0" />
			<a class="btn btn-primary modal_jform_created_by_name" title="<?php echo JText::_('COM_MYJSPACE_LABELSELECTUSER'); ?>" href="index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=jform_created_by_name" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><i class="icon-user"></i></a>
		</div>
<?php } ?>
				</td>
			</tr>
<?php
			if (count($this->model_page_list) >= 2) { // If several (2 pages + text_to_choose) model page list
?>
			<tr>
				<td>
					<?php echo JText::_('COM_MYJSPACE_TITLEMODEL'); ?>
				</td>
				<td>
					<select name="mjs_model_page" id="mjs_model_page" class="custom-select">
<?php
						foreach ($this->model_page_list as $key => $value) {
							echo '<option value="'.$key.'">'.$this->model_page_list[$key]['text']."</option>\n";
						}
?>
					</select>
				</td>
			</tr>
<?php
			}

	$categories_count = count($this->categories);
	if ($categories_count) {
?>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_LABELCATEGORY'); ?>
					</td>
					<td>
						<select name="mjs_categories" id="mjs_categories" class="custom-select">
<?php
							for ($i = 0; $i < $categories_count; $i++) {
								if ($this->categories[$i]['value'] == $this->default_catid)
									echo '<option value="'.$this->categories[$i]['value'].'" selected="selected">'.str_repeat('-', max(0, $this->categories[$i]['level']-1)).' '.$this->categories[$i]['text']."</option>\n";
								else
									echo '<option value="'.$this->categories[$i]['value'].'">'.str_repeat('-', max(0, $this->categories[$i]['level']-1)).' '.$this->categories[$i]['text']."</option>\n";
							}
?>
						</select>
					</td>
				</tr>
<?php
	}
?>
		</table>

		<input type="hidden" name="option" value="com_myjspace" />
		<input type="hidden" name="task" value="adm_create_page" />
		<?php echo JHtml::_('form.token'); ?>
	</fieldset>
</div>
</div>
</form>
