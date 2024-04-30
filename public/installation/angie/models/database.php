<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieModelDatabase extends AModel
{
	/**
	 * The databases.json contents
	 *
	 * @var array
	 */
	private $dbjson = [];

	/**
	 * Get the maximum packet size defined in the current database connection.
	 *
	 * @return  int
	 * @since   9.1.1
	 */
	public function getCurrentMaxPacketSize($connectionVars)
	{
		if (empty($connectionVars) || !is_object($connectionVars))
		{
			return 1048576;
		}

		$name = $connectionVars->dbtype;

		$options = [
			'database' => $connectionVars->dbname,
			'select'   => 1,
			'host'     => $connectionVars->dbhost,
			'user'     => $connectionVars->dbuser,
			'password' => $connectionVars->dbpass,
			'prefix'   => $connectionVars->prefix,
			'ssl'      => [
				'enable'             => (bool) $connectionVars->dbencryption,
				'cipher'             => $connectionVars->dbsslcipher,
				'ca'                 => $connectionVars->dbsslca,
				'key'                => $connectionVars->dbsslkey,
				'cert'               => $connectionVars->dbsslcert,
				'verify_server_cert' => (bool) $connectionVars->dbsslverifyservercert,
			],
		];

		try
		{
			$db = ADatabaseFactory::getInstance()->getDriver($name, $options);

			return $db->getMaxPacketSize();
		}
		catch (Exception $e)
		{
			return 1048576;
		}
	}

	/**
	 * Returns an object with a database's connection information
	 *
	 * @param   string  $key  The database's key (name of SQL file)
	 *
	 * @return  null|stdClass
	 */
	public function getDatabaseInfo($key)
	{
		$dbjson = $this->getDatabasesJson();

		if (array_key_exists($key, $dbjson))
		{
			return (object) $dbjson[$key];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Returns the keys of all available database definitions
	 *
	 * @return array
	 */
	public function getDatabaseNames()
	{
		$dbjson = $this->getDatabasesJson();

		return array_keys($dbjson);
	}

	/**
	 * Returns the cached databases.json information, parsing the databases.json
	 * file if necessary.
	 *
	 * @return array
	 */
	public function getDatabasesJson()
	{
		if (empty($this->dbjson))
		{
			$this->dbjson = $this->container->session->get('databases.dbjson', []);

			if (empty($this->dbjson))
			{
				$filename = APATH_INSTALLATION . '/sql/databases.json';

				if (file_exists($filename))
				{
					$raw_data     = file_get_contents($filename);
					$this->dbjson = json_decode($raw_data, true);
				}

				if (!empty($this->dbjson))
				{
					// Add the custom options
					$temp    = [];
					$siteSQL = null;

					foreach ($this->dbjson as $key => $data)
					{
						$data = array_merge([
							'dbtech'                 => null,
							'dbname'                 => null,
							'sqlfile'                => 'site.sql',
							'marker'                 => null,
							'dbhost'                 => null,
							'dbport'                 => null,
							'dbsocket'               => null,
							'dbuser'                 => null,
							'dbpass'                 => null,
							'prefix'                 => 'jos_',
							'dbencryption'           => false,
							'dbsslcipher'            => null,
							'dbsslca'                => null,
							'dbsslkey'               => null,
							'dbsslcert'              => null,
							'dbsslverifyservercert'  => false,
							'parts'                  => null,
							'tables'                 => [],
							'existing'               => 'drop',
							'foreignkey'             => true,
							'noautovalue'            => true,
							'replace'                => false,
							'utf8db'                 => false,
							'utf8tables'             => false,
							'utf8mb4'                => true,
							'maxexectime'            => 5,
							'throttle'               => 250,
							'break_on_failed_create' => true,
							'break_on_failed_insert' => true,
						], $data);

						// Skip section that have the db tech set to none (flat-file CMS)
						if (strtolower($data['dbtech']) == 'none')
						{
							continue;
						}

						// If we are using SQLite, let's replace any token we found inside the dbname index
						if ($data['dbtype'] == 'sqlite')
						{
							$data['dbname'] = str_replace('#SITEROOT#', APATH_ROOT, $data['dbname']);
						}

						if ($key == 'site.sql')
						{
							$siteSQL = $data;
						}
						else
						{
							$temp[$key] = $data;
						}
					}

					// Add the site db definition only if it was defined
					if ($siteSQL)
					{
						$temp = array_merge(['site.sql' => $siteSQL], $temp);
					}

					$this->dbjson = $temp;
				}

				$this->container->session->set('databases.dbjson', $this->dbjson);
			}
		}

		return $this->dbjson;
	}

	/**
	 * Detects if we have a flag file for large columns; if so it returns its contents (longest query we will have to
	 * run)
	 *
	 * @return  int
	 */
	public function getLargeTablesDetectedValue()
	{
		$file = APATH_INSTALLATION . '/large_tables_detected';

		if (!file_exists($file))
		{
			return 0;
		}

		$bytes = (int) file_get_contents($file);

		return $bytes;
	}

	/**
	 * Saves the (modified) databases information to the session
	 */
	public function saveDatabasesJson()
	{
		$this->container->session->set('databases.dbjson', $this->dbjson);
	}

	/**
	 * Sets a database's connection information
	 *
	 * @param   string  $key   The database's key (name of SQL file)
	 * @param   mixed   $data  The database's data (stdObject or array)
	 */
	public function setDatabaseInfo($key, $data)
	{
		$dbjson = $this->getDatabasesJson();

		$this->dbjson[$key] = (array) $data;

		$this->saveDatabasesJson();
	}
}
