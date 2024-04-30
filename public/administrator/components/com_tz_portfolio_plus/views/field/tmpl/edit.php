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

use Joomla\CMS\Factory;

$form   = $this -> form;
$doc    = Factory::getApplication() -> getDocument();

JHtml::_('behavior.formvalidator');
JHtml::_('bootstrap.tooltip');
if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
    JHtml::_('formbehavior.chosen', 'select');
}else{
    JHtml::_('formbehavior.chosen', 'select[multiple]');
}
?>
<form name="adminForm" method="post" id="adminForm" class="tpArticle"
      action="index.php?option=com_tz_portfolio_plus&view=field&layout=edit&id=<?php echo $this -> item -> id?>">

    <?php echo JHtml::_('tzbootstrap.addrow');?>
        <?php // Begin Content ?>
        <div class="span8 col-md-8 form-horizontal">
            <fieldset class="adminform">
            <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('JDETAILS', true)); ?>

                <?php echo JHtml::_('tzbootstrap.addrow');?>
                    <div class="span6 col-md-6">
                        <?php echo $this -> form -> renderField('title');?>
                        <?php echo $this -> form -> renderField('groupid');?>
                        <?php echo $this -> form -> renderField('published');?>
                        <?php echo $this -> form -> renderField('type');?>
                        <?php echo $this -> form -> renderField('images');?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $form -> getLabel('value');?></div>
                            <div class="controls">
                                <div id="<?php echo $form -> getField('value') -> id;?>">
                                    <?php
                                    if($fieldValue = $form->getInput('value')) {
                                        echo $fieldValue;
                                    }else{
                                        echo JText::_('COM_TZ_PORTFOLIO_PLUS_NO_VALUE');
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="span6 col-md-6">
                        <?php echo $this -> form -> renderField('list_view');?>
                        <?php echo $this -> form -> renderField('detail_view');?>
                        <?php echo $this -> form -> renderField('advanced_search');?>
                        <?php echo $this -> form -> renderField('access');?>
                    </div>
                <?php echo JHtml::_('tzbootstrap.endrow');?>

                <?php echo $this -> form -> renderField('description');?>

                <?php echo JHtml::_('bootstrap.endTab'); ?>

                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>

                    <?php echo $this -> form -> renderField('created');?>
                    <?php echo $this -> form -> renderField('created_by');?>

                <?php if ($this->item && $this->item->modified_by){ ?>
                    <?php echo $this -> form -> renderField('modified_by');?>
                    <?php echo $this -> form -> renderField('modified');?>
                <?php } ?>

                    <?php echo $this -> form -> renderField('id');?>
                <?php echo JHtml::_('bootstrap.endTab'); ?>

                <?php if ($this->canDo->get('core.admin')) : ?>
                    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('JCONFIG_PERMISSIONS_LABEL')); ?>
                    <?php echo $this->form->getInput('rules'); ?>
                    <?php echo JHtml::_('bootstrap.endTab'); ?>
                <?php endif; ?>

            <?php echo JHtml::_('bootstrap.endTabSet'); ?>
            </fieldset>
        </div>
        <?php // End Content ?>
        <?php // Begin Sidebar ?>
        <div class="span4 col-md-4">
            <div class="form-vertical">
            <?php echo JHtml::_('bootstrap.startAccordion', 'fieldOptions', array('active' => 'collapse0'
            , 'parent' => true));?>
                <?php
                // Display parameter's params from xml file
                $fieldSets = $this->form->getFieldsets('params');
                $i = 0;
                ?>
                <?php foreach ($fieldSets as $name => $fieldSet) :
                    $fields = $this->form->getFieldset($name);
                    if(count($fields)):
                ?>

                    <?php
                    // Start accordion parameters
                    echo JHtml::_('bootstrap.addSlide', 'fieldOptions',
                        JText::_(!empty($fieldSet->label)?$fieldSet -> label:'COM_TZ_PORTFOLIO_PLUS_FIELDSET_'
                            .strtoupper($name).'_LABEL'), 'collapse' . $i++);
                    ?>

                    <?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
                        <p class="tip"><?php echo $this->escape(JText::_($fieldSet->description));?></p>
                    <?php endif; ?>

                    <?php foreach ($fields as $field) {
                        echo $field->renderField();
                    } ?>

                    <?php echo JHtml::_('bootstrap.endSlide');?>
                <?php
                    endif;
                endforeach;
                ?>

            <?php echo JHtml::_('bootstrap.endAccordion');?>
            </div>

            <div class="form-horizontal">
                <?php echo JHtml::_('bootstrap.startAccordion', 'previewOptions', array('active' => 'preview_fieldset'));?>
                    <?php
                    // Start accordion preview
                    $preview    = $this->form -> getFieldset('preview_fieldset');
                    echo JHtml::_('bootstrap.addSlide', 'previewOptions', JText::_('JGLOBAL_PREVIEW'), 'preview_fieldset');
                    ?>
                    <?php echo $this -> form -> renderField('preview');?>
                    <?php echo JHtml::_('bootstrap.endSlide');?>
                <?php echo JHtml::_('bootstrap.endAccordion');?>
            </div>
        </div>
        <?php // End Sidebar ?>
    <?php echo JHtml::_('tzbootstrap.endrow');?>
    <input type="hidden" value="com_tz_portfolio_plus" name="option">
    <input type="hidden" value="" name="task">
    <?php echo JHTML::_('form.token');?>
</form>