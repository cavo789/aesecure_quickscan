<?php
// No direct access.
defined('_JEXEC') or die;

// After description position
if(isset($this -> afterDescription) && count($this -> afterDescription)) {
    $pt_after   = $this -> afterDescription;

?>
    <div class="mt-px-18">
        <?php
        echo JHtml::_('bootstrap.startAccordion', 'tp-ac-edit-add_ons__after-desc',
            array('active' => 'tp-ac-edit-add_ons__after-desc__'.$pt_after[0] -> group
                .'-'.$pt_after[0] -> addon, 'parent' => true, 'onHidden' => 'function(){
                    alert("test");
                }'));
        ?>
            <?php foreach ($pt_after as $i => $pt) {
                echo JHtml::_('bootstrap.addSlide', 'tp-ac-edit-add_ons__after-desc',
                    $pt -> title, 'tp-ac-edit-add_ons__after-desc__'.$pt -> group.'-'.$pt -> addon);
                echo isset($pt -> html)?$pt -> html:'';
                echo JHtml::_('bootstrap.endSlide');
            } ?>
        <?php echo JHtml::_('bootstrap.endAccordion'); ?>
    </div>
<?php
}
?>