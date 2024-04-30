<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AkeebaBackup\Administrator\Controller;

defined('_JEXEC') or die;

use Akeeba\Component\AkeebaBackup\Administrator\Mixin\ControllerCustomACLTrait;
use Akeeba\Component\AkeebaBackup\Administrator\Mixin\ControllerEventsTrait;
use Akeeba\Component\AkeebaBackup\Administrator\Mixin\ControllerRegisterTasksTrait;
use Akeeba\Component\AkeebaBackup\Administrator\Mixin\ControllerReusableModelsTrait;
use Akeeba\Component\AkeebaBackup\Administrator\Model\UpgradeModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;

class UpgradeController extends BaseController
{
	use ControllerEventsTrait;
	use ControllerCustomACLTrait;
	use ControllerRegisterTasksTrait;
	use ControllerReusableModelsTrait;

	public function main($cachable = false, $urlparams = [])
	{
		/** @var UpgradeModel $model */
		$model = $this->getModel('Upgrade', 'Administrator');
		$model->init();

		$this->display($cachable, $urlparams);
	}

	public function migrate($cachable = false, $urlparams = [])
	{
		$this->checkToken('get');

		/** @var UpgradeModel $model */
		$model = $this->getModel('Upgrade', 'Administrator');
		$model->init();

		$results = $model->runCustomHandlerEvent('onMigrateSettings');
		$success = in_array(true, $results, true);

		$redirect = Uri::base() . 'index.php?option=com_akeebabackup';
		$message  = Text::_('COM_AKEEBABACKUP_UPGRADE_LBL_' . ($success ? 'success' : 'fail'));

		$this->setRedirect($redirect, $message, $success ? 'success' : 'error');
	}
}