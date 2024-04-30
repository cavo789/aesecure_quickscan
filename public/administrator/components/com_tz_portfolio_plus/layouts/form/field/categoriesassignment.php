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

extract($displayData);


if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
    $doc    	= JFactory::getDocument();
    $wa = $doc->getWebAssetManager();
    $wa ->useScript('jquery');
}else{
    JHtml::_('jquery.framework');
}

?>
<fieldset id="<?php echo $id; ?>" class="checkboxes <?php echo $class; ?>"<?php echo $required?' required aria-required="true"':'';
echo $autofocus?' autofocus':'' ; ?>>
    <div class="btn-toolbar">
        <button class="btn btn-sm btn-secondary jform-rightbtn mb-2" type="button" onclick="jQuery('.chk-category').attr('checked', !jQuery('.chk-category').attr('checked'));">
            <i class="icon-checkbox-partial"></i> <?php echo JText::_('JGLOBAL_SELECTION_INVERT_ALL'); ?>
        </button>
    </div>

    <ul class="menu-links list-unstyled">
        <?php
        $colItems   = 10;
        foreach ($options as $i => $option)
        {
            // Initialize some option attributes.
            if (!isset($value) || empty($value))
            {
                $checked = (in_array((string) $option->value, (array) $checkedOptions) ? ' checked="checked"' : '');
            }
            else
            {
                $_value = !is_array($value) ? explode(',', $value) : $value;
                $checked = (in_array((string) $option->value, $_value) ? ' checked="checked"' : '');
            }

            $checked = empty($checked) && $option->checked ? ' checked="checked"' : $checked;

            $optDisabled = !empty($option->disable) || $disabled ? ' disabled' : '';

            // Initialize some JavaScript option attributes.
            $onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
            $onchange = !empty($option->onchange) ? ' onchange="' . $option->onchange . '"' : '';
            ?>
            <?php if($i % $colItems == 0){ ?>
            <li>
            <div class="menu-links-block">
        <?php } ?>
            <label for="<?php echo $id . $i; ?>" class="chk-category small d-block pt-1 pb-1 <?php
            if(isset($option ->class)){echo $option->class; } ?>">
                <input type="checkbox" id="<?php echo $id . $i; ?>" name="<?php echo $name; ?>" value="<?php
                echo htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8'); ?>"<?php
                echo $checked . $onclick . $onchange . $optDisabled; ?> class="chk-category"/>
                <?php echo JLayoutHelper::render('joomla.html.treeprefix',
                        array('level' => $option->level)) . $option->text; ?>
            </label>
            <?php if($i % $colItems == ($colItems -1) || $i == (count($options) - 1)){ ?>
            </div>
            </li>
        <?php } ?>
        <?php } ?>
    </ul>
</fieldset>