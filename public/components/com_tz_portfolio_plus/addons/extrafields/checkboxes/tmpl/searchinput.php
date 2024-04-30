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
?>
<fieldset id="<?php echo $this -> getSearchId();?>" class="checkboxes <?php echo $this -> getInputClass();?>">
    <label class="group-label"><?php echo $this -> getTitle();?></label>
    <ul class="list-unstyled list-extrafield mb-0">
        <?php foreach ($options AS $key => $option){
            if ($option->text == strtoupper($option->text))
            {
                $text = JText::_($option->text);
            }
            else
            {
                $text = $option->text;
            }

            $this->setAttribute("value", htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8'), "search");

            if (in_array($option->value, $value))
            {
                $this->setAttribute("checked", "checked", "search");
            }
            else
            {
                $this->setAttribute("checked", null, "search");
            }

            if (isset($option->disabled) && $option->disabled)
            {
                $this->setAttribute("disabled", "disabled", "search");
            }
            else
            {
                $this->setAttribute("disabled", null, "search");
            }
            ?>
            <li>
                <label class="checkbox" for="<?php echo $this -> getSearchId().$key;?>">
                <input id="<?php echo $this -> getSearchId().$key;?>" name="<?php echo $this -> getSearchName();?>"<?php
                echo $this->getAttribute(null, null, "search");
                ?>/> <?php echo $text;?></label>
            </li>
        <?php }?>
    </ul>
</fieldset>
<?php
}