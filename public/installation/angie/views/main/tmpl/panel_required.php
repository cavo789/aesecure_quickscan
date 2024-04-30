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
		<h3><?php echo AText::_('MAIN_HEADER_REQUIRED') ?></h3>
	</header>
	<p><?php echo AText::_('MAIN_LBL_REQUIRED') ?></p>
	<table class="akeeba-table--striped" width="100%">
		<thead>
		<tr>
			<th><?php echo AText::_('MAIN_LBL_SETTING') ?></th>
			<th><?php echo AText::_('MAIN_LBL_CURRENT_SETTING') ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($this->reqSettings as $key => $option): ?>
			<tr id="required-settings-<?php echo $key ?>">
				<td>
					<label style="width:250px">
						<?php echo $option['label']; ?>
					</label>
					<?php if (array_key_exists('notice',$option) && $option['notice']): ?>
						<div class="akeeba-help-text">
							<?php echo $option['notice']; ?>
						</div>
					<?php endif; ?>
				</td>
				<td>
						<span class="akeeba-label--<?php echo $option['current'] ? 'success' : 'failure'; ?>">
							<?php echo $option['current'] ? AText::_('GENERIC_LBL_YES') : AText::_('GENERIC_LBL_NO'); ?>
						</span>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
