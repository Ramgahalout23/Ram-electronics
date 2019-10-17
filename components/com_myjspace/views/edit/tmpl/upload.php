<?php
/**
* @version $Id: upload.php $
* @version		3.0.1 20/09/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2013 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

JHtml::_('stylesheet', 'components/com_myjspace/assets/myjspace.min.css');
JHtml::_('script', 'components/com_myjspace/assets/myjsp-tags.js');

// To insert HTML code into page : image or file
JFactory::getDocument()->addScriptDeclaration("\n".'page_url = "'.$this->user_page->foldername.'/'.$this->user_page->pagename.'/'.'";'."\n");

JHtml::_('script', 'components/com_myjspace/assets/drag_drop_upload.js');
JFactory::getDocument()->addScriptDeclaration($this->drag_drop_upload);
?>
<p> </p>

<div class="myjsp-upload">
<?php if ($this->user_page->blockedit != 2 || $this->isAdmin) {
		if ($this->uploadimg > 0 || $this->uploadmedia > 0) {
?>
	<div id="position-global">
		<p> </p>

		<form method="post" action="<?php echo JRoute::_('index.php'); ?>" >
			<fieldset class="selectf">
				<input type="hidden" name="option" value="com_myjspace" />
				<input type="hidden" name="layout" value="upload" />
				<input type="hidden" name="task" value="upload_file" />
				<input type="hidden" name="e_name" value="<?php echo $this->e_name; ?>" />
				<input type="hidden" name="id" value="<?php echo $this->user_page->id; ?>" />
				<input type="hidden" name="type" value="<?php echo $this->type; ?>" />

				<div class="input-group input-append">
				<select name="select_file" id="select_file">
					<option value=""><?php echo JText::_('COM_MYJSPACE_UPLOADCHOOSE') ?></option>
					<?php
						$nb = count($this->tab_list_file);
						if ($nb == 1)
							$selected = ' selected="selected"';
						else
							$selected = '';

						for ($i = 0 ; $i < $nb ; ++$i) {
							$chaine_tmp = $this->tab_list_file[$i];

							if ($nb == 1 || $chaine_tmp == $this->uploaded)
								$selected = ' selected="selected"';
							else
								$selected = '';

							if (strlen($chaine_tmp) > 25)
								$chaine_tmp = substr($chaine_tmp, 0, 25).'...';

							echo '<option value="'.urlencode($this->tab_list_file[$i]).'"'.$selected.'>'.utf8_encode($chaine_tmp)."</option>\n";
						}
					?>
				</select>

<?php		if ($this->type == 'image') { ?>
				<button class="btn btn-sm btn-secondary" onclick="insertTags(myjsp_set_tag_img(page_url+document.getElementById('select_file').value, document.getElementById('select_file').value),'<?php echo $this->e_name; ?>')">
					<span class="icon-publish"></span> <?php echo JText::_('COM_MYJSPACE_UPLOADCHOOSE0') ?>
				</button>
<?php		} else { ?>
				<button class="btn btn-sm btn-secondary" onclick="insertTags(myjsp_set_url(page_url+document.getElementById('select_file').value, document.getElementById('select_file').value),'<?php echo $this->e_name; ?>')">
					<span class="icon-publish"></span> <?php echo JText::_('COM_MYJSPACE_UPLOADCHOOSE0') ?>
				</button>
<?php		} ?>
				</div>
			</fieldset>
		</form>

		<form method="post" action="<?php echo JRoute::_('index.php'); ?>" enctype="multipart/form-data" id="upload_file_form" >
			<fieldset class="selectf">
				<input type="hidden" name="option" value="com_myjspace" />
				<input type="hidden" name="layout" value="upload" />
				<input type="hidden" name="task" value="upload_file" />
				<input type="hidden" name="e_name" value="<?php echo $this->e_name; ?>" />
				<input type="hidden" name="upload_id" value="<?php echo $this->user_page->id; ?>" />
				<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
				<div>
					<input type="file" name="upload_file" id="upload_file" onchange="document.getElementById('fileupload').className='encours';document.getElementById('upload_file_form').submit();" />
<!--					<input type="file" name="upload_file" id="upload_file" onchange="uploadFile();"> EVOL progress bar -->
					<span id="holder"> <!-- drag & drop zone, in a span zone -->
						&nbsp;
						<button class="btn btn-sm btn-success" ><span class="icon-download"></span> <?php echo JText::_('COM_MYJSPACE_UPLOAD'); ?></button>
					</span>
					<div><small><?php echo JText::sprintf('COM_MYJSPACE_MAXIMUM_UPLOAD_SIZE', $this->file_max_size_txt2); ?></small></div>
<!--
// EVOL progress bar
					<div>
						<progress id="progress-bar" value="0" max="100" style="width:200px;height:10px;"></progress>
						<span id="progress-status"> </span>
					</div>
-->
					<div id="fileupload" class="dragdrop">&nbsp;</div>
				</div>

				<?php echo JHtml::_('form.token'); ?>
			</fieldset>
		</form>


		<form method="post" action="<?php echo JRoute::_('index.php'); ?>" >
			<fieldset class="selectf">
				<input type="hidden" name="option" value="com_myjspace" />
				<input type="hidden" name="layout" value="upload" />
				<input type="hidden" name="task" value="delete_file" />
				<input type="hidden" name="tmpl" value="component" />
				<input type="hidden" name="delete_id" value="<?php echo $this->user_page->id; ?>" />
				<input type="hidden" name="e_name" value="<?php echo $this->e_name; ?>" />
				<input type="hidden" name="type" value="<?php echo $this->type; ?>" />

				<div class="input-group input-append">
					<select name="delete_file" id="delete_file">
						<option value=""><?php echo JText::_('COM_MYJSPACE_UPLOADCHOOSE') ?></option>
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

					<button class="btn btn-sm btn-danger">
						<span class="icon-trash"></span>
						<?php echo JText::_('COM_MYJSPACE_UPLOADDELETE') ?>
					</button>
				</div>

				<?php echo JHtml::_('form.token'); ?>
			</fieldset>
		</form>
	</div>
</div>
<?php
		}
	}
?>
