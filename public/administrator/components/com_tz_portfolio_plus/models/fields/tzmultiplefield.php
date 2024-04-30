<?php
/*------------------------------------------------------------------------

# TZ Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// no direct access
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;

JFormHelper::loadFieldClass('list');

class JFormFieldTZMultipleField extends JFormField
{
    protected $type     = 'TZMultipleField';
    protected $head     = false;
    protected $multiple = true;

    protected function getName($fieldName)
    {
        return parent::getName($fieldName);
    }

    protected function getInput()
    {
        if(!is_array($this -> value) && preg_match_all('/(\{.*?\})/',$this -> value,$match)) {
            $this -> setValue($match[1]);
        }
        $doc    = Factory::getApplication() -> getDocument();
        if(!$this -> head) {
            $doc->addScript(TZ_Portfolio_PlusUri::root(true,null,true).'/js/jquery-ui.min.js', array('version' => 'v=1.11.4'));
            $doc->addStyleSheet(TZ_Portfolio_PlusUri::root(true,null,true). '/css/jquery-ui.min.css', array('version' => 'v=1.11.4'));
            $doc->addStyleDeclaration('.tz_pricing-table-table .ui-sortable-helper{
                background: #fff;
            }');
            $lang   = Factory::getApplication() -> getLanguage();
            $lang -> load('com_tz_portfolio_plus');
            $this -> head   = true;
        }
        $id                 = $this -> id;
        $element            = $this -> element;
        $this -> __set('multiple','true');

        // Initialize some field attributes.
        $class = !empty($this->class) ? ' class="' . $this->class . '"' : '';
        $disabled = $this->disabled ? ' disabled' : '';

        // Initialize JavaScript field attributes.
        $onchange = $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

        // Get children fields from xml file
        $tzfields = $element->children();
        // Get field with tzfield tags
        $xml                = array();
        $html               = array();
        $thead              = array();
        $tbody_col_require  = array();
        $tbody_row_id       = array();
        $tbody_row_html     = array();
        $tzform_control_id  = array();
        $form_control       = array();

        $tbody_row_html[]   = '<td style="text-align: center;">'
            .'<span class="icon-move hasTooltip" title="'.JText::_('COM_TZ_PORTFOLIO_PLUS_MOVE').'"
             style="cursor: move;"></span></td>';

        ob_start();
?>
        <div id="<?php echo $id;?>-content">
        <div class="control-group">
            <button type="button" class="btn btn-success tz_btn-add">
                <span class="icon-plus icon-white" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_UPDATE');?>"></span>
                <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_UPDATE');?>
            </button>
            <button type="button" class="btn btn-secondary tz_btn-reset">
                <span class="icon-cancel" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_RESET');?>"></span>
                <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_RESET');?>
            </button>
        </div>
        <?php

        // Generate children fields from xml file
        if ($tzfields) {

            $i  = 0;
            foreach ($tzfields as $xmlElement) {
                $type = $xmlElement['type'];
                if (!$type) {
                    $type = 'text';
                }
                $tz_class   = 'JFormField'.ucfirst($type);

                if(!class_exists($tz_class)) {
                    JLoader::register($tz_class,JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'
                        .DIRECTORY_SEPARATOR.'form'
                    .DIRECTORY_SEPARATOR.'fields'.DIRECTORY_SEPARATOR.$type.'.php');
                }

                // Check formfield class of children field
                if(class_exists($tz_class)) {

                    // Create formfield class of children field
                    $tz_class = new $tz_class();
                    $tz_class -> setForm($this -> form);
                    $tz_class->formControl = 'tzform';
                    // Init children field for children class
                    $tz_class -> setup($xmlElement, '');
                    $tz_class -> value      = is_string($xmlElement['default']) ? $xmlElement['default'] : '';
                    $tz_name                = (string)$xmlElement['name'];
                    $tz_tbl_require         = (bool)$xmlElement['table_required'];

                    $tzform_control_id[$i]                      = array();
                    $tzform_control_id[$i]["id"]                = $tz_class -> id;
                    $tzform_control_id[$i]["type"]              = $tz_class -> type;
                    $tzform_control_id[$i]["fieldname"]         = $tz_class -> fieldname;
                    $tzform_control_id[$i]["table_required"]    = 0;
                    $tzform_control_id[$i]["name"]              = $tz_class -> name;
                    $tzform_control_id[$i]["default"]           = $tz_class ->default;
                    $tzform_control_id[$i]["field_required"]    = (bool)$xmlElement['field_required'];
                    $tzform_control_id[$i]["value_validate"]    = (string)$xmlElement['value_validate'];
                    $tzform_control_id[$i]["label"]             = $tz_class -> getTitle();

                    // Create table's head column (check attribute table_required of children field from xml file)
                    if ($tz_tbl_require) {
                        $tbody_row_id[]                             = $tz_class -> id;
                        $tbody_col_require[]                        = $tz_class -> fieldname;
                        $tzform_control_id[$i]["table_required"]    = 1;

                        ob_start();
                        ?>
                        <th><?php echo $tz_class -> getTitle(); ?></th>
                        <?php
                        $thead[] = ob_get_clean();
                        ob_start();
                        ?>
                        <td>{<?php echo $tz_class -> id;?>}</td>
                    <?php
                        $tbody_row_html[]   = ob_get_clean();
                    }
                    ob_start();
                    // Generate children field from xml file
                    ?>
                    <div class="control-group">
                        <div class="control-label"><?php echo $tz_class->getLabel($tz_name) ?></div>
                        <div class="controls"><?php echo $tz_class->getInput($tz_name); ?></div>
                    </div>
                    <?php
                    $form_control[] = ob_get_clean();
                }
                $i++;
            }
        }
        // Generate table
        if(count($thead)) {
            ?>
            <table class="table table-striped tz_pricing-table-table">
                <thead>
                <tr>
                    <th style="width: 3%; text-align: center;">#</th>
                    <?php echo implode("\n", $thead); ?>
                    <th style="width: 10%; text-align: center;"><?php echo JText::_('JSTATUS'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if($values = $this -> value){
                    if(count($values)){
                    foreach($values as $value){
                        $j_value    = json_decode($value);

                        $arr_j_value    = (array) $j_value;
                        $arr_j_value    = array_keys($arr_j_value);

                        $difference_keys    = array_diff( $tbody_col_require, $arr_j_value);
                        $difference_keys    = array_keys($difference_keys);
                ?>
                    <tr>
                        <td style="text-align: center;"><span class="icon-move hasTooltip" style="cursor: move;"
                                  title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_MOVE')?>"></span></td>
                        <?php
                        if($j_value && !empty($j_value)) {
                            $j = 0;
                            foreach ($j_value as $key => $_j_value) {
                                if(in_array($key,$tbody_col_require)){

                        ?>
                            <td><?php echo $_j_value ?></td>
                        <?php }
                        if(count($difference_keys) && in_array($j+1, $difference_keys)){
                            for($k=0; $k <count($difference_keys); $k++) {
                                ?>
                                <td></td>
                                <?php
                            }
                                }
                            $j++;
                            }
                        }
                        ?>
                        <td style="text-align: center;">
                            <div class="btn-group">
                                <button class="btn btn-secondary btn-small btn-sm tz_btn-edit hasTooltip"
                                        type="button" title="<?php echo JText::_('JACTION_EDIT');?>"><i class="icon-edit"></i></button>
                                <button class="btn btn-danger btn-small btn-sm tz_btn-remove hasTooltip"
                                        type="button" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_REMOVE');?>"><i class="icon-trash"></i></button>
                            </div>
                            <input type="hidden" name="<?php echo $this -> getName($this -> fieldname);?>"
                                   value="<?php echo htmlspecialchars( $value);?>" <?php echo $class . $disabled . $onchange?>/>
                            <?php ?>
                        </td>
                    </tr>
                <?php } } }?>
                </tbody>
            </table>
            <?php
        }

        echo implode("\n",$form_control);

        $tbody_row_html[]   = '<td style="text-align: center;">'
            .'<div class="btn-group">'
            .'<button type="button" class="btn btn-secondary btn-small btn-sm tz_btn-edit hasTooltip" title="'
            .JText::_('JACTION_EDIT').'"><i class="icon-edit"></i></button>'
            .'<button type="button" class="btn btn-danger btn-small btn-sm tz_btn-remove hasTooltip" title="'
            .JText::_('COM_TZ_PORTFOLIO_PLUS_REMOVE').'">'
            .'<i class="icon-trash"></i></button>'
            .'</div>'
            .'<input type="hidden" name="' . $this -> getName($this -> fieldname) . '" value="{'.
            $this -> id.'}"' . $class . $disabled . $onchange . ' />'
            .'</td>';

        $config = Factory::getConfig();

        $addEditor      = '';
        $editEditor     = '';
        $resetEditor    = '';

        if($config -> get('editor') == 'jce'){
            $addEditor  = '$content[value["fieldname"]]    =  WFEditor.getContent(value["id"]);';
        }elseif($config -> get('editor') == 'tinymce'){
            $addEditor  = '$content[value["fieldname"]]    =  tinyMCE.activeEditor.getContent();';
        }elseif($config -> get('editor') == 'codemirror'){
            $addEditor  = '$content[value["fieldname"]]    =  Joomla.editors.instances[value["id"]].getValue();';
        }

        if($config -> get('editor') == 'jce'){
            $editEditor = 'WFEditor.setContent(value["id"], $hidden_obj_value[value["fieldname"]]);';
        }elseif($config -> get('editor') == 'tinymce'){
            $editEditor = 'tinyMCE.activeEditor.setContent($hidden_obj_value[value["fieldname"]]);';
        }elseif($config -> get('editor') == 'codemirror'){
            $editEditor = 'Joomla.editors.instances[value["id"]].setValue($hidden_obj_value[value["fieldname"]]);';
        }

        if($config -> get('editor') == 'jce'){
            $resetEditor    = 'WFEditor.setContent(value["id"], value["default"]);';
        }elseif($config -> get('editor') == 'tinymce'){
            $resetEditor    = 'tinyMCE.activeEditor.setContent(value["default"]);';
        }elseif($config -> get('editor') == 'codemirror'){
            $resetEditor    = 'Joomla.editors.instances[value["id"]].setValue(value["default"]);';
        }

        $tbody_row_html = '<tr>'.implode('',$tbody_row_html).'</tr>';
        $doc -> addScriptDeclaration('function htmlspecialchars(str) {
                    if (typeof(str) == "string") {
                        str = str.replace(/&/g, "&amp;"); /* must do &amp; first */
                        str = str.replace(/"/g, "&quot;");
                        str = str.replace(/\'/g, "&#039;");
                        str = str.replace(/</g, "&lt;");
                        str = str.replace(/>/g, "&gt;");
                    }
                    return str;
                }


                (function($){
                    $(document).ready(function(){

                        var $tbody_row_html     = "'.jsPlusAddSlashes( trim($tbody_row_html)).'";
                        var $tzpricing_table_id = "'.$this -> id.'";
                        var $tbody_control_id   = '.json_encode($tzform_control_id ).';
                        var $hidden_name        = "'.jsPlusAddSlashes($this -> getName($this -> fieldname)).'";
                        var $tzpricing_position = -1;

                        // Add new data row
                        $("#'.$id.'-content .tz_btn-add").bind("click",function(e){

                            // Create input hidden with data were put
                            var $tbody_row_html_clone   = $tbody_row_html;
                            var $tbody_bool             = true;
                            var $content                = {};

                            $.each($tbody_control_id,function(key,value){
                                var input_name  = value["name"].replace(/\\[/,"\\\[")
                                    .replace(/\\]/,"\\\]");

                                if(value["field_required"]){
                                    $tbody_bool = false;
                                    if(!$("#" + value["id"]).val().length){
                                        alert("'.htmlspecialchars(JText::sprintf('JLIB_FORM_VALIDATE_FIELD_INVALID','')).'"
                                            + value["label"]);
                                        $("#" + value["id"]).focus();
                                        return false;
                                    }
                                }

                                if(value["value_validate"]){
                                    if($("#" + value["id"]).val() == value["value_validate"]){
                                        alert("'.htmlspecialchars(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_FAILED_TO_VALUE','')).'"
                                            + value["value_validate"]
                                            + " '.htmlspecialchars(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_FAILED_OF_FIELD','')).'"
                                            + value["label"]);
                                        return false;
                                    }
                                }

                                // Check required and create row for table
                                if(value["table_required"]){
                                    var pattern = "\\\{"+value["id"]+"\\\}";
                                    var regex   = new RegExp(pattern,"gi");
                                    $tbody_row_html_clone   = $tbody_row_html_clone.replace(regex,$("#" + value["id"]).val());
                                }

                                $tbody_bool = true;

                                if(value["type"].toLowerCase() == "editor"){
                                    // tinyMCE.activeEditor.getContent();
                                    //WFEditor.getContent(id)
                                    '.$addEditor.'
                                    $content[value["fieldname"]] = $("#" + value["id"]).val();
                                }else {
                                    if($("[name=" + input_name + "]").prop("tagName").toLowerCase() == "input"
                                        && $("[name=" + input_name + "]").prop("type") == "radio") {
                                        $content[value["fieldname"]] = $("[name="+ value["name"].replace(/\\[/,"\\\[")
                                            .replace(/\\]/,"\\\]")+"]:checked").val();
                                    }else {
                                        $content[value["fieldname"]] = $("#" + value["id"]).val();
                                    }
                                }
                            });

                            if($tbody_bool && Object.keys($content).length){
                                var pattern2 = "\\\{"+$tzpricing_table_id+"\\\}";
                                var regex2   = new RegExp(pattern2,"gi");
                                $tbody_row_html_clone   = $tbody_row_html_clone.replace(regex2,htmlspecialchars(JSON.stringify($content)));
                                if($tzpricing_position > -1 ) {
                                    $("#" + $tzpricing_table_id + "-content .tz_pricing-table-table tbody tr")
                                        .eq( $tzpricing_position).after($tbody_row_html_clone).remove();
                                    $tzpricing_position = -1;
                                }else {
                                    $("#" + $tzpricing_table_id + "-content .tz_pricing-table-table tbody").prepend($tbody_row_html_clone);
                                }

                                // Call trigger reset form
                                $("#'.$id.'-content .tz_btn-reset").trigger("click");

                                tzPricingTableAction();
                            }

                        });
                        // Reset form
                        $("#'.$id.'-content .tz_btn-reset").bind("click",function(){
                            if($tbody_control_id.length) {
                                $.each($tbody_control_id, function (key, value) {
                                    var input_name  = value["name"].replace(/\\[/,"\\\[")
                                        .replace(/\\]/,"\\\]");
                                    if (value["type"].toLowerCase() == "editor") {
                                        // tinyMCE.activeEditor.getContent();
                                        //WFEditor.getContent(id)
                                        '.$resetEditor.'
                                        $("#" + value["id"]).val("");
                                    } else {
                                        if($("[name=" + input_name + "]").prop("tagName").toLowerCase() == "select") {
                                            $("#" + value["id"]).val(value["default"])
                                                .trigger("liszt:updated");
                                        }else{
                                            if($("[name=" + input_name + "]").prop("tagName").toLowerCase() == "input"
                                                && $("[name=" + input_name + "]").prop("type") == "radio") {
                                                $("[name=" + input_name + "]").removeAttr("checked");
                                                $("#" + value["id"]+" label[for=" + $("[name=" + input_name + "][value="
                                                        + value["default"] +"]").attr("id")
                                                    +"]").trigger("click");
                                            }else {
                                                $("#" + value["id"]).val(value["default"]);
                                            }
                                        }
                                    }
                                });
                                $tzpricing_position = -1;
                            }
                        });

                        function tzPricingTableAction() {
                            // Edit data
                            $("#'.$id.'-content .tz_btn-edit").unbind("click").bind("click", function () {
                                var $hidden_value = $(this).parents("td").first()
                                    .find("input[name=\\"" + $hidden_name + "\\"]").val();
                                if ($hidden_value.length) {
                                    var $hidden_obj_value = $.parseJSON($hidden_value);
                                    if ($tbody_control_id.length) {
                                        $.each($tbody_control_id, function (key, value) {
                                            var input_name  = value["name"].replace(/\\[/,"\\\[")
                                                .replace(/\\]/,"\\\]");
                                            if (value["type"].toLowerCase() == "editor") {
                                                '.$editEditor.'
                                                $("#" + value["id"]).val($hidden_obj_value[value["fieldname"]]);
                                            } else{
                                                if($("[name=" + input_name + "]").prop("tagName").toLowerCase() == "select") {
                                                    $("#" + value["id"]).val($hidden_obj_value[value["fieldname"]])
                                                        .trigger("liszt:updated");
                                                }else{
                                                    if($("[name=" + input_name + "]").prop("tagName").toLowerCase() == "input"
                                                    && $("[name=" + input_name + "]").prop("type") == "radio") {
                                                        $("[name=" + input_name + "]").removeAttr("checked");
                                                        $("#" + value["id"]+" label[for=" + $("[name=" + input_name + "][value="
                                                            + $hidden_obj_value[value["fieldname"]] +"]").attr("id")
                                                            +"]").trigger("click");
                                                    }else {
                                                        $("#" + value["id"]).val($hidden_obj_value[value["fieldname"]]);
                                                    }
                                                }
                                            }
                                        });
                                        $tzpricing_position = $("#'.$id.'-content .tz_pricing-table-table tbody tr")
                                            .index($(this).parents("tr").first());
                                    }
                                }
                            });

                            // Remove data row
                            $("#'.$id.'-content .tz_btn-remove").unbind("click").bind("click", function () {
                                var message = confirm("'.htmlspecialchars(JText::_('COM_TZ_PORTFOLIO_PLUS_REMOVE_THIS_ITEM')).'");
                                if (message) {
                                    $(this).parents("tr").first().remove();
                                }
                            });
                        }
                        tzPricingTableAction();

                        // Sortable row
                        $("#" + $tzpricing_table_id + "-content .tz_pricing-table-table tbody").sortable({
                            cursor: "move",
                            items: "> tr",
                            revert: true,
                            handle: ".icon-move",
                            forceHelperSize: true,
                            placeholder: "ui-state-highlight"
                        });
                    });
                })(jQuery);');
        ?>

        </div>
        <?php
        $html[] = ob_get_contents();
        ob_end_clean();

        return implode("\n",$html);
    }
}