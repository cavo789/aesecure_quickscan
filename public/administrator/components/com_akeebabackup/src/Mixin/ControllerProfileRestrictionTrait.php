<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AkeebaBackup\Administrator\Mixin;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Joomla\CMS\Language\Text;

/**
 * Implements an onBeforeExecute to restrict access to profiles by access level
 */
trait ControllerProfileRestrictionTrait
{
	use ControllerProfileAccessTrait;

	protected function onBeforeExecute(&$task)
	{
		// Before doing anything, triple check that we truly have access to this profile
		$profileId = Platform::getInstance()->get_active_profile();

		if (!$this->checkProfileAccess($profileId))
		{
			Factory::getApplication()->getSession()->set('akeebabackup.profile', 1);

			$this->setRedirect('index.php?option=com_akeebabackup', Text::_('COM_AKEEBABACKUP_PROFILE_ERR_NOACCESS'), 'error');

			$this->redirect();
		}
	}

}