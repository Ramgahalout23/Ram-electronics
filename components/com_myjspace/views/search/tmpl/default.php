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

if ($this->separ <= 1)
	JHtml::_('script', 'media/system/js/core.js');

JHtml::_('stylesheet', 'components/com_myjspace/assets/myjspace.min.css');
JHtml::_('script', 'components/com_myjspace/assets/myjsp-search.js');

if ($this->search_image_effect_list == 1) {
	JHtml::_('stylesheet', 'components/com_myjspace/assets/lytebox/lytebox.css');
	JHtml::_('script', 'components/com_myjspace/assets/lytebox/lytebox.js');
}

if ($this->separ > 1) {
	JHtml::_('stylesheet', 'components/com_myjspace/assets/myjsp_blocks.min.css');
	JFactory::getDocument()->addStyleDeclaration($this->style_str);
	echo $this->chaine_ie; // IE Style specific
}

if ($this->search_page_title)
	echo '<h2>'.$this->search_page_title.'</h2>';
?>

<div class="myjspace" itemscope itemtype="http://schema.org/Article">
	<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm" class="myjspace-search">
<?php

$categories_count = count($this->categories);

if ($this->aff_select) { // Search selector to be printed

$my_class = (version_compare(JVERSION, '3.99.99', 'lt')) ? 'input-group' : 'form-inline';
?>
	<fieldset id="myjsp-search-panel">

	<legend><?php echo JText::_('COM_MYJSPACE_TITLESEARCH'); ?><?php if ($this->url_rss_feed != '') {echo '&nbsp;&nbsp;'.$this->url_rss_feed;} ?></legend>

	<div class="<?php echo $my_class; ?>">
		<label class="myjsp-label"><input type="text" name="svalue" id="svalue" class="form-control" value="<?php echo $this->svalue; ?>" placeholder="<?php echo JText::_('COM_MYJSPACE_FILTER'); ?>" /></label>
		<button id="bouton" name="bouton" onclick="this.form.submit()" class="btn btn-primary mjp-config" title="<?php echo JText::_('COM_MYJSPACE_SEARCH'); ?>"><span class="icon-search"></span> <?php echo JText::_('COM_MYJSPACE_SEARCH'); ?></button>
		&nbsp;
		<span class="myjsp-search-check">
		<?php if (isset($this->aff_search_asso['name']) && (isset($this->aff_search_asso['content']) || isset($this->aff_search_asso['description']))) { ?>
			<label class="myjsp-label"><input type="checkbox" name="check_search[]" <?php if (isset($this->check_search_asso['name'])) echo 'checked="checked"'; ?> value="name" /> <?php echo JText::_('COM_MYJSPACE_SEARCHSEARCHPNAME'); ?></label>
		<?php } ?>

		<?php if (isset($this->aff_search_asso['content'])) { ?>
			<label class="myjsp-label"><input type="checkbox" name="check_search[]" <?php if (isset($this->check_search_asso['content'])) echo 'checked="checked"'; ?> value="content" /> <?php echo JText::_('COM_MYJSPACE_SEARCHSEARCHCONTENT'); ?></label>
		<?php } ?>

		<?php if (isset($this->aff_search_asso['description'])) { ?>
			<label class="myjsp-label"><input type="checkbox" name="check_search[]" <?php if (isset($this->check_search_asso['description'])) echo 'checked="checked"'; ?> value="description" /> <?php echo JText::_('COM_MYJSPACE_SEARCHSEARCHDESCRIPTION'); ?></label>
		<?php } ?>
		</span>
<?php
if (isset($this->aff_search_asso['sort'])) { // Sort order
?>
	&nbsp;<select name="sort" id="myjsp-sort" class="myjsp-sort custom-select" onchange="mjsp_trie(this.value);return false;">
<?php
	foreach ($this->sort_list as $key => $value) {
			echo '<option value="'.$key.'">&nbsp;'.$value.'&nbsp;</option>';
	}
	echo "</select>\n";
}

if ($categories_count && isset($this->aff_search_asso['category'])) { // Category
	$catiddisplay = true;
?>
	&nbsp;<select name="catid" id="catid" class="myjsp-cat custom-select" onchange="this.form.submit();" >
		<option value="-1">- <?php echo JText::_('COM_MYJSPACE_LABELCATEGORY'); ?> -</option>
<?php
		for ($i = 0; $i < $categories_count; $i++) {
			if ($this->categories[$i]['value'] == $this->catid)
				echo '<option value="'.$this->categories[$i]['value'].'" selected="selected">'.str_repeat('-', max(0, $this->categories[$i]['level']-1)).' '.$this->categories[$i]['text']."</option>\n";
			else
				echo '<option value="'.$this->categories[$i]['value'].'">'.str_repeat('-', max(0, $this->categories[$i]['level']-1)).' '.$this->categories[$i]['text']."</option>\n";
		}
?>
		</select>
<?php
	}

	if (isset($this->aff_search_asso['jtag']) && $this->form) {	// Joomla! tags
		echo '&nbsp;<label>'.JText::_('COM_MYJSPACE_SEARCHSEARCHJTAG').'<span class="myjsp-jtag">'.$this->form->getInput('tags', 'metadata', $this->jtag).'</span></label>';
	}

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
		echo ' <span>'.$this->pagination->getLimitBox().'</span>';
		echo '</fieldset>';
	} else if ($this->url_rss_feed != '') {
		echo '<div class="mjp-rss-feed">'.$this->url_rss_feed.'</div>';
	}
}?>
	<fieldset>
	<div class="myjspace-result-search">
<?php
	if ($this->separ == 0) { // tab list
		echo "<table class=\"mjsp-search-tab adminlist table-striped noborder width100\">\n";
		$separ_l = '<td>';
		$separ_lt = '<th class="title">';
		$separ_l_img = "<td class=\"mjsp-search-img\">";
		$separ_r = '</td>';
		$separ_rt = '</th>';
	} else if ($this->separ == 1) { // raw
		$separ_l = '<span class="mjsp-search-row-field">';
		$separ_lt = $separ_l;
		$separ_l_img = '<span class="mjsp-search-row-field">';
		$separ_r = '</span> ';
		$separ_rt = $separ_r;
	} else if ($this->separ == 2) { // blocks
		echo "<div class=\"myjsp-blocks\" id=\"myjsp-blocks\">\n";
		$separ_l = ' ';
		$separ_l_img = '';
		$separ_r = "\n";
		$this->search_image_effect_list = 0;
	} else if ($this->separ == 3) { // Wall
		echo "<div class=\"myjsp-blocks2\">\n";
		$separ_l = ' ';
		$separ_r = "\n";
	} else {
		$separ_l = '';
		$separ_l_img = '';
		$separ_r = ' ';
	}

	$nb = count($this->result);

	for ($i = 0; $i < $nb ; ++$i) {
		// Set & transform content to be displayed
		$aff = $this->transform_fields($this, $i, $this->separ);

		if ($this->separ == 0 || $this->separ == 1) {
			if ($this->separ <= 1 && $this->search_labels == 1 && $i == 0) {
				if ($this->separ == 0)
					echo "<tr>\n";
				else
					echo "<div>\n";

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

				if ($this->separ == 0)
					echo "\n</tr>\n";
				else
					echo "\n</div>\n";
			}
			if ($this->separ == 0) {
				$n = $i%2;
				echo "<tr class=\"row$n\">\n";
			}
			if ($this->separ == 1)
				echo '<div class="mjsp_search_row">';

			if ($aff->image)
				echo $separ_l_img.$aff->image.$separ_r;
			else if ($this->search_aff_add & 64)
				echo $separ_l_img.' '.$separ_r;

			if ($aff->pagename)
				echo $separ_l.'<a href="'.$aff->page_url.'">'.$aff->title.'</a>'.$aff->share_page.$separ_r;
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

		} else if ($this->separ == 2) {
			echo "<div class=\"icon\">\n";

			$title = "\n".$aff->title."\n";
			if ($aff->username)
				$title .= JText::_('COM_MYJSPACE_LABELUSERNAME').JText::_('COM_MYJSPACE_2POINTS').$aff->username."\n";
			if ($aff->category !== false && $categories_count > 0)
				$title .= JText::_('COM_MYJSPACE_LABELCATEGORY').JText::_('COM_MYJSPACE_2POINTS').$aff->category."\n";
			if ($aff->create_date)
				$title .= JText::_('COM_MYJSPACE_LABELCREATIONDATE').JText::_('COM_MYJSPACE_2POINTS').$aff->create_date."\n";
			if ($aff->update_date)
				$title .= JText::_('COM_MYJSPACE_LABELLASTUPDATEDATE').JText::_('COM_MYJSPACE_2POINTS').$aff->update_date."\n";
			if($aff->share_page)
				$title .= JText::_('COM_MYJSPACE_TITLESHAREEDIT').JText::_('COM_MYJSPACE_2POINTS').JText::_('COM_MYJSPACE_YES')."\n";
			if ($aff->hits)
				$title .= JText::_('COM_MYJSPACE_LABELHITS').JText::_('COM_MYJSPACE_2POINTS').$aff->hits."\n";
			if ($aff->size)
				$title .= JText::_('COM_MYJSPACE_LABELSIZE').JText::_('COM_MYJSPACE_2POINTS').$aff->size."\n";
			if ($aff->language)
				$title .= JText::_('COM_MYJSPACE_LABELLANGUAGE').JText::_('COM_MYJSPACE_2POINTS').$aff->language."\n";
			if ($aff->blockview_alt)
				$title .= JText::_('COM_MYJSPACE_TITLEMODEVIEW').JText::_('COM_MYJSPACE_2POINTS').$aff->blockview_alt."\n";
			if ($aff->jtag)
				$title .= JText::_('COM_MYJSPACE_LABELJTAG').JText::_('COM_MYJSPACE_2POINTS').$aff->jtag."\n";
			if ($aff->content)
				$title .= "\n".$aff->content."\n";
			$title .= "\n";

			echo "<a href=\"".$aff->page_url."\" title=\"".$title."\" >";
			echo '<span class="myjsp-pagename">'.$aff->title.'</span>';
			echo '<div class="myjsp-spanimg">'.$aff->image.'</div>';
			if (($aff->description) && $aff->description != ' ')
				echo '<span class="myjsp-desc">'.$aff->description.'</span>';
			echo '<span>'.$aff->content.'</span>';
			echo "</a></div>\n";
		} else if ($this->separ == 3) {
			echo "<span class=\"grow pic\">\n";
			echo "<a href=\"".$aff->page_url."\">";

			$aff->title .= "\n";
			if ($aff->username)
				$aff->title .= JText::_('COM_MYJSPACE_LABELUSERNAME').JText::_('COM_MYJSPACE_2POINTS').$aff->username."\n";
			if ($aff->category !== false && $categories_count > 0)
				$aff->title .= JText::_('COM_MYJSPACE_LABELCATEGORY').JText::_('COM_MYJSPACE_2POINTS').$aff->category."\n";
			if ($aff->create_date)
				$aff->title .= JText::_('COM_MYJSPACE_LABELCREATIONDATE').JText::_('COM_MYJSPACE_2POINTS').$aff->create_date."\n";
			if ($aff->update_date)
				$aff->title .= JText::_('COM_MYJSPACE_LABELLASTUPDATEDATE').JText::_('COM_MYJSPACE_2POINTS').$aff->update_date."\n";
			if ($aff->hits)
				$aff->title .= JText::_('COM_MYJSPACE_LABELHITS').JText::_('COM_MYJSPACE_2POINTS').$aff->hits."\n";
			if (($aff->description) && $aff->description != ' ')
				$aff->title .= JText::_('COM_MYJSPACE_LABELMETAKEY').JText::_('COM_MYJSPACE_2POINTS').$aff->description."\n";
			if ($aff->size)
				$aff->title .= JText::_('COM_MYJSPACE_LABELSIZE').JText::_('COM_MYJSPACE_2POINTS').$aff->size."\n";
			if ($aff->language)
				$aff->title .= JText::_('COM_MYJSPACE_LABELLANGUAGE').JText::_('COM_MYJSPACE_2POINTS').$aff->language."\n";
			if ($aff->blockview_alt)
				$aff->title .= JText::_('COM_MYJSPACE_TITLEMODEVIEW').JText::_('COM_MYJSPACE_2POINTS').$aff->blockview_alt."\n";
			if ($aff->jtag)
				$aff->title .= JText::_('COM_MYJSPACE_LABELJTAG').JText::_('COM_MYJSPACE_2POINTS').$aff->jtag."\n";
			if ($aff->content)
				$aff->title .= "\n".$aff->content."\n";

			$aff->image = BS_Util::exist_image_html($aff->local_folder, JPATH_SITE, 'img-preview', 0, $aff->title, 'preview.jpg', $this->search_image_default, $this->search_image_type, $aff->text, $this->search_image_video, $aff->page_url);
			echo $aff->image;
			echo "</a></span>\n";
		}

		if ($this->separ == 0)
			echo "\n</tr>\n";
		else if ($this->separ == 1)
			echo "\n</div>\n";
	}

	if ($this->separ == 0)
		echo "</table>\n";
	else if ($this->separ >= 2)
		echo "</div>\n";
?>
	</div>
	</fieldset>

	<?php if ($this->search_pagination) { ?>
	<div><?php //echo $this->pagination->getLimitBox(); ?></div>
	<div><?php echo $this->pagination->getListFooter(); ?></div>
	<?php } ?>

<?php if (!isset($catiddisplay)) { ?>
	<input type="hidden" name="catid" id="catid" value="<?php echo $this->catid; ?>" />
<?php } ?>
	<input type="hidden" name="option" value="com_myjspace" />
	<input type="hidden" name="view" id="view" value="search" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
