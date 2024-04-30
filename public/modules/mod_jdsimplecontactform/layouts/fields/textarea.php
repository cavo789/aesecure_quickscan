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
if ($field->required) {
    $attrs[] = 'required';
    if (isset($field->custom_error) && !empty(trim($field->custom_error))) {
       $attrs[] = 'data-parsley-required-message="' . JText::sprintf($field->custom_error) . '"';
    } else {
       $attrs[] = 'data-parsley-required-message="' . JText::sprintf('MOD_JDSCF_REQUIRED_ERROR', strip_tags($label)) . '"';
    }
 }
if (isset($field->placeholder) && !empty($field->placeholder)) {
   $attrs[] = 'placeholder="' . $field->placeholder . '"';
}
?>
<textarea class="form-control" rows="<?php echo $field->textarearows; ?>" name="jdscf[<?php echo $field->name; ?>]" <?php echo implode(' ', $attrs); ?>></textarea>