<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

$this->container->session->disableSave();
?>
	<form class="akeeba-form--stretch" action="index.php" method="post">
		<div class="akeeba-panel--teal">
			<h2 class="form-signin-heading">
				<?php echo AText::_('PASSWORD_HEADER_LOCKED'); ?>
			</h2>

			<div class="akeeba-block--info small">
				<?php echo AText::_('PASSWORD_SELF_UNLOCK'); ?>
			</div>

			<div class="akeeba-form-group">
				<input type="password" name="password" id="password"
					   placeholder="<?php echo AText::_('PASSWORD_FIELD_PASSWORD_LABEL') ?>" />
			</div>

			<div class="akeeba-form-group">
				<button class="akeeba-btn--teal--big--block" type="submit">
					<span class="akion-lock-combination"></span>
					<?php echo AText::_('PASSWORD_BTN_UNLOCK') ?>
				</button>
			</div>
		</div>

		<div>
			<input type="hidden" name="view" value="password" />
			<input type="hidden" name="task" value="unlock" />
		</div>

	</form>
<?php
$script = <<<JS
akeeba.System.documentReady(function(){
	akeeba.System.triggerEvent(document.getElementById('password'), 'focus');
});

JS;

/** @var $this AView */

$document = $this->container->application->getDocument();

$x = $document->addScriptDeclaration($script);
