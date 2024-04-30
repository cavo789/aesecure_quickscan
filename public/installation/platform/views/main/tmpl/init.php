<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var  AngieViewMain  $this */

defined('_AKEEBA') or die();

echo $this->loadAnyTemplate('steps/steps', array(
	'helpurl' => 'https://www.akeeba.com/documentation/solo/angie-joomla.html#angie-joomla-first',
	'videourl' => 'https://www.akeeba.com/videos/1212-akeeba-backup-core/1618-abtc04-restore-site-new-server.html'
));
?>

<?php if (!$this->reqMet): ?>
<div class="akeeba-block--failure">
	<?php echo AText::_('MAIN_LBL_REQUIREDREDTEXT'); ?>
</div>
<?php endif; ?>

<div class="akeeba-container--50-50">
	<?php echo $this->loadAnyTemplate('init/panel_required', []); ?>
	<?php echo $this->loadAnyTemplate('init/panel_recommended', []); ?>
</div>

<div class="akeeba-container--50-50">
	<?php echo $this->loadAnyTemplate('init/panel_backupinfo', []); ?>
	<?php echo $this->loadAnyTemplate('init/panel_serverinfo', []); ?>
</div>
