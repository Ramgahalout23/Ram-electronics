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
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
<div class="myjspace <?php if (version_compare(JVERSION, '3.99.99', 'lt')) {echo '';} else {echo 'row';} ?>">
<?php if (isset($this->sidebar)) { ?>
	<div id="j-sidebar-container" class="col-md-2">
		<?php echo $this->sidebar; ?>
	</div>
<?php } ?>
	<div id="j-main-container" class="j-main-container col-md-12">
		<fieldset class="adminform">
	<legend class="title-back"><?php echo JText::_('COM_MYJSPACE_TOOLS');?></legend>

	<input type="hidden" name="option" value="com_myjspace" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>

	<table class="table-striped myjspbtn300l">
<?php
	// Default tools for page folders
	if ($this->link_folder) {
?>
		<tr>
			<td>
				<a class="btn myjspbtn300l" title="<?php echo JText::_('COM_MYJSPACE_ADMIN_DELETE_EMPTY_PAGES'); ?>" href="index.php?option=com_myjspace&amp;task=adm_delete_empty_pages&amp;<?php echo JSession::getFormToken();?>=1">
					<i class="icon-purge"></i>
					<span><?php echo JText::_('COM_MYJSPACE_ADMIN_DELETE_EMPTY_PAGES'); ?></span>
				</a>
			</td>
		</tr>
		<tr>
			<td>
				<a class="btn myjspbtn300l" title="<?php echo JText::_('COM_MYJSPACE_ADMIN_DELETE_FOLDER'); ?>" href="index.php?option=com_myjspace&amp;task=adm_delete_folder&amp;<?php echo JSession::getFormToken();?>=1">
					<i class="icon-purge"></i>
					<span><?php echo JText::_('COM_MYJSPACE_ADMIN_DELETE_FOLDER'); ?></span>
				</a>
			</td>
		</tr>
		<tr>
			<td>
				<a class="btn btn-primary myjspbtn300l" title="<?php echo JText::_('COM_MYJSPACE_ADMIN_CREATE_FOLDER'); ?>" href="index.php?option=com_myjspace&amp;task=adm_create_folder&amp;<?php echo JSession::getFormToken();?>=1">
					<i class="icon-loop"></i>
					<span><?php echo JText::_('COM_MYJSPACE_ADMIN_CREATE_FOLDER'); ?></span>
				</a>
			</td>
		</tr>
		<tr>
			<td>
				<a class="btn myjspbtn300l" title="<?php echo JText::_('COM_MYJSPACE_ADMIN_CHECK_FOLDER'); ?>" href="index.php?option=com_myjspace&amp;task=adm_check_folder&amp;<?php echo JSession::getFormToken();?>=1">
					<i class="icon-checkin"></i>
					<span><?php echo JText::_('COM_MYJSPACE_ADMIN_CHECK_FOLDER'); ?></span>
				</a>
			</td>
		</tr>
	</table>
	<p> </p>
<?php
	}

	if ($this->POtherTools) {
		foreach ($this->POtherTools as $id0 => $action0) {
			echo '<hr /><table class="table-striped myjspbtn300l">';
			foreach ($this->POtherTools[$id0] as $id => $action) {
				if (isset($action['label']) && $action['label']) {
?>
		<tr>
			<td>
<?php
			if (isset($action['action']) && $action['action'] == 'none') {
				echo $action['label']."\n";
			} else {
?>
				<a class="btn myjspbtn300l <?php if (isset($action['class'])) echo $action['class']; ?>" title="<?php echo $action['label']; ?>" href="index.php?option=com_myjspace&amp;task=other_tools&amp;id=<?php echo $id; ?>&amp;<?php echo JSession::getFormToken();?>=1">
<?php
				if (isset($action['icon']) && ($action['icon']))
					$icon = $action['icon'];
				else
					$icon = 'icon-apply';
				echo '<i class="'.$icon.'"></i>';
?>
					<span><?php echo $action['label']; ?></span>
				</a>
<?php		} ?>
			</td>
		</tr>
<?php
				}
			}
			echo '</table>';
		}
	}
?>
	</fieldset>

</div>
</div>
</form>
