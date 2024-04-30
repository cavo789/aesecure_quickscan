<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Layout variables
 * ---------------------
 *
 * @var  string   $selector       The id of the field
 * @var  string   $minTermLength  The minimum number of characters for the tag
 * @var  boolean  $allowCustom    Can we insert custom tags?
 */

extract($displayData);

$is_j4  = COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE;

if($is_j4) {
    $doc    	= JFactory::getDocument();
    $wa = $doc->getWebAssetManager();
    $wa ->useScript('jquery');


    $html = [];
    $attr = '';

    // Initialize some field attributes.
    $attr .= $multiple ? ' multiple' : '';
    $attr .= $autofocus ? ' autofocus' : '';
    $attr .= $onchange ? ' onchange="' . $onchange . '"' : '';
    $attr .= $dataAttribute;

    // To avoid user's confusion, readonly="readonly" should imply disabled="disabled".
    if ($readonly || $disabled) {
        $attr .= ' disabled="disabled"';
    }

    $attr2  = '';
    $attr2 .= !empty($class) ? ' class="' . $class . '"' : '';
    $attr2 .= ' placeholder="' . $this->escape($hint ?: Text::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')) . '" ';
    $attr2 .= $dataAttribute;

    if ($allowCustom) {
        $attr2 .= $allowCustom ? ' allow-custom' : '';
        $attr2 .= $allowCustom ? ' new-item-prefix="#new#"' : '';
    }

    if ($remoteSearch) {
        $attr2 .= ' remote-search';
        $attr2 .= ' url="' . Uri::root() . 'index.php?option=com_tz_portfolio_plus&task=tags.searchAjax"';
        $attr2 .= ' term-key="like"';
        $attr2 .= ' min-term-length="' . $minTermLength . '"';
    }

    if ($required) {
        $attr  .= ' required class="required"';
        $attr2 .= ' required';
    }

    // Create a read-only list (no name) with hidden input(s) to store the value(s).
    if ($readonly) {
        $html[] = HTMLHelper::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $value, $id);

        // E.g. form field type tag sends $this->value as array
        if ($multiple && is_array($value)) {
            if (!count($value)) {
                $value[] = '';
            }

            foreach ($value as $val) {
                $html[] = '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($val, ENT_COMPAT, 'UTF-8') . '">';
            }
        } else {
            $html[] = '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '">';
        }
    } else // Create a regular list.
    {
        $html[] = HTMLHelper::_('select.genericlist', $options, $name, trim($attr), 'value', 'text', $value, $id);
    }

    Text::script('JGLOBAL_SELECT_NO_RESULTS_MATCH');
    Text::script('JGLOBAL_SELECT_PRESS_TO_SELECT');

    Factory::getDocument()->getWebAssetManager()
        ->usePreset('choicesjs')
        ->useScript('webcomponent.field-fancy-select');

    ?>
    <joomla-field-fancy-select <?php echo $attr2; ?>><?php echo implode($html); ?></joomla-field-fancy-select>
    <?php
}else{
    JHtml::_('jquery.framework');

    // Tags field ajax
    $chosenAjaxSettings = new Registry(
        array(
            'selector' => $selector,
            'type' => 'POST',
            'url' => JUri::root() . 'index.php?option=com_tz_portfolio_plus&task=tags.searchAjax',
            'dataType' => 'json',
            'jsonTermKey' => 'like',
            'minTermLength' => $minTermLength
        )
    );

    JHtml::_('formbehavior.ajaxchosen', $chosenAjaxSettings);

    // Allow custom values?
    if ($allowCustom) {
        Factory::getApplication() -> getDocument()->addScriptDeclaration(
            "
        jQuery(document).ready(function ($) {
    
            var customTagPrefix = '#new#';
    
            // Method to add tags pressing enter
            $('" . $selector . "_chzn input, " . $selector . "_chosen input').keyup(function(event) {
    
                // Tag is greater than the minimum required chars and enter pressed
                if (this.value && this.value.length >= " . $minTermLength . " && (event.which === 13 || event.which === 188)) {
    
                    // Search a highlighted result
                    var highlighted = $('" . $selector . "_chzn, " . $selector . "_chosen').find('li.active-result.highlighted').first();
    
                    // Add the highlighted option
                    if (event.which === 13 && highlighted.text() !== '')
                    {
                        // Extra check. If we have added a custom tag with this text remove it
                        var customOptionValue = customTagPrefix + highlighted.text();
                        $('" . $selector . " option').filter(function () { return $(this).val() == customOptionValue; }).remove();
    
                        // Select the highlighted result
                        var tagOption = $('" . $selector . " option').filter(function () { return $(this).html() == highlighted.text(); });
                        tagOption.attr('selected', 'selected');
                    }
                    // Add the custom tag option
                    else
                    {
                        var customTag = this.value;
    
                        // Extra check. Search if the custom tag already exists (typed faster than AJAX ready)
                        var tagOption = $('" . $selector . " option').filter(function () { return $(this).html() == customTag; });
                        if (tagOption.text() !== '')
                        {
                            tagOption.attr('selected', 'selected');
                        }
                        else
                        {
                            var option = $('<option>');
                            option.text(this.value).val(customTagPrefix + this.value);
                            option.attr('selected','selected');
    
                            // Append the option and repopulate the chosen field
                            $('" . $selector . "').append(option);
                        }
                    }
    
                    this.value = '';
                    $('" . $selector . "').trigger('liszt:updated');
                    $('" . $selector . "').trigger('chosen:updated');
                    event.preventDefault();
    
                }
            });
        });
        "
        );
    }
}