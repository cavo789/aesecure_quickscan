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
    $switcherLabel      = null;
    $switcher           = $this -> params -> get('switcher', 0);
    $number_columns     = $this->params->get("number_columns", 0);
    $bootstrap_style    = $this -> params -> get('bootstrap_style',1);
    $versionCompare     = COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE;

    $class  = null;
    $attrib = null;
    if($bootstrap_style){
        $class  = 'btn-group radio';
        $attrib = ' data-toggle="buttons"';
    }

    if(!$versionCompare){
        $switcher   = false;
    }

    if($switcher){
        $class  = '';
        $attrib = null;
        JHtml::_('script', 'system/fields/switcher.js', array('version' => 'auto', 'relative' => true));
    }
?>

<fieldset id="<?php echo $this -> getId(); ?>" class="<?php echo (!$versionCompare)?$class:'';
?>"<?php echo (!$versionCompare)?$attrib:'';?>>
    <?php if($number_columns && !$bootstrap_style){ ?>
    <ul class="radio-box" style="">
    <?php } ?>

    <?php if($switcher){?>
    <span class="js-switcher switcher">
    <?php }else{ ?>
    <div class="<?php echo $class; ?>"<?php echo $attrib;?>>
    <?php } ?>

    <?php foreach($options as $key => $option){ ?>
        <?php
        if ($option->text == strtoupper($option->text))
        {
            $text = JText::_($option->text);
        }
        else
        {
            $text = $option->text;
        }
        $text = htmlspecialchars($text, ENT_COMPAT, 'UTF-8');

        $this->setAttribute("value", htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8'), "input");

        if($switcher && $key == 0){
            $this -> setAttribute("class", "active", "input");
        }else{
            $this -> setAttribute("class", "", "input");
        }

        if (($value && $option->value === $value) || ($switcher && !$value && $key == 0))
        {
            $this->setAttribute("checked", "checked", "input");
        }
        else
        {
            $this->setAttribute("checked", null, "input");
        }

        if ((isset($option->disabled) && $option->disabled))
        {
            $this->setAttribute("disabled", "disabled", "input");
        }
        else
        {
            $this->setAttribute("disabled", null, "input");
        }
        ?>
        <?php if(!$switcher){ ?>
            <?php if($number_columns && !$bootstrap_style){ ?>
                <?php
                $width = 100 / (int) $number_columns;
                ?>
            <li style="width: <?php echo $width; ?>%; float: left; clear: none;" >
            <?php } ?>

            <label class="<?php echo ($bootstrap_style?'btn btn-default':'inline'); ?> radio" for="<?php
            echo $this->getId() . $key; ?>">
        <?php } ?>
            <input id="<?php echo $this->getId() . $key; ?> " name="<?php echo $this->getName(); ?>" <?php
            echo $this->getAttribute(null, null, "input"); ?> /><?php
        if(!$switcher) {
            echo $text;
        }
        ?>

        <?php if($switcher){
            $switcherLabel  .= '<span class="switcher-label-'.$option->value.'">'.$option->text.'</span>';
            ?>

        <?php
        }else{ ?>
            </label>
            <?php if($number_columns && !$bootstrap_style){ ?>
            </li>
            <?php } ?>
        <?php } ?>
    <?php } ?>

    <?php if($switcher){?>
        <span class="switch"></span>
    </span>

        <?php if($switcherLabel){ ?>
        <span class="switcher-labels">
            <?php echo $switcherLabel; ?>
        </span>
        <?php } ?>
    <?php }else{ ?>
    </div>
    <?php } ?>
    <?php if($number_columns){ ?>
    </ul>
    <?php } ?>
</fieldset>
<?php
}