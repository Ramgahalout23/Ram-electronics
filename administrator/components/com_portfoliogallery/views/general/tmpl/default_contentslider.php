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
       <div class="back">
           <fieldset class="form-horizontal">
            <legend class="legend"><?php echo JText::_('Slider'); ?></legend>
                    <?php foreach ($this->form->getFieldset('ContentSlider') as $field): ?>
                     <div class="control-group">
                              <?php echo "<div class='control-label'>".$field->label."</div>";
                                    echo "<div class='controls'>".$field->input."</div>";
                                ?>
                     </div>
<?php endforeach; ?>
           </fieldset>
       </div>
       <div class="back">
           <fieldset class="form-horizontal">
            <legend class="legend"><?php echo JText::_('Title'); ?></legend>
            <?php foreach ($this->form->getFieldset('ContentSliderTitle') as $field): ?>
                    <div class="control-group">
                              <?php echo "<div class='control-label'>".$field->label."</div>";
                                    echo "<div class='controls'>".$field->input."</div>";
                                ?>
                    </div>
<?php endforeach; ?>
           </fieldset>
       </div>
       <div class="back">
           <fieldset class="form-horizontal">
            <legend class="legend"><?php echo JText::_('Link Button'); ?></legend>
           <?php foreach ($this->form->getFieldset('ContentSliderLinkButton') as $field): ?>
                     <div class="control-group">
                              <?php echo "<div class='control-label'>".$field->label."</div>";
                                    echo "<div class='controls'>".$field->input."</div>";
                                ?>
                     </div>
<?php endforeach; ?>
           </fieldset>
       </div>
    
        <div class="back">
            <fieldset class="form-horizontal">
            <legend class="legend"><?php echo JText::_('Images'); ?></legend>
                    <?php foreach ($this->form->getFieldset('ContentSliderImages') as $field): ?>
                   <div class="control-group">
                              <?php echo "<div class='control-label'>".$field->label."</div>";
                                    echo "<div class='controls'>".$field->input."</div>";
                                ?>
                    </div>
<?php endforeach; ?>
            </fieldset>
        </div>
         <div class="back">
            <fieldset class="form-horizontal">
            <legend class="legend"><?php echo JText::_('Description'); ?></legend>
             <?php foreach ($this->form->getFieldset('ContentSliderDescription') as $field): ?>
                    <div class="control-group">
                              <?php echo "<div class='control-label'>".$field->label."</div>";
                                    echo "<div class='controls'>".$field->input."</div>";
                                ?>
                    </div>
<?php endforeach; ?>
             </fieldset>
         </div>   
    </div>