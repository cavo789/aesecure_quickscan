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
}else{
    JHtml::_('formbehavior.chosen', 'select[multiple]');
}

$form   = $this -> form;
?>
<form name="adminForm" method="post" class="form-validate tpArticle" id="adminForm"
      action="index.php?option=com_tz_portfolio_plus&view=group&layout=edit&id=<?php echo (int) $this -> item -> id?>">
    <?php echo JHtml::_('tzbootstrap.addrow');?>
        <div class="span8 col-md-8 form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('JDETAILS', true)); ?>
                <?php echo JHtml::_('tzbootstrap.addrow');?>
                    <div class="span6 col-md-6">
                        <?php echo $this -> form -> renderField('title');?>
                        <?php echo $this -> form -> renderField('field_ordering_type');?>
                    </div>
                    <div class="span6 col-md-6">
                        <?php echo $this -> form -> renderField('published');?>
                        <?php echo $this -> form -> renderField('access');?>
                    </div>
                <?php echo JHtml::_('tzbootstrap.endrow');?>

                <div class="form-vertical form-no-margin">
                <?php echo $this -> form -> renderField('description');?>
                </div>
            <?php echo JHtml::_('bootstrap.endTab'); ?>

            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'categories_assignment', JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES_ASSIGNMENT', true)); ?>
            <?php echo $form->getInput('categories_assignment'); ?>
            <?php echo JHtml::_('bootstrap.endTab'); ?>

            <?php if ($this->canDo->get('core.admin')) : ?>
                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('JCONFIG_PERMISSIONS_LABEL')); ?>
                <?php echo $this->form->getInput('rules'); ?>
                <?php echo JHtml::_('bootstrap.endTab'); ?>
            <?php endif; ?>

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
        </div>

        <div class="span4 col-md-4 form-vertical">
        <?php echo JHtml::_('bootstrap.startAccordion', 'groupOptions', array('active' => 'collapse0'
        , 'parent' => true));?>
            <?php echo JHtml::_('bootstrap.addSlide', 'groupOptions', JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'collapse0'); ?>

                <?php echo $this -> form -> renderField('created');?>
                <?php echo $this -> form -> renderField('created_by');?>
                <?php if ($this->item && $this->item->modified_by){ ?>
                    <?php echo $this -> form -> renderField('modified_by');?>
                    <?php echo $this -> form -> renderField('modified');?>
                <?php } ?>
                <?php echo $this -> form -> renderField('id');?>

            <?php echo JHtml::_('bootstrap.endSlide');?>
        <?php echo JHtml::_('bootstrap.endAccordion');?>
        </div>
    <?php echo JHtml::_('tzbootstrap.endrow');?>

    <input type="hidden" value="" name="task">
    <?php echo JHTML::_('form.token');?>

</form>