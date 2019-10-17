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

if ($this->add_lightbox) {
	JHtml::_('stylesheet', 'components/com_myjspace/assets/lytebox/lytebox.css');
	JHtml::_('script', 'components/com_myjspace/assets/lytebox/lytebox.js');
}
?>

<div class="myjspace-see" <?php if ($this->css_background) echo 'style="'.$this->css_background.'"'; ?> itemscope itemtype="http://schema.org/Article">
<?php
	if ($this->jtag && $this->access_ok)
		echo $this->jtag;

	if ($this->edit_icon)
		echo $this->edit_icon;

	if ($this->allow_plugin > 1)
		echo $this->contenu->event->afterDisplayTitle.$this->contenu->event->beforeDisplayContent;
	echo $this->contenu->toc.$this->contenu->text;
	if ($this->allow_plugin > 1)
		echo $this->contenu->event->afterDisplayContent;
?>
</div>
