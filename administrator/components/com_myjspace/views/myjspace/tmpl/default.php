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
			<legend class="title-back"><?php echo JText::_('COM_MYJSPACE_TITLE');?></legend>
			<div><img src="<?php echo JURI::root(); ?>administrator/components/com_myjspace/images/myjspace.png" alt="BS MyJSpace"/></div>
			<div><p class="small mt-1"><?php echo $this->version_information; ?></p></div>
		</fieldset>

<?php if ($this->version_new) { ?>
		<fieldset class="adminform">
			<legend class="title-back-s"><?php echo JText::_('COM_MYJSPACE_NEWVERSION');?></legend>
			<div>
				<span class="myjsp-warning"><?php echo JText::_('COM_MYJSPACE_NEWVERSION'); ?> </span>
				<a class="btn btn-primary myjsp-statis" href="<?php echo JRoute::_('index.php?option=com_installer&view=update', false); ?>"> <?php echo $this->version_new; ?> </a>
			</div>
		</fieldset>
<?php } ?>

		<fieldset class="adminform">
			<legend class="title-back-s"><?php echo JText::_('COM_MYJSPACE_STATISTICS');?></legend>

			<table class="adminlist table">
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_NBPAGESTOTAL'); ?>
					</td>
					<td>
						<a class="btn btn-info btn-small myjsp-statis" href="<?php echo JRoute::_('index.php?option=com_myjspace&view=pages', false); ?>"><?php echo $this->nb_pages_total; ?></a>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('COM_MYJSPACE_NBDISTINCTUSERS'); ?>
					</td>
					<td>
						<a class="btn btn-info btn-small myjsp-statis" href="<?php echo JRoute::_('index.php?option=com_users', false); ?>"><?php echo $this->nb_distinct_users; ?></a>
					</td>
				</tr>
			</table>
		</fieldset>

	</div>
</div>
