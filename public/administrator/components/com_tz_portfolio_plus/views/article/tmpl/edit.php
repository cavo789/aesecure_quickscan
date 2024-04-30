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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
    HTMLHelper::_('behavior.tabstate');
    HTMLHelper::_('formbehavior.chosen', 'select');
}else{
    HTMLHelper::_('formbehavior.chosen', 'select[multiple]:not(.choices__input)');
}

$doc    = Factory::getApplication() -> getDocument();
$doc -> addScript(TZ_Portfolio_PlusUri::base(true, true).'/js/jquery-ui.min.js', array('version' => 'v=1.11.4'));
$doc -> addStyleSheet(TZ_Portfolio_PlusUri::base(true, true).'/css/jquery-ui.min.css', array('version' => 'v=1.11.4'));
$doc -> addStyleSheet(TZ_Portfolio_PlusUri::base(true, true).'/css/tz_portfolio_plus.min.css', array('version' => 'auto'));

// Create shortcut to parameters.
$params = $this->state->get('params');
if($params) {
    $params = $params->toArray();
}

// This checks if the config options have ever been saved. If they haven't they will fall back to the original settings.
$editoroptions = isset($params['show_publishing_options']);

if (!$editoroptions):
    $params['show_publishing_options'] = '1';
    $params['show_article_options'] = '1';
    $params['show_urls_images_backend'] = '0';
    $params['show_urls_images_frontend'] = '0';
endif;

// Check if the article uses configuration settings besides global. If so, use them.
if (!empty($this->item->attribs['show_publishing_options'])):
    $params['show_publishing_options'] = $this->item->attribs['show_publishing_options'];
endif;
if (!empty($this->item->attribs['show_article_options'])):
    $params['show_article_options'] = $this->item->attribs['show_article_options'];
endif;
if (!empty($this->item->attribs['show_urls_images_backend'])):
    $params['show_urls_images_backend'] = $this->item->attribs['show_urls_images_backend'];
endif;

$mediavalue = '';
$media      = array();

$pluginsTab = $this -> pluginsTab;

$assoc = JLanguageAssociations::isEnabled();

// Are associations implemented for this extension?
$extensionassoc = array_key_exists('item_associations', $this->form->getFieldsets());

$doc -> addScriptDeclaration('(function($){
        "use strict";
        $(document).ready(function(){
            $(\'#jform_catid\').on(\'change\',function(){
                $("#jform_second_catid option[value="+ $(this).val() +"]:selected").removeAttr("selected");
                $(\'#jform_second_catid option:disabled\').removeAttr(\'disabled\');
                $(\'#jform_second_catid option[value="\'+this.value+\'"]\').attr(\'disabled\',\'disabled\');
                $(\'#jform_second_catid\').trigger(\'liszt:updated\');
                
                var __second_fancy  = $("#jform_second_catid").closest("joomla-field-fancy-select")[0];
                if(typeof __second_fancy !== "undefined" && typeof __second_fancy.choicesInstance !== "undefined"){
                    __second_fancy.enableAllOptions();
                    __second_fancy.choicesInstance.removeActiveItemsByValue($(this).val());
                    __second_fancy.disableByValue($(this).val());
                }
            });
            $("#jform_catid").trigger("change");
        });
    })(jQuery);');

$jTab   = 'bootstrap';
if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){
    $jTab   = 'uitab';
}

?>
<form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=article&layout=edit&id='.(int) $this->item->id); ?>"
      method="post"
      name="adminForm"
      id="adminForm"
      class="form-validate tpArticle"
      enctype="multipart/form-data">
    <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
    <div class="span8 col-md-8 form-horizontal">
        <?php echo HTMLHelper::_($jTab.'.startTabSet', 'myTab', array('active' => 'general')); ?>

        <?php
        // Tab general
        echo HTMLHelper::_($jTab.'.addTab', 'myTab', 'general',
            JText::_('JDETAILS', true)); ?>
        <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
        <div class="span6 col-md-6">
            <?php echo $this -> form -> renderField('title');?>
            <?php echo $this -> form -> renderField('alias');?>
            <div class="control-group">
                <div class="control-label">
                    <label><?php echo $this->form->getLabel('tags');?></label>
                </div>
                <div class="controls">
                    <?php echo $this -> form -> getInput('tags');?>
                    <div><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_FORM_TAGS_DESC');?></div>
                </div>
            </div>
            <?php echo $this -> form -> renderField('state');?>
            <?php echo $this -> form -> renderField('access');?>
            <?php echo $this -> form -> renderField('priority');?>
            <?php echo $this -> form -> renderField('id');?>
        </div>
        <div class="span6 col-md-6">
            <?php echo $this -> form -> renderField('catid');?>
            <?php echo $this -> form -> renderField('second_catid');?>
            <?php echo $this -> form -> renderField('groupid');?>
            <?php echo $this -> form -> renderField('type');?>
            <?php echo $this -> form -> renderField('featured');?>
            <?php echo $this -> form -> renderField('language');?>
            <?php echo $this -> form -> renderField('template_id');?>
        </div>
        <?php echo HTMLHelper::_('tzbootstrap.endrow');?>

        <?php
        // Before description position
        echo $this -> loadTemplate('addon_before_description');

        ?>


        <?php echo HTMLHelper::_($jTab.'.startTabSet', 'myTabGenearal', ['active' => 'tz_content', 'recall' => true, 'breakpoint' => 768]); ?>
        <?php echo HTMLHelper::_($jTab.'.addTab', 'myTabGenearal', 'tz_content', JText::_('COM_TZ_PORTFOLIO_PLUS_TAB_CONTENT')); ?>
        <?php echo $this->form->getInput('articletext'); ?>
        <?php echo HTMLHelper::_($jTab.'.endTab'); ?>
        <?php
        if(!empty($this -> pluginsMediaTypeTab) && count($this -> pluginsMediaTypeTab)){
            foreach($this -> pluginsMediaTypeTab as $media){
                echo HTMLHelper::_($jTab.'.addTab', 'myTabGenearal', 'tztabsaddonsplg_mediatype'
                    . $media->type->value, $media -> type -> text);
                echo $media -> html;
                echo HTMLHelper::_($jTab.'.endTab');
            }
        }
        ?>
        <?php echo HTMLHelper::_($jTab.'.addTab', 'myTabGenearal', 'tztabsFields', JText::_('COM_TZ_PORTFOLIO_PLUS_TAB_FIELDS')); ?>
        <?php echo $this -> loadTemplate('extrafields');?>
        <?php echo HTMLHelper::_($jTab.'.endTab'); ?>

        <?php
        if(!empty($this -> advancedDesc) && count($this -> advancedDesc)){
            foreach($this -> advancedDesc as $advance){
                echo HTMLHelper::_($jTab.'.addTab', 'myTabGenearal', 'tztabsaddonsplg_'
                    .$advance -> group.'_'.$advance -> addon, $advance -> title);
                echo $advance -> html;
                echo HTMLHelper::_($jTab.'.endTab');
            }
        }
        ?>
        <?php echo HTMLHelper::_($jTab.'.endTabSet'); ?>

        <?php
        // After description position
        echo $this->loadTemplate('addon_after_description');
        ?>


        <?php echo HTMLHelper::_($jTab.'.endTab');
        // End tab general

        ?>

        <?php if($assoc && $extensionassoc){ ?>
            <?php echo HTMLHelper::_($jTab.'.addTab', 'myTab', 'associations',
                JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true)); ?>
            <?php echo $this->loadTemplate('associations'); ?>
            <?php echo HTMLHelper::_($jTab.'.endTab'); ?>
        <?php } ?>

        <?php if ($this->canDo->get('core.admin')){ ?>
            <?php echo HTMLHelper::_($jTab.'.addTab', 'myTab', 'permissions',
                JText::_('JCONFIG_PERMISSIONS_LABEL', true)); ?>
            <?php echo $this->form->getInput('rules'); ?>
            <?php echo HTMLHelper::_($jTab.'.endTab'); ?>
        <?php } ?>
        <?php echo HTMLHelper::_($jTab.'.endTabSet'); ?>

    </div>
    <div class="span4 col-md-4 form-vertical">
        <?php echo HTMLHelper::_('bootstrap.startAccordion', 'articleOptions', array('active' => 'collapse0'
        , 'parent' => true));?>

        <?php // Do not show the publishing options if the edit form is configured not to. ?>
        <?php  if ($params['show_publishing_options'] || ( $params['show_publishing_options'] = '' && !empty($editoroptions)) ): ?>
            <?php echo HTMLHelper::_('bootstrap.addSlide', 'articleOptions', JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'collapse0'); ?>
            <fieldset>
                <?php echo $this -> form -> renderField('created_by');?>
                <?php echo $this -> form -> renderField('created_by_alias');?>
                <?php echo $this -> form -> renderField('created');?>
                <?php echo $this -> form -> renderField('publish_up');?>
                <?php echo $this -> form -> renderField('publish_down');?>

                <?php if ($this->item && $this->item->modified_by) : ?>
                    <?php echo $this -> form -> renderField('modified_by');?>
                    <?php echo $this -> form -> renderField('modified');?>
                <?php endif; ?>

                <?php if ($this->item->version) : ?>
                    <?php echo $this -> form -> renderField('version');?>
                <?php endif; ?>

                <?php if ($this->item->hits) : ?>
                    <?php echo $this -> form -> renderField('hits');?>
                <?php endif; ?>
            </fieldset>
            <?php echo HTMLHelper::_('bootstrap.endSlide');?>
        <?php  endif; ?>

        <?php  $fieldSets = $this->form->getFieldsets('attribs'); ?>
        <?php $i = 1;?>
        <?php foreach ($fieldSets as $name => $fieldSet) : ?>
            <?php // If the parameter says to show the article options or if the parameters have never been set, we will
            // show the article options. ?>

            <?php if ($params['show_article_options'] || (( $params['show_article_options'] == '' && !empty($editoroptions) ))): ?>
                <?php // Go through all the fieldsets except the configuration and basic-limited, which are
                // handled separately below. ?>


                <?php if ($name != 'editorConfig' && $name != 'basic-limited') :?>
                    <?php //echo HTMLHelper::_('sliders.panel', JText::_($fieldSet->label), $name.'-options'); ?>
                    <?php echo HTMLHelper::_('bootstrap.addSlide', 'articleOptions', JText::_($fieldSet->label), 'collapse' . $i++); ?>
                    <?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
                        <p class="tip"><?php echo $this->escape(JText::_($fieldSet->description));?></p>
                    <?php endif; ?>
                    <fieldset>
                        <?php foreach ($this->form->getFieldset($name) as $field){
                            echo $field -> renderField();
                        } ?>
                    </fieldset>
                    <?php echo HTMLHelper::_('bootstrap.endSlide');?>
                <?php endif ?>
                <?php // If we are not showing the options we need to use the hidden fields so the values are not lost.  ?>
            <?php  elseif ($name == 'basic-limited'): ?>
                <?php foreach ($this->form->getFieldset('basic-limited') as $field) : ?>
                    <?php  echo $field->input; ?>
                <?php endforeach; ?>

            <?php endif; ?>
        <?php endforeach; ?>

        <?php // The url and images fields only show if the configuration is set to allow them.  ?>
        <?php // This is for legacy reasons. ?>

        <?php echo HTMLHelper::_('bootstrap.addSlide', 'articleOptions', JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'meta-options' ); ?>
        <fieldset class="panelform">
            <?php echo $this->loadTemplate('metadata'); ?>
        </fieldset>
        <?php echo HTMLHelper::_('bootstrap.endSlide');?>
        <?php echo HTMLHelper::_('bootstrap.endAccordion');?>

    </div>
    <?php echo HTMLHelper::_('tzbootstrap.endrow');?>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="return" value="<?php echo Factory::getApplication() -> input -> getCmd('return');?>" />
    <input type="hidden" name="contentid" id="contentid" value="<?php echo Factory::getApplication() -> input -> getCmd('id');?>">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>