<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

/**
 * Pimple service provider interface.
 *
 * @author Fabien Potencier
 * @author Dominik Zogg
 *
 * @codeCoverageIgnore
 */
interface ServiceProviderInterface
{
	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param APimple $pimple An Container instance
	 */
	public function register(APimple $pimple);
}
