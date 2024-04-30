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

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

$group  = 'presets';

$doc    = Factory::getApplication() -> getDocument();
$doc -> addScriptDeclaration('
    (function($){
        $(document).ready(function(){
            $(".load-preset").on("click", function(e){
                e.stopPropagation();
                e.preventDefault();
                $("#loadPreset").modal("toggle");
                var $thisPreset = $(this);
                $("#loadPresetAccept").click(function(e){
                    $("#jform_preset").val($thisPreset.attr("data-preset"));
                    Joomla.submitbutton("template_style.loadpreset");
                });
            });
            $(".removepreset").on("click", function(e){
                e.stopPropagation();
                e.preventDefault();
                $("#removePreset").modal("toggle");
                var $thisPreset = $(this);
                $("#removePresetAccept").click(function(e){
                    $("#jform_preset").val($thisPreset.attr("data-preset"));
                    Joomla.submitbutton("template_style.removepreset");
                });
            });
            $("#upload-preset-submit").on("click", function(e){
                e.stopPropagation();
                e.preventDefault();
                var $thisPreset = $(this);                
                $("#jform_preset").val($thisPreset.attr("data-preset"));
                Joomla.submitbutton("template_style.uploadpreset");
            });
            $(".downloadpreset").on("click", function(e){
                e.stopPropagation();
                e.preventDefault();
                var $thisPreset = $(this);                
                $("#jform_preset").val($thisPreset.attr("data-preset"));
                Joomla.submitbutton("template_style.downloadpreset");
            });
        });
    })(jQuery);
');
?>
<div class="row-fluid">
    <div class="span6">
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('name',$group);?></div>
            <div class="controls"><?php echo $this -> form -> getInput('name',$group);?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('image',$group);?></div>
            <div class="controls"><?php echo $this -> form -> getInput('image',$group);?></div>
        </div>
    </div>
    <div class="span6">
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('demo_link',$group);?></div>
            <div class="controls"><?php echo $this -> form -> getInput('demo_link',$group);?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('doc_link',$group);?></div>
            <div class="controls"><?php echo $this -> form -> getInput('doc_link',$group);?></div>
        </div>
    </div>
</div>
<div class="">
    <button type="button" data-toggle="collapse" data-target="#collapseUpload" data-bs-toggle="collapse" data-bs-target="#collapseUpload" class="btn btn-success">
        <span class="tps tp-upload"></span> <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_UPLOAD_PRESET');?></button>
    <div id="collapseUpload" class="collapse">

        <div class="control-group mt-px-18">
            <label for="upload-preset-file" class="list-inline"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_UPLOAD_FILE'); ?></label>
            <input type="file" id="upload-preset-file" name="Filepreset[]" multiple />
            <button type="button" class="btn btn-primary" id="upload-preset-submit"><i class="tpr tp-caret-square-right"></i>  <?php
                echo JText::_('COM_TZ_PORTFOLIO_PLUS_START_UPLOAD'); ?></button>
            <p class="help-block">
                <?php $maxSize = JUtility::getMaxUploadSize(); ?>
                <?php echo JText::sprintf('JGLOBAL_MAXIMUM_UPLOAD_SIZE_LIMIT', JHtml::_('number.bytes', $maxSize)); ?>
            </p>
        </div>
    </div>
</div>
<?php
if($presets = $this -> presets):
?>
<div class="presets">
    <div class="alert alert-warning">
        <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LOAD_PRESET_DESCRIPTION');?>
    </div>
    <?php foreach($presets as $preset){?>
        <div class="preset<?php echo (isset($this -> item -> preset)
            && ($this -> item -> preset == $preset -> name))?' active':'';?>">
            <div class="preset-screenshot<?php echo (!isset($preset -> image) || (isset($preset -> image) && !$preset -> image))?' background':''; ?>">
                <?php if(isset($preset -> image) && $preset -> image){?>
                <img src="<?php echo TZ_Portfolio_PlusUri::root().'/'.$preset -> image;?>" alt="<?php echo $preset -> name;?>">
                <?php }else{?>
                    <span><?php echo '287 x 220';?></span>
                <?php }?>
                <div class="preset-bar">
                    <div class="preset-bar-table">
                        <div class="preset-bar-table-cell">
                            <span data-preset="<?php echo $preset -> name;?>" data-target="#loadPreset" data-toggle="modal" data-bs-toggle="modal"
                                  class="load-preset btn btn-warning btn-small btn-sm"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LOAD_PRESET');?></span>
                            <?php if(isset($preset -> doc_link) && $preset -> doc_link){ ?>
                                <a target="_blank" class="btn btn-primary btn-small btn-sm"
                                   href="<?php echo $preset -> doc_link;?>"><?php echo JText::_('JTOOLBAR_HELP');?></a>
                            <?php }
                            if(isset($preset -> demo_link) && $preset -> demo_link){
                                ?>
                                <a target="_blank" class="btn btn-success btn-small btn-sm"
                                   href="<?php echo $preset -> demo_link;?>"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LIVE_PREVIEW');?></a>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="preset-bottom">
                <h3 class="preset-name hasTooltip" data-placement="bottom" title="<?php echo $preset -> name;?>"><?php echo $preset -> name;?></h3>
                <div class="action">
                    <span data-preset="<?php echo $preset -> name;?>"
                       title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_DOWNLOAD_PRESET_DESCRIPTION');?>"
                       class="tps tp-download downloadpreset hasTooltip"></span>
                    <i data-preset="<?php echo $preset -> name;?>" data-target="#removePreset" data-toggle="modal"
                       data-bs-toggle="modal" data-bs-target="#removePreset"
                       title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_REMOVE_PRESET_DESCRIPTION');?>"
                       class="tps tp-times removepreset hasTooltip"></i>
                </div>
            </div>
        </div>
    <?php }?>
</div>
<?php

echo $this -> form -> getInput('preset');

echo $this -> loadTemplate('presets_load_modal');
echo $this -> loadTemplate('presets_remove_modal');
endif;
