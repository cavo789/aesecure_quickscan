<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

/** @var $this AngieViewSetup */

$document = $this->container->application->getDocument();

$document->addScript('angie/js/json.min.js');
$document->addScript('angie/js/ajax.min.js');
$document->addScript('platform/js/setup.min.js');

$url = 'index.php';

$document->addScriptDeclaration(<<<JS
var akeebaAjax = null;

akeeba.System.documentReady(function(){
	akeebaAjax = new akeebaAjaxConnector('$url');
});

JS
);

$this->loadHelper('select');

echo $this->loadAnyTemplate('steps/buttons');
echo $this->loadAnyTemplate('steps/steps', ['helpurl' => 'https://www.akeeba.com/documentation/solo/angie-joomla-setup.html']);
?>
<form name="setupForm" action="index.php" method="post" class="akeeba-form--horizontal">
	<div>
		<button class="akeeba-btn--dark" style="float: right;" onclick="toggleHelp(); return false;">
			<span class="akion-help"></span>
			Show / hide help
		</button>
	</div>

	<div class="akeeba-container--50-50">
		<!-- Site parameters -->
		<div class="akeeba-panel--teal" style="margin-top: 0">
			<header class="akeeba-block-header">
				<h3><?php echo AText::_('SETUP_HEADER_SITEPARAMS') ?></h3>
			</header>

			<div class="akeeba-form-group">
				<label for="sitename">
					<?php echo AText::_('SETUP_LBL_SITENAME'); ?>
				</label>
				<input type="text" id="sitename" name="sitename" value="<?php echo $this->stateVars->sitename ?>" />
				<span class="akeeba-help-text" style="display: none">
                    <?php echo AText::_('SETUP_LBL_SITENAME_HELP') ?>
                </span>
			</div>
			<div class="akeeba-form-group">
				<label for="siteemail">
					<?php echo AText::_('SETUP_LBL_SITEEMAIL'); ?>
				</label>
				<input type="text" id="siteemail" name="siteemail"
					   value="<?php echo $this->stateVars->siteemail ?>" />
				<span class="akeeba-help-text" style="display: none">
                    <?php echo AText::_('SETUP_LBL_SITEEMAIL_HELP') ?>
                </span>
			</div>
			<div class="akeeba-form-group">
				<label for="emailsender">
					<?php echo AText::_('SETUP_LBL_EMAILSENDER'); ?>
				</label>
				<input type="text" id="emailsender" name="emailsender"
					   value="<?php echo $this->stateVars->emailsender ?>" />
				<span class="akeeba-help-text" style="display: none">
					<?php echo AText::_('SETUP_LBL_EMAILSENDER_HELP') ?>
				</span>
			</div>
			<div class="akeeba-form-group">
				<label for="livesite">
					<?php echo AText::_('SETUP_LBL_LIVESITE'); ?>
				</label>
				<input type="text" id="livesite" name="livesite" value="<?php echo $this->stateVars->livesite ?>" />
				<?php if (substr(PHP_OS, 0, 3) == 'WIN'): ?>
					<p class="akeeba-block--warning">
						<span class="akion-android-warning"></span>
						<?php echo AText::_('SETUP_LBL_LIVESITE_WINDOWS_WARNING') ?>
					</p>
				<?php endif; ?>
				<span class="akeeba-help-text" style="display: none">
					  <?php echo AText::_('SETUP_LBL_LIVESITE_HELP') ?>
				</span>
			</div>

			<?php if($this->protocolMismatch): ?>
				<div class="akeeba-block--warning">
					<?php echo AText::_('SETUP_LBL_SERVERCONFIG_DISABLEFORCESSL_WARN')?>
				</div>
			<?php endif; ?>

			<div class="akeeba-form-group">
				<label for="force_ssl">
					<?php echo AText::_('SETUP_LABEL_FORCESSL'); ?>
				</label>
				<?php echo AngieHelperSelect::forceSSL($this->stateVars->force_ssl); ?>
				<span class="akeeba-help-text" style="display: none">
					<?php echo AText::_('SETUP_LABEL_FORCESSL_TIP') ?>
				</span>
			</div>
			<div class="akeeba-form-group">
				<label for="cookiedomain">
					<?php echo AText::_('SETUP_LBL_COOKIEDOMAIN'); ?>
				</label>
				<input type="text" id="cookiedomain" name="cookiedomain"
					   value="<?php echo $this->stateVars->cookiedomain ?>" />
				<span class="akeeba-help-text" style="display: none">
					<?php echo AText::_('SETUP_LBL_COOKIEDOMAIN_HELP') ?>
				</span>
			</div>
			<div class="akeeba-form-group">
				<label for="cookiepath">
					<?php echo AText::_('SETUP_LBL_COOKIEPATH'); ?>
				</label>
				<input type="text" id="cookiepath" name="cookiepath"
					   value="<?php echo $this->stateVars->cookiepath ?>" />
				<span class="akeeba-help-text" style="display: none">
					<?php echo AText::_('SETUP_LBL_COOKIEPATH_HELP') ?>
				</span>
			</div>
			<?php if (true || version_compare($this->container->session->get('jversion'), '3.2', 'ge')): ?>
			<div class="akeeba-form-group">
				<label for="mailonline">
					<?php echo AText::_('SETUP_LBL_MAILONLINE'); ?>
				</label>
				<div class="akeeba-toggle">
					<input type="radio" <?php echo !$this->stateVars->mailonline ? 'checked="checked"' : '' ?>
						   name="mailonline" id="mailonline-0" value="0" />
					<label for="mailonline-0" class="red">
						<?php echo AText::_('GENERIC_LBL_NO') ?>
					</label>

					<input type="radio" <?php echo $this->stateVars->mailonline ? 'checked="checked"' : '' ?>
						   name="mailonline" id="mailonline-1" value="1" />
					<label for="mailonline-1" class="green">
						<?php echo AText::_('GENERIC_LBL_YES') ?>
					</label>
				</div>
			</div>
			<div class="akeeba-form-group">
				<label for="resetsessionoptions">
					<?php echo AText::_('SETUP_LBL_RESETSESSIONOPTIONS'); ?>
				</label>
				<div class="akeeba-toggle">
					<input type="radio" <?php echo !$this->stateVars->resetsessionoptions ? 'checked="checked"' : '' ?>
						   name="resetsessionoptions" id="resetsessionoptions-0" value="0" />
					<label for="resetsessionoptions-0" class="red">
						<?php echo AText::_('GENERIC_LBL_NO') ?>
					</label>

					<input type="radio" <?php echo $this->stateVars->resetsessionoptions ? 'checked="checked"' : '' ?>
						   name="resetsessionoptions" id="resetsessionoptions-1" value="1" />
					<label for="resetsessionoptions-1" class="green">
						<?php echo AText::_('GENERIC_LBL_YES') ?>
					</label>
				</div>
			</div>
			<div class="akeeba-form-group">
				<label for="resetcacheoptions">
					<?php echo AText::_('SETUP_LBL_RESETCACHEOPTIONS'); ?>
				</label>
				<div class="akeeba-toggle">
					<input type="radio" <?php echo !$this->stateVars->resetcacheoptions ? 'checked="checked"' : '' ?>
						   name="resetcacheoptions" id="resetcacheoptions-0" value="0" />
					<label for="resetcacheoptions-0" class="red">
						<?php echo AText::_('GENERIC_LBL_NO') ?>
					</label>

					<input type="radio" <?php echo $this->stateVars->resetcacheoptions ? 'checked="checked"' : '' ?>
						   name="resetcacheoptions" id="resetcacheoptions-1" value="1" />
					<label for="resetcacheoptions-1" class="green">
						<?php echo AText::_('GENERIC_LBL_YES') ?>
					</label>
				</div>
			</div>
			<?php endif; ?>
			<div class="akeeba-form-group--pull-right">
				<button type="button"
						class="akeeba-btn--dark"
						id="usesitedirs" name="usesitedirs">
					<?php echo AText::_('SETUP_LBL_USESITEDIRS'); ?>
				</button>
				<span class="akeeba-help-text" style="display: none">
					  <?php echo AText::_('SETUP_LBL_USESITEDIRS_HELP') ?>
				</span>
			</div>
		</div>

		<div class="akeeba-panel--orange">
			<header class="akeeba-block-header">
				<h3><?php echo AText::_('SETUP_HEADER_SERVERCONFIG') ?></h3>
			</header>

			<p class="akeeba-block--info small">
				<?php echo AText::_('SETUP_SERVERCONFIG_DESCR') ?>
			</p>

			<?php if ($this->htaccessSupported && $this->hasHtaccess): ?>
				<div class="akeeba-form-group">
					<label for="htaccessHandling"><?= AText::_('SETUP_LBL_HTACCESSCHANGE_LBL') ?></label>
					<?= AngieHelperSelect::genericlist($this->htaccessOptions, 'htaccessHandling', null, 'value', 'text', $this->htaccessOptionSelected) ?>
					<span class="akeeba-help-text" style="display: none">
					  <?= AText::_('SETUP_LBL_HTACCESSCHANGE_DESC') ?>
				</span>
				</div>
			<?php endif; ?>

			<?php if ($this->webConfSupported): ?>
				<div class="akeeba-form-group--checkbox--pull-right">
					<label <?php echo $this->replaceWeconfigOptions['disabled'] ?>>
						<input type="checkbox" value="1" id="replacewebconfig"
							   name="replacewebconfig" <?php echo $this->replaceWeconfigOptions['disabled'] ?> <?php echo $this->replaceWeconfigOptions['checked'] ?> />
						<?php echo AText::_('SETUP_LBL_SERVERCONFIG_REPLACEWEBCONFIG'); ?>
					</label>
					<span class="akeeba-help-text" style="display: none">
						  <?php echo AText::_($this->replaceWeconfigOptions['help']) ?>
					</span>
				</div>
			<?php endif; ?>

			<div class="akeeba-form-group--checkbox--pull-right">
				<label <?php echo $this->removePhpiniOptions['disabled'] ?>>
					<input type="checkbox" value="1" id="removephpini"
						   name="removephpini" <?php echo $this->removePhpiniOptions['disabled'] ?> <?php echo $this->removePhpiniOptions['checked'] ?> />
					<?php echo AText::_('SETUP_LBL_SERVERCONFIG_REMOVEPHPINI'); ?>
				</label>
				<span class="akeeba-help-text" style="display: none">
						  <?php echo AText::_($this->removePhpiniOptions['help']) ?>
					</span>
			</div>

			<?php if ($this->htaccessSupported): ?>
				<div class="akeeba-form-group--checkbox--pull-right">
					<label <?php echo $this->removeHtpasswdOptions['disabled'] ?>>
						<input type="checkbox" value="1" id="removehtpasswd"
							   name="removehtpasswd" <?php echo $this->removeHtpasswdOptions['disabled'] ?> <?php echo $this->removeHtpasswdOptions['checked'] ?> />
						<?php echo AText::_('SETUP_LBL_SERVERCONFIG_REMOVEHTPASSWD'); ?>
					</label>
					<span class="akeeba-help-text" style="display: none">
						  <?php echo AText::_($this->removeHtpasswdOptions['help']) ?>
					</span>
				</div>
			<?php endif; ?>

		</div>
	</div>

	<div class="akeeba-container--50-50">
		<!-- Fine-tuning -->
		<div class="akeeba-panel--info" style="margin-top: 0">
			<header class="akeeba-block-header">
				<h3><?php echo AText::_('SETUP_HEADER_FINETUNING') ?></h3>
			</header>

			<div class="form-horizontal">
				<div class="akeeba-form-group">
					<label for="siteroot">
						<?php echo AText::_('SETUP_LABEL_SITEROOT'); ?>
					</label>
					<input type="text" disabled="disabled" id="siteroot"
						   value="<?php echo $this->stateVars->site_root_dir ?>" />
					<span class="akeeba-help-text" style="display: none">
						<?php echo AText::_('SETUP_LABEL_SITEROOT_HELP') ?>
					</span>
				</div>
				<div class="akeeba-form-group">
					<label for="tmppath">
						<?php echo AText::_('SETUP_LABEL_TMPPATH'); ?>
					</label>
					<input type="text" id="tmppath" name="tmppath"
						   value="<?php echo $this->stateVars->tmppath ?>" />
					<span class="akeeba-help-text" style="display: none">
						<?php echo AText::_('SETUP_LABEL_TMPPATH_HELP') ?>
					</span>
				</div>
				<div class="akeeba-form-group">
					<label for="logspath">
						<?php echo AText::_('SETUP_LABEL_LOGSPATH'); ?>
					</label>
					<input type="text" id="logspath" name="logspath"
						   value="<?php echo $this->stateVars->logspath ?>" />
					<span class="akeeba-help-text" style="display: none">
						<?php echo AText::_('SETUP_LABEL_LOGSPATH_HELP') ?>
					</span>
				</div>
			</div>
		</div>
		<?php if (isset($this->stateVars->superusers)): ?>
			<!-- Super Administrator settings -->
			<div class="akeeba-panel--info">
				<header class="akeeba-block-header">
					<h3><?php echo AText::_('SETUP_HEADER_SUPERUSERPARAMS') ?></h3>
				</header>
				<div class="form-horizontal">
					<div class="akeeba-form-group">
						<label for="superuserid">
							<?php echo AText::_('SETUP_LABEL_SUPERUSER'); ?>
						</label>
						<?php echo AngieHelperSelect::superusers(); ?>
						<span class="akeeba-help-text" style="display: none">
							<?php echo AText::_('SETUP_LABEL_SUPERUSER_HELP') ?>
						</span>
					</div>
					<div class="akeeba-form-group" id="superuseremail_container">
						<label for="superuseremail">
							<?php echo AText::_('SETUP_LABEL_SUPERUSEREMAIL'); ?>
						</label>
						<input type="text" id="superuseremail" name="superuseremail" value="" />
						<span class="akeeba-help-text" style="display: none">
							<?php echo AText::_('SETUP_LABEL_SUPERUSEREMAIL_HELP') ?>
						</span>
					</div>
					<div class="akeeba-form-group" id="superuserpassword_container">
						<label for="superuserpassword">
							<?php echo AText::_('SETUP_LABEL_SUPERUSERPASSWORD'); ?>
						</label>
						<input type="password" id="superuserpassword" name="superuserpassword" value="" />
						<span class="akeeba-help-text" style="display: none">
							<?php echo AText::_('SETUP_LABEL_SUPERUSERPASSWORD_HELP2') ?>
						</span>
					</div>
					<div class="akeeba-form-group" id="superuserpasswordrepeat_container">
						<label for="superuserpasswordrepeat">
							<?php echo AText::_('SETUP_LABEL_SUPERUSERPASSWORDREPEAT'); ?>
						</label>
						<input type="password" id="superuserpasswordrepeat" name="superuserpasswordrepeat"
							   value="" />
						<span class="akeeba-help-text" style="display: none">
							<?php echo AText::_('SETUP_LABEL_SUPERUSERPASSWORDREPEAT_HELP') ?>
						</span>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<div class="akeeba-container--50-50">
		<!-- FTP options -->
		<?php if ($this->hasFTP): ?>
			<div class="akeeba-panel-info">
				<header class="akeeba-block-header">
					<h3>
						<?php echo AText::_('SETUP_HEADER_FTPPARAMS') ?>
					</h3>
				</header>
				<p class="akeeba-block--info small">
					<?php echo AText::_('SETUP_LABEL_FTPENABLE_HELP') ?>
				</p>

				<div class="text-center" style="margin-bottom: 20px">
                    <span id="showFtpOptions" class="akeeba-btn--green"
						  style="display: <?php echo $this->stateVars->ftpenable ? 'none' : 'inline'; ?>">
                        <?php echo AText::_('SETUP_LABEL_FTPENABLE') ?>
                    </span>
					<span id="hideFtpOptions" class="akeeba-btn--red"
						  style="display: <?php echo $this->stateVars->ftpenable ? 'inline' : 'none'; ?>">
                        <?php echo AText::_('SETUP_LABEL_FTPDISABLE') ?>
                    </span>
				</div>

				<input type="hidden" id="enableftp" name="enableftp"
					   value="<?php echo $this->stateVars->ftpenable; ?>" />

				<div id="ftpLayerHolder"
					 style="display: <?php echo $this->stateVars->ftpenable ? 'block' : 'none'; ?>">
					<div class="akeeba-form-group">
						<label for="ftphost">
							<?php echo AText::_('SETUP_LABEL_FTPHOST'); ?>
						</label>
						<input type="text" id="ftphost" name="ftphost"
							   value="<?php echo $this->stateVars->ftphost ?>" />
						<span class="akeeba-help-text" style="display: none">
							<?php echo AText::_('SETUP_LABEL_FTPHOST_HELP') ?>
						</span>
					</div>
					<div class="akeeba-form-group">
						<label for="ftpport">
							<?php echo AText::_('SETUP_LABEL_FTPPORT'); ?>
						</label>
						<input type="text" id="ftpport" name="ftpport"
							   value="<?php echo empty($this->stateVars->ftpport) ? '21' : $this->stateVars->ftpport ?>" />
						<span class="akeeba-help-text" style="display: none">
					 		<?php echo AText::_('SETUP_LABEL_FTPPORT_HELP') ?>
						</span>
					</div>
					<div class="akeeba-form-group">
						<label for="ftpuser">
							<?php echo AText::_('SETUP_LABEL_FTPUSER'); ?>
						</label>
						<input type="text" id="ftpuser" name="ftpuser"
							   value="<?php echo $this->stateVars->ftpuser ?>" />
						<span class="akeeba-help-text" style="display: none">
							<?php echo AText::_('SETUP_LABEL_FTPUSER_HELP') ?>
						</span>
					</div>
					<div class="akeeba-form-group">
						<label for="ftppass">
							<?php echo AText::_('SETUP_LABEL_FTPPASS'); ?>
						</label>
						<input type="password" id="ftppass" name="ftppass"
							   value="<?php echo $this->stateVars->ftppass ?>" />
						<span class="akeeba-help-text" style="display: none">
							<?php echo AText::_('SETUP_LABEL_FTPPASS_HELP') ?>
						</span>
					</div>
					<div class="akeeba-form-group">
						<label for="ftpdir">
							<?php echo AText::_('SETUP_LABEL_FTPDIR'); ?>
						</label>
						<div class="akeeba-input-group">
							<input type="text" id="ftpdir" name="ftpdir"
								   value="<?php echo $this->stateVars->ftpdir ?>" />
							<span class="akeeba-input-group-btn">
								<button type="button" class="akeeba-btn" id="ftpbrowser"
										onclick="openFTPBrowser();">
									<span class="akion-android-folder-open"></span>
									<?php echo AText::_('SESSION_BTN_BROWSE'); ?>
								</button>
							</span>
						</div>
						<span class="akeeba-help-text" style="display: none">
							<?php echo AText::_('SETUP_LABEL_FTPDIR_HELP') ?>
						</span>
					</div>
				</div>
			</div>
		<?php endif; ?>

	</div>

	<div style="display: none;">
		<input type="hidden" name="view" value="setup" />
		<input type="hidden" name="task" value="apply" />
	</div>
</form>

<div id="browseModal" class="modal" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="browseModalLabel"
	 style="display: none">
	<div class="akeeba-renderer-fef">
		<div class="akeeba-panel--teal">
			<header class="akeeba-block-header">
				<h3 id="browseModalLabel"><?php echo AText::_('GENERIC_FTP_BROWSER'); ?></h3>
			</header>
			<iframe id="browseFrame" src="about:blank" width="100%" height="300px"></iframe>
		</div>
	</div>
</div>

<script type="text/javascript">
	<?php if (isset($this->stateVars->superusers)): ?>
	setupSuperUsers = <?php echo json_encode($this->stateVars->superusers); ?>;
	<?php endif; ?>

	akeeba.System.documentReady(function() {
		<?php if (isset($this->stateVars->superusers)): ?>
		setupSuperUserChange();
		<?php endif; ?>
		setupDefaultTmpDir  = '<?php echo addcslashes($this->stateVars->default_tmp, '\\') ?>';
		setupDefaultLogsDir = '<?php echo addcslashes($this->stateVars->default_log, '\\') ?>';
	});
</script>
