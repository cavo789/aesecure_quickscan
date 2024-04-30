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

use Joomla\CMS\Factory;

$head               = $displayData['head'];
$group              = $displayData['group'];
$multiple           = $displayData['multiple'];
$formControl        = $displayData['formcontrol'];
$fieldValues        = $displayData['fieldValues'];
$defaultValues      = $displayData['defaultValues'];
$multiple_option    = $displayData['multiple_option'];

$id_head_text   = $formControl.'_'.$group.'_$i_text';
if($multiple_option){

    $default_type   = 'radio';
    if($multiple) {
        $default_type   = 'checkbox';
    }

    if(!$head) {
        ob_start();
        ?>
        <tr>
            <td class="center text-center"><i class="icon-menu" style="cursor: move;"></i></td>
            <td>
                <input type="text" name="<?php echo $formControl; ?>[<?php echo $group; ?>][$i][text]" id="<?php
                echo $id_head_text; ?>" required="" size="35" class="form-control input-medium required"/>
                <label id="<?php echo $id_head_text; ?>-lbl" class="required hide hidden" for="<?php
                echo $id_head_text; ?>"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_OPTION_FIELD_VALUE_LABEL'); ?></label>
            </td>
            <td>
                <input type="text" name="<?php echo $formControl; ?>[<?php echo $group;
                ?>][$i][value]" class="form-control input-mini" size="15"/>
            </td>
            <td class="center text-center">
                <?php if ($multiple) { ?>
                    <input type="checkbox" name="<?php echo $formControl; ?>[<?php echo $group; ?>][$i][default]"
                           value="1"/>
                <?php } else { ?>
                    <input type="radio" name="<?php echo $formControl; ?>[<?php echo $group; ?>][default]"
                           value="$i"/>
                <?php } ?>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-mini btn-sm tz_remove-option"><i class="icon-minus"></i><?php
                    echo JText::_('COM_TZ_PORTFOLIO_PLUS_REMOVE'); ?></button>
            </td>
        </tr>
        <?php
        $htmlHead = ob_get_contents();
        ob_end_clean();

        $doc = Factory::getApplication() -> getDocument();
        $doc->addScript(TZ_Portfolio_PlusUri::base(true, true) . '/js/jquery-ui.min.js',
            array('version' => 'v=1.11.4'));
        $doc->addStyleDeclaration('#jform_' . $group . ' .table{
                        margin-top: 5px;
                    }');
        $doc->addScriptDeclaration('
        (function($){
            $(document).ready(function(){
                function tz_extrafields(){
                    var $i  = ' . (($fieldValues && count($fieldValues)) ? count($fieldValues) : 0) . ';
                    function tzFieldRemove(){
                        $("#jform_' . $group . ' .tz_remove-option").unbind("click").bind("click",function(e){
                            $(this).parents("tr").first().remove();
                        });
                    }
                    tzFieldRemove();
                    $("#jform_' . $group . ' .tz_add-option").on("click",function(e){
                        var html    = "' . jsPlusAddSlashes($htmlHead) . '";
                        $("#jform_' . $group . ' .table tbody").first().append(html.replace(/\$i/mg, $i));
                        tzFieldRemove();
                        $i ++;
                    });
                    $("#jform_' . $group . ' .table tbody").sortable({
                        handle: ".icon-menu",
                        cursor: "move",
                        items: "tr",
                        axis: "y",
                        placeholder: "ui-state-highlight",
                        forcePlaceholderSize: true,
                        forceHelperSize: true,
                        distance: 2
                        ,start: function(event,ui){
                            $.each(ui.helper.find("td"),function(){
                                $(this).width($(this).innerWidth());
                            });
                            $.each(ui.item.find("td"),function(){
                                $(this).width($(this).innerWidth());
                            });
                        },
                        stop: function(event,ui){
                            ui.item.children().width("");
                        }
                    });
                }
                tz_extrafields();
    
            });
        })(jQuery);
        ');
    }
?>
<button type="button" class="btn btn-secondary btn-mini tz_add-option"><i class="icon-plus"></i><?php
    echo JText::_('COM_TZ_PORTFOLIO_PLUS_ADD_AN_OPTION'); ?></button>
<div class="max-height-300">
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SORT'); ?></th>
            <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_FIELD_TEXT'); ?>
                <span class="star">&nbsp;*</span>
            </th>
            <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_VALUE');?></th>
            <th><?php echo JText::_('JDEFAULT'); ?></th>
            <th><?php echo JText::_('JSTATUS'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php if($fieldValues && !is_string($fieldValues)){?>
            <?php foreach($fieldValues as $key => $value){
                $name_text  = $formControl.'['
                    .$group.']['.$key.'][text]';
                $id_text    = JApplicationHelper::stringURLSafe($name_text);
                $id_text    = preg_replace('#\W#', '_', $id_text);

                $name_value = $formControl.'['
                    .$group.']['.$key.'][value]';
                $id_value   = JApplicationHelper::stringURLSafe($name_value);
                $id_value   = preg_replace('#\W#', '_', $id_value);

            ?>
                <tr>
                    <td class="center text-center"><i class="icon-menu" style="cursor: move;"></i></td>
                    <td>
                        <input type="text" id="<?php echo $id_text; ?>" name="<?php echo $formControl; ?>[<?php
                        echo $group; ?>][<?php echo $key; ?>][text]" required="" size="35" value="<?php
                        echo is_object($value)?htmlspecialchars($value -> text):htmlspecialchars($value['text']);
                        ?>" class="form-control input-medium required"/>
                        <label id="<?php echo $id_text; ?>-lbl" class="required hide hidden" for="<?php echo $id_text;
                        ?>"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_OPTION_FIELD_VALUE_LABEL'); ?></label>
                    </td>
                    <td>
                        <input type="text" id="<?php echo $id_value; ?>" name="<?php echo $name_value;
                        ?>" class="form-control input-mini" size="15" value="<?php
                        echo is_object($value)?htmlspecialchars($value -> value):htmlspecialchars($value['value']); ?>"/>
                    </td>
                    <td class="center text-center">
                    <?php if($multiple) { ?>
                        <input type="<?php echo $default_type; ?>" name="<?php echo $formControl;
                        ?>[<?php echo $group; ?>][<?php echo $key; ?>][default]" value="1"<?php
                        echo ((isset($value->default) && $value->default == 1)? ' checked="checked"' : ''); ?>/>
                    <?php }else{ ?>
                        <input type="radio" name="<?php echo $formControl; ?>[<?php echo $group;
                        ?>][default]" value="<?php echo $key; ?>"<?php
                        echo ((isset($value->default) && $value->default == 1)? ' checked="checked"' : ''); ?>/>
                    <?php } ?>
                    </td>

                    <td>
                        <button type="button" class="btn btn-danger btn-mini btn-sm tz_remove-option"><i class="icon-minus"></i><?php
                            echo JText::_('COM_TZ_PORTFOLIO_PLUS_REMOVE'); ?></button>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php }else{ ?>
    <input type="text" name="<?php echo $formControl; ?>[<?php echo $group; ?>]" value="<?php
    echo htmlspecialchars($defaultValues); ?>" class="form-control inputbox"/>
<?php } ?>
