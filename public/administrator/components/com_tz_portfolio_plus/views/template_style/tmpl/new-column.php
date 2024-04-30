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

$class  = '';
$item   = $this -> columnItem;
$columnToolId   = 'columntools-'.uniqid(rand());

if($item){
    if($this -> get_value($item,"type")=='component' or $this -> get_value($item,"type")=='message'){
        $class  .= 'type-'.$this -> get_value($item,"type");
    }
    if($this -> get_value($item,"col-lg")){
        $class  .=  ' span'.$this -> get_value($item,"col-lg");
        $class  .=  ' col-md-'.$this -> get_value($item,"col-lg");
    }
    if(!empty($item->{"col-lg-offset"})){
        $class  .= ' offset'.$item ->{"col-lg-offset"};
    }
}
?>

<div class="column <?php echo $class; ?>">

    <span class="position-name"><?php echo $item?$this -> get_value($item,"type"):JText::_('JNONE'); ?></span>
    <div id="<?php echo $columnToolId; ?>" class="columntools">
        <a href="#columnsettingbox" rel="popover" data-placement="bottom" data-container="#<?php
        echo $columnToolId; ?>" data-bs-placement="bottom" data-bs-container="#<?php
        echo $columnToolId; ?>"
           title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_COLUMN_SETTINGS');?>" class="tps tp-cog rowcolumnspop"></a>
        <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ADD_NEW_ROW');?>" class="tps tp-bars add-rowin-column"></a>
        <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_REMOVE_COLUMN');?>" class="tps tp-times columndelete"></a>
        <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_MOVE_COLUMN');?>" class="tps tp-arrows-alt columnmove"></a>
    </div>

    <input type="hidden" class="widthinput-xs" name="" value="<?php echo $this -> get_value($item,"col-xs") ?>">
    <input type="hidden" class="widthinput-sm" name="" value="<?php echo $this -> get_value($item,"col-sm") ?>">
    <input type="hidden" class="widthinput-md" name="" value="<?php echo $this -> get_value($item,"col-md") ?>">
    <input type="hidden" class="widthinput-lg" name="" value="<?php echo $this -> get_value($item,"col-lg") ?>">
    <input type="hidden" class="offsetinput-xs" name="" value="<?php echo $this -> get_value($item,"col-xs-offset") ?>">
    <input type="hidden" class="offsetinput-sm" name="" value="<?php echo $this -> get_value($item,"col-sm-offset") ?>">
    <input type="hidden" class="offsetinput-md" name="" value="<?php echo $this -> get_value($item,"col-md-offset") ?>">
    <input type="hidden" class="offsetinput-lg" name="" value="<?php echo $this -> get_value($item,"col-lg-offset") ?>">
    <input type="hidden" class="typeinput" name="" value="<?php echo $this -> get_value($item,"type") ?>">
    <input type="hidden" class="customclassinput" name="" value="<?php echo $this -> get_value($item,"customclass") ?>">
    <input type="hidden" class="responsiveclassinput" name="" value="<?php echo $this -> get_value($item,"responsiveclass") ?>">
    <?php
    if( $item && !empty($item -> children) and is_array($item -> children) ){
        $this -> state -> set('template.rowincolumn', true);

        foreach($item -> children as $children) {
            $this->rowItem = $children;
            $this->setLayout('new-row');
            echo $this->loadTemplate();
        }
    }
    ?>
</div>