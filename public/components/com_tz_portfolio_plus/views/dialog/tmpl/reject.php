<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2018 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

$modalId    = 'tpp-myarticle__reject';
$cids       = $this -> state -> get('article.id', array());

ob_start();
?>
<form action="<?php JRoute::_('index.php?option=com_tz_portfolio_plus'); ?>" method="post" name="adminForm" class="adminForm tpp-dialog__reject">
    <?php echo $this -> formReject -> renderField('message'); ?>
    <?php echo $this -> formReject -> getInput('id'); ?>

    <?php if(count($cids)){
        foreach($cids as $cid){
        ?>
            <input type="hidden" name="cid[]" value="<?php echo $cid; ?>"/>
    <?php }
    } ?>

    <input type="hidden" name="component" value="com_tz_portfolio_plus"/>
    <input type="hidden" name="task" value="reject.apply"/>
    <input type="hidden" name="return" value="<?php echo base64_encode(TZ_Portfolio_PlusHelperRoute::getMyArticlesRoute()); ?>"/>
</form>
<?php
$html   = ob_get_contents();
ob_end_clean();

echo JHtml::_(
    'bootstrap.renderModal',
    $modalId,
    array(
        'title'      => JText::_('COM_TZ_PORTFOLIO_PLUS_REJECT_ARTICLE'),
        'width'      => '100%',
        'height'     => '500px',
        'modalWidth' => '40',
        'closeButton' => true,
        'class'       => 'tpp-dialog-modal',
        'footer'      => '<a class="btn btn-default" data-dismiss="modal" href="javascript:void(0);">'
            . JText::_('JCANCEL') . '</a><a class="btn btn-primary" href="javascript:void(0);" data-submit-button>'
            . JText::_('COM_TZ_PORTFOLIO_PLUS_REJECT') . '</a>',
    ),
    $html
);
?>