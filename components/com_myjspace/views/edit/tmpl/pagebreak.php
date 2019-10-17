<?php
/**
* @version $Id: pagebreak.php $
* @version		3.0.0 26/08/2019
* @package		com_myjspace
* @author		Bernard Saulmé
* @copyright	Copyright (C) 2015 - 2019 Bernard Saulmé
* @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('_JEXEC') or die;

JHtml::_('style', 'components/com_myjspace/assets/myjsp-pagebreak.js');
?>
<div id="system-message-container">
	<div id="system-message">
	</div>
</div>
<div class="container-popup">
	<form>
		<div class="control-group">
			<div class="control-label">
				<label for="title"><?php echo JText::_('COM_MYJSPACE_PAGEBREAK_TITLE'); ?></label>
			</div>
			<div class="controls">
				<input type="text" id="title" name="title">
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<label for="alias"><?php echo JText::_('COM_MYJSPACE_PAGEBREAK_TOC'); ?></label>
			</div>
			<div class="controls">
				<input type="text" id="alt" name="alt">
			</div>
		</div>

		<button onclick="insertPagebreak('<?php echo $this->e_name; ?>');" class="btn btn-success"><?php echo JText::_('COM_MYJSPACE_PAGEBREAK_INSERT_BUTTON'); ?></button>
	</form>
</div>
