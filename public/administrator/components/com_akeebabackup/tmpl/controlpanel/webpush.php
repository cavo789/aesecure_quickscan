<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \Akeeba\Component\AkeebaBackup\Administrator\View\Controlpanel\HtmlView */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// Protect from unauthorized access
defined('_JEXEC') || die();

// Pass parameters to the JavaScript
$vapidKeys = $this->getModel('push')->getVapidKeys('com_akeebabackup');

if ($vapidKeys === null):
?>
<div class="card mb-2">
	<h3 class="card-header bg-info text-white">
		<?= Text::_('COM_AKEEBABACKUP_CONTROLPANEL_WEBPUSH_HEAD') ?>
	</h3>
	<div class="card-body">
		<div class="alert alert-warning" id="webPushNotAvailable">
			<h3 class="alert-heading"><?= Text::_('COM_AKEEBABACKUP_CONTROLPANEL_WEBPUSH_LBL_UNAVAILABLE_HEAD') ?></h3>
			<p><?= Text::_('COM_AKEEBABACKUP_CONTROLPANEL_WEBPUSH_LBL_UNAVAILABLE_SERVER_BODY') ?></p>
		</div>
	</div>
</div>
<?php
endif;

$this->document->addScriptOptions('com_akeebabackup.webPush', [
	'workerUri'         => $this->document
		->getWebAssetManager()
		->getAsset('script', 'com_akeebabackup.webpush-worker')
		->getUri('true'),
	'subscribeUri'      => Route::_(
		'index.php?option=com_akeebabackup&task=push.webpushsubscribe',
		false,
		Route::TLS_IGNORE,
		true
	),
	'unsubscribeUri'    => Route::_(
		'index.php?option=com_akeebabackup&task=push.webpushunsubscribe',
		false,
		Route::TLS_IGNORE,
		true
	),
	'vapidKeys'         => $vapidKeys,
	'subscribeButton'   => '#btnWebPushSubscribe',
	'unsubscribeButton' => '#btnWebPushUnsubscribe',
	'unavailableInfo'   => '#webPushNotAvailable',
]);
// Load the JavaScript
$this->document->getWebAssetManager()->useScript('com_akeebabackup.webpush');

?>
<div class="card mb-2">
	<h3 class="card-header bg-info text-white">
		<?= Text::_('COM_AKEEBABACKUP_CONTROLPANEL_WEBPUSH_HEAD') ?>
	</h3>
	<div class="card-body">
		<details id="webPushDetails">
			<summary><?= Text::_('COM_AKEEBABACKUP_CONTROLPANEL_WEBPUSH_SUMMARY') ?></summary>
			<p>
				<?= Text::_('COM_AKEEBABACKUP_CONTROLPANEL_WEBPUSH_DETAILS') ?>
			</p>
		</details>

		<div class="alert alert-warning d-none" id="webPushNotAvailable">
			<h3 class="alert-heading"><?= Text::_('COM_AKEEBABACKUP_CONTROLPANEL_WEBPUSH_LBL_UNAVAILABLE_HEAD') ?></h3>
			<p><?= Text::_('COM_AKEEBABACKUP_CONTROLPANEL_WEBPUSH_LBL_UNAVAILABLE_BODY') ?></p>
		</div>

		<button
			type="button"
			id="btnWebPushSubscribe"
			class="btn btn-primary d-none disabled"
		>
			<?= Text::_('COM_AKEEBABACKUP_CONTROLPANEL_WEBPUSH_BTN_SUBSCRIBE') ?>
		</button>
		<button
			type="button"
			id="btnWebPushUnsubscribe"
			class="btn btn-danger d-none disabled"
		>
			<?= Text::_('COM_AKEEBABACKUP_CONTROLPANEL_WEBPUSH_BTN_UNSUBSCRIBE') ?>
		</button>
	</div>
</div>
