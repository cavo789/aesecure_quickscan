<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access.
defined('_JEXEC') or die;

if($fieldGroups = $this -> extraFields){
?>

<div class="form-horizontal">
    <?php
    echo JHtml::_('bootstrap.startAccordion', 'fieldGroupAdditionalAccordion',
        array('active' => 'fieldGroupCollapse0', 'parent' => true));?>
    <?php
        foreach($fieldGroups as $i => $group) {
            ?>
            <?php
            if ($fields = $group->fields) {
                if(count($fields)) {
                    // Start accordion
                    echo JHtml::_('bootstrap.addSlide', 'fieldGroupAdditionalAccordion', ucwords($group -> name), 'fieldGroupCollapse' . $i);
                    foreach ($fields as $field) {
                        ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->getLabel(); ?></div>
                            <div class="controls"><?php echo $field->getInput(); ?></div>
                        </div>
                    <?php }
                    echo JHtml::_('bootstrap.endSlide');
                }
            }
        }
    echo JHtml::_('bootstrap.endAccordion');
    ?>
</div>
<?php
}else{
?>
    <div id="system-message-container"><div id="system-message">
            <div class="alert alert-warning">
                <h4 class="alert-heading"><?php echo JText::_('WARNING');?></h4>
                <div>
                    <p><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_FIELD_GROUP_DESC');?></p>
                </div>
            </div>
        </div>
    </div>
<?php
}