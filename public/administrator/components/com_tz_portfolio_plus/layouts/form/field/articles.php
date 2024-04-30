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

if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
    $doc    	= JFactory::getDocument();
    $wa = $doc->getWebAssetManager();
    $wa ->useScript('jquery');
}else{
    JHtml::_('jquery.framework');
}
?>

<?php
$function   = 'tppSelectArticle_'.$id;
$modalId    = 'tppModalArticle_' . $id;
// Render the modal
echo JHtml::_(
    'bootstrap.renderModal',
    $modalId,
    array(
        'url'        => $link.'&function='.$function,
        'title'      => JText::_('COM_TZ_PORTFOLIO_PLUS_CHANGE_ARTICLES'),
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
    <a class="btn btn-primary hasTooltip" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_CHANGE_ARTICLES');
        ?>" data-toggle="modal" data-bs-toggle="modal" href="#tppModalArticle_<?php echo $id;?>"><i class="icon-copy"></i> <?php
        echo JText::_('JSELECT');?></a>
    <a href="javascript:" id="<?php echo $id; ?>_clear" class="btn btn-danger<?php echo $value ? '' : ' disabled';?>" onclick="return tppClearArticles('<?php
    echo $id; ?>')"><span class="icon-remove"></span> <?php echo JText::_('JCLEAR'); ?></a>
</div>
<div style="max-height: 330px; overflow-y: auto;">
    <table id="<?php echo $id.'_table';?>" class="table table-striped">
        <thead>
        <tr>
            <th><?php echo JText::_('JGLOBAL_TITLE');?></th>
            <th><?php echo JText::_('JCATEGORY');?></th>
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
                    <td><?php echo $item -> title; ?></td>
                    <td><?php echo $item -> category_title; ?></td>
                    <td class="center">
                        <?php if ($allowEdit) { ?>
                        <div class="btn-group">
                            <a class="btn btn-secondary btn-small btn-sm hasTooltip" target="_blank" title="<?php echo JText::_('JACTION_EDIT'); ?>"
                               href="index.php?option=com_tz_portfolio_plus&task=article.edit&id=<?php
                               echo $item->id; ?>"><span class="icon-edit"></span></a>
                        <?php } ?>
                        <a href="javascript:" class="btn btn-danger btn-small btn-sm hasTooltip" title="<?php echo JText::_('JTOOLBAR_REMOVE'); ?>"
                           onclick="tppClearArticle(this);"><i class="icon-remove"></i></a>
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
        window.tppClearArticles = function(id) {
            $("#" + id + "_table tbody").html("");
            $("#" + id + "_clear").addClass("disabled");
            return false;
        };

        window.tppClearArticle = function(obj){
            $(obj).tooltip("hide");
            $(obj).parents("tr").first().remove();
        };
        window.'.$function.' = function(ids, titles, categories){
            if(ids.length){
                var fieldId = "'.$id.'",
                    html = $("<div/>");
                for(var i = 0; i < ids.length; i++){
                    var tr    = $("<tr/>");
                    tr.html("<td>" + titles[i]
                        + "</td>"
                        + "<td>" + categories[i]+ "</td>"
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
                        + "  onclick=\"tppClearArticle(this);\"><i class=\"icon-remove\"></i></a>"
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