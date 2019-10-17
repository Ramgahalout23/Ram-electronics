<?php
/**
 * @package Portfolio Gallery
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 * */
defined('_JEXEC') or die;
?> 
<div class="tabsBack">
    <div class="back"><fieldset class="form-horizontal">
        <legend class="legend"><?php echo JText::_('First Shown Block'); ?></legend>
        <?php foreach ($this->form->getFieldset('FAQToggleUpDown') as $field): ?>
            <div class="control-group">
                <?php
                echo "<div class='control-label'>" . $field->label . "</div>";
                echo "<div class='controls'>" . $field->input . "</div>";
                ?>
            </div>
<?php endforeach; ?>
        </fieldset></div>
      <div class="back"><fieldset class="form-horizontal">
        <legend class="legend"><?php echo JText::_('Link Button'); ?></legend>
            <?php foreach ($this->form->getFieldset('FAQToggleUpDownLinkButton') as $field): ?>
            <div class="control-group">
                <?php
                echo "<div class='control-label'>" . $field->label . "</div>";
                echo "<div class='controls'>" . $field->input . "</div>";
                ?>
            </div>
<?php endforeach; ?>
          </fieldset></div>
    
    
     <div class="back"><fieldset class="form-horizontal">
        <legend class="legend"><?php echo JText::_('Sorting Style'); ?></legend>
            <?php foreach ($this->form->getFieldset('FAQToggleUpDownSortingStyle') as $field): ?>
            <div class="control-group">
                <?php
                echo "<div class='control-label'>" . $field->label . "</div>";
                echo "<div class='controls'>" . $field->input . "</div>";
                ?>
            </div>
<?php endforeach; ?>
          </fieldset></div>
    
    
    
     <div class="back"><fieldset class="form-horizontal">
        <legend class="legend"><?php echo JText::_('Category Style'); ?></legend>
            <?php foreach ($this->form->getFieldset('FAQToggleUpDownCategoryStyle') as $field): ?>
            <div class="control-group">
                <?php
                echo "<div class='control-label'>" . $field->label . "</div>";
                echo "<div class='controls'>" . $field->input . "</div>";
                ?>
            </div>
<?php endforeach; ?>
          </fieldset></div>
    
    
      <div class="back"><fieldset class="form-horizontal">
        <legend class="legend"><?php echo JText::_('Title'); ?></legend>

            <?php foreach ($this->form->getFieldset('FAQToggleUpDownTitle') as $field): ?>
            <div class="control-group">
            <?php
            echo "<div class='control-label'>" . $field->label . "</div>";
            echo "<div class='controls'>" . $field->input . "</div>";
            ?>
            </div>
<?php endforeach; ?>

          </fieldset> </div>
      <div class="back"><fieldset class="form-horizontal">
        <legend class="legend"><?php echo JText::_('Description'); ?></legend>

            <?php foreach ($this->form->getFieldset('FAQToggleUpDownDescription') as $field): ?>
            <div class="control-group">
            <?php
            echo "<div class='control-label'>" . $field->label . "</div>";
            echo "<div class='controls'>" . $field->input . "</div>";
            ?>
            </div>
<?php endforeach; ?>
          </fieldset>

</div>
    <div class="back"> <fieldset class="form-horizontal">
            <legend class="legend" ><?php echo JText::_('Pagination Styles'); ?></legend>
            <?php foreach ($this->form->getFieldset('ContentPaginationPosition4') as $field): ?>
                <div class="control-group  options-block-title-wrapper first">
                    <?php
                    echo "<div class='control-label'>" . $field->label . "</div>";
                    echo "<div class='controls'>" . $field->input . "</div>";
                    ?>
                </div>
            <?php endforeach; ?>
        </fieldset></div>

    <div class="back"> <fieldset class="form-horizontal">
            <legend class="legend" ><?php echo JText::_('Load More Styles'); ?></legend>
            <?php foreach ($this->form->getFieldset('ContentLoadMore4') as $field): ?>
                <div class="control-group  options-block-title-wrapper first">
                    <?php
                    echo "<div class='control-label'>" . $field->label . "</div>";
                    echo "<div class='controls'>" . $field->input . "</div>";
                    ?>
                </div>
            <?php endforeach; ?>
        </fieldset></div>
</div>