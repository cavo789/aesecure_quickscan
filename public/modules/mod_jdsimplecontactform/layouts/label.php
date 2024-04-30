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
$label = ModJDSimpleContactFormHelper::getLabelText($field);
$show_label = $field->show_label === null ? 1 : $field->show_label;
?>
<label for="<?php echo $field->id; ?>" class="<?php echo $show_label ? 'd-block' : 'd-none'; ?>">
   <?php echo $label; ?>
   <?php if ($field->required) { ?>
      <small class="text-danger">*</small>
   <?php } ?>
</label>