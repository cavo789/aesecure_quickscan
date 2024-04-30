<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author    DuongTVTemPlaza

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Copyright Copyright (C) 2015-2019 TZ Portfolio (http:://tzportfolio.com). All Rights Reserved.

-------------------------------------------------------------------------*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
extract($displayData);

$allowEdit  = false;

if(isset($field) && $field) {
    if($edit = $field -> getAttribute('edit')) {
        $allowEdit  = $edit;
    }
}
?>

<?php
$function   = 'tppSelectAddOn_'.$id;
$modalId    = 'tppModalAddOn_' . $id;
// Render the modal
echo JHtml::_(
    'bootstrap.renderModal',
    $modalId,
    array(
        'url'        => $link.'&function='.$function,
        'title'      => JText::_('COM_TZ_PORTFOLIO_PLUS_CHANGE_ADDON'),
        'width'      => '400px',
        'height'     => '800px',
        'modalWidth' => '70',
        'bodyHeight' => '70',
        'closeButton' => true,
        'footer'      => '<a class="btn" data-dismiss="modal" data-bs-dismiss="modal" aria-hidden="true">' . JText::_('JCANCEL') . '</a>',
    )
);
if(strpos('form-control', $class) == false){
    $class  .= ' form-control';
}
if(strpos('class=', $class) == false){
    $class  = 'class="'.$class.'"';
}
?>
<div class="input-append input-group">
    <input type="text" <?php echo $required; ?> readonly="readonly" id="<?php echo $id; ?>_name" value="<?php
            echo $title; ?>" <?php echo (!empty($size)?$size:''). $class; ?>  placeholder="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SELECT_AN_ADDON');?>" />
    <a id="<?php echo $id; ?>_select" class="btn btn-primary hasTooltip" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_CHANGE_ADDON');
        ?>" data-toggle="modal" data-bs-toggle="modal" href="#<?php echo $modalId;?>"><i class="icon-file me-1"></i><?php
        echo JText::_('JSELECT');?></a>
    <?php if($allowEdit){?>
        <a id="<?php echo $id; ?>_edit" class="btn<?php echo $value ? '' : ' hidden';?>" target="_blank"
           href="index.php?option=com_tz_portfolio_plus&task=article.edit&id=<?php
           echo $value; ?>"><span class="icon-edit"></span><?php echo JText::_('JACTION_EDIT'); ?></a>
    <?php } ?>
    <a href="javascript:" id="<?php echo $id; ?>_clear" class="btn btn-danger<?php echo $value ? '' : ' hidden';?>" onclick="return tppClearAddOn('<?php
    echo $id; ?>')"><span class="icon-remove"></span> <?php echo JText::_('JCLEAR'); ?></a>
</div>

    <input class="input-small" id="<?php echo $id; ?>" type="hidden" name="<?php echo $name; ?>" value="<?php
    echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8') ?>"/>
<?php
$doc    = Factory::getApplication() -> getDocument();
$doc -> addScriptDeclaration('
    (function($, window){
        "use strict";
        
        $(document).ready(function(){
            $("#'.$modalId.'").on("show.bs.modal", function() {
                $(this).find("iframe").attr("src", function(index, value){                
                    var fieldId = "'.$id.'";
                    var inputs = $("#" + fieldId + "_table tbody input[type=hidden][name=\\"'.$name.'\\"]");
                    value   = value.replace(/&filter_exclude_ids\\[\\]=[0-9]+/, "");
                    '.((isset($excludes) && count($excludes))?'value += "&filter_exclude_ids[]='.join('&filter_exclude_ids[]=', $excludes).'";':'').'
                    if(inputs.length){
                       inputs.each(function(){
                            var inputVal = $(this).val();
                            if(inputVal.length){                            
                                var patt    = new RegExp("&filter_exclude_ids\\\[\\\]=" + inputVal);
                                if(!patt.test(value)){
                                    value += "&filter_exclude_ids[]=" + inputVal;
                                }
                            }
                       });
                    }
                    return value;
                });
            });
        });
        
        window.tppClearAddOn = function(id) {
            $("#" + id + "_name").val("");
            $("#" + id ).val("");
            $("#" + id + "_clear").addClass("hidden");
            $("#" + id + "_edit").addClass("hidden");
            $("#" + id + "_select").removeClass("hidden");
                '.($submitform?'$("#'.$id.'").parents("form").first().submit()':'').'
            return false;
        };
        window.'.$function.' = function(id, title, category){
            if(id.length){
                var fieldId = "'.$id.'";
                $("#" + fieldId).val(id);
                $("#" + fieldId + "_name").val(title);
                $("#'.$modalId.'").modal("hide");
                $("#" + fieldId + "_clear").removeClass("hidden");
                '.($allowEdit?'
                $("#" + fieldId + "_edit").removeClass("hidden")
                    .attr("href",function(index, href){
                        return "index.php?option=com_tz_portfolio_plus&task=article.edit&id="+id;
                    });
                $("#" + fieldId + "_select").addClass("hidden");':'')
                .($submitform?'$("#'.$id.'").parents("form").first().submit()':'').'
            }
        };
    })(jQuery, window);');