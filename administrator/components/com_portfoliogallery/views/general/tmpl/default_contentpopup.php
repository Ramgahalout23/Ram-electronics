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
        <legend class="legend" ><?php echo JText::_('Element Styles'); ?></legend>
        <?php foreach ($this->form->getFieldset('GalleryContentPopupStyle') as $field): ?>
        <div class="control-group  options-block-title-wrapper first">
        <?php echo "<div class='control-label'>".$field->label."</div>";
              echo "<div class='controls'>".$field->input."</div>";
        ?>
        </div>
<?php endforeach; ?>
       </fieldset>
        </div>
            
        <div class="back">
        <fieldset class="form-horizontal">
        <legend class="legend" ><?php echo JText::_('Sorting Styles'); ?></legend>
        <?php foreach ($this->form->getFieldset('GalleryContentPopupSortingStyle') as $field): ?>
        <div class="control-group  options-block-title-wrapper first">
        <?php echo "<div class='control-label'>".$field->label."</div>";
              echo "<div class='controls'>".$field->input."</div>";
        ?>
        </div>
<?php endforeach; ?>
       </fieldset>
        </div>
              <div class="back">
        <fieldset class="form-horizontal">
        <legend class="legend" ><?php echo JText::_('Category Styles'); ?></legend>
        <?php foreach ($this->form->getFieldset('GalleryContentPopupCategoryStyle') as $field): ?>
        <div class="control-group  options-block-title-wrapper first">
        <?php echo "<div class='control-label'>".$field->label."</div>";
              echo "<div class='controls'>".$field->input."</div>";
        ?>
        </div>
<?php endforeach; ?>
       </fieldset>
        </div>
       <div class="back"><fieldset class="form-horizontal">
       <legend class="legend" ><?php echo JText::_('Popup Styles'); ?></legend>
       <?php foreach ($this->form->getFieldset('PopupStyles') as $field): ?>
       <div class="control-group ">
        <?php echo "<div class='control-label'>".$field->label."</div>";
              echo "<div class='controls'>".$field->input."</div>";
        ?>
       </div>
<?php endforeach; ?>
       </fieldset></div>
        <div class="back"><fieldset class="form-horizontal">
        <legend class="legend"><?php echo JText::_('Popup Description'); ?></legend>
        <?php foreach ($this->form->getFieldset('PopupDescription') as $field): ?>
        <div class="control-group">
        <?php echo "<div class='control-label'>".$field->label."</div>";
              echo "<div class='controls'>".$field->input."</div>";
        ?>
        </div>
        <?php endforeach; ?>
        </fieldset></div>
        <div class="back"><fieldset class="form-horizontal">
        <legend class="legend"><?php echo JText::_('Element Title'); ?></legend>
        <?php foreach ($this->form->getFieldset('PopupDescriptionTitle') as $field): ?>
        <div class="control-group">
        <?php echo "<div class='control-label'>".$field->label."</div>";
              echo "<div class='controls'>".$field->input."</div>";
        ?>
        </div>
        <?php endforeach; ?>
        </fieldset></div>
        <div class="back"><fieldset class="form-horizontal">
                <legend class="legend"><?php echo JText::_('Element Link Button'); ?></legend>
                <?php foreach ($this->form->getFieldset('PopupDescriptionLinkButton') as $field): ?>
                    <div class="control-group">
                        <?php echo "<div class='control-label'>".$field->label."</div>";
                        echo "<div class='controls'>".$field->input."</div>";
                        ?>
                    </div>
                <?php endforeach; ?>
            </fieldset></div>
            <div class="back"><fieldset class="form-horizontal">
        <legend class="legend"><?php echo JText::_('Popup Title'); ?></legend>
        <?php foreach ($this->form->getFieldset('PopupDescriptionPopupTitle') as $field): ?>
        <div class="control-group">
        <?php echo "<div class='control-label'>".$field->label."</div>";
              echo "<div class='controls'>".$field->input."</div>";
        ?>
        </div>
        <?php endforeach; ?>
                </fieldset></div>
        <div class="back"><fieldset class="form-horizontal">
            <legend class="legend"><?php echo JText::_('Popup Thumbnails'); ?></legend>
          <?php foreach ($this->form->getFieldset('PopupDescriptionPopupThumbnails') as $field): ?>
                    <div class="control-group">
                              <?php echo "<div class='control-label'>".$field->label."</div>";
                                    echo "<div class='controls'>".$field->input."</div>";
                                ?>
                    </div>
    <?php endforeach; ?>
            </fieldset></div>
            <div class="back"><fieldset class="form-horizontal">
            <legend class="legend"><?php echo JText::_('Popup Link Button'); ?></legend>
            <?php foreach ($this->form->getFieldset('PopupDescriptionPopupLinkButton') as $field): ?>
                   <div class="control-group">
                              <?php echo "<div class='control-label'>".$field->label."</div>";
                                    echo "<div class='controls'>".$field->input."</div>";
                                ?>
                    </div>
<?php endforeach; ?>
                </fieldset></div>
            <div class="back"> <fieldset class="form-horizontal">
                    <legend class="legend" ><?php echo JText::_('Pagination Styles'); ?></legend>
                    <?php foreach ($this->form->getFieldset('ContentPaginationPosition2') as $field): ?>
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
                    <?php foreach ($this->form->getFieldset('ContentLoadMore2') as $field): ?>
                        <div class="control-group  options-block-title-wrapper first">
                            <?php
                            echo "<div class='control-label'>" . $field->label . "</div>";
                            echo "<div class='controls'>" . $field->input . "</div>";
                            ?>
                        </div>
                    <?php endforeach; ?>
                </fieldset></div>
        </div>