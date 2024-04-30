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
switch ($field->type) {
   case 'email':
      $attrs[] = 'data-parsley-type="email"';
      $attrs[] = 'data-parsley-type-message="' . JText::_("MOD_JDSCF_EMAIL_REQUIRED_ERROR") . '"';
      break;
   case 'number':
      $attrs[] = 'data-parsley-type="number"';
      break;
   case 'url':
      $attrs[] = 'data-parsley-type="url"';
      break;
}
if (isset($field->placeholder) && !empty($field->placeholder)) {
   $attrs[] = 'placeholder="' . $field->placeholder . '"';
}
if ($field->type == 'text' || $field->type == 'number') {
   if (!empty($field->min_length)) {
      $attrs[] = 'data-parsley-minlength="' . $field->min_length . '"';
      $attrs[] = 'data-parsley-minlength-message="' . JText::sprintf('MOD_JDSCF_NUMBER_MIN_LENGTH_ERROR', strip_tags($label), $field->min_length) . '"';
   }
   if (!empty($field->max_length)) {
      $attrs[] = 'data-parsley-maxlength="' . $field->max_length . '"';
      $attrs[] = 'data-parsley-maxlength-message="' . JText::sprintf('MOD_JDSCF_NUMBER_MAX_LENGTH_ERROR', strip_tags($label), $field->max_length) . '"';
   }
   if ($field->type == 'number') {
      if (!empty($field->min)) {
         $attrs[] = 'data-parsley-min="' . $field->min . '"';
         $attrs[] = 'data-parsley-min-message="' . JText::sprintf('MOD_JDSCF_NUMBER_MIN_ERROR', strip_tags($label), $field->min) . '"';
      }
      if (!empty($field->max)) {
         $attrs[] = 'data-parsley-max="' . $field->max . '"';
         $attrs[] = 'data-parsley-max-message="' . JText::sprintf('MOD_JDSCF_NUMBER_MAX_ERROR', strip_tags($label), $field->max) . '"';
      }
   }
}

if ($field->required) {
   $attrs[] = 'required';
   if (isset($field->custom_error) && !empty(trim($field->custom_error))) {
      $attrs[] = 'data-parsley-required-message="' . JText::sprintf($field->custom_error) . '"';
   } else {
      $attrs[] = 'data-parsley-required-message="' . JText::sprintf('MOD_JDSCF_REQUIRED_ERROR', strip_tags($label)) . '"';
   }
}
?>
<input type="text" name="jdscf[<?php echo $field->name; ?>][<?php echo $field->type; ?>]" class="form-control" <?php echo implode(' ', $attrs); ?> />