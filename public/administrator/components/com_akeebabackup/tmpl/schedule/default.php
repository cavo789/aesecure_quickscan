<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var \Akeeba\Component\AkeebaBackup\Administrator\View\Schedule\HtmlView $this */

// Protect from unauthorized access
defined('_JEXEC') || die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

echo HTMLHelper::_('uitab.startTabSet', 'akeebabackup-scheduling', ['active' => 'akeebabackup-scheduling-backups']);

echo HTMLHelper::_('uitab.addTab', 'akeebabackup-scheduling', 'akeebabackup-scheduling-backups', Text::_('COM_AKEEBABACKUP_SCHEDULE_LBL_RUN_BACKUPS', true));

echo $this->loadAnyTemplate('schedule/backup');

echo HTMLHelper::_('uitab.endTab');

echo HTMLHelper::_('uitab.addTab', 'akeebabackup-scheduling', 'akeebabackup-scheduling-checkbackups', Text::_('COM_AKEEBABACKUP_SCHEDULE_LBL_CHECK_BACKUPS', true));

echo $this->loadAnyTemplate('schedule/check');

echo HTMLHelper::_('uitab.endTab');

echo HTMLHelper::_('uitab.endTabSet');