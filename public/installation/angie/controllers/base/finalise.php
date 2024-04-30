<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieControllerBaseFinalise extends AController
{
	public function cleanup()
	{
		try
		{
            /** @var AngieModelBaseFinalise $model */
            $model  = $this->getThisModel();
			$result = $model->cleanup();
		}
		catch (Exception $exc)
		{
			$result = false;
		}

		// If OPcache is installed we need to reset it
		if (function_exists('opcache_reset'))
		{
			opcache_reset();
		}
		// Also do that for APC cache
		elseif (function_exists('apc_clear_cache'))
		{
			@apc_clear_cache();
		}

		// If we have removed files, ANGIE will return a 500 Internal Server
		// Error instead of the result. This works around it.
		@ob_end_clean();
		echo '###'.json_encode($result).'###';
		die();
	}
}
