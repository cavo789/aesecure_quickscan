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
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
    HTMLHelper::_('bootstrap.tooltip','.hasTooltip,[data-toggle=tooltip]');
    HTMLHelper::_('behavior.tabstate');
    HTMLHelper::_('formbehavior.chosen', '#menuOptions select');
}
else{

    $wa = $this->document->getWebAssetManager();
    $wa->useScript('core')
        ->useScript('bootstrap.popover');
    HTMLHelper::_('formbehavior.chosen', 'select[multiple]');
}

$doc    = Factory::getApplication() -> getDocument();
$doc -> addScriptDeclaration('
    jQuery(function($) {
        "use strict";
        $(\'input[type=radio][name="jform[params][use_single_layout_builder]"]\').change(function() {
            if (this.value == "1") {
                $("#layout_params").css("display", "block");
                $("#layout_disable").css("display", "none");
            }
            else {
                $("#layout_params").css("display", "none");
                $("#layout_disable").css("display", "block");
            }
        });
    });');

$jTab   = 'bootstrap';
if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){
    $jTab   = 'uitab';
}
?>
<form name="adminForm" method="post" id="template-form" class="tpArticle" enctype="multipart/form-data"
      action="index.php?option=com_tz_portfolio_plus&view=template_style&layout=edit&id=<?php echo $this -> item -> id?>">
    <div class="container-fluid" id="plazart_layout_builder">
        <div class="form-horizontal">
            <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
                <div class="span8 col-md-8 form-horizontal">
                    <fieldset class="adminForm">
                        <legend><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_DETAILS');?></legend>

                        <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
                        <div class="span6 col-md-6">
                            <?php echo $this -> form -> renderField('title'); ?>
                            <?php echo $this -> form -> renderField('home'); ?>
                        </div>
                        <div class="span6 col-md-6">
                            <?php echo $this -> form -> renderField('template'); ?>
                            <?php echo $this -> form -> renderField('id'); ?>
                        </div>
                        <?php echo HTMLHelper::_('tzbootstrap.endrow');?>

                        <?php if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){ ?>
                        <div class="main-card">
                        <?php } ?>
                        <?php echo HTMLHelper::_($jTab.'.startTabSet', 'myTab', array('active' => 'layout')); ?>

                            <?php
                            $use_single_lb  = true;
                            if(isset($this -> item -> params) && !empty($this -> item -> params) && isset($this -> item -> params -> use_single_layout_builder)) {
                                $use_single_lb = filter_var($this -> item -> params -> use_single_layout_builder, FILTER_VALIDATE_BOOLEAN);
                            }
                            ?>
                        <?php echo HTMLHelper::_($jTab.'.addTab', 'myTab', 'layout', JText::_('COM_TZ_PORTFOLIO_PLUS_LAYOUT', true)); ?>
                        <div id="layout_params" style="<?php echo $use_single_lb ? 'display: block;' : 'display: none;'; ?>">
                            <div id="plazart-admin-device">
                                <div class="pull-left float-left float-start plazart-admin-layout-header"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LAYOUTBUIDER_HEADER')?></div>
                                <div class="pull-right float-right float-end btn-group-sm mt-3">
                                    <button type="button" class="btn btn-outline-secondary tz-admin-dv-lg active" data-device="lg">
                                        <i class="tps tp-desktop"></i> <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LARGE');?>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary tz-admin-dv-md" data-device="md" data-toggle="tooltip"
                                            title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ONLY_BOOTSTRAP_3');?>">
                                        <i class="tps tp-laptop"></i> <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_MEDIUM');?>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary tz-admin-dv-sm" data-device="sm" data-toggle="tooltip"
                                            title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ONLY_BOOTSTRAP_3');?>">
                                        <i class="tps tp-tablet-alt"></i> <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SMALL');?>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary tz-admin-dv-xs" data-device="xs" data-toggle="tooltip"
                                            title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ONLY_BOOTSTRAP_3');?>">
                                        <i class="tps tp-mobile-alt"></i> <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_EXTRA_SMALL');?>
                                    </button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <?php echo $this -> loadTemplate('column_settings');?>
                            <?php echo $this -> loadTemplate('generator');?>
                        </div>
                        <div id="layout_disable" style="<?php echo intval($this->item->params->use_single_layout_builder) ? 'display: none;' : 'display: block;'; ?>">
                            <h3 style="text-align: center;"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LAYOUT_DISABLED');?></h3>
                        </div>
                        <?php echo HTMLHelper::_($jTab.'.endTab'); ?>

                        <?php echo HTMLHelper::_($jTab.'.addTab', 'myTab', 'menus_assignment', JText::_('COM_TZ_PORTFOLIO_PLUS_MENUS_ASSIGNMENT', true)); ?>
                        <?php echo $this -> loadTemplate('menu_assignment'); ?>
                        <?php echo HTMLHelper::_($jTab.'.endTab'); ?>

                        <?php echo HTMLHelper::_($jTab.'.addTab', 'myTab', 'categories_assignment', JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES_ASSIGNMENT', true)); ?>
                        <?php echo $this->form->getInput('categories_assignment'); ?>
                        <?php echo HTMLHelper::_($jTab.'.endTab'); ?>

                        <?php echo HTMLHelper::_($jTab.'.addTab', 'myTab', 'articles_assignment', JText::_('COM_TZ_PORTFOLIO_PLUS_ARTICLES_ASSIGNMENT', true)); ?>
                        <?php echo $this->form->getInput('articles_assignment'); ?>
                        <?php echo HTMLHelper::_($jTab.'.endTab'); ?>

                        <?php echo HTMLHelper::_($jTab.'.addTab', 'myTab', 'presets', JText::_('Preset', true)); ?>
                        <?php echo $this -> loadTemplate('presets');?>
                        <?php echo HTMLHelper::_($jTab.'.endTab'); ?>

                        <?php echo HTMLHelper::_($jTab.'.endTabSet'); ?>

                        <?php if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){ ?>
                        </div>
                        <?php } ?>

                    </fieldset>
                </div>
                <div class="span4 col-md-4 text-break">
                    <?php echo HTMLHelper::_('bootstrap.startAccordion', 'menuOptions', array('active' => 'collapse0'));?>
                    <?php  $fieldSets = $this->form->getFieldsets('params'); ?>
                    <?php $i = 0;?>
                    <?php foreach ($fieldSets as $name => $fieldSet) :?>
                        <?php // If the parameter says to show the article options or if the parameters have never been set, we will
                        // show the article options. ?>
                        <?php
                        $fields = $this->form->getFieldset($name);
                        if($fields && count($fields)):
                            $fieldSetLabel  = $fieldSet->label?$fieldSet->label:strtoupper('COM_TZ_PORTFOLIO_PLUS_'.$name.'_FIELDSET_LABEL');
                            ?>
                            <?php echo HTMLHelper::_('bootstrap.addSlide', 'menuOptions', JText::_($fieldSetLabel), 'collapse' . $i++); ?>
                            <?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
                                <p class="tip"><?php echo $this->escape(JText::_($fieldSet->description));?></p>
                            <?php endif; ?>
                            <fieldset>
                                <?php foreach ($fields as $field){
                                    if(version_compare(JVERSION, '3.10', '<')
                                        && $field -> __get('fieldname') == 'use_single_layout_builder') {
                                        $field->__set('layout', 'joomla.form.field.radio');
                                    }
                                    echo $field -> renderField();
                                } ?>
                            </fieldset>
                            <?php echo HTMLHelper::_('bootstrap.endSlide');?>
                        <?php endif;?>
                    <?php endforeach; ?>
                    <?php echo HTMLHelper::_('bootstrap.endAccordion');?>
                </div>
            <?php echo HTMLHelper::_('tzbootstrap.endrow');?>

        </div>
    </div>

    <input type="hidden" value="com_tz_portfolio_plus" name="option">
    <input type="hidden" value="" name="task">
    <?php echo HTMLHelper::_('form.token');?>
</form>