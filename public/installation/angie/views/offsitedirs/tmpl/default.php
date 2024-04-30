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

$document = $this->container->application->getDocument();

$this->loadHelper('select');

$document->addScript('angie/js/json.min.js');
$document->addScript('angie/js/ajax.min.js');
$document->addScript('angie/js/offsitedirs.min.js');
$url = 'index.php';
$document->addScriptDeclaration(<<<JS
var akeebaAjax = null;

akeeba.System.documentReady(function(){
	akeebaAjax = new akeebaAjaxConnector('$url');
});

JS
);

echo $this->loadAnyTemplate('steps/buttons');
echo $this->loadAnyTemplate('steps/steps', array('helpurl' => 'https://www.akeeba.com/documentation/solo/angie-installers.html#angie-common-offsite'));
?>

<div id="restoration-dialog" style="display: none;">
	<div class="akeeba-renderer-fef">
		<h4><?php echo AText::_('OFFSITEDIRS_HEADER_COPY') ?></h4>

		<div id="restoration-progress">
			<div class="akeeba-progress">
				<div class="akeeba-progress-fill" id="restoration-progress-bar" style="width: 40%;"></div>
			</div>
		</div>
		<div id="restoration-success">
			<div class="akeeba-block--success">
				<?php echo AText::_('OFFSITEDIRS_HEADER_SUCCESS'); ?>
			</div>
			<p>
				<?php echo AText::_('OFFSITEDIRS_MSG_SUCCESS'); ?>
			</p>
			<button type="button" onclick="databaseBtnSuccessClick(); return false;" class="akeeba-btn--success">
				<span class="akion-arrow-right-c"></span>
				<?php echo AText::_('OFFSITEDIRS_BTN_SUCCESS'); ?>
			</button>
		</div>
		<div id="restoration-error">
			<div class="akeeba-block--failure">
				<?php echo AText::_('OFFSITEDIRS_HEADER_ERROR'); ?>
			</div>
			<div class="akeeba-panel--information" id="restoration-lbl-error">

			</div>
		</div>
	</div>
</div>

<?php if ($this->number_of_substeps): ?>
	<h1><?php echo AText::sprintf('OFFSITEDIRS_HEADER_MASTER', $this->substep['target']) ?></h1>
<?php endif; ?>

<div class="akeeba-block--info">
	<?php echo AText::sprintf(
			'OFFSITEDIRS_LBL_EXPLANATION',
			$this->substep['target'],
			$this->substep['virtual'],
			APATH_SITE
		) ?>
</div>

<div class="AKEEBA_MASTER_FORM_STYLING akeeba-form--horizontal">
	<div class="akeeba-panel--teal">
		<header class="akeeba-block-header">
			<h3><?php echo AText::_('OFFSITEDIRS_FOLDER_DETAILS');?></h3>
		</header>

		<div class="akeeba-form-group">
			<label class="control-label" for="virtual_folder"><?php echo AText::_('OFFSITEDIRS_VIRTUAL_FOLDER') ?></label>
			<input type="text" id="virtual_folder" class="input-xxlarge" disabled="disabled" value="<?php echo $this->substep['virtual']?>"/>
		</div>

		<div class="akeeba-form-group">
			<label class="control-label" for="target_folder"><?php echo AText::_('OFFSITEDIRS_TARGET_FOLDER')?></label>
			<input type="text" id="target_folder" class="input-xxlarge" value="<?php echo $this->substep['target']?>"/>
		</div>
	</div>
</div>
