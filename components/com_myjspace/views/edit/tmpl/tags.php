<?php
/**
* @version $Id: tags.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2012 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

JHtml::_('stylesheet', 'components/com_myjspace/assets/myjspace.min.css');
JHtml::_('stylesheet', 'media/system/css/adminlist.css');

JHtml::_('script', 'components/com_myjspace/assets/myjsp-tags.js');

// Tags list
$tag_list = array('#userid', '#name', '#username', '#title', '#pagename', '#id', '#access', '#lastupdate', '#lastaccess', '#createdate', '#description', '#category', '#fileslist');
$tag_label = array(JText::_('COM_MYJSPACE_TAG_USERID'),
					JText::_('COM_MYJSPACE_TAG_NAME'),
					JText::_('COM_MYJSPACE_TAG_USERNAME'),
					JText::_('COM_MYJSPACE_TAG_TITLE'),
					JText::_('COM_MYJSPACE_TAG_PAGENAME'),
					JText::_('COM_MYJSPACE_TAG_ID'),
					JText::_('COM_MYJSPACE_TAG_ACCESS'),
					JText::_('COM_MYJSPACE_TAG_LASTUPDATE'),
					JText::_('COM_MYJSPACE_TAG_LASTACCESS'),
					JText::_('COM_MYJSPACE_TAG_CREATEDATE'),
					JText::_('COM_MYJSPACE_TAG_DESCRIPTION'),
					JText::_('COM_MYJSPACE_TAG_CATEGORY'),
					JText::_('COM_MYJSPACE_TAG_FILESLIST'));

if ($this->show_tags) {
	$tag_list[] = '#jtag';
	$tag_label[] = JText::_('COM_MYJSPACE_TAG_JTAG');
}

if ($this->share_page) {
	$tag_list[] = '#shareedit';
	$tag_label[] = JText::_('COM_MYJSPACE_TAG_SHAREEDIT');
	$tag_list[] = '#modifiedby';
	$tag_label[] = JText::_('COM_MYJSPACE_TAG_MODIFIEDBY');
}

if ($this->language_filter) {
	$tag_list[] = '#language';
	$tag_label[] = JText::_('COM_MYJSPACE_TAG_LANGUAGE');
}

if (@file_exists(JPATH_ROOT.'/components/com_comprofiler')) { // Add CB
	$tag_list[] = '#cbprofile';
	$tag_label[] = JText::_('COM_MYJSPACE_TAG_CBPROFILE');
}

if (@file_exists(JPATH_ROOT.'/components/com_community')) { // Add Jomsocial
	$tag_list[] = '#jomsocial-profile';
	$tag_label[] = JText::_('COM_MYJSPACE_TAG_JOOMSOCIALPROFILE');
	$tag_list[] = '#jomsocial-photos';
	$tag_label[] = JText::_('COM_MYJSPACE_TAG_JOOMSOCIALPHOTOS');
}

if ($this->allow_tag_myjsp_iframe == 1) { // Allow Tag myjsp iframe
	$tag_list[] = '{myjsp iframe URL}';
	$tag_label[] = JText::_('COM_MYJSPACE_TAG_MYJSP_IFRAME');
}

if ($this->allow_tag_myjsp_include == 1) { // Allow Tag myjsp include
	$tag_list[] = '{myjsp include URL}';
	$tag_label[] = JText::_('COM_MYJSPACE_TAG_MYJSP_INCLUDE');
}

?>
<div class="myjspace">
	<fieldset class="addtags front">
		<table class="adminlist">
<?php
	for ($i = 0; $i < count($tag_list); $i++) {
		echo '<tr class="row'.($i%2).'"><td>';
		echo '<a class="pointer" href="#" onclick="insertTags(\''.$tag_list[$i].'\', \''.$this->e_name.'\');">'.$tag_label[$i]."</a>\n";
		echo '</td></tr>';
	}
?>
		</table>
	</fieldset>
</div>
