<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \Akeeba\Component\AkeebaBackup\Administrator\View\Controlpanel\HtmlView */

// Protect from unauthorized access
defined('_JEXEC') || die();

// Obsolete PHP version warning
echo $this->loadAnyTemplate('commontemplates/phpversion_warning', true, [
	'softwareName'          => 'Akeeba Backup',
	'class_priority_low'    => 'alert alert-info',
	'class_priority_medium' => 'alert alert-warning',
	'class_priority_high'   => 'alert alert-danger',
]);