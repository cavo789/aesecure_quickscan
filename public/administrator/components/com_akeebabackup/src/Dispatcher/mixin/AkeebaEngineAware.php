<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AkeebaBackup\Administrator\Dispatcher\Mixin;

defined('_JEXEC') || die();

use Akeeba\Component\AkeebaBackup\Administrator\Helper\PushMessages;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Joomla\CMS\Factory as JoomlaFactory;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Database\DatabaseInterface;

trait AkeebaEngineAware
{
	public function loadAkeebaEngine(?DatabaseInterface $dbo = null, MVCFactoryInterface $factory = null)
	{
		$app = property_exists($this, 'app') ? $this->app : JoomlaFactory::getApplication();
		$dbo = $dbo ?? $app->bootComponent('com_akeebabackup')->getContainer()->get('DatabaseDriver');

		// Necessary defines for Akeeba Engine
		if (!defined('AKEEBAENGINE'))
		{
			define('AKEEBAENGINE', 1);
		}

		if (!defined('AKEEBAROOT'))
		{
			define('AKEEBAROOT', realpath(__DIR__ . '/../../../engine'));
		}

		if (!defined('AKEEBA_CACERT_PEM'))
		{
			$caCertPath = class_exists('\\Composer\\CaBundle\\CaBundle')
				? \Composer\CaBundle\CaBundle::getBundledCaBundlePath()
				: JPATH_LIBRARIES . '/src/Http/Transport/cacert.pem';

			define('AKEEBA_CACERT_PEM', $caCertPath);
		}

		// Make sure we have a profile set throughout the component's lifetime
		$profile_id = $app->getSession()->get('akeebabackup.profile', null);

		if (is_null($profile_id))
		{
			$app->getSession()->set('akeebabackup.profile', 1);
		}

		// Load Akeeba Engine
		require_once __DIR__ . '/../../../engine/Factory.php';

		// Tell the Akeeba Engine where to load the platform from
		Platform::addPlatform('joomla', __DIR__ . '/../../../platform/Joomla');

		// Add our custom push notifications handler
		Factory::setPushClass(PushMessages::class);
		PushMessages::$mvcFactory = $factory;

		// !!! IMPORTANT !!! DO NOT REMOVE! This triggers Akeeba Engine's autoloader. Without it the next line fails!
		$DO_NOT_REMOVE = Platform::getInstance();

		// Set the DBO to the Akeeba Engine platform for Joomla
		Platform\Joomla::setDbDriver($dbo);
	}

	public function loadAkeebaEngineConfiguration()
	{
		$akeebaEngineConfig = Factory::getConfiguration();

		Platform::getInstance()->load_configuration();

		unset($akeebaEngineConfig);
	}
}