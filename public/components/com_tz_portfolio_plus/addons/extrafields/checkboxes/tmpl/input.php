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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$html = "";
if ($options)
{
    $number_columns = $this->params->get("number_columns", 0);
?>
<fieldset id="<?php echo $this -> getId()?>" class="checkboxes <?php echo $this -> getInputClass(); ?>">
    <?php if($number_columns){ ?>
    <ul class='nav'>
    <?php }?>

    <?php foreach ($options AS $key => $option){
        $optText        = null;
        $optValue       = null;
        $optDisabled    = null;

        if(is_object($option)){
            $optText    = $option -> text;
            $optValue   = $option -> value;
            if ((isset($option->disabled) && $option->disabled))
            {
                $optDisabled    = $option -> disabled;
            }
        }else{
            $optText    = $option['text'];
            $optValue   = $option['value'];
            if ((isset($option['disabled']) && $option['disabled']))
            {
                $optDisabled    = $option['disabled'];
            }
        }
        ?>

        <?php if($number_columns){ ?>
        <?php
            $width = 100 / (int) $number_columns;
        ?>
        <li style="width: <?php echo $width; ?>%; float: left; clear: none;">
        <?php }?>
            <?php
            if ($optText == strtoupper($optText))
            {
                $text = JText::_($optText);
            }
            else
            {
                $text = $optText;
            }

            $this->setAttribute("value", htmlspecialchars($optValue, ENT_COMPAT, 'UTF-8'), "input");
            $this -> setAttribute("class", "form-check-input", "input");

            if (in_array($optValue, $value))
            {
                $this->setAttribute("checked", "checked", "input");
            }
            else
            {
                $this->setAttribute("checked", null, "input");
            }

            if ((isset($optDisabled) && $optDisabled))
            {
                $this->setAttribute("disabled", "disabled", "input");
            }
            else
            {
                $this->setAttribute("disabled", null, "input");
            }
            ?>
            <?php ?>
            <div class="<?php if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) { echo "form-check form-check-inline"; }
            else{ echo "checkbox inline";}
            ?>">
                <label for="<?php echo $this -> getId().$key; ?>" class="<?php
                echo (COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE?"form-check-label":"checkbox inline"); ?>">
                    <input id="<?php echo $this -> getId().$key; ?>" name="<?php echo $this -> getName();?>" <?php
                    echo $this -> getAttribute(null, null, "input"); ?>/><?php echo ' '.$text; ?></label>
            </div>
        <?php if($number_columns){ ?>
        </li>
        <?php }?>
    <?php } ?>

    <?php if($number_columns){ ?>
    </ul>
    <?php } ?>
</fieldset>
<?php
}