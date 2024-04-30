<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AkeebaBackup\Administrator\Controller;

defined('_JEXEC') || die;

use Akeeba\Component\AkeebaBackup\Administrator\Mixin\ControllerCustomACLTrait;
use Akeeba\Component\AkeebaBackup\Administrator\Mixin\ControllerEventsTrait;
use Akeeba\Component\AkeebaBackup\Administrator\Mixin\ControllerProfileAccessTrait;
use Akeeba\Component\AkeebaBackup\Administrator\Mixin\ControllerProfileRestrictionTrait;
use Akeeba\Component\AkeebaBackup\Administrator\Mixin\ControllerRegisterTasksTrait;
use Akeeba\Component\AkeebaBackup\Administrator\Model\ConfigurationModel;
use Akeeba\Component\AkeebaBackup\Administrator\Model\ProfileModel;
use Akeeba\Engine\Platform;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;

class ConfigurationController extends BaseController
{
	use ControllerEventsTrait;
	use ControllerRegisterTasksTrait;
	use ControllerProfileAccessTrait;
	use ControllerCustomACLTrait
	{
		ControllerCustomACLTrait::onBeforeExecute as onBeforeExecuteACL;
	}
	use ControllerProfileRestrictionTrait
	{
		ControllerProfileRestrictionTrait::onBeforeExecute as onBeforeExecuteRestrictedProfile;
	}

	/**
	 * The default view.
	 *
	 * @since  1.6
	 * @var    string
	 */
	protected $default_view = 'Configuration';

	public function __construct($config = [], MVCFactoryInterface $factory = null, ?CMSApplication $app = null, ?Input $input = null)
	{
		parent::__construct($config, $factory, $app, $input);

		$this->registerControllerTasks('main');
	}

	/**
	 * Handle the apply task which saves the configuration settings and shows the page again
	 */
	public function apply($cachable = false, $urlparams = [])
	{
		// CSRF prevention
		$this->checkToken();

		// Get the var array from the request
		$data                        = $this->input->get('var', [], 'raw');
		$data['akeeba.flag.confwiz'] = 1;

		/** @var ConfigurationModel $model */
		$model = $this->getModel('Configuration', 'Administrator');
		$model->setState('engineconfig', $data);
		$model->saveEngineConfig();

		// Finally, save the profile description if it has changed
		$profileId = Platform::getInstance()->get_active_profile();

		$this->triggerEvent('onAfterApply', [$profileId]);

		// Update the profile name and quick icon definition
		/** @var ProfileModel $profileModel */
		$profileModel = $this->getModel('Profile', 'Administrator');
		/** @var object $profileRecord */
		$profileRecord = $profileModel->getItem($profileId);

		if ($profileRecord === false)
		{
			throw new \RuntimeException('Internal error: cannot load the profile you are configuring. Did you delete it before saving the Configuration page?!', 500);
		}

		$oldProfileName = $profileRecord->description;
		$oldQuickIcon   = $profileRecord->quickicon;

		$profileName = $this->input->getString('profilename', null);
		$profileName = trim($profileName);

		$quickIconValue = $this->input->getCmd('quickicon', '');
		$quickIcon      = (int) !empty($quickIconValue);

		$mustSaveProfile = !empty($profileName) && ($profileName != $oldProfileName);
		$mustSaveProfile = $mustSaveProfile || ($quickIcon != $oldQuickIcon);

		if ($mustSaveProfile)
		{
			$profileRecord->description = $profileName;
			$profileRecord->quickicon   = $quickIcon;

			$profileModel->save((array) $profileRecord);
		}

		$this->setRedirect(Uri::base() . 'index.php?option=com_akeebabackup&view=Configuration', Text::_('COM_AKEEBABACKUP_CONFIG_SAVE_OK'));
	}

	/**
	 * Handle the cancel task which doesn't save anything and returns to the Control Panel page
	 */
	public function cancel($cachable = false, $urlparams = [])
	{
		// CSRF prevention
		$this->checkToken();
		$this->setRedirect(Uri::base() . 'index.php?option=com_akeebabackup');
	}

	/**
	 * Runs a custom API call against the selected data processing engine and returns the JSON encoded result
	 */
	public function dpecustomapi($cachable = false, $urlparams = [])
	{
		/** @var ConfigurationModel $model */
		$model = $this->getModel('Configuration', 'Administrator');
		$model->setState('engine', $this->input->get('engine', '', 'raw'));
		$model->setState('method', $this->input->get('method', '', 'raw'));
		$model->setState('params', $this->input->get('params', [], 'raw'));

		@ob_end_clean();
		echo '###' . json_encode($model->dpeCustomAPICall()) . '###';
		flush();

		$this->app->close();
	}

	/**
	 * Runs a custom API call against the selected data processing engine and returns the raw result
	 */
	public function dpecustomapiraw($cachable = false, $urlparams = [])
	{
		/** @var ConfigurationModel $model */
		$model = $this->getModel('Configuration', 'Administrator');
		$model->setState('engine', $this->input->get('engine', '', 'raw'));
		$model->setState('method', $this->input->get('method', '', 'raw'));
		$model->setState('params', $this->input->get('params', [], 'raw'));

		@ob_end_clean();
		echo $model->dpeCustomAPICall();
		flush();

		$this->app->close();
	}

	/**
	 * Opens an OAuth window for the selected data processing engine
	 */
	public function dpeoauthopen($cachable = false, $urlparams = [])
	{
		/** @var ConfigurationModel $model */
		$model = $this->getModel('Configuration', 'Administrator');
		$model->setState('engine', $this->input->get('engine', '', 'raw'));
		$model->setState('params', $this->input->get('params', [], 'raw'));

		@ob_end_clean();
		$model->dpeOuthOpen();
		flush();

		$this->app->close();
	}

	public function main($cachable = false, $urlparams = [])
	{
		return parent::display($cachable, $urlparams);
	}

	protected function onBeforeExecute(&$task)
	{
		$this->onBeforeExecuteACL($task);
		$this->onBeforeExecuteRestrictedProfile($task);
	}

	/**
	 * Handle the save task which saves the configuration settings and returns to the Control Panel page
	 */
	public function save($cachable = false, $urlparams = [])
	{
		$this->apply();
		$this->setRedirect(Uri::base() . 'index.php?option=com_akeebabackup', Text::_('COM_AKEEBABACKUP_CONFIG_SAVE_OK'));
	}

	/**
	 * Handle the save & new task which saves settings, creates a new backup profile, activates it and proceed to the
	 * configuration page once more.
	 */
	public function savenew($cachable = false, $urlparams = [])
	{
		$this->checkToken();

		// Save the current profile
		$this->apply();

		// Create a new profile
		$profileId = Platform::getInstance()->get_active_profile();

		/** @var ProfileModel $profilesModel */
		$profilesModel = $this->getModel('Profile');
		$profile       = $profilesModel->getTable();

		if (!$profile->load($profileId))
		{
			throw new \RuntimeException(sprintf("Profile %u not found.", $profileId), 404);
		}

		// Must unset ID before save. The ID cannot be bound with bind()/save(), hence the need to do it the hard way.
		$profile->id = null;
		$profile
			->save([
				'description' => Text::_('COM_AKEEBABACKUP_CONFIG_SAVENEW_DEFAULT_PROFILE_NAME'),
			]);

		// Activate and edit the new profile
		$returnUrl = base64_encode($this->redirect);
		$token     = $this->app->getFormToken();
		$url       = Uri::base() . 'index.php?option=com_akeebabackup&task=SwitchProfile&profileid=' . $profile->getId() .
			'&returnurl=' . $returnUrl . '&' . $token . '=1';
		$this->setRedirect($url);
	}

	/**
	 * Tests the validity of the FTP connection details
	 */
	public function testftp($cachable = false, $urlparams = [])
	{
		/** @var ConfigurationModel $model */
		$model = $this->getModel('Configuration', 'Administrator');
		$model->setState('isCurl', $this->input->get('isCurl', 0, 'int'));
		$model->setState('host', $this->input->get('host', '', 'raw'));
		$model->setState('port', $this->input->get('port', 21, 'int'));
		$model->setState('user', $this->input->get('user', '', 'raw'));
		$model->setState('pass', $this->input->get('pass', '', 'raw'));
		$model->setState('initdir', $this->input->get('initdir', '', 'raw'));
		$model->setState('usessl', (bool) $this->input->getInt('usessl', 0));
		$model->setState('passive', (bool) $this->input->getInt('passive', 0));
		$model->setState('passive_mode_workaround', (bool) $this->input->getInt('passive_mode_workaround', 0));

		try
		{
			$model->testFTP();
			$testResult = true;
		}
		catch (\RuntimeException $e)
		{
			$testResult = $e->getMessage();
		}

		@ob_end_clean();
		echo '###' . json_encode($testResult) . '###';
		flush();

		$this->app->close();
	}

	/**
	 * Tests the validity of the SFTP connection details
	 */
	public function testsftp($cachable = false, $urlparams = [])
	{
		/** @var ConfigurationModel $model */
		$model = $this->getModel('Configuration', 'Administrator');
		$model->setState('isCurl', $this->input->get('isCurl', 0, 'int'));
		$model->setState('host', $this->input->get('host', '', 'raw'));
		$model->setState('port', $this->input->get('port', 21, 'int'));
		$model->setState('user', $this->input->get('user', '', 'raw'));
		$model->setState('pass', $this->input->get('pass', '', 'raw'));
		$model->setState('privkey', $this->input->get('privkey', '', 'path'));
		$model->setState('pubkey', $this->input->get('pubkey', '', 'path'));
		$model->setState('initdir', $this->input->get('initdir', '', 'raw'));

		try
		{
			$model->testSFTP();
			$testResult = true;
		}
		catch (\RuntimeException $e)
		{
			$testResult = $e->getMessage();
		}

		@ob_end_clean();
		echo '###' . json_encode($testResult) . '###';
		flush();

		$this->app->close();
	}
}