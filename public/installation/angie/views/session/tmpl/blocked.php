<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

/** @var AngieViewSession $this */

$rootBasename = basename(dirname(APATH_BASE));
$rootBasename = empty($rootBasename) ? 'public_html' : $rootBasename;
$mySessionId = AApplication::getInstance()->getContainer()->session->getSessionKey();
?>
<div id="angie-session-blocked">
    <div class="akeeba-block--warning">
        <h3><?php echo AText::_('SESSIONBLOCKED_HEADER_IN_USE') ?></h3>
        <p>
	        <?php echo AText::_('SESSIONBLOCKED_LBL_IN_USE_TEXT') ?>
        </p>
    </div>

    <div class="akeeba-panel--success">
        <header class="akeeba-block-header">
            <h2>
		        <?php echo AText::_('SESSIONBLOCKED_HEADER_INERROR') ?>
            </h2>
        </header>

        <ol class="bigger">
            <li><?php echo AText::_('SESSIONBLOCKED_LBL_INSTRUCTIONS_CONNECT') ?></li>
            <li><?php echo AText::sprintf('SESSIONBLOCKED_LBL_INSTRUCTIONS_GOTOROOT', $rootBasename) ?></li>
            <li><?php echo AText::_('SESSIONBLOCKED_LBL_INSTRUCTIONS_GOTOINSTALLER') ?></li>
            <li><?php echo AText::_('SESSIONBLOCKED_LBL_INSTRUCTIONS_GOTOTMP') ?></li>
            <li><?php echo AText::sprintf('SESSIONBLOCKED_LBL_INSTRUCTIONS_KEEPTHIS', $mySessionId) ?></li>
            <li><?php echo AText::_('SESSIONBLOCKED_LBL_INSTRUCTIONS_DELETETHESE') ?></li>
            <li><?php echo AText::_('SESSIONBLOCKED_LBL_INSTRUCTIONS_RELOAD') ?></li>
        </ol>
    </div>

    <h4><?php echo AText::_('SESSIONBLOCKED_HEADER_WHY_AM_I_SEEING_THIS') ?></h4>
    <p><?php echo AText::_('SESSIONBLOCKED_LBL_BECAUSE_SECURITY') ?></p>
    <p><?php echo AText::_('SESSIONBLOCKED_LBL_BECAUSE_WE_CARE') ?></p>

    <?php if (!defined('AKEEBA_PASSHASH')): ?>
    <h4><?php echo AText::_('SESSIONBLOCKED_HEADER_BEST_WAY_TO_AVOID') ?></h4>
    <p><?php echo AText::_('SESSIONBLOCKED_LBL_BEST_WAY_TO_AVOID') ?></p>
    <?php endif; ?>
</div>
