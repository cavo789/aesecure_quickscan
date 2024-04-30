<?php
// No direct access.
defined('_JEXEC') or die;

// Before description position
if(isset($this -> beforeDescription) && count($this -> beforeDescription)) {
    $pt_before   = $this -> beforeDescription;

    echo JHtml::_('bootstrap.startAccordion', 'tp-ac-edit-add_ons__before-desc',
        array('active' => 'tp-ac-edit-add_ons__before-desc__'.$pt_before[0] -> group.'-'
            .$pt_before[0] -> addon, 'parent' => true));
    ?>
    <fieldset>
        <?php foreach ($pt_before as $i => $pt) {
            echo JHtml::_('bootstrap.addSlide', 'tp-ac-edit-add_ons__before-desc',
                $pt -> title, 'tp-ac-edit-add_ons__before-desc__'.$pt -> group.'-'.$pt -> addon);
            echo isset($pt -> html)?$pt -> html:'';
            echo JHtml::_('bootstrap.endSlide');
        } ?>
    </fieldset>
    <?php
    echo JHtml::_('bootstrap.endAccordion');
}
?>