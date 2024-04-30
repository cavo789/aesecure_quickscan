<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

?>
<div class="akeeba-block--warning">
	<?php echo AText::_('FINALISE_LBL_DONTFORGETCONFIG'); ?>
</div>

<div class="akeeba-panel--info">
	<header class="akeeba-block-header">
		<h3>
			<?php echo AText::_('FINALISE_HEADER_CONFIGURATION'); ?>
		</h3>
	</header>
	<p>
		<?php echo AText::_('FINALISE_LBL_CONFIGINTRO'); ?>
	</p>
	<pre class="scrollmore"><?php echo htmlentities($this->configuration) ?></pre>
	<p>
		<?php echo AText::_('FINALISE_LBL_CONFIGOUTRO'); ?>
	</p>

</div>
<div class="akeeba-panel--orange">
	<header class="akeeba-block-header">
		<h3>
			<?php echo AText::_('FINALISE_HEADER_AFTERCONFIGURATION'); ?>
		</h3>
	</header>
