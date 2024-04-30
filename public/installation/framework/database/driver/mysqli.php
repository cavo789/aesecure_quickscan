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
 * This file may contain code from the Joomla! Platform, Copyright (c) 2005 -
 * 2012 Open Source Matters, Inc. This file is NOT part of the Joomla! Platform.
 * It is derivative work and clearly marked as such as per the provisions of the
 * GNU General Public License.
 */

/**
 * MySQLi database driver
 *
 * @see         http://php.net/manual/en/book.mysqli.php
 */
class ADatabaseDriverMysqli extends ADatabaseDriver
{
	use ADatabaseFixmysql;

	/**
	 * @var    string  The database technology family supported, e.g. mysql, mssql
	 */
	public static $dbtech = 'mysql';

	/**
	 * @var    string  The minimum supported database version.
	 */
	protected static $dbMinimum = '5.0.4';

	/**
	 * The name of the database driver.
	 *
	 * @var    string
	 */
	public $name = 'mysqli';

	/**
	 * The character(s) used to quote SQL statement names such as table names or field names,
	 * etc. The child classes should define this as necessary.  If a single character string the
	 * same character is used for both sides of the quoted name, else the first character will be
	 * used for the opening quote and the second for the closing quote.
	 *
	 * @var    string
	 */
	protected $nameQuote = '`';

	/**
	 * The null or zero representation of a timestamp for the database driver.  This should be
	 * defined in child classes to hold the appropriate value for the engine.
	 *
	 * @var    string
	 */
	protected $nullDate = '0000-00-00 00:00:00';

	private $isReconnecting = false;

	/**
	 * Constructor.
	 *
	 * @param   array  $options  List of options used to configure the connection
	 *
	 */
	public function __construct($options)
	{
		// Get some basic values from the options.
		$options['host']     = (isset($options['host'])) ? $options['host'] : 'localhost';
		$options['user']     = (isset($options['user'])) ? $options['user'] : 'root';
		$options['password'] = (isset($options['password'])) ? $options['password'] : '';
		$options['database'] = (isset($options['database'])) ? $options['database'] : '';
		$options['select']   = (isset($options['select'])) ? (bool) $options['select'] : true;
		$options['port']     = (isset($options['port'])) ? (int) $options['port'] : null;
		$options['socket']   = (isset($options['socket'])) ? $options['socket'] : null;
		$options['utf8mb4']  = (isset($options['utf8mb4'])) ? $options['utf8mb4'] : true;

		$options['ssl'] = isset($options['ssl']) ? $options['ssl'] : [];
		$options['ssl'] = is_array($options['ssl']) ? $options['ssl'] : [];

		foreach ([
			         ['enable', 'dbencryption', false],
			         ['cipher', 'dbsslcipher', null],
			         ['ca', 'dbsslca', null],
			         ['key', 'dbsslkey', null],
			         ['cert', 'dbsslcert', null],
			         ['verify_server_cert', 'dbsslverifyservercert', false],
		         ] as $tuples)
		{
			list($sslKey, $optionsKey, $default) = $tuples;
			$sslValue                = isset($options['ssl'][$sslKey]) ? $options['ssl'][$sslKey] : null;
			$optionsValue            = isset($options[$optionsKey]) ? $options[$optionsKey] : null;
			$value                   = is_null($sslValue) ? $optionsValue : $sslValue;
			$options['ssl'][$sslKey] = is_null($value) ? $default : $value;;
		}

		$options['ssl']['enable']             = (isset($options['ssl']['enable']) ? $options['ssl']['enable'] : false) ?: false;
		$options['ssl']['cipher']             = (isset($options['ssl']['cipher']) ? $options['ssl']['cipher'] : null) ?: null;
		$options['ssl']['ca']                 = (isset($options['ssl']['ca']) ? $options['ssl']['ca'] : null) ?: null;
		$options['ssl']['capath']             = (isset($options['ssl']['capath']) ? $options['ssl']['capath'] : null) ?: null;
		$options['ssl']['key']                = (isset($options['ssl']['key']) ? $options['ssl']['key'] : null) ?: null;
		$options['ssl']['cert']               = (isset($options['ssl']['cert']) ? $options['ssl']['cert'] : null) ?: null;
		$options['ssl']['verify_server_cert'] = (isset($options['ssl']['verify_server_cert']) ? $options['ssl']['verify_server_cert'] : null) ?: false;

		// Figure out if a port is included in the host name
		$this->fixHostnamePortSocket($options['host'], $options['port'], $options['socket'], $options['ssl']['enable']);

		// Finalize initialisation.
		parent::__construct($options);
	}

	/**
	 * Test to see if the MySQL connector is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 */
	public static function isSupported()
	{
		return function_exists('mysqli_connect');
	}

	/**
	 * Destructor.
	 *
	 */
	public function __destruct()
	{
		if (is_callable($this->connection, 'close'))
		{
			$this->connection->close();
		}
	}

	/**
	 * Connects to the database if needed.
	 *
	 * @return  void  Returns void if the database connected successfully.
	 *
	 * @throws  RuntimeException
	 */
	public function connect()
	{
		if ($this->connection)
		{
			return;
		}

		// Make sure the MySQLi extension for PHP is installed and enabled.
		if (!function_exists('mysqli_connect'))
		{
			throw new RuntimeException('The MySQL adapter mysqli is not available');
		}

		$this->connection = mysqli_init() ?: null;

		$connectionFlags = 0;

		// For SSL/TLS connection encryption.
		if ($this->options['ssl'] !== [] && $this->options['ssl']['enable'] === true)
		{
			$connectionFlags = $connectionFlags | MYSQLI_CLIENT_SSL;

			// Verify server certificate is only available in PHP 5.6.16+. See https://www.php.net/ChangeLog-5.php#5.6.16
			if (isset($this->options['ssl']['verify_server_cert']))
			{
				// New constants in PHP 5.6.16+. See https://www.php.net/ChangeLog-5.php#5.6.16
				if ($this->options['ssl']['verify_server_cert'] === true && defined('MYSQLI_CLIENT_SSL_VERIFY_SERVER_CERT'))
				{
					$connectionFlags = $connectionFlags | MYSQLI_CLIENT_SSL_VERIFY_SERVER_CERT;
				}
				elseif ($this->options['ssl']['verify_server_cert'] === false && defined('MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT'))
				{
					$connectionFlags = $connectionFlags | MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;
				}
				elseif (defined('MYSQLI_OPT_SSL_VERIFY_SERVER_CERT'))
				{
					$this->connection->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, $this->options['ssl']['verify_server_cert']);
				}
			}

			// Add SSL/TLS options only if changed.
			$this->connection->ssl_set(
				(isset($this->options['ssl']['key']) ? $this->options['ssl']['key'] : null) ?: null,
				(isset($this->options['ssl']['cert']) ? $this->options['ssl']['cert'] : null) ?: null,
				(isset($this->options['ssl']['ca']) ? $this->options['ssl']['ca'] : null) ?: null,
				(isset($this->options['ssl']['capath']) ? $this->options['ssl']['capath'] : null) ?: null,
				(isset($this->options['ssl']['cipher']) ? $this->options['ssl']['cipher'] : null) ?: null
			);
		}

		// Attempt to connect to the server, use error suppression to silence warnings and allow us to throw an Exception separately.
		$connected = !empty($this->connection) && @$this->connection->real_connect(
				$this->options['host'],
				$this->options['user'],
				$this->options['password'] ?: null,
				null,
				$this->options['port'] ?: 3306,
				$this->options['socket'] ?: null,
				$connectionFlags
			);

		// Attempt to connect to the server.
		if (!$connected)
		{
			throw new RuntimeException('Could not connect to MySQL.');
		}

		// Set sql_mode to non_strict mode
		mysqli_query($this->connection, "SET @@SESSION.sql_mode = '';");

		// If auto-select is enabled select the given database.
		if ($this->options['select'] && !empty($this->options['database']))
		{
			$this->select($this->options['database']);
		}

		// Set charactersets (needed for MySQL 4.1.2+).
		$this->setUTF();
	}

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @return  boolean  True if connected to the database engine.
	 *
	 */
	public function connected()
	{
		if (is_object($this->connection))
		{
			return mysqli_ping($this->connection);
		}

		return false;
	}

	/**
	 * Disconnects the database.
	 *
	 * @return  void
	 *
	 */
	public function disconnect()
	{
		// Close the connection.
		if (is_callable($this->connection, 'close'))
		{
			mysqli_close($this->connection);
		}

		$this->connection = null;
	}

	/**
	 * Drops a table from the database.
	 *
	 * @param   string   $tableName  The name of the database table to drop.
	 * @param   boolean  $ifExists   Optionally specify that the table must exist before it is dropped.
	 *
	 * @return  ADatabaseDriverMysqli  Returns this object to support chaining.
	 *
	 * @throws  RuntimeException
	 */
	public function dropTable($tableName, $ifExists = true)
	{
		$this->connect();

		$query = $this->getQuery(true);

		$this->setQuery('DROP TABLE ' . ($ifExists ? 'IF EXISTS ' : '') . $query->quoteName($tableName));

		$this->execute();

		return $this;
	}

	/**
	 * Method to escape a string for usage in an SQL statement.
	 *
	 * @param   string   $text   The string to be escaped.
	 * @param   boolean  $extra  Optional parameter to provide extra escaping.
	 *
	 * @return  string  The escaped string.
	 *
	 */
	public function escape($text, $extra = false)
	{
		$this->connect();

		if (is_null($text))
		{
			return 'NULL';
		}

		$result = mysqli_real_escape_string($this->getConnection(), $text);

		if ($extra)
		{
			$result = addcslashes($result, '%_');
		}

		return $result;
	}

	/**
	 * Execute the SQL statement.
	 *
	 * @return  mixed  A database cursor resource on success, boolean false on failure.
	 *
	 * @throws  RuntimeException
	 */
	public function execute()
	{
		$this->connect();

		if (!is_object($this->connection))
		{
			throw new RuntimeException($this->errorMsg, $this->errorNum);
		}

		// Take a local copy so that we don't modify the original query and cause issues later
		$sql = $this->replacePrefix((string) $this->sql);
		if ($this->limit > 0 || $this->offset > 0)
		{
			$sql .= ' LIMIT ' . $this->offset . ', ' . $this->limit;
		}

		// Increment the query counter.
		$this->count++;

		// If debugging is enabled then let's log the query.
		if ($this->debug)
		{
			// Add the query to the object queue.
			$this->log[] = $sql;
		}

		// Reset the error values.
		$this->errorNum = 0;
		$this->errorMsg = '';

		// Execute the query. Error suppression is used here to prevent warnings/notices that the connection has been lost.
		$this->cursor = @mysqli_query($this->connection, $sql);

		// If an error occurred handle it.
		if (!$this->cursor)
		{
			$this->errorNum = (int) mysqli_errno($this->connection);
			$this->errorMsg = (string) mysqli_error($this->connection) . "\n SQL=" . $sql;

			// Check if the server was disconnected.
			if (!$this->connected() && !$this->isReconnecting)
			{
				$this->isReconnecting = true;

				try
				{
					// Attempt to reconnect.
					$this->connection = null;
					$this->connect();
				}
					// If connect fails, ignore that exception and throw the normal exception.
				catch (RuntimeException $e)
				{
					$this->errorNum = (int) mysqli_errno($this->connection);
					$this->errorMsg = (string) mysqli_error($this->connection) . ' SQL=' . $sql;

					unset($sql);

					throw new RuntimeException($this->errorMsg, $this->errorNum);
				}

				// Since we were able to reconnect, run the query again.
				unset($sql);

				$result               = $this->execute();
				$this->isReconnecting = false;

				return $result;
			}
			// The server was not disconnected.
			else
			{
				unset($sql);
				throw new RuntimeException($this->errorMsg, $this->errorNum);
			}
		}

		unset($sql);

		return $this->cursor;
	}

	/**
	 * Get the number of affected rows for the previous executed SQL statement.
	 *
	 * @return  integer  The number of affected rows.
	 *
	 */
	public function getAffectedRows()
	{
		$this->connect();

		return mysqli_affected_rows($this->connection);
	}

	/**
	 * Method to get the database collation in use by sampling a text field of a table in the database.
	 *
	 * @return  mixed  The collation in use by the database (string) or boolean false if not supported.
	 *
	 * @throws  RuntimeException
	 */
	public function getCollation()
	{
		$this->connect();

		$this->setQuery('SHOW FULL COLUMNS FROM #__users');
		$array = $this->loadAssocList();

		return $array['2']['Collation'];
	}

	/**
	 * Get the number of returned rows for the previous executed SQL statement.
	 *
	 * @param   resource  $cursor  An optional database cursor resource to extract the row count from.
	 *
	 * @return  integer   The number of returned rows.
	 *
	 */
	public function getNumRows($cursor = null)
	{
		return mysqli_num_rows($cursor ? $cursor : $this->cursor);
	}

	/**
	 * Retrieves field information about a given table.
	 *
	 * @param   string   $table     The name of the database table.
	 * @param   boolean  $typeOnly  True to only return field types.
	 *
	 * @return  array  An array of fields for the database table.
	 *
	 * @throws  RuntimeException
	 */
	public function getTableColumns($table, $typeOnly = true)
	{
		$this->connect();

		$result = [];

		// Set the query to get the table fields statement.
		$this->setQuery('SHOW FULL COLUMNS FROM ' . $this->quoteName($this->escape($table)));
		$fields = $this->loadObjectList();

		// If we only want the type as the value add just that to the list.
		if ($typeOnly)
		{
			foreach ($fields as $field)
			{
				$result[$field->Field] = preg_replace("/[(0-9)]/", '', $field->Type);
			}
		}
		// If we want the whole field data object add that to the list.
		else
		{
			foreach ($fields as $field)
			{
				$result[$field->Field] = $field;
			}
		}

		return $result;
	}

	/**
	 * Shows the table CREATE statement that creates the given tables.
	 *
	 * @param   mixed  $tables  A table name or a list of table names.
	 *
	 * @return  array  A list of the create SQL for the tables.
	 *
	 * @throws  RuntimeException
	 */
	public function getTableCreate($tables)
	{
		$this->connect();

		$result = [];

		// Sanitize input to an array and iterate over the list.
		settype($tables, 'array');
		foreach ($tables as $table)
		{
			// Set the query to get the table CREATE statement.
			$this->setQuery('SHOW CREATE table ' . $this->quoteName($this->escape($table)));
			$row = $this->loadRow();

			// Populate the result array based on the create statements.
			$result[$table] = $row[1];
		}

		return $result;
	}

	/**
	 * Get the details list of keys for a table.
	 *
	 * @param   string  $table  The name of the table.
	 *
	 * @return  array  An array of the column specification for the table.
	 *
	 * @throws  RuntimeException
	 */
	public function getTableKeys($table)
	{
		$this->connect();

		// Get the details columns information.
		$this->setQuery('SHOW KEYS FROM ' . $this->quoteName($table));
		$keys = $this->loadObjectList();

		return $keys;
	}

	/**
	 * Method to get an array of all tables in the database.
	 *
	 * @return  array  An array of all the tables in the database.
	 *
	 * @throws  RuntimeException
	 */
	public function getTableList()
	{
		$this->connect();

		// Set the query to get the tables statement.
		$this->setQuery('SHOW TABLES');
		$tables = $this->loadColumn();

		return $tables;
	}

	/**
	 * Get the version of the database connector.
	 *
	 * @return  string  The database connector version.
	 *
	 */
	public function getVersion()
	{
		$this->connect();

		return mysqli_get_server_info($this->connection);
	}

	/**
	 * Method to get the auto-incremented value from the last INSERT statement.
	 *
	 * @return  integer  The value of the auto-increment field from the last inserted row.
	 *
	 */
	public function insertid()
	{
		$this->connect();

		return mysqli_insert_id($this->connection);
	}

	/**
	 * Locks a table in the database.
	 *
	 * @param   string  $table  The name of the table to unlock.
	 *
	 * @return  ADatabaseDriverMysqli  Returns this object to support chaining.
	 *
	 * @throws  RuntimeException
	 */
	public function lockTable($table)
	{
		$this->setQuery('LOCK TABLES ' . $this->quoteName($table) . ' WRITE')->execute();

		return $this;
	}

	/**
	 * Renames a table in the database.
	 *
	 * @param   string  $oldTable  The name of the table to be renamed
	 * @param   string  $newTable  The new name for the table.
	 * @param   string  $backup    Not used by MySQL.
	 * @param   string  $prefix    Not used by MySQL.
	 *
	 * @return  ADatabaseDriverMysqli  Returns this object to support chaining.
	 *
	 * @throws  RuntimeException
	 */
	public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
	{
		$this->setQuery('RENAME TABLE ' . $oldTable . ' TO ' . $newTable)->execute();

		return $this;
	}

	/**
	 * Select a database for use.
	 *
	 * @param   string  $database  The name of the database to select for use.
	 *
	 * @return  boolean  True if the database was successfully selected.
	 *
	 * @throws  RuntimeException
	 */
	public function select($database)
	{
		$this->connect();

		if (!$database)
		{
			return false;
		}

		if (!mysqli_select_db($this->connection, $database))
		{
			throw new RuntimeException('Could not connect to database.');
		}

		return true;
	}

	/**
	 * Set the connection to use UTF-8 character encoding.
	 *
	 * @return  boolean  True on success.
	 *
	 */
	public function setUTF()
	{
		$this->connect();

		$allowUtf8Mb4 = isset($this->options['utf8mb4']) ? $this->options['utf8mb4'] : false;
		$charset      = ($allowUtf8Mb4 && $this->supportsUtf8mb4()) ? 'utf8mb4' : 'utf8';

		$result = @$this->connection->set_charset($charset);

		if (!$result)
		{
			$this->supportsUTF8MB4 = false;
			$result                = @$this->connection->set_charset('utf8');
		}

		return $result;
	}

	/**
	 * Does this database server support UTF-8 four byte (utf8mb4) collation?
	 *
	 * libmysql supports utf8mb4 since 5.5.3 (same version as the MySQL server). mysqlnd supports utf8mb4 since 5.0.9.
	 *
	 * This method's code is based on WordPress' wpdb::has_cap() method
	 *
	 * @return  bool
	 */
	public function supportsUtf8mb4()
	{
		if (is_null($this->supportsUTF8MB4))
		{
			$this->supportsUTF8MB4 = $this->serverClaimsUtf8();
		}

		return $this->supportsUTF8MB4;
	}

	/**
	 * Method to commit a transaction.
	 *
	 * @return  void
	 *
	 * @throws  RuntimeException
	 */
	public function transactionCommit()
	{
		$this->connect();

		$this->setQuery('COMMIT');
		$this->execute();
	}

	/**
	 * Method to roll back a transaction.
	 *
	 * @return  void
	 *
	 * @throws  RuntimeException
	 */
	public function transactionRollback()
	{
		$this->connect();

		$this->setQuery('ROLLBACK');
		$this->execute();
	}

	/**
	 * Method to initialize a transaction.
	 *
	 * @return  void
	 *
	 * @throws  RuntimeException
	 */
	public function transactionStart()
	{
		$this->connect();

		$this->setQuery('START TRANSACTION');
		$this->execute();
	}

	/**
	 * Unlocks tables in the database.
	 *
	 * @return  ADatabaseDriverMysqli  Returns this object to support chaining.
	 *
	 * @throws  RuntimeException
	 */
	public function unlockTables()
	{
		$this->setQuery('UNLOCK TABLES')->execute();

		return $this;
	}

	/**
	 * Method to fetch a row from the result set cursor as an array.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 */
	protected function fetchArray($cursor = null)
	{
		return mysqli_fetch_row($cursor ? $cursor : $this->cursor);
	}

	/**
	 * Method to fetch a row from the result set cursor as an associative array.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 */
	protected function fetchAssoc($cursor = null)
	{
		return mysqli_fetch_assoc($cursor ? $cursor : $this->cursor);
	}

	/**
	 * Method to fetch a row from the result set cursor as an object.
	 *
	 * @param   mixed   $cursor  The optional result set cursor from which to fetch the row.
	 * @param   string  $class   The class name to use for the returned row object.
	 *
	 * @return  mixed   Either the next row from the result set or false if there are no more rows.
	 *
	 */
	protected function fetchObject($cursor = null, $class = 'stdClass')
	{
		return mysqli_fetch_object($cursor ? $cursor : $this->cursor, $class);
	}

	/**
	 * Method to free up the memory used for the result set.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  void
	 *
	 */
	protected function freeResult($cursor = null)
	{
		mysqli_free_result($cursor ? $cursor : $this->cursor);
	}

	private function serverClaimsUtf8()
	{
		if (is_null($this->connection))
		{
			return true;
		}

		$mariadb        = stripos($this->connection->server_info, 'mariadb') !== false;
		$client_version = $this->connection->client_info;
		$server_version = $this->getVersion();

		if (version_compare($server_version, '5.5.3', '<'))
		{
			return false;
		}

		if ($mariadb && version_compare($server_version, '10.0.0', '<'))
		{
			return false;
		}

		if (strpos($client_version, 'mysqlnd') !== false)
		{
			$client_version = preg_replace('/^\D+([\d.]+).*/', '$1', $client_version);

			return version_compare($client_version, '5.0.9', '>=');
		}

		return version_compare($client_version, '5.5.3', '>=');
	}
}
