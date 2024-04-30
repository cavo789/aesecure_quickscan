<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

$cmsCode          = defined('ANGIE_INSTALLER_NAME') ? ANGIE_INSTALLER_NAME : 'Generic';
$cmsName          = ($cmsCode === 'Generic') ? 'generic PHP site' : "$cmsCode site";
$supportedDrivers = (defined('ANGIE_DBDRIVER_ALLOWED') && is_array(ANGIE_DBDRIVER_ALLOWED))
	? ANGIE_DBDRIVER_ALLOWED
	: ['mysqli', 'pdomysql'];

$supportedDrivers = array_map(function ($x) {
	return AText::_('DATABASE_LBL_TYPE_' . $x);
}, $supportedDrivers);

$lastDriver           = array_pop($supportedDrivers);
$manyDrivers          = count($supportedDrivers) >= 1;
$joiner               = $manyDrivers ? ' or ' : '';
$supportedDriversText = implode(', ', $supportedDrivers) . $joiner . $lastDriver;

?>
<div class="akeeba-panel--red">
	<header class="akeeba-block-header">
		<h3>
			<?= AText::sprintf('DATABASE_NOCONNECTORS_PAGE_HEADER', $supportedDriversText, PHP_VERSION) ?></h3>
	</header>
	<div>
		<p>
			<?= AText::sprintf('DATABASE_NOCONNECTORS_LBL_NEEDS_DB', $cmsName) ?>
		</p>
		<p>
			<?= AText::sprintf('DATABASE_NOCONNECTORS_LBL_DB_REQUIREMENTS', $cmsName) ?>
		</p>
		<ol>
			<li><?= AText::_('DATABASE_NOCONNECTORS_LBL_DB_REQUIREMENT_ONE') ?></li>
			<li><?= AText::_('DATABASE_NOCONNECTORS_LBL_DB_REQUIREMENT_TWO') ?></li>
		</ol>
		<p>
			<?= AText::_('DATABASE_NOCONNECTORS_LBL_DB_REQUIREMENT_NOTMET') ?>
		</p>
		<p>
			<?= AText::sprintf('DATABASE_NOCONNECTORS_LBL_DB_REQUIREMENT_NOTMET_WHY', $cmsName, $supportedDriversText, PHP_VERSION) ?>
		</p>
		<div class="akeeba-block--info large">
			<p>
				<?= AText::sprintf('DATABASE_NOCONNECTORS_LBL_CONTACTHOST_BEFORE', $supportedDriversText, PHP_VERSION) ?>
			</p>
			<p>
				<?= AText::_('DATABASE_NOCONNECTORS_LBL_CONTACTHOST_AFTER') ?>
			</p>
		</div>
		<hr />
		<h5><?= AText::_('DATABASE_NOCONNECTORS_FURTHER_HEAD') ?></h5>
		<p class="small">
			<?= AText::sprintf('DATABASE_NOCONNECTORS_LBL_THIS_IS_DEFINITELY_NOT_A_BUG', PHP_VERSION, $cmsName) ?>
		</p>
		<p class="small">
			<?= AText::sprintf('DATABASE_NOCONNECTORS_LBL_PHP_VERSIONS_HAVE_DIFF_CONFIGS', PHP_VERSION) ?>
		</p>
		<p class="small">
			<?= AText::_('DATABASE_NOCONNECTORS_LBL_I_ALREADY_TOLD_YOU_SO') ?>
		</p>
		<p class="small">
			<?= AText::_('DATABASE_NOCONNECTORS_LBL_THX') ?>
		</p>
	</div>
</div>
