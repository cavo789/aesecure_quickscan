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
<div class="akeeba-panel--info" style="margin-top: 0">
    <header class="akeeba-block-header">
        <h3><?php echo AText::_('MAIN_HEADER_EXTRAINFO') ?></h3>
    </header>
    <p><?php echo AText::_('MAIN_LBL_EXTRAINFO') ?></p>
	<?php if (empty($this->extraInfo)): ?>
        <div class="akeeba-block--warning">
			<?php echo AText::_('MAIN_ERR_EXTRAINFO') ?>
        </div>
	<?php else: ?>
        <table class="akeeba-table--striped" width="100%">
            <thead>
            <tr>
                <th width="40%">
					<?php echo AText::_('MAIN_LBL_SETTING') ?>
                </th>
                <th>
					<?php echo AText::_('MAIN_LBL_BACKUP_SETTING') ?>
                </th>
            </tr>
            </thead>
            <tbody>
			<?php foreach ($this->extraInfo as $option): ?>
                <tr>
                    <td>
                        <label>
							<?php echo $option['label']; ?>
                        </label>
                    </td>
                    <td>
						<?php echo $option['current'] ?>
                    </td>
                </tr>
			<?php endforeach; ?>
			<?php if (@file_exists('README.html')): ?>
                <tr>
                    <td></td>
                    <td>
                        <button type="button" onclick="mainOpenReadme()" class="akeeba-btn--ghost">
                            <span class="akion-document-text"></span>
							<?php echo AText::_('MAIN_BTN_OPENREADME') ?>
                        </button>
                        <br/>
                        <span class="akeeba-help-text">
                        <?php echo AText::_('MAIN_LBL_OPENREADME'); ?>
                    </span>
                    </td>
                </tr>
			<?php endif; ?>
            </tbody>
        </table>
	<?php endif; ?>
</div>
