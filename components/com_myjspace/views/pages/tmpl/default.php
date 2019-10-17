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
JHtml::_('script', 'components/com_myjspace/assets/myjsp-search.js');

if ($this->search_image_effect_list == 1) {
	JHtml::_('stylesheet', 'components/com_myjspace/assets/lytebox/lytebox.css');
	JHtml::_('script', 'components/com_myjspace/assets/lytebox/lytebox.js');
}

if ($this->search_page_title)
	echo '<h2>'.$this->search_page_title.'</h2>';
?>
<div class="myjspace" itemscope itemtype="http://schema.org/Article">
	<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
<?php
$categories_count = count($this->categories);

if ($this->aff_select) { // Search selector to be printed
?>
	<fieldset>
	<legend><?php echo JText::_('COM_MYJSPACE_TITLEPAGES').' - '.$this->user_title->name; ?><?php if ($this->url_rss_feed != '') {echo '&nbsp;&nbsp;'.$this->url_rss_feed;} ?></legend>

	<div class="input-group">
		<input type="text" name="svalue" id="svalue" class="form-control" value="<?php echo $this->svalue; ?>" placeholder="<?php echo JText::_('COM_MYJSPACE_FILTER'); ?>" />
		&nbsp;<button id="bouton" name="bouton" onclick="this.form.submit()" class="btn btn-primary mjp-config" title="<?php echo JText::_('COM_MYJSPACE_SEARCH'); ?>"><span class="icon-search"></span> <?php echo JText::_('COM_MYJSPACE_SEARCH'); ?></button>
<?php
	if ($this->search_pagination) {
		echo '&nbsp;<span>'.$this->pagination->getLimitBox().'</span>';
	}
?>
	</div>
	</fieldset>
<?php
} else {
	if ($this->search_pagination) {
		echo '<fieldset class="adminform front">';
		echo '<legend>'.JText::_('COM_MYJSPACE_TITLESEARCH').'&nbsp;&nbsp;'.$this->url_rss_feed.'</legend>';
		echo '<div>'.$this->pagination->getLimitBox().'</div>';
		echo '</fieldset>';
	} else if ($this->url_rss_feed != '') {
		echo '<div class="mjp-rss-feed">'.$this->url_rss_feed.'</div>';
	}
}?>
	<fieldset>
	<div class="myjspace-result-search">
<?php
	// Table list
	echo "<table class=\"mjsp-search-tab adminlist table-striped noborder width100\">\n";
	$separ_l = '<td>';
	$separ_lt = '<th class="title">';
	$separ_l_img = "<td class=\"mjsp-search-img\">";
	$separ_r = '</td>';
	$separ_rt = '</th>';

	$nb = count($this->result);
	for ($i = 0; $i < $nb ; $i++) {
		// Set & transform content to be displayed
		$aff = $this->transform_fields($this, $i, $this->separ, $this->lview);

		if ($this->search_labels == 1 && $i == 0) {
			echo "<tr>\n";
			if ($this->association == '')
				echo $separ_lt.' '.$separ_rt;

			if ($aff->image)
				echo $separ_lt.' '.$separ_rt;
			else if ($this->search_aff_add & 64)
				echo $separ_lt.' '.$separ_rt;

				if ($aff->pagename && !$this->search_sort_use)
					echo $separ_lt.JText::_('COM_MYJSPACE_TITLENAME').$separ_rt;
				else if ($aff->pagename && $this->search_sort_use)
					echo $separ_lt.JHtml::_('grid.sort', JText::_('COM_MYJSPACE_TITLENAME'), 'pagename', @$this->lists['order_Dir'], @$this->lists['order']).$separ_rt;

				if ($aff->username && !$this->search_sort_use)
					echo $separ_lt.JText::_('COM_MYJSPACE_LABELUSERNAME').$separ_rt;
				else if ($aff->username && $this->search_sort_use)
					echo $separ_lt.JHtml::_('grid.sort', JText::_('COM_MYJSPACE_LABELUSERNAME'), 'uname', @$this->lists['order_Dir'], @$this->lists['order']).$separ_rt;

				if ($aff->category !== false && !$this->search_sort_use && $categories_count > 0)
					echo $separ_lt.JText::_('COM_MYJSPACE_LABELCATEGORY').$separ_rt;
				else if ($aff->category !== false && $this->search_sort_use && $categories_count > 0)
					echo $separ_lt.JHtml::_('grid.sort', JText::_('COM_MYJSPACE_LABELCATEGORY'), 'ctitle', @$this->lists['order_Dir'], @$this->lists['order']).$separ_rt;

				if ($aff->description && !$this->search_sort_use)
					echo $separ_lt.JText::_('COM_MYJSPACE_LABELMETAKEY').$separ_rt;
				else if ($aff->description && $this->search_sort_use)
					echo $separ_lt.JHtml::_('grid.sort', JText::_('COM_MYJSPACE_LABELMETAKEY'), 'metakey', @$this->lists['order_Dir'], @$this->lists['order']).$separ_rt;

				if ($aff->create_date && !$this->search_sort_use)
					echo $separ_lt.JText::_('COM_MYJSPACE_LABELCREATIONDATE').$separ_rt;
				else if ($aff->create_date && $this->search_sort_use)
					echo $separ_lt.JHtml::_('grid.sort', JText::_('COM_MYJSPACE_LABELCREATIONDATE'), 'create_date', @$this->lists['order_Dir'], @$this->lists['order']).$separ_rt;

				if ($aff->update_date && !$this->search_sort_use)
					echo $separ_lt.JText::_('COM_MYJSPACE_LABELLASTUPDATEDATE').$separ_rt;
				else if ($aff->update_date && $this->search_sort_use)
					echo $separ_lt.JHtml::_('grid.sort', JText::_('COM_MYJSPACE_LABELLASTUPDATEDATE'), 'last_update_date', @$this->lists['order_Dir'], @$this->lists['order']).$separ_rt;

				if ($aff->hits !== false && !$this->search_sort_use)
					echo $separ_lt.JText::_('COM_MYJSPACE_LABELHITS').$separ_rt;
				else if ($aff->hits !== false && $this->search_sort_use)
					echo $separ_lt.JHtml::_('grid.sort', JText::_('COM_MYJSPACE_LABELHITS'), 'hits', @$this->lists['order_Dir'], @$this->lists['order']).$separ_rt;

				if ($aff->content && !$this->search_sort_use)
					echo $separ_lt.JText::_('COM_MYJSPACE_LABELCONTENT').$separ_rt;
				else if ($aff->content && $this->search_sort_use)
					echo $separ_lt.JHtml::_('grid.sort', JText::_('COM_MYJSPACE_LABELCONTENT'), 'content', @$this->lists['order_Dir'], @$this->lists['order']).$separ_rt;

				if ($aff->size && !$this->search_sort_use)
					echo $separ_lt.JText::_('COM_MYJSPACE_LABELSIZE').$separ_rt;
				else if ($aff->size && $this->search_sort_use)
					echo $separ_lt.JHtml::_('grid.sort', JText::_('COM_MYJSPACE_LABELSIZE'), 'size', @$this->lists['order_Dir'], @$this->lists['order']).$separ_rt;

				if ($aff->language && !$this->search_sort_use)
					echo $separ_lt.JText::_('COM_MYJSPACE_LABELLANGUAGE').$separ_rt;
				else if ($aff->language && $this->search_sort_use)
					echo $separ_lt.JHtml::_('grid.sort', JText::_('COM_MYJSPACE_LABELLANGUAGE'), 'language', @$this->lists['order_Dir'], @$this->lists['order']).$separ_rt;

				if ($aff->blockview && !$this->search_sort_use)
					echo $separ_lt.JText::_('COM_MYJSPACE_TITLEMODEVIEW').$separ_rt;
				else if ($aff->blockview && $this->search_sort_use)
					echo $separ_lt.JHtml::_('grid.sort', JText::_('COM_MYJSPACE_TITLEMODEVIEW'), 'blockview', @$this->lists['order_Dir'], @$this->lists['order']).$separ_rt;

				if ($aff->jtag)
					echo $separ_lt.'<a href="#">'.JText::_('COM_MYJSPACE_LABELJTAG').'</a>'.$separ_rt;

			echo "\n</tr>\n";
		}
		$n = $i%2;
		echo "<tr class=\"row$n\">\n";

		if ($this->association == '')
			echo $separ_l_img.$aff->select.$separ_r;
		if ($aff->image)
			echo $separ_l_img.$aff->image.$separ_r;
		else if ($this->search_aff_add & 64)
			echo $separ_l_img.' '.$separ_r;
		if ($aff->pagename) {
			if ($this->association == '') {
				if (($this->current_user->id == $aff->userid && in_array($this->lview, array('config', 'delete', 'edit', 'see'))) || ($aff->share_page && in_array($this->lview, array('edit', 'see'))))
					echo $separ_l.'<a href="'.$aff->page_url.'">'.$aff->title.'</a>'.$aff->share_page.$separ_r;
				else
					echo $separ_l.$aff->title.$aff->share_page.$separ_r;
			} else { // Modal page for association
				if ($aff->language && $aff->title) {
					echo $separ_l;
?>
				<a class="pointer" href="#" onclick="window.parent.jSelectMyjsp_jform_modal('<?php echo $aff->id ?>', '<?php echo $aff->title; ?>', '<?php echo $aff->lang; ?>');"><?php echo $aff->title; ?></a>
<?php
					echo $separ_r;
				}
			}
		}
		if ($aff->username)
			echo $separ_l.$aff->username.$separ_r;
		if ($aff->category !== false && $categories_count > 0)
			echo $separ_l.$aff->category.$separ_r;
		if ($aff->description)
			echo $separ_l.$aff->description.$separ_r;
		if ($aff->create_date)
			echo $separ_l.$aff->create_date.$separ_r;
		if ($aff->update_date)
			echo $separ_l.$aff->update_date.$separ_r;
		if ($aff->hits !== false)
			echo $separ_l.$aff->hits.$separ_r;
		if ($aff->content)
			echo $separ_l.$aff->content.$separ_r;
		if ($aff->size)
			echo $separ_l.$aff->size.$separ_r;
		if ($aff->language)
			echo $separ_l.$aff->language.$separ_r;
		if ($aff->blockview)
			echo $separ_l.$aff->blockview.$separ_r;
		if ($aff->jtag === true)
			echo $separ_l.$separ_r;
		else if ($aff->jtag)
			echo $separ_l.$aff->jtag.$separ_r;

		echo "\n</tr>\n";
	}

	echo "</table>\n";
	echo "<br />\n";
?>
<?php
if ($this->association == '' && (($this->uid > 0 && $this->uid == $this->user->id) || ($this->uid == 0 && $this->user->id > 0))) {
?>
	<span class="mjp-all-button">
<?php
		if (JFactory::getUser()->authorise('user.config', 'com_myjspace')) {
?>
			<button name="bt_config" class="btn btn-secondary mjp-config" onclick="if (document.querySelector('input[name=cid]:checked') === null){alert('<?php echo JText::_( 'COM_MYJSPACE_PAGELIST_ALERT'); ?>');}else{document.getElementById('view').value='config';this.form.submit();}" >
				<span class="icon-equalizer"></span>
				<?php echo JText::_('COM_MYJSPACE_TITLECONFIG1') ?>
			</button>
<?php	}
		if (JFactory::getUser()->authorise('user.edit', 'com_myjspace')) {
?>
			<button name="bt_edit" class="btn btn-secondary mjp-config" onclick="if (document.querySelector('input[name=cid]:checked') === null){alert('<?php echo JText::_( 'COM_MYJSPACE_PAGELIST_ALERT'); ?>');}else{document.getElementById('view').value='edit';this.form.submit();}" >
				<span class="icon-edit"></span>
				<?php echo JText::_('COM_MYJSPACE_TITLEEDIT1') ?>
			</button>
<?php	}
		if (JFactory::getUser()->authorise('user.see', 'com_myjspace')) {
?>
			<button name="bt_see" class="btn btn-secondary mjp-config" onclick="if (document.querySelector('input[name=cid]:checked') === null){alert('<?php echo JText::_( 'COM_MYJSPACE_PAGELIST_ALERT'); ?>');}else{document.getElementById('view').value='see';this.form.submit();}" >
				<span class="icon-zoom-in"></span>
				<?php echo JText::_('COM_MYJSPACE_TITLESEE1') ?>
			</button>
<?php	}
		if (JFactory::getUser()->authorise('user.delete', 'com_myjspace')) {
?>
			<button name="bt_delete" class="btn btn-secondary mjp-config" onclick="if (document.querySelector('input[name=cid]:checked') === null){alert('<?php echo JText::_( 'COM_MYJSPACE_PAGELIST_ALERT'); ?>');}else{document.getElementById('view').value='delete';this.form.submit();}" >
				<span class="icon-trash"></span>
				<?php echo JText::_('COM_MYJSPACE_DELETE') ?>
			</button>
<?php	}
		if ($this->total < $this->nb_max_page && !$this->user_limit_page_this_cat_reached && (($this->new_page_rview == 'edit' && JFactory::getUser()->authorise('user.edit', 'com_myjspace')) || ($this->new_page_rview == 'config' && JFactory::getUser()->authorise('user.config', 'com_myjspace')))) {
?>
			<button name="bt_new" class="btn btn-secondary mjp-config" onclick="document.getElementById('view').value='<?php echo $this->new_page_rview; ?>';document.getElementById('id').value='-1';this.form.submit();" >
				<span class="icon-new"></span>
				<?php echo JText::_('COM_MYJSPACE_CREATEPAGE') ?>
			</button>
<?php }
		if ($this->total < $this->nb_max_page && (($this->copy_page_rview == 'edit' && JFactory::getUser()->authorise('user.edit', 'com_myjspace')) || ($this->copy_page_rview == 'config' && JFactory::getUser()->authorise('user.config', 'com_myjspace')))) {
?>
			<button name="bt_copy" class="btn btn-secondary mjp-config" onclick="if (document.querySelector('input[name=cid]:checked') === null){alert('<?php echo JText::_( 'COM_MYJSPACE_PAGELIST_ALERT'); ?>');}else{document.getElementById('view').value='<?php echo $this->copy_page_rview; ?>';document.getElementById('id').value='-1';this.form.submit();}" >
				<span class="icon-copy"></span>
				<?php echo JText::_('COM_MYJSPACE_COPY') ?>
			</button>
<?php	} ?>
	</span>
<?php } ?>

	</div>
	</fieldset>

	<?php if ($this->search_pagination) { ?>
	<div><?php echo $this->pagination->getListFooter(); ?></div>
	<?php } ?>

	<input type="hidden" name="option" value="com_myjspace" />
	<input type="hidden" name="view" id="view" value="pages" />
	<input type="hidden" name="id" id="id" value="0" />
<?php if ($this->tmpl) { ?>
	<input type="hidden" name="tmpl" value="<?php echo $this->tmpl; ?>" />
<?php } ?>
	<input type="hidden" name="layout" value="<?php echo $this->layout; ?>" />
	<input type="hidden" name="association" value="<?php echo $this->association; ?>" />
<?php if (!isset($catiddisplay)) { ?>
	<input type="hidden" name="catid" id="catid" value="<?php echo $this->catid; ?>" />
<?php } ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="uid" value="<?php echo $this->uid; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
