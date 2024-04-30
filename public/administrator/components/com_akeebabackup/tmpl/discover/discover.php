<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') || die();

/** @var \Akeeba\Component\AkeebaBackup\Administrator\View\Discover\HtmlView $this */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$hasFiles = !empty($this->files);
$task     = $hasFiles ? 'import' : 'default';
?>
<form name="adminForm" id="adminForm"
	  action="<?= Route::_('index.php?option=com_akeebabackup&view=Discover&task=' . $task) ?>"
	  method="post">
	<?php if ($hasFiles): ?>
		<div class="border bg-light mb-3 p-2">
			<div class="row">
				<label for="directory2" class="col-sm-3 col-form-label">
					<?= Text::_('COM_AKEEBABACKUP_DISCOVER_LABEL_DIRECTORY') ?>
				</label>
				<div class="col-sm-9">
					<input type="text" name="directory2" id="directory2"
						   value="<?= $this->escape($this->directory) ?>"
						   disabled="disabled" class="form-control" />
				</div>
			</div>
		</div>

		<div class="row mb-3">
			<label for="files" class="col-sm-3 col-form-label">
				<?= Text::_('COM_AKEEBABACKUP_DISCOVER_LABEL_FILES') ?>
			</label>
			<div class="col-sm-9">
				<select name="files[]" id="files" multiple="multiple" class="form-select">
					<?php foreach ($this->files as $file): ?>
						<option value="<?= $this->escape(basename($file)) ?>">
							<?= $this->escape(basename($file)) ?>
						</option>
					<?php endforeach ?>
				</select>
				<p class="form-text">
					<?= Text::_('COM_AKEEBABACKUP_DISCOVER_LABEL_SELECTFILES') ?>
				</p>
			</div>
		</div>

		<div class="row mb-3">
			<div class="col-sm-9 col-sm-offset-3">
				<button class="btn btn-primary" type="submit">
					<span class="fa fa-file-import"></span>
					<?= Text::_('COM_AKEEBABACKUP_DISCOVER_LABEL_IMPORT') ?>
				</button>
				<a class="btn btn-outline-warning"
				   href="<?= Route::_('index.php?option=com_akeebabackup&view=Discover') ?>">
					<span class="fa fa-arrow-left"></span>
					<?= Text::_('COM_AKEEBABACKUP_DISCOVER_LABEL_GOBACK') ?>

				</a>
			</div>
		</div>
	<?php else: ?>
		<div class="alert alert-warning">
			<?= Text::_('COM_AKEEBABACKUP_DISCOVER_ERROR_NOFILES') ?>
		</div>
		<p>
			<button type="submit"
					class="btn btn-warning">
				<span class="fa fa-arrow-left"></span>
				<?= Text::_('COM_AKEEBABACKUP_DISCOVER_LABEL_GOBACK') ?>
			</button>
		</p>
	<?php endif; ?>

	<?php if ($hasFiles): ?>
		<input type="hidden" name="directory" value="<?= $this->escape($this->directory) ?>" />
	<?php endif; ?>
	<?= HTMLHelper::_('form.token') ?>
</form>
