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

//no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.formvalidator');
JHtml::_('bootstrap.tooltip');
if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
    JHtml::_('behavior.tabstate');
    JHtml::_('formbehavior.chosen', 'select');
}
else{
    JHtml::_('formbehavior.chosen', 'select[multiple]');
}

$form   = $this -> form;
?>

<form name="adminForm" method="post" class="form-validate tpArticle" id="adminForm"
      action="index.php?option=com_tz_portfolio_plus&view=tag&layout=edit&id=<?php echo $this -> item -> id?>">

    <div class="form-horizontal">
        <fieldset class="adminform">

            <?php
            $article_assign = $form -> getField('articles_assignment');
            ?>

            <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('JDETAILS', true)); ?>
                    <?php echo $this -> form -> renderField('title');?>
                    <?php echo $this -> form -> renderField('alias');?>
                    <?php echo $this -> form -> renderField('published');?>
                    <?php echo $this -> form -> renderField('id');?>
                    <?php echo $this -> form -> renderField('description');?>
                <?php echo JHtml::_('bootstrap.endtab'); ?>

                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'articles_assignment',
                    JText::_($article_assign -> getAttribute('label'), true)); ?>
                    <?php echo $form->getInput('articles_assignment'); ?>
                <?php echo JHtml::_('bootstrap.endtab'); ?>

            <?php echo JHtml::_('bootstrap.endTabSet'); ?>
        </fieldset>

    </div>

    <input type="hidden" value="" name="task">
    <?php echo JHTML::_('form.token');?>
</form>