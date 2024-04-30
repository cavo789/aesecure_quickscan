<?php
// No direct access.
defined('_JEXEC') or die;

// Before description position
if(isset($this -> beforeDescription) && count($this -> beforeDescription)) {
    $pt_before   = $this -> beforeDescription;

    echo JHtml::_('bootstrap.startAccordion', 'beforeDescriptionOptions',
        array('active' => 'beforeDescriptionCollapse0', 'parent' => true));
    ?>
    <fieldset>
        <?php foreach ($pt_before as $i => $pt) {
            echo JHtml::_('bootstrap.addSlide', 'beforeDescriptionOptions',
                $pt -> title, 'beforeDescriptionCollapse'.$i);
            echo isset($pt -> html)?$pt -> html:'';
            echo JHtml::_('bootstrap.endSlide');
        } ?>
    </fieldset>
    <?php
    echo JHtml::_('bootstrap.endAccordion');
}
?>