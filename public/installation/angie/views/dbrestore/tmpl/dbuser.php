<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

/**
 * Error: The database host name, user name or password is wrong
 *
 * @var AngieViewDbrestore $this
 */

?>

<h4><?= AText::_('DATABASE_RESTORE_DBUSER_HEAD') ?></h4>

<p>
	<?= AText::_('DATABASE_RESTORE_DBUSER_LBL_EXPLAIN') ?>
</p>
<p>
	<?= AText::_('DATABASE_RESTORE_COMMON_LBL_ERROR_RECEIVED') ?>
</p>

<div class="akeeba-panel--red">
	<div>
		<?php $exc = $this->exception;
		do {
		?>
			<p>
				<?= $exc->getMessage() ?>
			</p>
		<?php
		} while ($exc = $exc->getPrevious());
		?>
	</div>
</div>

<h4 class="dbrestore-hideable">
	<span>
		<?= AText::_('DATABASE_RESTORE_COMMON_LBL_HOWTOFIX') ?>
	</span>
	<button type="button"
			class="akeeba-btn--ghost--small"
			data-target="dbrestore-how-to-fix"
	>
		<?= AText::_('DATABASE_RESTORE_COMMON_LBL_SHOWHIDE') ?>
	</button>
</h4>

<div id="dbrestore-how-to-fix">
	<ul>
		<li>
			<?= AText::_('DATABASE_RESTORE_DBUSER_SUGGESTION_1') ?>
		</li>
		<li>
			<?= AText::_('DATABASE_RESTORE_DBUSER_SUGGESTION_2') ?>
		</li>
		<li>
			<?= AText::_('DATABASE_RESTORE_DBUSER_SUGGESTION_3') ?>
		</li>
		<li>
			<?= AText::_('DATABASE_RESTORE_DBUSER_SUGGESTION_4') ?>
		</li>
		<li>
			<?= AText::_('DATABASE_RESTORE_DBUSER_SUGGESTION_5') ?>
		</li>
		<li>
			<?= AText::_('DATABASE_RESTORE_DBUSER_SUGGESTION_6') ?>
		</li>
	</ul>
</div>

<h4 class="dbrestore-hideable">
	<span>
		<?= AText::_('DATABASE_RESTORE_COMMON_LBL_DEBUG') ?>
	</span>
	<button type="button"
			class="akeeba-btn--ghost--small"
			data-target="dbrestore-debug"
	>
		<?= AText::_('DATABASE_RESTORE_COMMON_LBL_SHOWHIDE') ?>
	</button>
</h4>

<div id="dbrestore-debug">
	<p>
		<?= AText::_('DATABASE_RESTORE_COMMON_LBL_PLSINCLUDE') ?>
	</p>

	<?php
	$exc = $this->exception;
	do {
		?>
		<h5>
			<?= $exc->getMessage() ?>
		</h5>
		<p>
			<?= $exc->getFile() ?>:<?= $exc->getLine() ?>
		</p>
		<pre><?= $exc->getTraceAsString() ?></pre>
		<?php
	} while ($exc = $exc->getPrevious()) ?>
</div>
