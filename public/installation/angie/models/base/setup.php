<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

abstract class AngieModelBaseSetup extends AModel
{
	/**
	 * Cached copy of the configuration model
	 *
	 * @var  AngieModelWordpressConfiguration
	 */
	protected $configModel = null;

	/**
	 * Overridden constructor
	 *
	 * @param   array        $config  Configuration array
	 * @param   \AContainer  $container
	 */
	public function __construct($config = [], AContainer $container = null)
	{
		parent::__construct($config, $container);

		$this->configModel = AModel::getAnInstance('Configuration', 'AngieModel', [], $this->container);
	}

	/**
	 * Return an object containing the configuration variables we read from the
	 * state or the request.
	 *
	 * @return  stdClass
	 */
	public function getStateVariables()
	{
		static $params = [];

		if (empty($params))
		{
			$params = array_merge($params, $this->getSiteParamsVars());
			$params = array_merge($params, $this->getSuperUsersVars());
		}

		return (object) $params;
	}

	/**
	 * Apply the settings to the configuration file and the database
	 *
	 * @return  bool
	 */
	abstract public function applySettings();

	/**
	 * Are we restoring to a new host?
	 *
	 * @return bool
	 */
	public function isNewhost()
	{
		/** @var AngieModelBaseMain $mainModel */
		$mainModel = AModel::getAnInstance('Main', 'AngieModel');
		$extrainfo = $mainModel->getExtraInfo();

		if (isset($extrainfo['host']))
		{
			$uri = AUri::getInstance();

			if ($extrainfo['host']['current'] != $uri->getHost())
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Are we restoring to a different filesystem?
	 *
	 * @return bool
	 */
	public function isDifferentFilesystem()
	{
		/** @var AngieModelBaseMain $mainModel */
		$mainModel = AModel::getAnInstance('Main', 'AngieModel');
		$extrainfo = $mainModel->getExtraInfo();

		if (isset($extrainfo['root']))
		{
			// Trim any trailing slashes to be sure
			$old_path = rtrim($extrainfo['root']['current'], '/\\');
			$new_path = rtrim(APATH_ROOT, '/\\');

			if ($old_path != $new_path)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if current htaccess file contains an AddHandler rule
	 *
	 * @return bool
	 */
	public function hasAddHandler()
	{
		if (!$this->hasHtaccess())
		{
			return false;
		}

		$files = [
			'htaccess.bak',
			'.htaccess',
		];

		foreach ($files as $file)
		{
			if (!file_exists(APATH_ROOT . '/' . $file))
			{
				continue;
			}

			$contents = file_get_contents(APATH_ROOT . '/' . $file);

			if (stripos($contents, 'AddHandler') !== false || (stripos($contents, 'SetHandler') !== false))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if current .htaccess file has Kickstart tags about PHP Handlers
	 *
	 * @return  bool|string
	 */
	public function getKickstartTagContents()
	{
		if (!file_exists(APATH_ROOT . '/.htaccess'))
		{
			return false;
		}

		$contents = file_get_contents(APATH_ROOT . '/.htaccess');

		if (!$contents)
		{
			return false;
		}

		$startPos = stripos($contents, '### AKEEBA_KICKSTART_PHP_HANDLER_BEGIN ###');

		// No open marker? No need to continue then
		if ($startPos === false)
		{
			return false;
		}

		$endPos = stripos($contents, '### AKEEBA_KICKSTART_PHP_HANDLER_END ###', $startPos);

		// No ending marker??? Abort Abort!
		if ($endPos === false)
		{
			return false;
		}

		$handlerRules = substr($contents, $startPos + 42, $endPos - ($startPos + 42));
		$handlerRules = trim($handlerRules);

		// Sanity check on resulting value
		if (strlen($handlerRules) < 10)
		{
			return false;
		}

		return $handlerRules;
	}

	/**
	 * Checks if the current site has .htaccess files
	 *
	 * @return bool
	 */
	public function hasHtaccess()
	{
		$files = [
			'.htaccess',
			'htaccess.bak',
		];

		foreach ($files as $file)
		{
			if (file_exists(APATH_ROOT . '/' . $file))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if the current site has webconfig files
	 *
	 * @return bool
	 */
	public function hasWebconfig()
	{
		$files = [
			'web.config',
			'web.config.bak',
		];

		foreach ($files as $file)
		{
			if (file_exists(APATH_ROOT . '/' . $file))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Return the basic site parameters
	 *
	 * @return  array
	 */
	abstract protected function getSiteParamsVars();

	/**
	 * Return information about the most privileged administrative users of the site
	 *
	 * @return  array
	 */
	abstract protected function getSuperUsersVars();

	/**
	 * Returns the database connection variables for the default database.
	 *
	 * @return null|stdClass
	 */
	protected function getDbConnectionVars()
	{
		/** @var AngieModelDatabase $model */
		$model      = AModel::getAnInstance('Database', 'AngieModel', [], $this->container);
		$keys       = $model->getDatabaseNames();
		$firstDbKey = array_shift($keys);

		return $model->getDatabaseInfo($firstDbKey);
	}

	/**
	 * Shorthand method to get the connection to the current database
	 *
	 * @return ADatabaseDriver
	 */
	protected function getDatabase()
	{
		$connectionVars = $this->getDbConnectionVars();
		$name           = $connectionVars->dbtype;

		$options = [
			'database' => $connectionVars->dbname,
			'select'   => 1,
			'host'     => $connectionVars->dbhost,
			'user'     => $connectionVars->dbuser,
			'password' => $connectionVars->dbpass,
			'prefix'   => $connectionVars->prefix,
		];

		$db = ADatabaseFactory::getInstance()->getDriver($name, $options);

		return $db;
	}

	/**
	 * Removes password protection from a folder
	 *
	 * @param   string  $folder
	 *
	 * @return  bool
	 */
	protected function removeHtpasswd($folder)
	{
		if (!$this->hasHtpasswd())
		{
			return true;
		}

		$files = [
			'.htaccess',
			'.htpasswd',
		];

		foreach ($files as $file)
		{
			$absolutePath = $folder . '/' . $file;

			if (file_exists($absolutePath))
			{
				@unlink($absolutePath);
			}
		}

		return true;
	}

	/**
	 * Reads specified file and fetches *Handler rules
	 *
	 * @param   string  $targetFile
	 *
	 * @return  string
	 */
	protected function getHandlerRules($targetFile)
	{
		if (!file_exists($targetFile))
		{
			return '';
		}

		$contents = file_get_contents($targetFile);

		return AUtilsHandlerextract::extractHandler($contents);
	}

	/**
	 * Replace the .htaccess file with the default for this platform
	 *
	 * @return  void
	 */
	protected function replaceHtaccess()
	{
		// This method is meant to be implemented by each ANGIE platform.
	}

	/**
	 * Applies the .htaccess handling preferences
	 *
	 * @param   string  $htaccessHandling
	 *
	 * @return  void
	 */
	protected function applyHtaccessHandling($htaccessHandling = 'none')
	{
		switch ($htaccessHandling)
		{
			// No change to the .htaccess
			case 'none':
			default:
				break;

			// Replace the .htaccess file with the Joomla default
			case 'default':
				$this->replaceHtaccess();
				break;

			// Remove PHP handlers
			case 'removehandler':
				$this->removeAddHandler();
				break;

			// Replace PHP handlers
			case 'replacehandler':
				$this->replaceAddHandler();

				break;
		}
	}

	/**
	 * Removes the Add/SetHandler block(s)
	 *
	 * @return  void
	 */
	protected function removeAddHandler()
	{
		// Nothing to do? Let's stop here
		if (!$this->hasAddHandler())
		{
			return;
		}

		$files = [
			'htaccess.bak',
			'.htaccess',
		];

		foreach ($files as $file)
		{
			$this->updateHandlerRules('', APATH_ROOT . '/' . $file);
		}

		return;
	}

	/**
	 * Replaces the Add/SetHandler block(s)
	 *
	 * @return  void
	 */
	protected function replaceAddHandler()
	{
		// Do I have to fetch any *Handler rules from Kickstart or current .htaccess?
		$newRules = $this->getKickstartTagContents();

		if (!$newRules)
		{
			$newRules = $this->getHandlerRules(APATH_ROOT . '/.htaccess');
		}

		if (empty($newRules))
		{
			$newRules = '';
		}

		$files = [
			'htaccess.bak',
			'.htaccess',
		];

		foreach ($files as $file)
		{
			$this->updateHandlerRules($newRules, APATH_ROOT . '/' . $file);
		}
	}

	/**
	 * Replaces *Handler rules with new ones
	 *
	 * @param   string  $newValues   New values that should be placed
	 * @param   string  $targetFile  File to be updated
	 */
	protected function updateHandlerRules($newValues, $targetFile)
	{
		if (!file_exists($targetFile))
		{
			return true;
		}

		$contents   = file_get_contents($targetFile);
		$old_values = AUtilsHandlerextract::extractHandler($contents);

		if (!$old_values)
		{
			if (empty($newValues))
			{
				return true;
			}

			$new_data = $contents . "\n\n" . $newValues;
		}
		else
		{
			$new_data = str_replace($old_values, $newValues, $contents);
		}

		return file_put_contents($targetFile, $new_data);
	}
}
