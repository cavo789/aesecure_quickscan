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
$singleCCName = $params->get('singleSendCopyEmail_field', '');
$singleCCTitle = $params->get('singleSendCopyEmailField_title', 'MOD_JDSCF_SINGLE_SEND_COPY_LBL_TITLE');
?>
<div class="form-group form-check">
    <input id="<?php echo $singleCCName; ?>" type="checkbox" name="jdscf[<?php echo $singleCCName; ?>][single_cc]" value="1" />
    <label for="<?php echo $singleCCName; ?>" class="form-check-label"><?php echo JText::_($singleCCTitle); ?></label>
</div>