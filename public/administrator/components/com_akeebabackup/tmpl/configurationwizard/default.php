<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') || die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

?>

<div id="akeeba-confwiz">

    <div id="backup-progress-pane">
        <div class="alert alert-warning">
            <?= Text::_('COM_AKEEBABACKUP_CONFWIZ_INTROTEXT') ?>
        </div>

        <div id="backup-progress-header" class="card">
			<h3 class="card-header bg-primary text-white">
				<span class="fa fa-diagnoses"></span>
		        <?= Text::_('COM_AKEEBABACKUP_CONFWIZ_PROGRESS') ?>
			</h3>

            <div id="backup-progress-content" class="card-body">
                <div id="backup-steps" class="d-flex flex-column align-items-stretch">
                    <div id="step-minexec" class="mt-1 mb-1 p-1 border rounded bg-light">
                        <?= Text::_('COM_AKEEBABACKUP_CONFWIZ_MINEXEC') ?>
                    </div>
                    <div id="step-directory" class="mt-1 mb-1 p-1 border rounded bg-light">
                        <?= Text::_('COM_AKEEBABACKUP_CONFWIZ_DIRECTORY') ?>
                    </div>
                    <div id="step-dbopt" class="mt-1 mb-1 p-1 border rounded bg-light">
                        <?= Text::_('COM_AKEEBABACKUP_CONFWIZ_DBOPT') ?>
                    </div>
                    <div id="step-maxexec" class="mt-1 mb-1 p-1 border rounded bg-light">
                        <?= Text::_('COM_AKEEBABACKUP_CONFWIZ_MAXEXEC') ?>
                    </div>
                    <div id="step-splitsize" class="mt-1 mb-1 p-1 border rounded bg-light">
                        <?= Text::_('COM_AKEEBABACKUP_CONFWIZ_SPLITSIZE') ?>
                    </div>
                </div>
                <div class="backup-steps-container mt-4 p-2 bg-info border-top border-3 text-white">
                    <div id="backup-substep">&nbsp;</div>
                </div>
            </div>
        </div>

    </div>

    <div id="error-panel" class="alert alert-danger" style="display:none">
        <h3><?= Text::_('COM_AKEEBABACKUP_CONFWIZ_HEADER_FAILED') ?></h3>
        <div id="errorframe">
            <p id="backup-error-message">
            </p>
        </div>
    </div>

    <div id="backup-complete" style="display: none">
        <div class="alert alert-success">
            <h3><?= Text::_('COM_AKEEBABACKUP_CONFWIZ_HEADER_FINISHED') ?></h3>
            <div id="finishedframe">
                <p>
                    <?= Text::_('COM_AKEEBABACKUP_CONFWIZ_CONGRATS') ?>
                </p>
                <p>
                    <a
                            class="btn btn-primary btn-lg"
                            href="<?= $this->escape( Uri::base() )?>index.php?option=com_akeebabackup&view=Backup">
                        <span class="fa fa-play"></span>
                        <?= Text::_('COM_AKEEBABACKUP_BACKUP') ?>
                    </a>
                    <a
                            class="btn btn-outline-secondary"
                            href="<?= $this->escape( Uri::base() )?>index.php?option=com_akeebabackup&view=Configuration">
                        <span class="fa fa-wrench"></span>
                        <?= Text::_('COM_AKEEBABACKUP_CONFIG') ?>
                    </a>
					<?php if(AKEEBABACKUP_PRO): ?>
                    <a
                            class="btn btn-outline-dark"
                            href="<?= $this->escape( Uri::base() )?>index.php?option=com_akeebabackup&view=Schedule">
                        <span class="fa fa-calendar"></span>
                        <?= Text::_('COM_AKEEBABACKUP_SCHEDULE') ?>
                    </a>
                    <?php endif ?>
                </p>
            </div>
        </div>
    </div>
</div>
