<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

/** @var $this AView */

$platformJSFile = APATH_INSTALLATION . '/platform/js/main.min.js';
$document       = $this->container->application->getDocument();

$document->addScript('angie/js/json.min.js');
$document->addScript('angie/js/ajax.min.js');

if (file_exists($platformJSFile) && (@filesize($platformJSFile) > 512))
{
	$document->addScript('platform/js/main.min.js');
}
else
{
	$document->addScript('angie/js/main.min.js');
}

$url = 'index.php';

$document->addScriptDeclaration(<<<JS

var akeebaAjax = null;

akeeba.System.documentReady(function ()
{
	akeebaAjax = new akeebaAjaxConnector('$url');
});
JS

);

$document->appendButton(
	'GENERIC_BTN_STARTOVER', 'index.php?view=main&task=startover', 'red', 'fireball', 'startover'
);
$document->appendButton(
	'GENERIC_BTN_RECHECK', 'javascript:mainGetPage();', 'orange', 'loop', 'checkagain'
);

echo $this->loadAnyTemplate('steps/buttons');
?>
<noscript>
<div class="alert alert-error">
	<h3><?php echo AText::_('MAIN_HEADER_NOJAVASCRIPT') ?></h3>
	<p><?php echo AText::_('MAIN_LBL_NOJAVASCRIPT') ?></p>
</div>
</noscript>
<div class="well" style="text-align: center;">
	<h1><?php echo AText::_('MAIN_HEADER_INITIALISING') ?></h1>
	<p>
		<img src="template/flat/image/loading_small.gif" />
	</p>
	<p>
		<?php echo AText::_('MAIN_LBL_INITIALISINGWAIT') ?>
	</p>
</div>
