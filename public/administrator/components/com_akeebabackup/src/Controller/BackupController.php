<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AkeebaBackup\Administrator\Controller;

defined('_JEXEC') || die;

use Akeeba\Component\AkeebaBackup\Administrator\Helper\Utils;
use Akeeba\Component\AkeebaBackup\Administrator\Mixin\ControllerProfileAccessTrait;
use Akeeba\Component\AkeebaBackup\Administrator\Mixin\ControllerCustomACLTrait;
use Akeeba\Component\AkeebaBackup\Administrator\Mixin\ControllerEventsTrait;
use Akeeba\Component\AkeebaBackup\Administrator\Mixin\ControllerRegisterTasksTrait;
use Akeeba\Component\AkeebaBackup\Administrator\Model\BackupModel;
use Akeeba\Engine\Platform;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Input\Input;

class BackupController extends BaseController
{
	use ControllerEventsTrait;
	use ControllerCustomACLTrait;
	use ControllerRegisterTasksTrait;
	use ControllerProfileAccessTrait;

	public function __construct($config = [], MVCFactoryInterface $factory = null, ?CMSApplication $app = null, ?Input $input = null)
	{
		parent::__construct($config, $factory, $app, $input);

		$this->registerControllerTasks();
	}

	/**
	 * This task handles the AJAX requests
	 */
	public function ajax()
	{
		$profile_id = $this->input->get('profileid', Platform::getInstance()->get_active_profile(), 'int');

		// Double check that the user is actually allowed to access this profile
		if (!$this->checkProfileAccess($profile_id))
		{
			$ret_array = [
				'HasRun'   => 0,
				'Domain'   => 'init',
				'Step'     => '',
				'Substep'  => '',
				'Error'    => Text::_('COM_AKEEBABACKUP_BACKUP_ERROR_PROFILE_NO_ACCESS'),
				'Warnings' => [],
				'Progress' => 0,
			];

			// We use this nasty trick to avoid broken 3PD plugins from barfing all over our output
			@ob_end_clean();
			header('Content-type: text/plain');
			header('Connection: close');
			echo '###' . json_encode($ret_array) . '###';
			flush();

			$this->app->close();
		}

		/** @var BackupModel $model */
		$model = $this->getModel('Backup', 'Administrator');

		// Push all necessary information to the model's state
		$model->setState('profile', $profile_id);
		$model->setState('ajax', $this->input->get('ajax', '', 'cmd'));
		$model->setState('description', $this->input->get('description', '', 'string'));
		$model->setState('comment', $this->input->get('comment', '', 'html'));
		$model->setState('jpskey', $this->input->get('jpskey', '', 'raw'));
		$model->setState('angiekey', $this->input->get('angiekey', '', 'raw'));
		$model->setState('backupid', $this->input->get('backupid', null, 'cmd'));
		$model->setState('tag', $this->input->get('tag', 'backend', 'cmd'));
		$model->setState('errorMessage', $this->input->getString('errorMessage', ''));

		// System Restore Point backup state variables (obsolete)
		$model->setState('type', strtolower($this->input->get('type', '', 'cmd')));
		$model->setState('name', strtolower($this->input->get('name', '', 'cmd')));
		$model->setState('group', strtolower($this->input->get('group', '', 'cmd')));
		$model->setState('customdirs', $this->input->get('customdirs', [], 'array'));
		$model->setState('customfiles', $this->input->get('customfiles', [], 'array'));
		$model->setState('extraprefixes', $this->input->get('extraprefixes', [], 'array'));
		$model->setState('customtables', $this->input->get('customtables', [], 'array'));
		$model->setState('skiptables', $this->input->get('skiptables', [], 'array'));
		$model->setState('langfiles', $this->input->get('langfiles', [], 'array'));
		$model->setState('xmlname', $this->input->getString('xmlname', ''));

		// Set up the tag
		define('AKEEBA_BACKUP_ORIGIN', $this->input->get('tag', 'backend', 'cmd'));

		// Run the backup step
		$ret_array = $model->runBackup();

		// We use this nasty trick to avoid broken 3PD plugins from barfing all over our output
		@ob_end_clean();
		header('Content-type: text/plain');
		header('Connection: close');
		echo '###' . json_encode($ret_array) . '###';
		flush();

		$this->app->close();
	}

	/**
	 * Default task; shows the initial page where the user selects a profile and enters description and comment
	 */
	public function display($cachable = false, $urlparams = [])
	{
		$document   = $this->app->getDocument();
		$viewType   = $document->getType();
		$viewName   = $this->input->get('view', $this->default_view);
		$viewLayout = $this->input->get('layout', 'default', 'string');

		$view = $this->getView(
			$viewName, $viewType, '',
			[
				'base_path' => $this->basePath,
				'layout'    => $viewLayout,
			]
		);

		// Push the Control Panel model
		$controlPanelModel = $this->getModel('Controlpanel', 'Administrator');
		$view->setModel($controlPanelModel, false);

		// Get/Create the default model
		/** @var BackupModel $model */
		$model = $this->getModel('Backup', 'Administrator');
		$view->setModel($model, true);

		// Push the document
		$view->document = $document;

		// Did the user ask to switch the active profile?
		$newProfile = $this->input->get('profileid', -10, 'int');
		$autostart  = $this->input->get('autostart', 0, 'int');

		if (is_numeric($newProfile) && ($newProfile > 0))
		{
			/**
			 * We have to remove CSRF protection due to the way the Joomla administrator menu manager works. Menu item
			 * options are passed as URL parameters. However, we cannot pass dynamic parameters (like the token). This
			 * means that a user can create a menu item with a specific backup profile ID. Normally this would cause a
			 * 403 which is frustrating to the user because they might want to give their client the option to run a
			 * backup with a specific profile AND let them enter a description and comment. Therefore we have to remove
			 * the CSRF protection.
			 *
			 * NB! We do understand the potential risk involved. Between Joomla's BAD implementation of custom
			 * administrator menus and user demands for features we have to (have these very vocal users and everyone
			 * else) assume that (actually really small) risk.
			 */
			// $this->checkToken();
			$this->app->getSession()->set('akeebabackup.profile', $newProfile);

			/**
			 * DO NOT REMOVE!
			 *
			 * The Model will only try to load the configuration after nuking the factory. This causes Profile 1 to be
			 * loaded first. Then it figures out it needs to load a different profile and it does – but the protected keys
			 * are NOT replaced, meaning that certain configuration parameters are not replaced. Most notably, the chain.
			 * This causes backups to behave weirdly. So, DON'T REMOVE THIS UNLESS WE REFACTOR THE MODEL.
			 */
			Platform::getInstance()->load_configuration($newProfile);
		}

		// Deactivate the menus
		$this->app->input->set('hidemainmenu', 1);

		// Sanitize the return URL
		$returnUrl = $this->input->getRaw('returnurl', '');
		$returnUrl = Utils::safeDecodeReturnUrl($returnUrl);

		// Push data to the model
		//var_dump($model->getState('profile'));
		$model->setState('profile', $this->input->get('profileid', -10, 'int'));
		$model->setState('description', $this->input->get('description', '', 'string'));
		$model->setState('comment', $this->input->get('comment', '', 'html'));
		$model->setState('ajax', $this->input->get('ajax', '', 'cmd'));
		$model->setState('autostart', $autostart);
		$model->setState('jpskey', $this->input->get('jpskey', '', 'raw'));
		$model->setState('angiekey', $this->input->get('angiekey', '', 'raw'));
		$model->setState('returnurl', $returnUrl);
		$model->setState('backupid', $this->input->get('backupid', null, 'cmd'));

		$view->display();

		return $this;
	}
}