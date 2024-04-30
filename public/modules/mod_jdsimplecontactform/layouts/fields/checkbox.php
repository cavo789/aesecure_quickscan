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
$options = ModJDSimpleContactFormHelper::getOptions($field->options);
$attrs = [];
$attrs[] = 'id="' . $field->name . '-' . $module->id .'"';
if ($field->required) {
    $attrs[] = 'required';
    if (isset($field->custom_error) && !empty(trim($field->custom_error))) {
       $attrs[] = 'data-parsley-required-message="' . JText::sprintf($field->custom_error) . '"';
    } else {
       $attrs[] = 'data-parsley-required-message="' . JText::sprintf('MOD_JDSCF_REQUIRED_ERROR', strip_tags($label)) . '"';
    }
}
?>
<div class="form-check form-check-inline">
   <input class="form-check-input" type="checkbox" name="jdscf[<?php echo $field->name; ?>][cb]" value="1" <?php echo implode(' ', $attrs); ?> />
   <label class="form-check-label" for="<?php echo $field->name; ?>-<?php echo $module->id; ?>">
      <?php echo $label; ?>
   </label>
</div>