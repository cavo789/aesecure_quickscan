<?php
/**
 * @package   JD Simple Contact Form
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2021 Joomdev, Inc. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// no direct access
defined('_JEXEC') or die;
extract($displayData);
$attrs = [];
$attrs[] = 'id="' . $field->id . '"';
if (isset($field->placeholder) && !empty($field->placeholder)) {
    $attrs[] = 'placeholder="' . $field->placeholder . '"';
}

if (!empty($field->id)) {
    $attrs[] = 'id="' . $field->id . '"';
}

if ($field->required) {
    $attrs[] = 'required';
    if (isset($field->custom_error) && !empty(trim($field->custom_error))) {
        $attrs[] = 'data-parsley-required-message="' . JText::sprintf($field->custom_error) . '"';
    } else {
        $attrs[] = 'data-parsley-required-message="' . JText::sprintf('MOD_JDSCF_REQUIRED_ERROR', strip_tags($label)) . '"';
    }
}

$document = JFactory::getDocument();
$style = 'label.calendar_icon {'
    . 'display: inherit;'
    . 'cursor: pointer;'
    . 'margin: 0px;'
    . 'border-radius: 0;'
    . '}';
$document->addStyleDeclaration($style);
?>

<div class="input-group mb-2">
	<input type="text" name="jdscf[<?php echo $field->name; ?>]" class="form-control" <?php echo implode(' ', $attrs); ?> autocomplete="off" />
    <label class="calendar_icon" for="<?php echo $field->id; ?>">
    	<div class="input-group-prepend">
    	  <div class="input-group-text">
    	  	<img height="16" width="16" src="data:image/svg+xml;base64,PHN2ZyBhcmlhLWhpZGRlbj0idHJ1ZSIgZm9jdXNhYmxlPSJmYWxzZSIgZGF0YS1wcmVmaXg9ImZhciIgZGF0YS1pY29uPSJjYWxlbmRhci1hbHQiIGNsYXNzPSJzdmctaW5saW5lLS1mYSBmYS1jYWxlbmRhci1hbHQgZmEtdy0xNCIgcm9sZT0iaW1nIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA0NDggNTEyIj48cGF0aCBmaWxsPSJjdXJyZW50Q29sb3IiIGQ9Ik0xNDggMjg4aC00MGMtNi42IDAtMTItNS40LTEyLTEydi00MGMwLTYuNiA1LjQtMTIgMTItMTJoNDBjNi42IDAgMTIgNS40IDEyIDEydjQwYzAgNi42LTUuNCAxMi0xMiAxMnptMTA4LTEydi00MGMwLTYuNi01LjQtMTItMTItMTJoLTQwYy02LjYgMC0xMiA1LjQtMTIgMTJ2NDBjMCA2LjYgNS40IDEyIDEyIDEyaDQwYzYuNiAwIDEyLTUuNCAxMi0xMnptOTYgMHYtNDBjMC02LjYtNS40LTEyLTEyLTEyaC00MGMtNi42IDAtMTIgNS40LTEyIDEydjQwYzAgNi42IDUuNCAxMiAxMiAxMmg0MGM2LjYgMCAxMi01LjQgMTItMTJ6bS05NiA5NnYtNDBjMC02LjYtNS40LTEyLTEyLTEyaC00MGMtNi42IDAtMTIgNS40LTEyIDEydjQwYzAgNi42IDUuNCAxMiAxMiAxMmg0MGM2LjYgMCAxMi01LjQgMTItMTJ6bS05NiAwdi00MGMwLTYuNi01LjQtMTItMTItMTJoLTQwYy02LjYgMC0xMiA1LjQtMTIgMTJ2NDBjMCA2LjYgNS40IDEyIDEyIDEyaDQwYzYuNiAwIDEyLTUuNCAxMi0xMnptMTkyIDB2LTQwYzAtNi42LTUuNC0xMi0xMi0xMmgtNDBjLTYuNiAwLTEyIDUuNC0xMiAxMnY0MGMwIDYuNiA1LjQgMTIgMTIgMTJoNDBjNi42IDAgMTItNS40IDEyLTEyem05Ni0yNjB2MzUyYzAgMjYuNS0yMS41IDQ4LTQ4IDQ4SDQ4Yy0yNi41IDAtNDgtMjEuNS00OC00OFYxMTJjMC0yNi41IDIxLjUtNDggNDgtNDhoNDhWMTJjMC02LjYgNS40LTEyIDEyLTEyaDQwYzYuNiAwIDEyIDUuNCAxMiAxMnY1MmgxMjhWMTJjMC02LjYgNS40LTEyIDEyLTEyaDQwYzYuNiAwIDEyIDUuNCAxMiAxMnY1Mmg0OGMyNi41IDAgNDggMjEuNSA0OCA0OHptLTQ4IDM0NlYxNjBINDh2Mjk4YzAgMy4zIDIuNyA2IDYgNmgzNDBjMy4zIDAgNi0yLjcgNi02eiI+PC9wYXRoPjwvc3ZnPg==" alt="Calendar">
    	  </div>
    	</div>
    </label>
</div>

<?php

$js = 'var monthNames = [ "'. JText::_("MOD_JDSCF_JANUARY") .'", "'. JText::_("MOD_JDSCF_FEBRUARY") .'", "'. JText::_("MOD_JDSCF_MARCH") .'", "'. JText::_("MOD_JDSCF_APRIL") .'", "'. JText::_("MOD_JDSCF_MAY") .'", "'. JText::_("MOD_JDSCF_JUNE") .'", "'. JText::_("MOD_JDSCF_JULY") .'", "'. JText::_("MOD_JDSCF_AUGUST") .'", "'. JText::_("MOD_JDSCF_SEPTEMBER") .'", "'. JText::_("MOD_JDSCF_OCTOBER") .'", "'. JText::_("MOD_JDSCF_NOVEMBER") .'", "'. JText::_("MOD_JDSCF_DECEMBER") .'" ];';

$js .= 'var weekDays = [ "'. JText::_("MOD_JDSCF_SUNDAY") .'", "'. JText::_("MOD_JDSCF_MONDAY") .'", "'. JText::_("MOD_JDSCF_TUESDAY") .'", "'. JText::_("MOD_JDSCF_WEDNESDAY") .'", "'. JText::_("MOD_JDSCF_THURSDAY") .'", "'. JText::_("MOD_JDSCF_FRIDAY") .'", "'. JText::_("MOD_JDSCF_SATURDAY") .'" ];';

$js .= 'var shortWeekDays    = [ "'. JText::_("MOD_JDSCF_SUN") .'", "'. JText::_("MOD_JDSCF_MON") .'", "'. JText::_("MOD_JDSCF_TUE") .'", "'. JText::_("MOD_JDSCF_WED") .'", "'. JText::_("MOD_JDSCF_THUR") .'", "'. JText::_("MOD_JDSCF_FRI") .'", "'. JText::_("MOD_JDSCF_SAT") .'" ];';

$js .= 'var jdscf_picker_' . $module->id . ' = new Pikaday({'
. 'field: document.getElementById("' . $field->id . '")';
if (isset($field->calendar_min) && !empty($field->calendar_min) && $field->calendar_min != '0000-00-00 00:00:00') {
    $js .= ',minDate: moment("' . $field->calendar_min . '").toDate()';
}
if (isset($field->calendar_max) && !empty($field->calendar_max) && $field->calendar_max != '0000-00-00 00:00:00') {
    $js .= ',maxDate: moment("' . $field->calendar_max . '").toDate()';
}
if (isset($field->calendar_format) && !empty($field->calendar_format)) {
    $js .= ',format: "' . $field->calendar_format . '"';
} else {
    $js .= ',format: "MM-DD-YYYY"';
}
$js .= ',i18n: {';
$js .= 'months       : monthNames,';
$js .= 'weekdays     : weekDays,';
$js .= 'weekdaysShort: shortWeekDays';
$js .= '}';
$js .= ',defaultDate: moment("' . date('Y-m-d') . '").toDate(),setDefaultDate:true';
$js .= '});';

ModJDSimpleContactFormHelper::addJS($js, $module->id);
?>