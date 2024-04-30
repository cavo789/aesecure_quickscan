<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015-2017 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

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
$modalId    = 'tppModalArticle_' . $id;
// Render the modal
echo JHtml::_(
    'bootstrap.renderModal',
    $modalId,
    array(
        'url'        => $link.'&function='.$function,
        'title'      => JText::_('COM_TZ_PORTFOLIO_PLUS_CHANGE_ADDONS'),
        'width'      => '400px',
        'height'     => '800px',
        'modalWidth' => '70',
        'bodyHeight' => '70',
        'closeButton' => true,
        'footer'      => '<a class="btn" data-dismiss="modal" data-bs-dismiss="modal" aria-hidden="true">' . JText::_('JCANCEL') . '</a>',
    )
);
?>
<div class="btn-group control-group">
    <a class="btn btn-primary hasTooltip" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_CHANGE_ADDONS');
        ?>" data-toggle="modal" data-bs-toggle="modal" href="#tppModalArticle_<?php echo $id;?>"><i class="icon-copy"></i> <?php
        echo JText::_('JSELECT');?></a>
    <a href="javascript:" id="<?php echo $id; ?>_clear" class="btn btn-danger<?php echo $value ? '' : ' disabled';?>" onclick="return tppClearAddOns('<?php
    echo $id; ?>')"><span class="icon-remove"></span> <?php echo JText::_('JCLEAR'); ?></a>
</div>
<div style="max-height: 330px; overflow-y: auto;">
    <table id="<?php echo $id.'_table';?>" class="table table-striped">
        <thead>
        <tr>
            <th><?php echo JText::_('JGLOBAL_TITLE');?></th>
            <th width="5%" class="center"><?php echo JText::_('JSTATUS');?></th>
            <th style="width: 5%;"><?php echo JText::_('JGRID_HEADING_ID');?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($items) && $items){
            foreach($items as $item) {
                ?>
                <tr>
                    <td><?php echo JText::_(strtoupper($item -> title)); ?></td>
                    <td class="center">
                        <?php if ($allowEdit) { ?>
                        <div class="btn-group">
                            <a class="btn btn-secondary btn-small btn-sm hasTooltip" target="_blank" title="<?php echo JText::_('JACTION_EDIT'); ?>"
                               href="index.php?option=com_tz_portfolio_plus&task=article.edit&id=<?php
                               echo $item->id; ?>"><span class="icon-edit"></span></a>
                        <?php } ?>
                        <a href="javascript:" class="btn btn-danger btn-small btn-sm hasTooltip" title="<?php echo JText::_('JTOOLBAR_REMOVE'); ?>"
                           onclick="tppClearAddOn(this);"><i class="icon-remove"></i></a>
                        <?php if ($allowEdit) { ?>
                        </div>
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo $item->id; ?>
                        <input type="hidden" name="<?php echo $name; ?>"
                               value="<?php echo $item->id; ?>">
                    </td>
                </tr>
                <?php
                }
            }?>
        </tbody>
    </table>
</div>

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
        window.tppClearAddOns = function(id) {
            $("#" + id + "_table tbody").html("");
            $("#" + id + "_clear").addClass("disabled");
            return false;
        };

        window.tppClearAddOn = function(obj){
            $(obj).tooltip("hide");
            $(obj).parents("tr").first().remove();
        };
        window.'.$function.' = function(ids, titles){
            if(ids.length){
                var fieldId = "'.$id.'",
                    html = $("<div/>");
                for(var i = 0; i < ids.length; i++){
                    var tr    = $("<tr/>");
                    tr.html("<td>" + titles[i]
                        + "</td>"
                        + "<td>"
                        '.($allowEdit?'
                        + "<div class=\"btn-group\">"
                        + "<a class=\"btn btn-secondary btn-small btn-sm hasTooltip\" target=\"_blank\" title=\"'
                            .JText::_('JACTION_EDIT').'\""
                         +"  href=\"index.php?option=com_tz_portfolio_plus'
                            .'&task=article.edit&id="+ ids[i] +"\"><span"
                         +" class=\"icon-edit\"></span></a>"
                        ':'').'
                        + "<a href=\"javascript:\" class=\"btn btn-danger btn-small btn-sm hasTooltip\" title=\"'.JText::_('JTOOLBAR_REMOVE').'\""
                        + "  onclick=\"tppClearAddOn(this);\"><i class=\"icon-remove\"></i></a>"
                       '.($allowEdit?'+ "</div>"':'').'
                        +"</td>"
                        + "<td>" + ids[i]
                        + "<input type=\"hidden\" name=\"'.$name.'\" value=\""+ ids[i] +"\"/>"
                        + "</td>");
                        if(!$("#" + fieldId + "_table tbody input[value=\""+ ids[i] + "\"]").length){
                            html.append(tr);
                        }
                }
                $("#'.$modalId.'").modal("hide");
                $("#" + fieldId + "_table tbody").prepend(html.html())
                .find(".hasTooltip").tooltip({"html": true,"container": "body"});
                $("#" + fieldId + "_clear").removeClass("disabled");
            }
        };
    })(jQuery, window);');