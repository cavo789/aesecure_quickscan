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
$show_label = $field->show_label === null ? 1 : $field->show_label;
if ($field->type == 'hidden') {
   echo $input;
} else {
?>
   <div class="jdscf-col-md-<?php echo $field->width; ?>">
      <div class="form-group">
         <?php echo $label; ?>
         <?php echo $input; ?>
      </div>
   </div>
<?php } ?>