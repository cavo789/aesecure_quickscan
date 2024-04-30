<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

if (!defined('DATA_CHUNK_LENGTH'))
{
	define('DATA_CHUNK_LENGTH', 65536);            // How many bytes to read per step
	define('MAX_QUERY_LINES', 300);            // How many lines may be considered to be one query (except text lines)
}

abstract class ADatabaseRestore
{
	/**
	 * A list of error codes (numbers) which should not block cause the
	 * restoration to halt. Used for soft errors and warnings which do not cause
	 * problems with the restored site.
	 *
	 * @var  array
	 */
	protected $allowedErrorCodes = [];

	/**
	 * A list of comment line delimiters. Lines starting with these strings are
	 * skipped over during restoration.
	 *
	 * @var  array
	 */
	protected $comment = [];

	/**
	 * A list of the part files of the database dump we are importing
	 *
	 * @var  array
	 */
	protected $partsMap = [];

	/**
	 * The total size of all database dump files
	 *
	 * @var  int
	 */
	protected $totalSize = 0;

	/**
	 * The part file currently being processed
	 *
	 * @var  string
	 */
	protected $curpart = null;

	/**
	 * The offset into the part file being processed
	 *
	 * @var  int
	 */
	protected $foffset = 0;

	/**
	 * The total size of all database dump files processed so far
	 *
	 * @var  int
	 */
	protected $runSize = 0;

	/**
	 * The file pointer to the SQL file currently being restored
	 *
	 * @var  resource
	 */
	protected $file = null;

	/**
	 * The filename of the SQL file currently being restored
	 *
	 * @var  string
	 */
	protected $filename = null;

	/**
	 * The starting line number of processing the current file
	 *
	 * @var  integer
	 */
	protected $start = null;

	/**
	 * The ATimer object used to guard against timeouts
	 *
	 * @var  ATimer
	 */
	protected $timer = null;

	/**
	 * The database file key used to determine which dump we're restoring
	 *
	 * @var  string
	 */
	protected $dbkey = null;

	/**
	 * Cached copy of the up-to-date databases.json values of the database dump
	 * we are currently restoring.
	 *
	 * @var  array
	 */
	protected $dbjsonValues = null;

	/**
	 * The database driver used to connect to this database
	 *
	 * @var  ADatabaseDriver
	 */
	protected $db = null;

	/**
	 * Total queries run so far
	 *
	 * @var  integer
	 */
	protected $totalqueries = null;

	/**
	 * Line number in the current file being processed
	 *
	 * @var  integer
	 */
	protected $linenumber = null;

	/**
	 * Number of queries run in this restoration step
	 *
	 * @var  integer
	 */
	protected $queries = null;

	/**
	 * Application container
	 *
	 * @var   AContainer
	 */
	protected $container;

	/**
	 * The full path to a log file which contains failed queries which were ignored
	 *
	 * @var   string
	 */
	protected $logFile;

	/**
	 * Should I halt the restoration when a CREATE query fails?
	 *
	 * @var   bool
	 */
	protected $breakOnFailedCreate = true;

	/**
	 * Should I halt the restoration when an INSERT (or other non-CREATE) query fails?
	 *
	 * @var   bool
	 */
	protected $breakOnFailedInsert = true;

	/**
	 * How many SQL queries resulted in an error during the restoration
	 *
	 * @var   int
	 */
	protected $errorcount = 0;

	/**
	 * Marker denoting a new line has started
	 *
	 * @var string
	 */
	protected $marker = "\n";

	/**
	 * @var int
	 */
	protected $totalsizeread;

	/**
	 * List of specific entities that we want to restore. Leave empty to restore all entities in the databse
	 *
	 * @var array
	 */
	protected $specific_entities = [];

	/**
	 * Public constructor. Initialises the database restoration engine.
	 *
	 * @param   string      $dbkey         The databases.json key of the current database
	 * @param   string      $dbjsonValues  The databases.json configuration variables for the current database
	 * @param   AContainer  $container     Application container
	 *
	 * @throws AExceptionApp
	 */
	public function __construct($dbkey, $dbjsonValues, AContainer $container = null)
	{
		if (is_null($container))
		{
			$container = AApplication::getInstance()->getContainer();
		}

		$this->container = $container;

		$this->dbkey        = $dbkey;
		$this->dbjsonValues = $dbjsonValues;

		$this->populatePartsMap();

		if (!key_exists('maxexectime', $this->dbjsonValues))
		{
			$this->dbjsonValues['maxexectime'] = 5;
		}

		if (!key_exists('runtimebias', $this->dbjsonValues))
		{
			$this->dbjsonValues['runtimebias'] = 75;
		}

		if (!key_exists('failed_query_log', $this->dbjsonValues))
		{
			$this->dbjsonValues['failed_query_log'] = sprintf('failed_queries_%s.log', $this->sanitizeDBKey($dbkey));
		}

		if (!key_exists('break_on_failed_create', $this->dbjsonValues))
		{
			$this->dbjsonValues['break_on_failed_create'] = true;
		}

		if (!key_exists('break_on_failed_insert', $this->dbjsonValues))
		{
			$this->dbjsonValues['break_on_failed_insert'] = true;
		}

		if (!key_exists('marker', $this->dbjsonValues))
		{
			$this->dbjsonValues['marker'] = "\n";
		}

		$this->logFile             = APATH_TEMPINSTALL . '/' . $this->dbjsonValues['failed_query_log'];
		$this->breakOnFailedCreate = $this->dbjsonValues['break_on_failed_create'];
		$this->breakOnFailedInsert = $this->dbjsonValues['break_on_failed_insert'];
		$this->marker              = $this->dbjsonValues['marker'];
		$this->timer               = new ATimer(0, (int) $this->dbjsonValues['maxexectime'], (int) $this->dbjsonValues['runtimebias']);
	}

	/**
	 * Gets an instance of the database restoration class based on the $dbkey.
	 * If it doesn't exist, a new instance is created based on $dbkey and
	 * $dbjsonValues provided.
	 *
	 * @staticvar  array  $instances  The array of ADatabaseRestore instances
	 *
	 * @param   string      $dbkey         The key of the database being restored
	 * @param   array       $dbjsonValues  The database restoration configuration variables
	 * @param   AContainer  $container     Application container
	 *
	 * @return  ADatabaseRestore
	 *
	 * @throws Exception
	 */
	public static function getInstance($dbkey, $dbjsonValues = null, AContainer $container = null)
	{
		static $instances = [];

		if (!array_key_exists($dbkey, $instances))
		{
			if (empty($dbjsonValues))
			{
				throw new Exception(AText::sprintf('ANGI_RESTORE_ERROR_UNKNOWNKEY', $dbkey));
			}

			if (is_object($dbjsonValues))
			{
				$dbjsonValues = (array) $dbjsonValues;
			}

			if (is_null($container))
			{
				$container = AApplication::getInstance()->getContainer();
			}

			$class             = 'ADatabaseRestore' . ucfirst($dbjsonValues['dbtype']);
			$instances[$dbkey] = new $class($dbkey, $dbjsonValues, $container);
		}

		return $instances[$dbkey];
	}

	/**
	 * Public destructor. Closes open handlers.
	 */
	public function __destruct()
	{
		if (is_object($this->db) && ($this->db instanceof ADatabaseDriver))
		{
			try
			{
				$this->db->disconnect();
			}
			catch (Exception $exc)
			{
				// Nothing. We just never want to fail when closing the
				// database connection.
			}
		}

		if (is_resource($this->file))
		{
			@fclose($this->file);
			$this->file = null;
		}
	}

	/**
	 * Setter to specify only a subset of entities that should be restored
	 *
	 * @param   array  $specific_entities
	 */
	public function setSpecificEntities(array $specific_entities)
	{
		$this->specific_entities = $specific_entities;

		$this->setToStorage('specific_entities', $this->specific_entities);
		$this->container->session->saveData();
	}

	public function shouldRestoreEntity($entityName)
	{
		// List is empty, we have to restore everything
		if (!$this->specific_entities)
		{
			return true;
		}

		// Do we have a white list of entities? If so process it only if it's inside it
		if (in_array($entityName, $this->specific_entities))
		{
			return true;
		}

		// Otherwise ignore it
		return false;
	}

	/**
	 * Remove all cached information from the session storage
	 */
	public function removeInformationFromStorage()
	{
		$variables = [
			'start', 'foffset', 'totalqueries', 'curpart',
			'partsmap', 'totalsize', 'runsize', 'errorcount', 'specific_entities',
		];
		$session   = $this->container->session;

		foreach ($variables as $var)
		{
			$session->remove('restore.' . $this->dbkey . '.' . $var);
		}
	}

	/**
	 * Runs a restoration step and returns an array to be used in the response.
	 *
	 * @return  array
	 *
	 * @throws Exception
	 */
	public function stepRestoration()
	{
		$parts = $this->getParam('parts', 1);

		$this->openFile();

		$this->linenumber    = $this->start;
		$this->totalsizeread = 0;
		$this->queries       = 0;

		// In the beginning of the restoration drop all tables, if the user has selected to do so.
		if (($this->curpart == 0) && ($this->foffset == 0))
		{
			$this->conditionallyDropAll();
		}

		while ($this->timer->getTimeLeft() > 0)
		{
			// Get the next query line
			try
			{
				$query = $this->readNextLine();
			}
			catch (Exception $exc)
			{
				if ($exc->getCode() == 200)
				{
					break;
				}
				elseif ($exc->getCode() == 201)
				{
					continue;
				}
			}

			// Process the query line, running drop/rename queries as necessary
			$this->processQueryLine($query);

			// Update variables
			$this->totalsizeread += strlen($query);
			$this->totalqueries++;
			$this->queries++;
			$query = "";
			$this->linenumber++;
		}

		// Get the current file position
		$current_foffset = ftell($this->file);

		if ($current_foffset === false)
		{
			if (is_resource($this->file))
			{
				@fclose($this->file);
				$this->file = null;
			}

			throw new Exception(AText::_('ANGI_RESTORE_ERROR_CANTREADPOINTER'));
		}

		if (is_null($this->foffset))
		{
			$this->foffset = 0;
		}

		$bytes_in_step = $current_foffset - $this->foffset;
		$this->runSize = (is_null($this->runSize) ? 0 : $this->runSize) + $bytes_in_step;
		$this->foffset = $current_foffset;

		// Return statistics
		$bytes_togo = $this->totalSize - $this->runSize;

		// Check for global EOF
		if (($this->curpart >= ($parts - 1)) && feof($this->file))
		{
			$bytes_togo = 0;
		}

		// Save variables in storage
		$this->setToStorage('start', $this->start);
		$this->setToStorage('foffset', $this->foffset);
		$this->setToStorage('totalqueries', $this->totalqueries);
		$this->setToStorage('runsize', $this->runSize);
		$this->setToStorage('errorcount', $this->errorcount);

		if ($bytes_togo == 0)
		{
			// Clear stored variables if we're finished
			$lines_togo   = '0';
			$lines_tota   = $this->linenumber - 1;
			$queries_togo = '0';
			$queries_tota = $this->totalqueries;
			$this->removeInformationFromStorage();
		}

		$this->container->session->saveData();

		// Calculate estimated time
		$bytesPerSecond = $bytes_in_step / $this->timer->getRunningTime();

		if ($bytesPerSecond <= 0.01)
		{
			$remainingSeconds = 120;
		}
		else
		{
			$remainingSeconds = round($bytes_togo / $bytesPerSecond, 0);
		}

		// Close the file if it is still open at this point
		if (!empty($this->file) && is_resource($this->file))
		{
			@fclose($this->file);
			$this->file = null;
		}

		// Return meaningful data
		return [
			'percent'          => round(100 * ($this->runSize / $this->totalSize), 1),
			'restored'         => $this->sizeformat($this->runSize),
			'total'            => $this->sizeformat($this->totalSize),
			'queries_restored' => $this->totalqueries,
			'errorcount'       => $this->errorcount,
			'errorlog'         => $this->getLogPath(),
			'current_line'     => $this->linenumber,
			'current_part'     => $this->curpart,
			'total_parts'      => $parts,
			'eta'              => $this->etaformat($remainingSeconds),
			'error'            => '',
			'done'             => ($bytes_togo == 0) ? '1' : '0',
		];
	}

	/**
	 * Returns the cached total size of the SQL dump.
	 *
	 * @param   boolean  $use_units  Should I automatically figure out and use
	 *
	 * @return  string
	 */
	public function getTotalSize($use_units = false)
	{
		$size = $this->totalSize;

		if ($use_units)
		{
			$size = $this->sizeformat($size);
		}

		return $size;
	}

	public function getTimer()
	{
		return $this->timer;
	}

	/**
	 * Remove the failed query log. You need to call this at the beginning of the restoration.
	 *
	 * @return  void
	 */
	public function removeLog()
	{
		if (empty($this->logFile))
		{
			return;
		}

		if (@file_exists($this->logFile))
		{
			@unlink($this->logFile);
		}
	}

	/**
	 * Returns the full filesystem path of the failed query log file.
	 *
	 * @return  string
	 */
	public function getLogPath()
	{
		return $this->logFile;
	}

	/**
	 * Return a value from the session storage
	 *
	 * @param   string  $var      The name of the variable
	 * @param   mixed   $default  The default value (null if ommitted)
	 *
	 * @return  mixed  The variable's value
	 */
	protected function getFromStorage($var, $default = null)
	{
		$session = $this->container->session;

		return $session->get('restore.' . $this->dbkey . '.' . $var, $default);
	}

	/**
	 * Sets a value to the session storage
	 *
	 * @param   string  $var    The name of the variable
	 * @param   mixed   $value  The value to store
	 */
	protected function setToStorage($var, $value)
	{
		$session = $this->container->session;

		$session->set('restore.' . $this->dbkey . '.' . $var, $value);
	}

	/**
	 * Gets a database configuration variable, as cached in the $dbjsonValues
	 * array
	 *
	 * @param   string  $key      The name of the variable to get
	 * @param   mixed   $default  Default value (null if skipped)
	 *
	 * @return  mixed  The configuration variable's value
	 */
	protected function getParam($key, $default = null)
	{
		if (is_array($this->dbjsonValues))
		{
			if (array_key_exists($key, $this->dbjsonValues))
			{
				return $this->dbjsonValues[$key];
			}
			else
			{
				return $default;
			}
		}
		else
		{
			return $default;
		}
	}

	protected function populatePartsMap()
	{
		// Nothing to do if it's already populated, right?
		if (!empty($this->partsMap))
		{
			return;
		}

		// First, try to fetch from the session storage
		$this->totalSize         = $this->getFromStorage('totalsize', 0);
		$this->runSize           = $this->getFromStorage('runsize', 0);
		$this->partsMap          = $this->getFromStorage('partsmap', []);
		$this->curpart           = $this->getFromStorage('curpart', 0);
		$this->foffset           = $this->getFromStorage('foffset', 0);
		$this->start             = $this->getFromStorage('start', 0);
		$this->totalqueries      = $this->getFromStorage('totalqueries', 0);
		$this->errorcount        = $this->getFromStorage('errorcount', 0);
		$this->specific_entities = $this->getFromStorage('specific_entities', []);

		// If that didn't work try a full initalisation
		if (empty($this->partsMap))
		{
			$sqlfile = $this->dbjsonValues['sqlfile'];

			$parts = $this->getParam('parts', 1);

			$this->partsMap   = [];
			$path             = APATH_INSTALLATION . '/sql';
			$this->totalSize  = 0;
			$this->runSize    = 0;
			$this->curpart    = 0;
			$this->foffset    = 0;
			$this->errorcount = 0;

			for ($index = 0; $index <= $parts; $index++)
			{
				if ($index == 0)
				{
					$basename = $sqlfile;
				}
				else
				{
					$basename = substr($sqlfile, 0, -4) . '.s' . sprintf('%02u', $index);
				}

				$file = $path . '/' . $basename;
				if (!file_exists($file))
				{
					$file = 'sql/' . $basename;
				}
				$filesize         = @filesize($file);
				$this->totalSize  += intval($filesize);
				$this->partsMap[] = $file;
			}

			$this->setToStorage('totalsize', $this->totalSize);
			$this->setToStorage('runsize', $this->runSize);
			$this->setToStorage('partsmap', $this->partsMap);
			$this->setToStorage('curpart', $this->curpart);
			$this->setToStorage('foffset', $this->foffset);
			$this->setToStorage('start', $this->start);
			$this->setToStorage('totalqueries', $this->totalqueries);
			$this->setToStorage('errorcount', $this->errorcount);

			$this->container->session->saveData();
		}
	}

	/**
	 * Proceeds to opening the next SQL part file
	 * @return bool True on success
	 */
	protected function getNextFile()
	{
		$parts = $this->getParam('parts', 1);

		if ($this->curpart >= ($parts - 1))
		{
			return false;
		}

		$this->curpart++;
		$this->foffset = 0;

		$this->setToStorage('curpart', $this->curpart);
		$this->setToStorage('foffset', $this->foffset);

		$this->container->session->saveData();

		// Close an already open file (if one was indeed already open)
		if (!empty($this->file) && is_resource($this->file))
		{
			@fclose($this->file);
			$this->file = null;
		}

		return $this->openFile();
	}

	/**
	 * Opens the SQL part file whose ID is specified in the $curpart variable
	 * and updates the $file, $start and $foffset variables.
	 *
	 * @return bool True on success
	 *
	 * @throws \Exception
	 */
	protected function openFile()
	{
		// If there is an already open file, close it before proceeding
		if (!empty($this->file) && is_resource($this->file))
		{
			@fclose($this->file);
			$this->file = null;
		}

		if (!is_numeric($this->curpart))
		{
			$this->curpart = 0;
		}
		$this->filename = $this->partsMap[$this->curpart];

		if (!$this->file = @fopen($this->filename, "r"))
		{
			throw new Exception(AText::sprintf('ANGI_RESTORE_ERROR_CANTOPENDUMPFILE', $this->filename));
		}
		else
		{
			// Get the file size
			if (fseek($this->file, 0, SEEK_END) == 0)
			{
				$this->filesize = ftell($this->file);
			}
			else
			{
				throw new Exception(AText::_('ANGI_RESTORE_ERROR_UNKNOWNFILESIZE'));
			}
		}

		// Check start and foffset are numeric values
		if (!is_numeric($this->start) || !is_numeric($this->foffset))
		{
			throw new Exception(AText::_('ANGI_RESTORE_ERROR_INVALIDPARAMETERS'));
		}

		$this->start   = floor($this->start);
		$this->foffset = floor($this->foffset);

		// Check $foffset upon $filesize
		if ($this->foffset > $this->filesize)
		{
			throw new Exception(AText::_('ANGI_RESTORE_ERROR_AFTEREOF'));
		}

		// Set file pointer to $foffset
		if (fseek($this->file, $this->foffset) != 0)
		{
			throw new Exception(AText::_('ANGI_RESTORE_ERROR_CANTSETOFFSET'));
		}

		return true;
	}

	/**
	 * Returns the instance of the database driver, creating it if it doesn't
	 * exist.
	 *
	 * @return  ADatabaseDriver
	 *
	 * @throws RuntimeException
	 */
	protected function getDatabase($selectDatabase = true)
	{
		if (!is_object($this->db))
		{
			$options = [
				'driver'   => $this->dbjsonValues['dbtype'],
				'database' => $this->dbjsonValues['dbname'],
				'select'   => 0,
				'host'     => $this->dbjsonValues['dbhost'],
				'user'     => $this->dbjsonValues['dbuser'],
				'password' => $this->dbjsonValues['dbpass'],
				'prefix'   => $this->dbjsonValues['prefix'],
				'ssl'      => [
					'enable'             => (bool) $this->dbjsonValues['dbencryption'],
					'cipher'             => $this->dbjsonValues['dbsslcipher'] ?: null,
					'ca'                 => $this->dbjsonValues['dbsslca'] ?: null,
					'key'                => $this->dbjsonValues['dbsslkey'] ?: null,
					'cert'               => $this->dbjsonValues['dbsslcert'] ?: null,
					'verify_server_cert' => (bool) $this->dbjsonValues['dbsslverifyservercert'],
				],
			];

			if (!$selectDatabase)
			{
				unset($options['database']);
			}

			$class = 'ADatabaseDriver' . ucfirst(strtolower($options['driver']));

			try
			{
				$this->db = new $class($options);
				$this->db->setUTF();
			}
			catch (RuntimeException $e)
			{
				throw new RuntimeException(sprintf('Unable to connect to the Database: %s', $e->getMessage()));
			}
		}

		return $this->db;
	}

	/**
	 * Executes a SQL statement, ignoring errors in the $allowedErrorCodes list.
	 *
	 * @param   string  $sql  The SQL statement to execute
	 *
	 * @return  mixed  A database cursor on success, false on failure
	 *
	 * @throws  Exception
	 */
	protected function execute($sql)
	{
		$db = $this->getDatabase();

		try
		{
			$db->setQuery($sql);
			$result = $db->execute();
		}
		catch (Exception $exc)
		{
			$result = false;

			// Let's replace the prefix with the current one so users can easily copy/paste the queries
			$sql = $db->replacePrefix($sql);

			$this->handleFailedQuery($sql, $exc);
		}

		return $result;
	}

	/**
	 * Read the next line from the database dump
	 *
	 * @return  string  The query string
	 *
	 * @throws Exception
	 */
	protected function readNextLine()
	{
		$parts = $this->getParam('parts', 1);

		$query = "";

		while (is_resource($this->file) && !feof($this->file) && (strpos($query, $this->marker) === false))
		{
			$query .= fgets($this->file, DATA_CHUNK_LENGTH);
		}

		// An empty query is most likely EOF. Are we done or should I skip to the next file?
		if (empty($query) || ($query === false))
		{
			if ($this->curpart >= ($parts - 1))
			{
				throw new Exception('All done', 200);
			}

			// Register the bytes read
			$current_foffset = @ftell($this->file);

			if (is_null($this->foffset))
			{
				$this->foffset = 0;
			}

			$this->runSize = (is_null($this->runSize) ? 0 : $this->runSize) + ($current_foffset - $this->foffset);

			// Get the next file
			$this->getNextFile();

			// Rerun the fetcher
			throw new Exception('Continue', 201);
		}

		/**
		 * If we have not reached EOF and the query does not end with our marker we have read too much data. We need to
		 * locate the marker, roll back the file pointer to this point and only keep our query up to the marker.
		 */
		if (!feof($this->file) && substr($query, strlen($this->marker)) != $this->marker)
		{
			$rollback = strlen($query) - strpos($query, $this->marker);
			$query    = substr($query, 0, -$rollback);

			fseek($this->file, -$rollback + 1, SEEK_CUR);
		}

		// Handle DOS linebreaks
		$query = str_replace("\r\n", "\n", $query);
		$query = str_replace("\r", "\n", $query);

		// Skip comments and blank lines only if NOT in parents
		$skipline = false;
		reset($this->comment);

		foreach ($this->comment as $comment_value)
		{
			if (trim($query) == "" || strpos($query, $comment_value) === 0)
			{
				$skipline = true;
				break;
			}
		}

		if ($skipline)
		{
			$this->linenumber++;
			throw new Exception('Continue', 201);
		}

		$query = trim($query, " \n");
		$query = rtrim($query, ';');

		return $query;
	}

	/**
	 * Processes the query line in the best way each restoration engine sees
	 * fit. This method is supposed to take care of backing up and dropping
	 * tables, changing table collation if requested and converting INSERT to
	 * REPLACE if requested. It is also supposed to execute $query against the
	 * database, replacing the metaprefix #__ with the real prefix.
	 *
	 * @param   string  $query  The query to process
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 */
	abstract protected function processQueryLine($query);

	/**
	 * Drops tables etc before restoration. Obviously only has effect when the 'existing' option is set to 'dropprefix'
	 * or 'dropall'.
	 *
	 * @return  void
	 */
	protected function conditionallyDropAll()
	{
		$existing = $this->dbjsonValues['existing'];

		if (!in_array($existing, ['dropall', 'dropprefix']))
		{
			return;
		}

		try
		{
			$this->conditionallyDropTables();
		}
		catch (Exception $e)
		{
			// It doesn't matter if it fails
		}

		try
		{
			$this->conditionallyDropViews();
		}
		catch (Exception $e)
		{
			// It doesn't matter if it fails
		}

		try
		{
			$this->conditionallyDropTriggers();
		}
		catch (Exception $e)
		{
			// It doesn't matter if it fails
		}

		try
		{
			$this->conditionallyDropFunctions();
		}
		catch (Exception $e)
		{
			// It doesn't matter if it fails
		}

		try
		{
			$this->conditionallyDropProcedures();
		}
		catch (Exception $e)
		{
			// It doesn't matter if it fails
		}
	}

	/**
	 * Drops tables before restoration if the 'existing' option is set to 'dropprefix' or 'dropall'.
	 *
	 * @return  void
	 */
	protected function conditionallyDropTables()
	{
		// Implement this in child classes
	}

	/**
	 * Drops views before restoration if the 'existing' option is set to 'dropprefix' or 'dropall'.
	 *
	 * @return  void
	 */
	protected function conditionallyDropViews()
	{
		// Implement this in child classes
	}

	/**
	 * Drops triggers before restoration if the 'existing' option is set to 'dropprefix' or 'dropall'.
	 *
	 * @return  void
	 */
	protected function conditionallyDropTriggers()
	{
		// Implement this in child classes
	}

	/**
	 * Drops functions before restoration if the 'existing' option is set to 'dropprefix' or 'dropall'.
	 *
	 * @return  void
	 */
	protected function conditionallyDropFunctions()
	{
		// Implement this in child classes
	}

	/**
	 * Drops procedures before restoration if the 'existing' option is set to 'dropprefix' or 'dropall'.
	 *
	 * @return  void
	 */
	protected function conditionallyDropProcedures()
	{
		// Implement this in child classes
	}

	private function etaformat($Raw, $measureby = '', $autotext = true)
	{
		$Clean = abs($Raw);

		$calcNum = [
			['s', 60],
			['m', 60 * 60],
			['h', 60 * 60 * 60],
			['d', 60 * 60 * 60 * 24],
			['y', 60 * 60 * 60 * 24 * 365],
		];

		$calc = [
			's' => [1, 'second'],
			'm' => [60, 'minute'],
			'h' => [60 * 60, 'hour'],
			'd' => [60 * 60 * 24, 'day'],
			'y' => [60 * 60 * 24 * 365, 'year'],
		];

		if ($measureby == '')
		{
			$usemeasure = 's';

			for ($i = 0; $i < count($calcNum); $i++)
			{
				if ($Clean <= $calcNum[$i][1])
				{
					$usemeasure = $calcNum[$i][0];
					$i          = count($calcNum);
				}
			}
		}
		else
		{
			$usemeasure = $measureby;
		}

		$datedifference = floor($Clean / $calc[$usemeasure][0]);

		if ($datedifference == 1)
		{
			return $datedifference . ' ' . $calc[$usemeasure][1];
		}
		else
		{
			return $datedifference . ' ' . $calc[$usemeasure][1] . 's';
		}
	}

	private function sizeformat($size)
	{
		if ($size < 0)
		{
			return 0;
		}
		$unit = ['b', 'KB', 'MB', 'GB', 'TB', 'PB'];
		$i    = floor(log($size, 1024));
		if (($i < 0) || ($i > 5))
		{
			$i = 0;
		}

		return @round($size / pow(1024, ($i)), 2) . ' ' . $unit[$i];
	}

	/**
	 * This method processes a string and replaces non alphanumeric characters with underscores
	 *
	 * @param   string  $string  String to process
	 *
	 * @return  string  Processed string
	 */
	private function sanitizeDBKey($string)
	{
		// Sanitize non-alphanumeric to underscores
		$str = preg_replace('/(\s|[^A-Za-z0-9\_])+/', '_', $string);

		// Trim underscores at beginning and end of the string
		$str = trim($str, '_');

		return $str;
	}

	/**
	 * Add a failed query to the failed query log file.
	 *
	 * @param   string  $sql    The failed database query to log
	 * @param   string  $error  Error reported by the database
	 *
	 * @return  void
	 */
	private function logQuery($sql)
	{
		if (empty($this->logFile))
		{
			return;
		}

		$fp = @fopen($this->logFile, 'a');

		if ($fp === false)
		{
			return;
		}

		$sql = rtrim($sql, "\n") . ";\n\n";

		@fwrite($fp, $sql);
		@fclose($fp);
	}

	/**
	 * Handle a query which failed to execute
	 *
	 * @param   string     $sql  The failed query
	 * @param   Exception  $exc  The exception generated by the database driver
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 */
	private function handleFailedQuery($sql, $exc)
	{
		// If the database error code is within the list of ignored codes we do nothing
		if (in_array($exc->getCode(), $this->allowedErrorCodes))
		{
			return;
		}

		// Increase the SQL error counter
		$this->errorcount++;

		// Is this a CREATE query?
		$isCreateQuery = (substr($sql, 0, 7) == 'CREATE ');

		// Should I throw an exception (halt the restoration) for this failed query?
		$throwException = $this->breakOnFailedInsert;

		if ($isCreateQuery)
		{
			$throwException = $this->breakOnFailedCreate;
		}

		// Log the failed query. If writing to the log fails nothing bad happens.
		$this->logQuery($sql);

		// If I am not supposed to halt the restoration stop here.
		if (!$throwException)
		{
			return;
		}

		// Format the error message in a human readable way and throw it again
		$message = '<h2>' . AText::sprintf('ANGI_RESTORE_ERROR_ERRORATLINE', $this->linenumber) . '</h2>' . "\n";
		$message .= '<p>' . AText::_('ANGI_RESTORE_ERROR_MYSQLERROR') . '</p>' . "\n";
		$message .= '<code>ErrNo #' . htmlspecialchars($exc->getCode()) . '</code>' . "\n";
		$message .= '<pre>' . htmlspecialchars($exc->getMessage()) . '</pre>' . "\n";
		$message .= '<p>' . AText::_('ANGI_RESTORE_ERROR_RAWQUERY') . '</p>' . "\n";
		$message .= '<pre>' . htmlspecialchars($sql) . '</pre>' . "\n";

		throw new Exception($message);
	}
}
