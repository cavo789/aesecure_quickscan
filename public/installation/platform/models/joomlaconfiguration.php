<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieModelJoomlaConfiguration extends AngieModelBaseConfiguration
{
	protected $joomlaVersion = '';

	public function __construct($config = array(), AContainer $container = null)
	{
		// Call the parent constructor
		parent::__construct($config, $container);

		// Get the Joomla! version from the configuration or the session
		$jVersion = $this->container->session->get('jversion', '2.5.0');

		if (array_key_exists('jversion', $config))
		{
			$jVersion = $config['jversion'];
		}

		$this->joomlaVersion = $jVersion;

		// Load the configuration variables from the session or the default configuration shipped with ANGIE
		$this->configvars = $this->container->session->get('configuration.variables');

		if (empty($this->configvars))
		{
			// Get default configuration based on the Joomla! version. The default is Joomla! 4.x.
			$v = '40';

			// Check for Joomla! 3.0 to 3.10
			if (version_compare($jVersion, '2.9999.9999', 'gt') && version_compare($jVersion, '3.9999.9999', 'lt'))
			{
				$v = '30';
			}

			// Check for Joomla! 2.5 or earlier (covers 1.6, 1.7, 2.5)
			if (version_compare($jVersion, '2.5.0', 'ge') && version_compare($jVersion, '3.0.0', 'lt'))
			{
				$v = '25';
			}

			// Check for Joomla! 1.5.x
			if (version_compare($jVersion, '1.4.99999', 'ge') && version_compare($jVersion, '1.6.0', 'lt'))
			{
				$v = '15';
			}

			// Check for Joomla! 1.0.x
			if (version_compare($jVersion, '1.4.99999', 'lt'))
			{
				die('Woah! Joomla! 1.0 is way too old for this restoration script. You need to use JoomlaPack - the Akeeba Backup predecessor the development of which we discontinued back in 2009.');
			}

			$className = 'J' . $v . 'Config';
			$filename = APATH_INSTALLATION . '/platform/models/jconfig/j' . $v . '.php';
			$this->configvars = $this->loadFromFile($filename, $className, true);

			if (!empty($this->configvars))
			{
				$this->saveToSession();
			}
		}
	}

	/**
	 * Loads the configuration information from a PHP file
	 *
	 * @param   string  $file              The full path to the file
	 * @param   string  $className         The name of the configuration class
	 * @param   bool    $useDirectInclude  Should I include the .php file (if true) or should I use the Pythia-derived
	 *                                     string parser method (if false, default). The latter is safer in case your
	 *                                     file contains arbitrary, executable PHP code instead of just a class
	 *                                     declaration.
	 *
     * @return  array
	 */
	public function loadFromFile($file, $className = 'JConfig', $useDirectInclude = false)
	{
		if (!$useDirectInclude)
		{
			return $this->extractConfiguration($file);
		}

		$ret = array();

		include_once $file;

		if (class_exists($className))
		{
			foreach (get_class_vars($className) as $key => $value)
			{
				$ret[$key] = $value;
			}
		}

		return $ret;
	}

	/**
	 * Get the contents of the configuration.php file
	 *
	 * @param   string $className The name of the configuration class, by default it's JConfig
	 *
	 * @return  string  The contents of the configuration.php file
	 */
	public function getFileContents($className = 'JConfig')
	{
		$out = <<< PHP
<?php
/**
 * Joomla Global Configuration
 *
 * This file has been modified by ANGIE, the Akeeba Backup restoration script, when restoring or transferring your site.
 * 
 * This comment is removed whe you save the Global Configuration from Joomla's interface and/or when a third party
 * extension modifies your site's Global Configuration.
 */
class $className
{

PHP;

		// Sort the configuration values to give Yet Another Hint that this file is modified by ANGIE.
		ksort($this->configvars);

		foreach ($this->configvars as $name => $value)
		{
			if (is_array($value))
			{
				$pieces = array();

				foreach ($value as $key => $data)
				{
					$data = addcslashes($data, '\'\\');
					$pieces[] = "'" . $key . "' => '" . $data . "'";
				}

				$value = "array (\n" . implode(",\n", $pieces) . "\n)";
			}
			else
			{
				// Log and temp paths in Windows systems will be forward-slash encoded
				if ((($name == 'tmp_path') || ($name == 'log_path')))
				{
					$value = $this->TranslateWinPath($value);
				}

				if (($name == 'dbtype') && ($value == 'pdomysql'))
				{
					/**
					 * Joomla! 4 renamed 'pdomysql' to 'mysql'. Internally we still use 'pdomysql' so I need to translate.
					 *
					 * This is where we translate our ANGIE db driver name to Joomla's configuration name. The opposite
					 * takes place in extractConfiguration() in this class.
					 */
					if (version_compare($this->joomlaVersion, '3.99999.99999', 'gt'))
					{
						$value = 'mysql';
					}
				}

				$value = "'" . addcslashes($value, '\'\\') . "'";
			}
			$out .= "\tpublic $" . $name . " = " . $value . ";\n";
		}

		$out .= '}' . "\n";

		return $out;
	}

	/**
	 * Extracts the Joomla! Global Configuration from a configuration.php file without including the file. This works
	 * very well with most sites, as long as the configuration was not messed with by the user.
	 *
	 * @param   string  $filePath  The absolute path to the configuration.php file
	 *
	 * @return  array
	 */
	public function extractConfiguration($filePath)
	{
		$isJoomla4 = version_compare($this->joomlaVersion, '3.99999.99999', 'gt');

		$parser = AUtilsParserAbstract::getParser();
		$ret    = $parser->parseFile($filePath, 'JConfig');

		/**
		 * Joomla! 4 renamed 'pdomysql' to 'mysql'. Internally we still use 'pdomysql' so I need to translate.
		 *
		 * This is where we translate Joomla's configuration to our ANGIE db driver name. The opposite takes
		 * place in getFileContents() in this class.
		 */
		if ($isJoomla4 && isset($ret['dbtype']) && $ret['dbtype'] = 'mysql')
		{
			$ret['dbtype'] = 'pdomysql';
		}

		return $ret;
	}
}
