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

if ($this->categories_page_title)
	echo '<h2>'.$this->categories_page_title.'</h2>';
?>
<div class="myjspace">
	<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
		<div><?php echo $this->pagination->getLimitBox(); ?></div>
<?php
		foreach ($this->categories as $i => $value) {
?>
		<div>
			<h3 class="page-header item-title">
			<?php
			if ($this->userid)
				$url = 'index.php?option=com_myjspace&view=pages&catid='.$value['catid'].'&userid='.$this->userid;
			else
				$url = 'index.php?option=com_myjspace&view=search&catid='.$value['catid'];
			?>
			<a href="<?php echo JRoute::_($url, false); ?>"> <?php echo str_repeat('-', max(0, $value['level']-1)).' '.$value['title']?></a>
<?php		if ($this->categorie_count) { ?>
			<span class="badge badge-info tip hasTooltip" title="<?php echo JText::_('COM_MYJSPACE_CATEGORIES_COUNT') ?>"><?php echo JText::_('COM_MYJSPACE_CATEGORIES_COUNT').$value['nb']; ?></span>
<?php		} ?>
			</h3>
		</div>
<?php
		}
?>
		<div><?php echo $this->pagination->getListFooter(); ?></div>

		<input type="hidden" name="option" value="com_myjspace" />
		<input type="hidden" name="view" value="categories" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
