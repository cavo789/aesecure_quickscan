<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \Akeeba\Component\AkeebaBackup\Administrator\View\Controlpanel\HtmlView */

// Protect from unauthorized access
defined('_JEXEC') || die();

use Joomla\CMS\Language\Text;

?>
<div class="card mb-2">
	<h3 class="card-header">
		<?= Text::_('COM_AKEEBABACKUP_CPANEL_HEADER_BASICOPS') ?>
	</h3>

	<div class="card-body">
		<div class="d-flex flex-row flex-wrap align-items-stretch">
			<?php if ($this->permissions['backup']): ?>
				<a class="text-center align-self-stretch btn btn-outline-success border-0" style="width: 10em"
				   href="index.php?option=com_akeebabackup&view=Backup">
					<div class="bg-success text-white d-block text-center p-3 h2">
						<span class="fa fa-play"></span>
					</div>
					<span>
						<?= Text::_('COM_AKEEBABACKUP_BACKUP') ?>
					</span>
				</a>
			<?php endif ?>

			<?php if ($this->permissions['download'] && AKEEBABACKUP_PRO): ?>
				<a class="text-center align-self-stretch btn btn-outline-success border-0" style="width: 10em"
				   href="index.php?option=com_akeebabackup&view=Transfer">
					<div class="bg-success text-white d-block text-center p-3 h2">
						<span class="fa fa-external-link-alt"></span>
					</div>
					<span>
						<?= Text::_('COM_AKEEBABACKUP_TRANSFER') ?>
					</span>
				</a>
			<?php endif ?>

			<a class="text-center align-self-stretch btn btn-outline-primary border-0" style="width: 10em"
			   href="index.php?option=com_akeebabackup&view=Manage">
				<div class="bg-primary text-white d-block text-center p-3 h2">
					<span class="fa fa-list-alt"></span>
				</div>
				<span>
					<?= Text::_('COM_AKEEBABACKUP_BUADMIN') ?>
				</span>
			</a>

			<?php if ($this->permissions['configure']): ?>
				<a class="text-center align-self-stretch btn btn-outline-primary border-0" style="width: 10em"
				   href="index.php?option=com_akeebabackup&view=Configuration">
					<div class="bg-primary text-white d-block text-center p-3 h2">
						<span class="fa fa-cog"></span>
					</div>
					<span>
						<?= Text::_('COM_AKEEBABACKUP_CONFIG') ?>
					</span>
				</a>
			<?php endif ?>

			<?php if ($this->permissions['configure']): ?>
				<a class="text-center align-self-stretch btn btn-outline-primary border-0" style="width: 10em"
				   href="index.php?option=com_akeebabackup&view=Profiles">
					<div class="bg-primary text-white d-block text-center p-3 h2">
						<span class="fa fa-user-friends"></span>
					</div>
					<span>
						<?= Text::_('COM_AKEEBABACKUP_PROFILES') ?>
					</span>
				</a>
			<?php endif ?>
		</div>
	</div>
</div>
