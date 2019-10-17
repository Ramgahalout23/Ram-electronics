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

?>
<div class="myjspace <?php if (version_compare(JVERSION, '3.99.99', 'lt')) {echo '';} else {echo 'row';} ?>">
<?php if (isset($this->sidebar)) { ?>
	<div id="j-sidebar-container" class="col-md-2">
		<?php echo $this->sidebar; ?>
	</div>
<?php } ?>
	<div id="j-main-container" class="j-main-container col-md-12">
		<fieldset class="adminform">
			<legend class="title-back"><i class="icon-loop"></i> <?php echo JText::_('COM_MYJSPACE_ADMIN_INFO_0'); ?></legend>
			<?php $this->aff_tabinfo($this->data_help); ?>
		</fieldset>

		<fieldset class="adminform">
			<legend class="title-back"><i class="icon-loop"></i> <?php echo JText::_('COM_MYJSPACE_ADMIN_INFO_OTHER'); ?></legend>
			<?php $this->aff_tabinfo($this->data_others); ?>
		</fieldset>

		<fieldset class="adminform">
			<legend class="title-back"><i class="icon-loop"></i> <?php echo JText::_('COM_MYJSPACE_ADMIN_REPORT'); ?></legend>
			<p><a href="#" class="btn btn-small btn-primary" onclick="document.getElementById('report').select()"><i class="icon icon-signup"></i> <?php echo JText::_('COM_MYJSPACE_ADMIN_REPORT_SELECT'); ?></a></p>
			<p> </p>
			<textarea id="report"><?php echo htmlspecialchars($this->report, ENT_COMPAT, 'UTF-8'); ?></textarea>
		</fieldset>
	</div>
</div>
