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

?>
<!-- Column setting popbox -->
<div id="columnsettingbox" style="display: none;">
    <ul class="nav nav-tab<?php echo (COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE)?' nav-tabs':'';?>" id="columnsettings">
        <li class="nav-item active"><a href="#basic" data-toggle="tab" data-bs-toggle="tab" class="active"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_BASIC');?></a></li>
        <li class="nav-item"><a href="#responsive" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_RESPONSIVE');?></a></li>
<!--        <li><a href="#animation" data-toggle="tab">--><?php //echo JText::_('COM_TZ_PORTFOLIO_PLUS_ANIMATION');?><!--</a></li>-->
    </ul>

    <div class="tab-content border-0 p-3">
        <div class="tab-pane active" id="basic">
            <div id="includetypes">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_TYPE');?>: </label>
                <?php if($this -> includeTypes && count($this -> includeTypes)){?>
                <select class="includetypes custom-select custom-select-sm">
                    <?php foreach($this -> includeTypes as $type){
                        if(is_array($type)){
                            foreach($type as $t){
                    ?>
                        <option value="<?php echo $t -> value;?>"><?php echo $t -> text;?></option>
                    <?php }
                        }else{
                    ?>
                        <option value="<?php echo $type -> value;?>"><?php echo $type -> text;?></option>
                    <?php
                        }
                    }
                    ?>

                </select>
                <?php }?>
            </div>

            <div id="spanwidth">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_WIDTH_LABEL');?>: </label>
                <select class="possiblewidths custom-select custom-select-sm">
                    <option value=""><?php echo JText::_('JNONE')?></option>
                    <option value="1">span1</option>
                    <option value="2">span2</option>
                    <option value="3">span3</option>
                    <option value="4">span4</option>
                    <option value="5">span5</option>
                    <option value="6">span6</option>
                    <option value="7">span7</option>
                    <option value="8">span8</option>
                    <option value="9">span9</option>
                    <option value="10">span10</option>
                    <option value="11">span11</option>
                    <option value="12">span12</option>
                </select>
            </div>

            <div id="spanoffset">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_OFFSET');?> </label>
                <select class="possibleoffsets custom-select custom-select-sm">
                    <option value=""><?php echo JText::_('JNONE');?></option>
                    <option value="1">offset1</option>
                    <option value="2">offset2</option>
                    <option value="3">offset3</option>
                    <option value="4">offset4</option>
                    <option value="5">offset5</option>
                    <option value="6">offset6</option>
                    <option value="7">offset7</option>
                    <option value="8">offset8</option>
                    <option value="9">offset9</option>
                    <option value="10">offset10</option>
                </select>
            </div>

            <div id="customclass" class="d-block">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_CUSTOM_CLASS');?> </label>
                <input type="text" class="form-control form-control-sm customclass" id="inputcustomclass">
            </div>
        </div>

        <div class="tab-pane" id="responsive">
            <?php echo JHtml::_('tzbootstrap.addrow');?>
            <div class="span6 col-md-6">
                <label class="checkbox"> <input type="checkbox" value="visible-lg"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_VISIBLE_LARGE');?></label>
                <label class="checkbox"> <input type="checkbox" value="visible-md"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_VISIBLE_MEDIUM');?></label>
                <label class="checkbox"> <input type="checkbox" value="visible-sm"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_VISIBLE_SMALL');?></label>
                <label class="checkbox"> <input type="checkbox" value="visible-xs"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_VISIBLE_EXTRA_SMALL');?></label>
            </div>
            <div class="span6 col-md-6">
                <label class="checkbox"> <input type="checkbox" value="hidden-lg"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_HIDDEN_LARGE');?></label>
                <label class="checkbox"> <input type="checkbox" value="hidden-md"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_HIDDEN_MEDIUM');?></label>
                <label class="checkbox"> <input type="checkbox" value="hidden-sm"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_HIDDEN_SMALL');?></label>
                <label class="checkbox"> <input type="checkbox" value="hidden-xs"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_HIDDEN_EXTRA_SMALL');?></label>
            </div>
            <?php echo JHtml::_('tzbootstrap.endrow');?>
        </div>
    </div>
</div>

<!-- Row setting popbox -->
<div id="rowsettingbox" style="display: none;">
    <h3 class="row-header"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ROW_SETTINGS');?></h3>

    <div>
        <?php echo JHtml::_('tzbootstrap.addrow');?>

            <div class="span6 col-md-6 rownameOuter">
                <label><?php echo JText::_('JFIELD_NAME_LABEL');?>: </label>
                <input type="text" class="form-control form-control-sm small rowname" id="">
            </div>

            <div class="span6 col-md-6 rowclassOuter">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_CUSTOM_CLASS');?> </label>
                <input type="text" class="form-control form-control-sm small rowcustomclass" id="">
            </div>

        <?php echo JHtml::_('tzbootstrap.endrow');?>

        <?php echo JHtml::_('tzbootstrap.addrow');?>
            <div class="span6 col-md-6 rowcolorOuter">
                <label class="fs-6"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_BACKGROUND');?> </label>
                <input type="text" class="form-control form-control-sm small rowbackgroundcolor" id="">
            </div>

            <div class="span6 col-md-6 rowcolorOuter">
                <label class="fs-6"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_TEXT');?>: </label>
                <input type="text" class="form-control form-control-sm small rowtextcolor" id="">
            </div>
        <?php echo JHtml::_('tzbootstrap.endrow');?>

        <?php echo JHtml::_('tzbootstrap.addrow');?>
            <div class="span6 col-md-6 rowcolorOuter">
                <label class="fs-6"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LINK');?>: </label>
                <input type="text" class="form-control form-control-sm small rowlinkcolor" id="">
            </div>

            <div class="span6 col-md-6 rowcolorOuter">
                <label class="fs-6"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LINK_HOVER');?>: </label>
                <input type="text" class="form-control form-control-sm small rowlinkhovercolor" id="">
            </div>
        <?php echo JHtml::_('tzbootstrap.endrow');?>

        <?php echo JHtml::_('tzbootstrap.addrow');?>
            <div class="span6 col-md-6 rownameOuter mt-2">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_MARGIN');?>: </label>
                <input type="text" class="form-control form-control-sm small rowmargin" id="">
            </div>

            <div class="span6 col-md-6 rowclassOuter mt-2">
                <label><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_PADDING');?>: </label>
                <input type="text" class="form-control form-control-sm small rowpadding" id="">
            </div>
        <?php echo JHtml::_('tzbootstrap.endrow');?>

        <?php echo JHtml::_('tzbootstrap.addrow', array("attribute" => 'id="rowresponsiveinputs"'));?>
            <div class="span6 col-md-6">
                <label class="checkbox"> <input type="checkbox" value="visible-xs"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_VISIBLE_EXTRA_SMALL');?></label>
                <label class="checkbox"> <input type="checkbox" value="visible-sm"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_VISIBLE_SMALL');?></label>
                <label class="checkbox"> <input type="checkbox" value="visible-md"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_VISIBLE_MEDIUM');?></label>
                <label class="checkbox"> <input type="checkbox" value="visible-lg"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_VISIBLE_LARGE');?></label>
            </div>
            <div class="span6 col-md-6">
                <label class="checkbox"> <input type="checkbox" value="hidden-xs"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_HIDDEN_EXTRA_SMALL');?></label>
                <label class="checkbox"> <input type="checkbox" value="hidden-sm"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_HIDDEN_SMALL');?></label>
                <label class="checkbox"> <input type="checkbox" value="hidden-md"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_HIDDEN_MEDIUM');?></label>
                <label class="checkbox"> <input type="checkbox" value="hidden-lg"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_HIDDEN_LARGE');?></label>
            </div>
        <?php echo JHtml::_('tzbootstrap.endrow');?>

    </div>
</div>