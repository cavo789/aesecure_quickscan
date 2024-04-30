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


if ($field->required) {
    $attrs[] = 'required';
    if (isset($field->custom_error) && !empty(trim($field->custom_error))) {
       $attrs[] = 'data-parsley-required-message="' . JText::sprintf($field->custom_error) . '"';
    } else {
       $attrs[] = 'data-parsley-required-message="' . JText::sprintf('MOD_JDSCF_REQUIRED_ERROR', strip_tags($label)) . '"';
    }
}
?>
<select name="jdscf[<?php echo $field->name; ?>]" class="form-control" <?php echo implode(' ', $attrs); ?>>
   <?php foreach ($options as $option) { ?>
      <option value="<?php echo $option['value']; ?>"><?php echo $option['text']; ?></option>
   <?php } ?>
</select>