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
            <legend class="legend"><?php echo JText::_('Element Styles'); ?></legend>
            <?php foreach ($this->form->getFieldset('FullHeightBlocks') as $field): ?>
                    <div class="control-group">
                              <?php echo "<div class='control-label'>".$field->label."</div>";
                                    echo "<div class='controls'>".$field->input."</div>";
                                ?>
                     </div>
                <?php endforeach; ?>
            
        </fieldset></div>

    <div class="back">
      <fieldset class="form-horizontal">
          <legend class="legend"><?php echo JText::_('Description'); ?></legend>
           <?php foreach ($this->form->getFieldset('FullHeightBlocksDescription') as $field): ?>
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
          <legend class="legend"><?php echo JText::_('Sorting Styles'); ?></legend>
           <?php foreach ($this->form->getFieldset('sortingStylesFullHeight') as $field): ?>
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
          <legend class="legend"><?php echo JText::_('Category Styles'); ?></legend>
           <?php foreach ($this->form->getFieldset('categoryStylesFullHeight') as $field): ?>
               <div class="control-group">
                  <?php echo "<div class='control-label'>".$field->label."</div>";
                        echo "<div class='controls'>".$field->input."</div>";
                    ?>
                 </div>
            <?php endforeach; ?>
      </fieldset>
    </div>
   
    
    <div class="back"><fieldset class="form-horizontal">
            <legend class="legend"><?php echo JText::_('Title'); ?></legend>
         
<?php foreach ($this->form->getFieldset('FullHeightBlocksTitle') as $field): ?>
                  <div class="control-group">
                              <?php echo "<div class='control-label'>".$field->label."</div>";
                                    echo "<div class='controls'>".$field->input."</div>";
                                ?>
                     </div>
                    <?php endforeach; ?>
          
        </fieldset></div>
    <div class="back"><fieldset class="form-horizontal">
            <legend class="legend"><?php echo JText::_('Thumbnails'); ?></legend>
                <?php foreach ($this->form->getFieldset('FullHeightBlocksThumbnails') as $field): ?>
                   <div class="control-group">
                              <?php echo "<div class='control-label'>".$field->label."</div>";
                                    echo "<div class='controls'>".$field->input."</div>";
                                ?>
                     </div>
                <?php endforeach; ?>
        </fieldset></div>
    <div class="back"><fieldset class="form-horizontal">
            <legend class="legend"><?php echo JText::_('Link Button'); ?></legend>
            <?php foreach ($this->form->getFieldset('FullHeightBlocksLink') as $field): ?>
         <div class="control-group">
                              <?php echo "<div class='control-label'>".$field->label."</div>";
                                    echo "<div class='controls'>".$field->input."</div>";
                                ?>
                     </div>
<?php endforeach; ?>
        </fieldset></div>
    <div class="back"> <fieldset class="form-horizontal">
            <legend class="legend" ><?php echo JText::_('Pagination Styles'); ?></legend>
            <?php foreach ($this->form->getFieldset('ContentPaginationPosition1') as $field): ?>
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
            <?php foreach ($this->form->getFieldset('ContentLoadMore1') as $field): ?>
                <div class="control-group  options-block-title-wrapper first">
                    <?php
                    echo "<div class='control-label'>" . $field->label . "</div>";
                    echo "<div class='controls'>" . $field->input . "</div>";
                    ?>
                </div>
            <?php endforeach; ?>
        </fieldset></div>


    </div> 
