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
$buttonText = $params->get('submittext', 'JSUBMIT');
$buttonClass = $params->get('submitclass', 'btn-primary');
$buttonWidth = $params->get('submit_btn_width', '12');
?>
<div class="jdscf-submit-btn jdscf-col-md-<?php echo $buttonWidth ?>">
   <button type="submit" class="btn<?php echo!empty($buttonClass) ? ' ' . $buttonClass : ''; ?> btn-block"><?php echo JText::_($buttonText); ?></button>
</div>