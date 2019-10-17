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
JHtml::_('script', 'components/com_myjspace/assets/myjsp-clear-pages.js');

$check = 'Joomla.checkAll(this)';
$colspan = 9;
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

	<table class="adminlist">
		<tr>
			<td class="width100">
				<div class="js-stools-container-bar">
					<div class="btn-wrapper input-append">
						<input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="text_area form-control" onchange="document.adminForm.submit();" placeholder="<?php echo JText::_('COM_MYJSPACE_FILTER'); ?>" />
						<button type="button" class="btn hasTooltip btn-primary" onclick="this.form.submit();"><i class="icon-search"></i></button>
						<button type="button" class="btn hasTooltip btn-primary" onclick="MyjspClearPages();document.adminForm.submit();"><?php echo JText::_('COM_MYJSPACE_CLEAR'); ?></button>
<?php 			if ($this->association != '') {?>
						<input type="button" class="btn" value="<?php echo JText::_('COM_MYJSPACE_NONE'); ?>" onclick="window.parent.jSelectMyjsp_jform_modal('0', '', '<?php echo $this->association; ?>');" />
<?php 			} ?>
					</div>
				</div>
			</td>
			<td>
				<div class="myjsp-pages"><?php if ($this->lists['category']) {echo $this->lists['category']; }?></div>
			</td>
			<td>
				<div class="myjsp-pages"><?php echo $this->lists['type'];?></div>
			</td>
			<td>
				<div class="myjsp-pages"><?php echo $this->lists['logged'];?></div>
			</td>
			<td class="limit-back">
				<?php echo $this->pagination->getLimitBox(); ?>
			</td>
		</tr>
	</table>

	<table class="adminlist table table-striped">
		<thead>
			<tr>
				<th class="title">
					<?php if ($this->association == '' && $this->layout != 'modal') {
						echo JHtml::_('grid.checkall');
					} ?>
				</th>
				<th class="title title-pagename">
					<?php echo JHtml::_('grid.sort', JText::_('COM_MYJSPACE_TITLENAME'), 'a.title', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', JText::_('COM_MYJSPACE_LABELUSERNAME'), 'b.username', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
				<th class="title title-cdate">
					<?php echo JHtml::_('grid.sort', JText::_('COM_MYJSPACE_LABELCREATIONDATE'), 'a.create_date', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', JText::_('COM_MYJSPACE_LABELHITS'), 'a.hits', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', JText::_('COM_MYJSPACE_LABELSIZE'), 'size', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
<?php if ($this->language_filter > 0) { $colspan++; ?>
				<th class="title">
					<?php echo JHtml::_('grid.sort', JText::_('COM_MYJSPACE_LABELLANGUAGE'), 'a.language', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
<?php } ?>
				<th class="title">
					<?php echo JHtml::_('grid.sort', JText::_('COM_MYJSPACE_TITLEMODEEDIT'), 'a.blockedit', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
				<th class="title title-access">
					<?php echo JHtml::_('grid.sort', JText::_('COM_MYJSPACE_TITLEMODEVIEW'), 'a.blockview', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', JText::_('COM_MYJSPACE_LABELID'), 'a.id', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
			</tr>
		</thead>

		<tbody>
		<?php
			$link_pre = "components/com_myjspace/images/";
			$k = 0;
			for ($i = 0, $n = count($this->items); $i < $n; $i++) {
				$row = $this->items[$i];
				if (!$row->username)
					$row->block = 1	; // Look coherence :-)
				$userblock_class = $row->block? 'icon-unpublish' : 'icon-publish';
				$userblock_alt = $row->block? JText::_('COM_MYJSPACE_ADMIN_USER_BLOCKED') : JText::_('COM_MYJSPACE_ADMIN_USER_ENABLE');

				if ($row->blockedit == 1)
					$blockedit_img = 'active_su.png';
				else if ($row->blockedit == 2)
					$blockedit_img = 'active_no.png';
				else
					$blockedit_img = 'active_yes.png';

				if ($row->blockedit == 0)
					$blockedit_alt = JText::_('COM_MYJSPACE_TITLEMODEEDIT0');
				else if ($row->blockedit == 1)
					$blockedit_alt = JText::_('COM_MYJSPACE_TITLEMODEEDIT1');
				else
					$blockedit_alt = JText::_('COM_MYJSPACE_TITLEMODEEDIT2');

				if ($row->blockview == 1)
					$blockview_img = "publish_g.png";
				else if ($row->blockview == 0)
					$blockview_img = "publish_r.png";
				else if ($row->blockview == 2)
					$blockview_img = "publish_y.png";
				else
					$blockview_img = "publish_x.png";

				$blockview_alt = BS_UtilAcl::get_assetgroup_label($row->blockview);

				$id_link = 'index.php?option=com_users&task=user.edit&id='.$row->userid;
				$page_link = 'index.php?option=com_myjspace&view=page&id='.$row->id;
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
				<?php if ($this->association == '' && $this->layout != 'modal') { ?>
					<?php echo JHtml::_('grid.id', $i, $row->id); ?>
				<?php } ?>
				</td>
				<td>
					<div>
						<?php if ($this->association == '' && $this->layout != 'modal') { ?>
						<a href="<?php echo JRoute::_($page_link, false); ?>" title="<?php echo JText::_('COM_MYJSPACE_ADMIN_EDIT').' '.$row->title; ?>"><span class="fa fa-pen-square mr-2" aria-hidden="true"></span><?php echo $row->title; ?></a>
						<?php } else  if ($this->layout == 'modal') { ?>
						<a class="pointer" href="#" onclick="window.parent.<?php echo $this->modal_fct; ?>('<?php echo $row->id; ?>', '<?php echo $row->title; ?>', '<?php echo $row->language; ?>');"><span class="fa fa-pen-square mr-2" aria-hidden="true"><?php echo $row->title; ?></a>
						<?php
						}
						if ($this->share_page != 0 && $row->access > 0) {
							echo ' <img src="'.$link_pre.'share_nb.png" class="icon12" alt="access" title="'.JText::_('COM_MYJSPACE_TITLESHAREEDIT').JText::_('COM_MYJSPACE_2POINTS').BS_UtilAcl::get_assetgroup_label($row->access, true).'" />';
						}
						?>
					</div>
					<?php if ($row->catid) { ?>
					<div class="small">
						<?php
						if ($this->association == '' && $this->layout != 'modal') {
							echo JText::_('COM_MYJSPACE_CATEGORY').': <a class="hasTooltip" href="'.JRoute::_('index.php?option=com_categories&amp;task=category.edit&amp;id='.$row->catid.'&amp;extension=com_myjspace').'" title="'.JText::_('COM_MYJSPACE_CATEGORY_EDIT').'">'.BSHelperUser::getCategoryLabel($row->catid).'</a>';
						} else {
							echo JText::_('COM_MYJSPACE_CATEGORY').': '.BSHelperUser::getCategoryLabel($row->catid);
						}
						?>
					</div>
					<?php } ?>
				</td>
				<td>
<?php
					if ($row->username) { ?>
						<span class="<?php echo $userblock_class;?>" title="<?php echo $userblock_alt; ?>"></span>
						<span class="small">
<?php
						if ($this->association == '' && $this->layout != 'modal')
							echo '<a href="'.JRoute::_($id_link, false).'" title="'.$row->name.'">'.$row->username.'</a>';
						else
							echo '<span title="'.$row->name.'">'.$row->username.'</span>';
					} else {
						echo '<span class="icon-unpublish" title="'.JText::_('COM_MYJSPACE_ADMIN_USER_UNKNOWN').' ('.$row->userid.')">';
					}
?>
						</span>
				</td>
				<td><span class="small title-cdate2"><span class="icon-calendar" aria-hidden="true"></span><?php echo date(JText::_('COM_MYJSPACE_DATE_FORMAT'), strtotime($row->create_date)); ?></span></td>
				<td><div class="fg-1"><span class="badge badge-info"><?php echo $row->hits; ?></span></div></td>
				<td><div class="fg-1"><span class="badge badge-secondary myjsp-size"><?php echo $this->convertSize($row->size); ?></span></div></td>
<?php
				if ($this->language_filter > 0) {
					if ($this->language_filter == 2 && $row->association > 0)
						$img_association = '<img src="'.$link_pre.'association.png" alt="association" title="'.JText::_('COM_MYJSPACE_LABELASSOCIATION').'" />';
					else
						$img_association = '';

					if (isset($this->languages[$row->language])) {
						$sef = $this->languages[$row->language]->sef;
						$aff_language = JHtml::_('image', 'mod_languages/'.$sef.'.gif', $sef, array('title' => $this->languages[$row->language]->title), true);
					} else {
						$aff_language = $row->language;
						if ($aff_language == '*')
							$aff_language = JText::_('COM_MYJSPACE_LANGUAGE_ALL');
					}

					echo '<td>'.$aff_language.$img_association.'</td>';
				}
?>
				<td class="myjsp-icon">
					<img src="<?php echo $link_pre.$blockedit_img;?>" alt="blockedit" title="<?php echo $blockedit_alt; ?>" />
				</td>
				<td class="myjsp-icon">
					<?php echo $this->is_img_lock($row->publish_up, $row->publish_down); ?>
					<img src="<?php echo $link_pre.$blockview_img;?>" alt="blockview" title="<?php echo $blockview_alt; ?>" />
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
			</tr>
			<?php
				$k = 1 - $k;
				}
			?>
		</tbody>

		<tfoot>
			<tr>
				<td colspan="<?php echo $colspan; ?>">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="option" value="com_myjspace" />
	<input type="hidden" name="view" value="pages" />
	<input type="hidden" name="task" value="" />
<?php if ($this->tmpl)  { ?>
	<input type="hidden" name="tmpl" value="<?php echo $this->tmpl; ?>" />
<?php } ?>
	<input type="hidden" name="layout" value="<?php echo $this->layout; ?>" />
	<input type="hidden" name="association" value="<?php echo $this->association; ?>" />
	<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_('form.token'); ?>

	</fieldset>
	</div>
</div>
</form>
