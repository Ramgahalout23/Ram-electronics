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
JHtml::_('script', 'media/system/js/core.js');

if (version_compare(JVERSION, '3.99.99', 'gt')) { // J!4+
	HTMLHelper::_('webcomponent', 'system/fields/joomla-field-user.min.js', ['version' => 'auto', 'relative' => true]);
} else if (version_compare(JVERSION, '3.4.99', 'gt')) { // J!3.5+
	JHtml::_('script', 'media/jui/js/fielduser.min.js');
	JHtml::_('bootstrap.renderModal', 'modal_jform_created_by_name');
	JHtml::_('behavior.modal', 'a.modal_association');
} else { // Between J!3.4+ & < J!3.5
	JHtml::_('behavior.modal', 'a.modal_jform_created_by_name');
	JHtml::_('script', 'media/system/js/mootools-more.js');
	JHtml::_('script', 'components/com_myjspace/assets/myjsp-create-user-j3.4.js');
	JHtml::_('behavior.modal', 'a.modal_association');
}

if (count($this->associations))
	JHtml::_('script', 'components/com_myjspace/assets/association.js');

if ($this->publish_mode == 2 && version_compare(JVERSION, '3.7.0', 'lt'))
	JHtml::_('behavior.calendar');
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
						<?php echo JText::_('COM_MYJSPACE_PAGELINK'); ?>
					</td>
					<td>
						<span class="small mt-1"><a href="<?php echo $this->link ?>" target="_blank"><?php echo $this->link ?></a></span>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_TITLENAME'); ?>
					</td>
					<td>
						<input type="text" name="mjs_pagename" class="inputbox form-control" value="<?php echo $this->user_page->title; ?>" placeholder="<?php echo JText::_('COM_MYJSPACE_SET_PAGENAME'); ?>" size="<?php echo $this->name_page_size_max; ?>" maxlength="<?php echo $this->name_page_size_max; ?>" />
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_LABELPAGEID'); ?>
					</td>
					<td>
						<?php echo $this->user_page->id; ?>
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
		<input type="text" id="jform_created_by_name" class="form-control mjs_username" value="<?php echo $this->user->name; ?>" placeholder="<?php echo JText::_('COM_MYJSPACE_SELECT_USER'); ?>" readonly>
		<span class="input-group-append">
			<a class="btn btn-primary button-select" title="<?php echo JText::_('COM_MYJSPACE_SELECT_USER'); ?>"><span class="fa fa-user icon-white" aria-hidden="true"></span></a>
			<div id="usermodal_jform_created_by_name" role="dialog" tabindex="-1" class="joomla-modal modal fade" data-url="index.php?option=com_users&view=users&layout=modal&tmpl=component&required=0&field=jform_created_by_name" data-iframe="&lt;iframe class=&quot;iframe&quot; src=&quot;index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;required=0&amp;field=jform_created_by_name&quot; name=&quot;Select User&quot; height=&quot;100%&quot; width=&quot;100%&quot;&gt;&lt;/iframe&gt;">
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
				<input type="text" id="jform_created_by_nammjs_editable" value="<?php echo $this->user->name; ?>" readonly disabled="disabled" class="mjs_username " />
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
			<input type="text" name="mjs_username2" id="mjs_username2" class="inputbox form-control" value="<?php echo $this->user->name; ?>" disabled="disabled" />
			<input type="hidden" name="mjs_userid" id="mjs_userid" value="<?php echo $this->user->id; ?>" />
			<a class="btn btn-primary modal_jform_created_by_name" title="<?php echo JText::_('COM_MYJSPACE_LABELSELECTUSER'); ?>" href="index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=jform_created_by_name" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><i class="icon-user"></i></a>
		</div>
<?php } ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_LABELMETAKEY'); ?>
					</td>
					<td>
						<input type="text" name="mjs_metakey" class="inputbox form-control" value="<?php echo $this->user_page->metakey; ?>" size="80" />
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_LABELCREATIONDATE'); ?>
					</td>
					<td>
						<?php echo $this->user_page->create_date; ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_LABELLASTUPDATEDATE'); ?>
					</td>
					<td>
						<?php echo $this->user_page->last_update_date; ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_TITLEUPDATENAME'); ?>
					</td>
					<td>
						<?php echo $this->modified_by->name; ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_LABELLASTACCESSDATE'); ?>
					</td>
					<td>
						<?php echo $this->user_page->last_access_date; ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_LABELHITS'); ?>
					</td>
					<td>
						<?php echo $this->user_page->hits;
						if ($this->user_page->hits > 0) { ?>
							&nbsp;<button class="button btn btn-secondary" onclick="document.getElementById('resethits').value='yes';this.form.submit();"><span class="icon-refresh"></span> <?php echo JText::_('COM_MYJSPACE_LABELHITSRESET'); ?></button>
						<input type="hidden" name="resethits" id="resethits" value="no" />
					<?php } ?>
					</td>
				</tr>
<?php
	if ($this->publish_mode != 0) {
?>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_LABELPUBLISHUP').' '.$this->img_publish_up; ?>
					</td>
					<td>
						<?php echo JHtml::_('calendar', $this->user_page->publish_up, 'jform_publish_up', 'jform_publish_up', JText::_('COM_MYJSPACE_DATE_CALENDAR2'), null); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_LABELPUBLISHDOWN').' '.$this->img_publish_down; ?>
					</td>
					<td>
						<?php echo JHtml::_('calendar', $this->user_page->publish_down, 'jform_publish_down', 'jform_publish_down', JText::_('COM_MYJSPACE_DATE_CALENDAR2'), null); ?>
					</td>
				</tr>
<?php
	}
?>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_TITLEMODEEDIT'); ?>
					</td>
					<td>
						<select name="mjs_mode_edit" id="mjs_mode_edit" class="custom-select">
							<option value="0" <?php if ($this->user_page->blockedit == 0) echo " selected='selected'"; ?> ><?php echo JText::_('COM_MYJSPACE_TITLEMODEEDIT0') ?></option>
							<option value="1" <?php if ($this->user_page->blockedit == 1) echo " selected='selected'"; ?> ><?php echo JText::_('COM_MYJSPACE_TITLEMODEEDIT1') ?></option>
							<option value="2" <?php if ($this->user_page->blockedit == 2) echo " selected='selected'"; ?> ><?php echo JText::_('COM_MYJSPACE_TITLEMODEEDIT2') ?></option>						</select>
					</td>
				</tr>
<?php
	if ($this->group_list) {
?>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_TITLESHAREEDIT'); ?>
					</td>
					<td>
						<select name="mjsp_share" id="mjsp_share" class="custom-select">
<?php
						if ($this->user_page->access == 0)
							echo "<option value=\"0\" selected=\"selected\">&nbsp;-</option>\n";
						else
							echo "<option value=\"0\">&nbsp;-</option>\n";

						foreach ($this->group_list as $value) {
							if ($value->value != 1 && !($value->value == 5)) { // No 'public' & 'guest' groups
								if ($value->value == $this->user_page->access)
									echo '<option value="'.$value->value.'" selected="selected">'.$value->text.' '.$value->value."</option>\n";
								else
									echo '<option value="'.$value->value.'">'.$value->text.' '.$value->value."</option>\n";
							}
						}
?>
						</select>
					</td>
				</tr>
<?php
	}
?>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_TITLEMODEVIEW'); ?>
					</td>
					<td>
						<select name="mjs_mode_view" id="mjs_mode_view" class="custom-select">
<?php
						foreach ($this->blockview_list as $value) {
							if ($value->value == $this->user_page->blockview)
								echo '<option value="'.$value->value.'" selected="selected">'.$value->text."</option>\n";
							else
								echo '<option value="'.$value->value.'">'.$value->text."</option>\n";
						}
?>
						</select>
					</td>
				</tr>
<?php
	if ($this->user_mode_view_userread) {
?>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_TITLEMODEVIEWUSERREAD'); ?>
					</td>
					<td>
						<input type="text" name="mjs_userread" id="mjs_userread" class="inputbox form-control" value="<?php echo $this->user_page->userread_name; ?>" />
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
								if ($this->categories[$i]['value'] == $this->user_page->catid)
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

	$language_list_count = count($this->language_list);
	if ($language_list_count) {
?>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_LABELLANGUAGE'); ?>
					</td>
					<td>
						<select name="mjs_language" id="mjs_language" class="custom-select" onchange="document.getElementById('mjs_categories').value='<?php echo $this->default_catid ?>'; ">
<?php
							for ($i = 0; $i < $language_list_count; $i++) {
								if ($this->language_list[$i]->lang_code == $this->user_page->language)
									echo '<option value="'.$this->language_list[$i]->lang_code.'" selected="selected">'.$this->language_list[$i]->title."</option>\n";
								else
									echo '<option value="'.$this->language_list[$i]->lang_code.'">'.$this->language_list[$i]->title."</option>\n";
							}
?>
						</select>
					</td>
				</tr>
<?php
	if (count($this->associations)) {
		foreach ($this->associations as $tag => $data) {
?>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_LABELASSOCIATION'); ?>
					</td>
					<td>
						<div class="input-append">
<?php
	$url_asso = 'index.php?option=com_myjspace&amp;view=pages&amp;layout=modal&amp;tmpl=component&amp;field=pagename&amp;association='.$tag.'&amp;'.JSession::getFormToken().'=1';
?>
							<input type="text" class="input-medium" id="jform_associations_<?php echo $tag; ?>_name" value="<?php echo $this->associations[$tag]->pagename; ?>" disabled="disabled" placeholder="<?php echo JText::_('COM_MYJSPACE_SELECT_A_PAGE'); ?>" />
							<a class="modal btn modal_association myjspbtn" title="<?php echo JText::_('COM_MYJSPACE_SELECT_A_PAGE'); ?>" href="<?php echo $url_asso; ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><i class="icon-file"></i> <?php echo $this->associations[$tag]->language; ?></a>
							<a class="btn hasTooltip" href="#" onclick="jInsertFieldValue('', 'jform_associations_<?php echo $tag; ?>_name'); jInsertFieldValue('', 'jform_associations_<?php echo $tag; ?>_id');return false;"><span class="icon-remove"></span><?php echo JText::_('COM_MYJSPACE_ASSOCIATION_CLEAR'); ?></a>
							<input type="hidden" id="jform_associations_<?php echo $tag; ?>_id" name="associations[<?php echo $tag; ?>]" value="<?php echo BSHelperUser::ifExistPageName($this->associations[$tag]->pagename); ?>" />
						</div>
					</td>
				</tr>
<?php
			}
		}
	}

	if ($this->form) {
?>
			<tr>
				<td>
					<?php echo $this->form->getLabel('tags', 'metadata'); ?>
				</td>
				<td>
					<div class="input-append">
						<?php echo $this->form->getInput('tags', 'metadata', $this->tags).'<br />'; ?>
					</div>
				</td>
			</tr>
<?php
	}

	if (count($this->tab_template)) {
?>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_TEMPLATE'); ?>
					</td>
					<td>
						<select name="mjs_template" id="mjs_template" class="custom-select">
						<?php
						if ('' == $this->user_page->template)
							echo "<option value=\"\" selected=\"selected\">-</option>\n";
						else
							echo "<option value=\"\">-</option>\n";

						foreach ($this->tab_template as $key => $value) {
							if ($key == $this->user_page->template)
								echo '<option value="'.$key.'" selected="selected">'.$value."</option>\n";
							else
								echo '<option value="'.$key.'">'.$value."</option>\n";
						}
						?>
						</select>
					</td>
				</tr>
<?php
	}

	if ($this->link_folder && ($this->uploadimg > 0 || $this->uploadmedia > 0)) {
?>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_LABELUSAGE0'); ?>
					</td>
					<td>
						<?php echo JText::sprintf('COM_MYJSPACE_LABELUSAGE1', $this->page_size, $this->dir_max_size, $this->page_number); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_LABELUSAGE2'); ?>
					</td>
					<td>
						<?php echo $this->file_max_size_txt; ?>
					</td>
				</tr>
<?php
	}
?>
			</table>

<?php if ($categories_count == 0) { ?>
			<input type="hidden" name="mjs_categories"  id="mjs_categories" value="0" />
<?php } ?>
			<input type="hidden" name="mjs_id" id="mjs_id" value="<?php echo $this->user_page->id; ?>" />
			<input type="hidden" name="option" id="view" value="com_myjspace" />
			<input type="hidden" name="view" value="page" />
			<input type="hidden" name="task" value="adm_save_page" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>

		<fieldset class="adminform">
		<legend class="title-back-s"><?php echo JText::_('COM_MYJSPACE_PAGE_CONTENT'); ?></legend>
<?php
	$editor = JEditor::getInstance($this->editor_selection);
	echo $editor->display($this->mjs_editable, $this->user_page->content, $this->edit_x, $this->edit_y, null, null, $this->editor_button);
?>
		<br />
		</fieldset>
	</div>

	<div class="clr"></div>
</div>
</form>
