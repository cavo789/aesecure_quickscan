<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \Akeeba\Component\AkeebaBackup\Administrator\View\Profiles\HtmlView $this */

HTMLHelper::_('behavior.multiselect');

$user      = Factory::getApplication()->getIdentity();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

?>
<form action="<?= Route::_('index.php?option=com_akeebabackup&view=Profiles'); ?>"
      method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<?= LayoutHelper::render('joomla.searchtools.default', ['view' => $this]) ?>
				<?php if (empty($this->items)) : ?>
					<div class="alert alert-info">
						<span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?= Text::_('INFO'); ?></span>
						<?= Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
					<table class="table" id="articleList">
						<caption class="visually-hidden">
							<?= Text::_('COM_AKEEBABACKUP_PROFILES_TABLE_CAPTION'); ?>,
							<span id="orderedBy"><?= Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
							<span id="filteredBy"><?= Text::_('JGLOBAL_FILTERED_BY'); ?></span>
						</caption>
						<thead>
						<tr>
							<td class="w-1 text-center">
								<?= HTMLHelper::_('grid.checkall'); ?>
							</td>
							<th scope="col" class="w-25 d-none d-md-table-cell">
							</th>
							<th scope="col">
								<?= HTMLHelper::_('searchtools.sort', 'COM_AKEEBABACKUP_PROFILES_LABEL_DESCRIPTION', 'description', $listDirn, $listOrder); ?>
							</th>
							<th scope="col" class="w-1 text-center">
								<?= Text::_('COM_AKEEBABACKUP_CONFIG_QUICKICON_LABEL') ?>
							</th>
                            <th scope="col" class="w-1 text-center">
	                            <?= Text::_('JGRID_HEADING_ACCESS') ?>
                            </th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($this->items as $i => $item) :?>
							<tr class="row<?= $i % 2; ?>">
								<td class="text-center">
									<?= HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->name); ?>
								</td>

								<td class="d-none d-md-table-cell">
									<a href="<?= Route::_('index.php?option=com_akeebabackup&task=SwitchProfile&profileid=' . $item->id . '&returnurl=' . base64_encode('index.php?option=com_akeebabackup&view=Configuration') . '&' . Factory::getApplication()->getFormToken() . '=1') ?>"
									   class="btn btn-primary btn-sm text-decoration-none"
									>
										<span class="fa fa-cog"></span>
										<?= Text::_('COM_AKEEBABACKUP_CONFIG_UI_CONFIG') ?>
									</a>
									<a href="<?= Route::_('index.php?option=com_akeebabackup&task=Profile.export&id=' . (int) $item->id . '&format=json&' . Factory::getApplication()->getFormToken() . '=1'); ?>"
									   class="btn btn-secondary btn-sm text-decoration-none"
									>
										<span class="fa fa-download"></span>
										<?= Text::_('COM_AKEEBABACKUP_PROFILES_BTN_EXPORT') ?>
									</a>
								</td>

								<td scope="row">
									<div class="break-word">
										<a href="<?= Route::_('index.php?option=com_akeebabackup&task=Profile.edit&id=' . (int) $item->id); ?>"
										   title="<?= Text::_('JACTION_EDIT'); ?><?= $this->escape($item->description); ?>">
											<?= $this->escape($item->description); ?>
										</a>
										<div class="small">
											<?= Text::_('JGLOBAL_FIELD_ID_LABEL') ?>:
											<strong><?= $item->id ?></strong>
										</div>
									</div>
								</td>

								<td class="text-center">
									<?= HTMLHelper::_('jgrid.published', $item->quickicon, $i, 'Profiles.', true, 'cb'); ?>
								</td>

                                <td>
									<?= $this->escape($item->access_level) ?>
                                </td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>

					<?php // Load the pagination. ?>
					<?= $this->pagination->getListFooter(); ?>
				<?php endif; ?>

				<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<?= HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>

<div id="importModal"
	 class="modal fade"
	 role="dialog"
	 tabindex="-1"
	 aria-labelledby="akeeba-config-confwiz-title"
	 aria-hidden="true"
>
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title" id="akeeba-config-confwiz-title">
					<?= Text::_('COM_AKEEBABACKUP_PROFILES_IMPORT') ?>
				</h3>
				<button type="button" class="btn-close novalidate" data-bs-dismiss="modal"
						aria-label="<?= Text::_('JLIB_HTML_BEHAVIOR_CLOSE') ?>"></button>
			</div>
			<div class="modal-body p-5">
				<form action="index.php" method="post" name="importForm" id="importForm"
					  enctype="multipart/form-data"
					  class="border rounded p-3 bg-light"
				>
					<input type="hidden" name="option" value="com_akeebabackup" />
					<input type="hidden" name="view" value="Profiles" />
					<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
					<input type="hidden" name="task" id="task" value="import" />
					<?= HTMLHelper::_('form.token') ?>

					<div class="input-group mb-2">
						<input type="file" name="importfile" class="form-control" />

						<button type="submit"
								class="btn btn-success">
							<span class="fa fa-upload"></span>
							<?= Text::_('COM_AKEEBABACKUP_PROFILES_HEADER_IMPORT') ?>
						</button>
					</div>

					<div class="text-muted">
						<?= Text::_('COM_AKEEBABACKUP_PROFILES_LBL_IMPORT_HELP') ?>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>