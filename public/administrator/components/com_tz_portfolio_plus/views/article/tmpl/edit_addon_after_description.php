<?php
// No direct access.
defined('_JEXEC') or die;

// After description position
if(isset($this -> afterDescription) && count($this -> afterDescription)) {
    $pt_after   = $this -> afterDescription;

?>
    <div class="mt-px-18">
        <?php
        echo JHtml::_('bootstrap.startAccordion', 'afterDescriptionOptions',
            array('active' => 'afterDescriptionCollapse0', 'parent' => true));
        ?>
<!--        <fieldset>-->
            <?php foreach ($pt_after as $i => $pt) {
                echo JHtml::_('bootstrap.addSlide', 'afterDescriptionOptions',
                    $pt -> title, 'afterDescriptionCollapse'.$i);
                echo isset($pt -> html)?$pt -> html:'';
                echo JHtml::_('bootstrap.endSlide');
            } ?>
<!--        </fieldset>-->
        <?php echo JHtml::_('bootstrap.endAccordion'); ?>
    </div>
<?php
}
?>