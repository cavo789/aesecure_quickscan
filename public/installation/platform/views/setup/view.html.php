<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieViewSetup extends AView
{
	/** @var null */
	public $stateVars = null;

	/** @var bool */
	public $hasFTP = true;

	/**
	 * Are we running under Apache webserver?
	 *
	 * @var bool
	 */
	public $htaccessSupported = false;

	/**
	 * Are we running under NGINX webserver?
	 *
	 * @var bool
	 */
	public $nginxSupported = false;

	/**
	 * Are we running under IIS webserver?
	 *
	 * @var bool
	 */
	public $webConfSupported = false;

	/** @var array */
	public $removePhpiniOptions = [];

	/** @var array */
	public $replaceWeconfigOptions = [];

	/** @var array */
	public $removeHtpasswdOptions = [];

	public $hasHtaccess = false;

	public $htaccessOptionSelected = 'none';

	public $htaccessOptions = [];

	/**
	 * Are we restoring under HTTP but we have the option Force SSL enabled? If so print the warning
	 *
	 * @var bool
	 */
	public $protocolMismatch = false;

	/**
	 * @return bool
	 */
	public function onBeforeMain()
	{
		$this->loadHelper('select');
		$jVersion = $this->container->session->get('jversion', '3.6.0');

		/** @var AngieModelJoomlaSetup $model */
		$model           = $this->getModel();
		$this->stateVars = $model->getStateVariables();
		$this->hasFTP    = function_exists('ftp_connect') && version_compare($jVersion, '3.999.999', 'le');

		$this->htaccessSupported = AUtilsServertechnology::isHtaccessSupported();
		$this->nginxSupported    = AUtilsServertechnology::isNginxSupported();
		$this->webConfSupported  = AUtilsServertechnology::isWebConfigSupported();

		// Prime the options array with some default info
		$this->removePhpiniOptions = [
			'checked'  => '',
			'disabled' => '',
			'help'     => 'SETUP_LBL_SERVERCONFIG_REMOVEPHPINI_HELP',
		];

		$this->removeHandlerOptions = [
			'checked'  => '',
			'disabled' => '',
			'help'     => 'SETUP_LBL_SERVERCONFIG_REMOVEADDHANDLER_HELP',
		];

		/** @var AngieModelJoomlaMain $mainModel */
		$mainModel = AModel::getAnInstance('Main', 'AngieModel', [], $this->container);
		$extraInfo = $mainModel->getExtraInfo();

		$php_version = '';

		if (isset($extraInfo['php_version']))
		{
			$php_version = $extraInfo['php_version']['current'];
		}

		$this->updateHandlerOptions = [
			'checked'  => '',
			'disabled' => '',
			'help'     => AText::sprintf('SETUP_LBL_SERVERCONFIG_UPDATEADDHANDLER_HELP', $php_version),
		];

		$this->replaceHtaccessOptions = [
			'checked'  => '',
			'disabled' => '',
			'help'     => 'SETUP_LBL_SERVERCONFIG_REPLACEHTACCESS_HELP',
		];

		$this->replaceWeconfigOptions = [
			'checked'  => '',
			'disabled' => '',
			'help'     => 'SETUP_LBL_SERVERCONFIG_REPLACEWEBCONFIG_HELP',
		];

		$this->removeHtpasswdOptions = [
			'checked'  => '',
			'disabled' => '',
			'help'     => 'SETUP_LBL_SERVERCONFIG_REMOVEHTPASSWD_HELP',
		];

		// If we are restoring to a new server everything is checked by default
		if ($model->isNewhost())
		{
			$this->removePhpiniOptions['checked']    = 'checked="checked"';
			$this->replaceHtaccessOptions['checked'] = 'checked="checked"';
			$this->replaceWeconfigOptions['checked'] = 'checked="checked"';
			$this->removeHtpasswdOptions['checked']  = 'checked="checked"';
		}

		// Special case for AddHandler rule: we want to show that if it's a new host OR the file path is different
		if ($model->isNewhost() || $model->isDifferentFilesystem())
		{
			$this->removeHandlerOptions['checked'] = 'checked="checked"';
		}

		// If any option is not valid (ie missing files) we gray out the option AND remove the check
		// to avoid user confusion
		if (!$model->hasPhpIni())
		{
			$this->removePhpiniOptions['disabled'] = 'disabled="disabled"';
			$this->removePhpiniOptions['checked']  = '';
			$this->removePhpiniOptions['help']     = 'SETUP_LBL_SERVERCONFIG_NONEED_HELP';
		}

		if (!$model->hasWebconfig())
		{
			$this->replaceWeconfigOptions['disabled'] = 'disabled="disabled"';
			$this->replaceWeconfigOptions['checked']  = '';
			$this->replaceWeconfigOptions['help']     = 'SETUP_LBL_SERVERCONFIG_NONEED_HELP';
		}

		if (!$model->hasHtpasswd())
		{
			$this->removeHtpasswdOptions['disabled'] = 'disabled="disabled"';
			$this->removeHtpasswdOptions['checked']  = '';
			$this->removeHtpasswdOptions['help']     = 'SETUP_LBL_SERVERCONFIG_NONEED_HELP';
		}

		$this->protocolMismatch = $model->protocolMismatch();

		$this->loadHelper('select');

		$this->hasHtaccess            = $model->hasHtaccess();
		$this->htaccessOptionSelected = 'none';

		$options = ['none', 'default'];

		if ($model->hasAddHandler())
		{
			$options[] = 'removehandler';

			$this->htaccessOptionSelected = $model->isNewhost() ? 'removehandler' : 'none';
		}

		if ($model->hasAddHandler())
		{
			$options[] = 'replacehandler';

			$this->htaccessOptionSelected = 'replacehandler';
		}

		$this->htaccessOptionSelected = $model->getState('htaccessHandling', $this->htaccessOptionSelected);

		foreach ($options as $opt)
		{
			$this->htaccessOptions[] = AngieHelperSelect::option($opt, AText::_('SETUP_LBL_HTACCESSCHANGE_' . $opt));
		}

		return true;
	}
}
