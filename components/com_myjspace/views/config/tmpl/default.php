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

if (version_compare(JVERSION, '3.99.99', 'lt'))
	JHtml::_('behavior.modal', 'a.modal_association');

if ($this->publish_mode == 2 && version_compare(JVERSION, '3.7.0', 'lt'))
	JHtml::_('behavior.calendar');

if (count($this->associations))
	JHtml::_('script', 'components/com_myjspace/assets/association.js');

JHtml::_('script', 'components/com_myjspace/assets/drag_drop_upload.js');
JFactory::getDocument()->addScriptDeclaration($this->drag_drop_upload);
?>

<h2><?php echo JText::_('COM_MYJSPACE_TITLECONFIG'); ?></h2>
<div class="myjspace">
<?php if ($this->user_page->blockedit != 2 && $this->alert_root_page == 0) { ?>
		<form action="<?php echo JRoute::_('index.php'); ?>" method="post">
		<fieldset class="adminform front">
		<legend><?php echo JText::_('COM_MYJSPACE_LABELUSERDETAILS'); ?></legend>
			<table class="admintable">
<?php if ($this->show_link_admin) { ?>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_PAGELINK'); ?></label>
					</td>
					<td>
						<a href="<?php echo $this->link; ?>"><?php echo $this->link; ?></a>
					</td>
				</tr>
<?php } ?>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_TITLENAME'); ?></label>
					</td>
					<td>
					<?php if ($this->pagename_username) { ?>
						<?php echo $this->user_page->pagename; ?>
						<input type="hidden" name="mjs_pagename" value="<?php echo $this->user_page->title; ?>" size="<?php echo $this->name_page_size_max; ?>" maxlength="<?php echo $this->name_page_size_max; ?>" /> <?php echo $this->msg_tmp; ?>
					<?php } else { ?>
						<input type="text" name="mjs_pagename" class="form-control" value="<?php echo $this->user_page->title; ?>" placeholder="<?php echo JText::_('COM_MYJSPACE_SET_PAGENAME'); ?>" size="<?php echo $this->name_page_size_max; ?>" maxlength="<?php echo $this->name_page_size_max; ?>" /> <?php echo $this->msg_tmp; ?>
					<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELUSERNAME'); ?></label>
					</td>
					<td>
						<?php echo $this->user->username; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELMETAKEY'); ?></label>
					</td>
					<td>
						<input type="text" name="mjs_metakey" class="form-control" value="<?php echo $this->user_page->metakey; ?>" size="80" />
					</td>
				</tr>
<?php
		$model_page_list_count = count($this->model_page_list);
		if ($this->msg_tmp != '' && $model_page_list_count >= 2) { // If several (2 pages + text_to_choixe) model page list
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_TITLEMODEL'); ?></label>
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
<?php	}	?>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELCREATIONDATE'); ?></label>
					</td>
					<td>
						<?php echo $this->user_page->create_date; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELLASTUPDATEDATE'); ?></label>
					</td>
					<td>
						<?php echo $this->user_page->last_update_date; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELLASTACCESSDATE'); ?></label>
					</td>
					<td>
						<?php echo $this->user_page->last_access_date; ?>
					</td>
				</tr>
<?php
	if ($this->page_increment) {
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELHITS'); ?></label>
					</td>
					<td>
						<?php echo $this->user_page->hits;
						if ($this->user_page->hits) { ?>
							&nbsp;<button class="button btn btn-secondary" onclick="document.getElementById('resethits').value='yes';this.form.submit();"><span class="icon-refresh"></span> <?php echo JText::_('COM_MYJSPACE_LABELHITSRESET'); ?></button>
							<input type="hidden" name="resethits" id="resethits" value="no" />
					<?php } ?>
					</td>
				</tr>
<?php
	}

	if ($this->publish_mode == 2) {
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELPUBLISHUP').' '.$this->img_publish_up; ?></label>
					</td>
					<td>
						<?php echo JHtml::_('calendar', $this->user_page->publish_up, 'jform_publish_up', 'jform_publish_up', JText::_('COM_MYJSPACE_DATE_CALENDAR2'), null); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELPUBLISHDOWN').' '.$this->img_publish_down; ?></label>
					</td>
					<td>
						<?php echo JHtml::_('calendar', $this->user_page->publish_down, 'jform_publish_down', 'jform_publish_down', JText::_('COM_MYJSPACE_DATE_CALENDAR2'), null); ?>
					</td>
				</tr>
<?php
	}

	if ($this->share_page != 0) {
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_TITLESHAREEDIT'); ?></label>
					</td>
					<td>
<?php
					if ($this->share_page == 2) {
						echo "<select name=\"mjsp_share\" id=\"mjsp_share\" class=\"custom-select inputbox\">\n";
						if ($this->user_page->access == 0)
							echo "<option value=\"0\" selected=\"selected\">&nbsp;-</option>\n";
						else
							echo "<option value=\"0\">&nbsp;-</option>\n";

						foreach ($this->group_list as $value) {
							if ($value->value != 1 && !($value->value == 5)) { // No 'public' & 'guest' groups
								if ($value->value == $this->user_page->access)
									echo '<option value="'.$value->value.'" selected="selected">'.$value->text."</option>\n";
								else
									echo '<option value="'.$value->value.'">'.$value->text."</option>\n";
							}
						}
						echo "</select>\n";
					} else
						echo BS_UtilAcl::get_assetgroup_label($this->user_page->access);
?>
					</td>
				</tr>
<?php
	}

	if ($this->share_page != 0 && $this->user_page->access > 0) {
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_TITLEUPDATENAME'); ?></label>
					</td>
					<td>
						<?php echo $this->modified_by->username; ?>
					</td>
				</tr>
<?php
	}

	$categories_count = count($this->categories);
	if ($categories_count) {
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELCATEGORY'); ?></label>
					</td>
					<td>
<?php
					if ($this->select_category) {
?>
						<select name="mjs_categories" id="mjs_categories" class="custom-select">
<?php
							for ($i = 0; $i < $categories_count; $i++) {
								if ($this->categories[$i]['value'] == $this->user_page->catid || $this->categories[$i]['value'] == $this->catid)
									echo '<option value="'.$this->categories[$i]['value'].'" selected="selected">'.str_repeat('-', max(0, $this->categories[$i]['level']-1)).' '.$this->categories[$i]['text']."</option>\n";
								else
									echo '<option value="'.$this->categories[$i]['value'].'">'.str_repeat('-', max(0, $this->categories[$i]['level']-1)).' '.$this->categories[$i]['text']."</option>\n";
							}
?>
						</select>
<?php
					} else {
						for ($i = 0; $i < $categories_count; $i++) {
							if ($this->categories[$i]['value'] == $this->user_page->catid || $this->categories[$i]['value'] == $this->catid) {
								echo $this->categories[$i]['text'];
?>
								<input type="hidden" name="mjs_categories" value="<?php echo $this->categories[$i]['value']; ?>" />
<?php
							}
						}
					}
?>
					</td>
				</tr>
<?php
	}

	$language_list_count = count($this->language_list);
	if ($language_list_count) {
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELLANGUAGE'); ?></label>
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
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELASSOCIATION'); ?></label>
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
				<td class="key">
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
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_TEMPLATE'); ?></label>
					</td>
					<td>
						<select name="mjs_template" id="mjs_template" class="custom-select">
						<option value="">-</option>
						<?php
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

	if ($this->user_mode_view) {
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_TITLEMODEVIEW'); ?></label>
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
	}

	if ($this->user_mode_view_userread) {
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_TITLEMODEVIEWUSERREAD'); ?></label>
					</td>
					<td>
						<input type="text" name="mjs_userread" id="mjs_userread" class="inputbox form-control" value="<?php echo $this->user_page->userread_name; ?>" />
					</td>
				</tr>
<?php
	}

	if ($this->link_folder && ($this->uploadimg > 0 || $this->uploadmedia > 0)) {
?>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELUSAGE0'); ?></label>
					</td>
					<td>
						<?php echo JText::sprintf('COM_MYJSPACE_LABELUSAGE1', $this->page_size, $this->dir_max_size, $this->page_number); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_('COM_MYJSPACE_LABELUSAGE2'); ?></label>
					</td>
					<td>
						<?php echo $this->file_max_size_txt; ?>
					</td>
				</tr>
<?php
	}
?>
			</table>
			<p> </p>

<?php if ($categories_count == 0) { ?>
			<input type="hidden" name="mjs_categories"  id="mjs_categories" value="0" />
<?php } ?>
			<input type="hidden" name="option" value="com_myjspace" />
			<input type="hidden" name="task" value="save_config" />
			<input type="hidden" name="id" value="<?php echo $this->user_page->id; ?>" />

			<button class="btn btn-primary mjp-config">
				<span class="icon-save"></span>
				<?php echo JText::_('COM_MYJSPACE_SAVE') ?>
			</button>

			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
		</form>
<?php if ($this->msg_tmp == '') { ?>
	<fieldset class="adminform front">
		<legend><?php echo JText::_('COM_MYJSPACE_TITLEACTION') ?></legend>
		<table class="noborder width100" ><tr>
		<td>
<?php 	if (JFactory::getUser()->authorise('user.edit', 'com_myjspace')) { ?>
			<form method="post" action="<?php echo JRoute::_('index.php?option=com_myjspace&view=edit&id='.$this->user_page->id.'&pagename='.$this->user_page->pagename, false); ?>">
				<button class="btn btn-secondary mjp-config">
					<span class="icon-edit"></span>
					<?php echo JText::_('COM_MYJSPACE_TITLEEDIT1') ?>
				</button>
			</form>
<?php	} ?>
		</td>
		<td>
<?php 	if (JFactory::getUser()->authorise('user.see', 'com_myjspace')) { ?>
			<form method="post" action="<?php echo JRoute::_('index.php?option=com_myjspace&view=see&id='.$this->user_page->id.'&pagename='.$this->user_page->pagename, false); ?>">
				<button class="btn btn-secondary mjp-config">
					<span class="icon-zoom-in"></span>
					<?php echo JText::_('COM_MYJSPACE_TITLESEE1') ?>
				</button>
			</form>
<?php	} ?>
		</td>
		<td>
<?php	if (JFactory::getUser()->authorise('user.delete', 'com_myjspace')) { ?>
			<form method="post" action="<?php echo JRoute::_('index.php?option=com_myjspace&view=delete&id='.$this->user_page->id.'&pagename='.$this->user_page->pagename, false); ?>">
				<button class="btn btn-secondary mjp-config">
					<span class="icon-trash"></span>
					<?php echo JText::_('COM_MYJSPACE_DELETE') ?>
				</button>
			</form>
<?php	} ?>
		</td>
		<td>
<?php	if ($this->nb_max_page > 1 && JFactory::getUser()->authorise('user.pages', 'com_myjspace')) { ?>
			<form method="post" action="<?php echo JRoute::_('index.php?option=com_myjspace&view=pages'.$this->catid_url, false); ?>">
				<button class="btn btn-secondary mjp-config">
					<span class="icon-new"></span>
					<?php echo JText::_('COM_MYJSPACE_NEW') ?>
				</button>
			</form>
<?php	} ?>
		</td>
		</tr></table>
	</fieldset>
<?php
		}
		if ($this->uploadimg > 0 || $this->uploadmedia > 0) {
?>
	<fieldset class="adminform front">
		<legend><?php echo JText::_('COM_MYJSPACE_UPLOADTITLE') ?></legend>

		<form method="post" action="<?php echo JRoute::_('index.php'); ?>" enctype="multipart/form-data" id="upload_file_form" >
			<input type="hidden" name="option" value="com_myjspace" />
			<input type="hidden" name="layout" value="" />
			<input type="hidden" name="task" value="upload_file" />
			<input type="hidden" name="upload_id" value="<?php echo $this->user_page->id; ?>" />
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $this->file_max_size; ?>" />

			<div>
				<input type="file" name="upload_file" id="upload_file" onchange="document.getElementById('fileupload').className='encours';document.getElementById('upload_file_form').submit();" />
				<span id="holder">
					&nbsp;
					<button class="btn btn-success" ><span class="icon-download"></span> <?php echo JText::_('COM_MYJSPACE_UPLOAD'); ?></button>
					<span><small><?php echo JText::sprintf('COM_MYJSPACE_MAXIMUM_UPLOAD_SIZE', $this->file_max_size_txt2); ?></small></span>
				</span>
				<div id="fileupload" class="dragdrop">&nbsp;</div>
			</div>

			<?php echo JHtml::_('form.token'); ?>
		</form>

		<p> </p>

		<form method="post" action="<?php echo JRoute::_('index.php'); ?>" >
			<input type="hidden" name="option" value="com_myjspace" />
			<input type="hidden" name="task" value="delete_file" />
			<input type="hidden" name="delete_id" value="<?php echo $this->user_page->id; ?>" />

			<div>
				<select name="delete_file" id="delete_file" class="custom-select">
					<option value="" selected="selected"><?php echo JText::_('COM_MYJSPACE_UPLOADCHOOSE') ?></option>
					<?php
						$nb = count($this->tab_list_file);

						if ($nb == 1)
							$selected = ' selected="selected"';
						else
							$selected = '';

						for ($i = 0 ; $i < $nb ; ++$i) {
							$chaine_tmp = $this->tab_list_file[$i];
							if (strlen($chaine_tmp) > 25)
								$chaine_tmp = substr($chaine_tmp, 0, 25).'...';
							echo '<option value="'.utf8_encode($this->tab_list_file[$i]).'"'.$selected.'>'.utf8_encode($chaine_tmp)."</option>\n";
						}
					?>
				</select>

				<button class="btn btn-danger">
					<span class="icon-trash"></span>
					<?php echo JText::_('COM_MYJSPACE_UPLOADDELETE') ?>
				</button>
			</div>

			<?php echo JHtml::_('form.token'); ?>
		</form>
	</fieldset>
<?php
		}
	} else if ($this->alert_root_page == 1)
 		echo JText::_('COM_MYJSPACE_ALERTYOURADMIN');
	else if ($this->user_page->blockedit == 1)
		echo JText::_('COM_MYJSPACE_EDITBLOCKED');
	else
		echo JText::_('COM_MYJSPACE_EDITLOCKED');
?>

</div>
