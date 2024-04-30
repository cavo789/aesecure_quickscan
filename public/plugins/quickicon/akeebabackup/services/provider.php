<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Plugin\Quickicon\AkeebaBackup\Extension\AkeebaBackup;

return new class implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   9.0.0
	 */
	public function register(Container $container)
	{
		$container->registerServiceProvider(new MVCFactory('Akeeba\\Component\\AkeebaBackup'));

		$container->set(
			PluginInterface::class,
			function (Container $container)
			{
				$config = (array) PluginHelper::getPlugin('quickicon', 'akeebabackup');

				$plugin = new AkeebaBackup(
					$container->get(DispatcherInterface::class),
					Factory::getApplication()->getDocument(),
					$config
				);

				$plugin->setMVCFactory($container->get(MVCFactoryInterface::class));
				$plugin->setApplication(Factory::getApplication());
				$plugin->setDatabase($container->get('DatabaseDriver'));

				return $plugin;
			}
		);
	}
};
