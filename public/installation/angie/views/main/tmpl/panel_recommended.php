<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var  AngieViewMain  $this */
?>
<div class="akeeba-panel-info">
    <header class="akeeba-block-header">
        <h3><?php echo AText::_('MAIN_HEADER_RECOMMENDED') ?></h3>
    </header>
    <p><?php echo AText::_('MAIN_LBL_RECOMMENDED') ?></p>
    <table class="akeeba-table--striped" width="100%">
        <thead>
        <tr>
            <th><?php echo AText::_('MAIN_LBL_SETTING') ?></th>
            <th><?php echo AText::_('MAIN_LBL_RECOMMENDED_VALUE') ?></th>
            <th><?php echo AText::_('MAIN_LBL_CURRENT_SETTING') ?></th>
        </tr>
        </thead>
        <tbody>
		<?php foreach ($this->recommendedSettings as $option): ?>
            <tr>
                <td>
                    <label style="width:230px" class="akeeba-label--<?php echo ($option['current'] == $option['recommended']) ? 'green' : 'orange'; ?>">
						<?php echo $option['label']; ?>
                    </label>
                </td>
                <td>
						<span class="akeeba-label--grey">
							<?php echo $option['recommended'] ? AText::_('GENERIC_LBL_ON') : AText::_('GENERIC_LBL_OFF'); ?>
						</span>
                </td>
                <td>
                        <span class="akeeba-label--<?php echo ($option['current'] == $option['recommended']) ? 'success' : 'warning'; ?>">
							<?php echo $option['current'] ? AText::_('GENERIC_LBL_ON') : AText::_('GENERIC_LBL_OFF'); ?>
						</span>
                </td>
            </tr>
		<?php endforeach; ?>
        </tbody>
    </table>
</div>
