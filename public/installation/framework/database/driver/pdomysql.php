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
 * MySQL database driver through PDO
 */
class ADatabaseDriverPdomysql extends ADatabaseDriverMysqli
{
	/**
	 * @var    string  The database technology family supported, e.g. mysql, mssql
	 */
	public static $dbtech = 'mysql';

	/**
	 * The name of the database driver.
	 *
	 * @var    string
	 */
	public $name = 'pdomysql';

	/** @var string Connection character set */
	protected $charset = 'UTF8';

	/** @var PDO The db connection resource */
	protected $connection = null;

	/** @var PDOStatement The database connection cursor from the last query. */
	protected $cursor;

	/** @var array Driver options for PDO */
	protected $driverOptions = [];

	private $isReconnecting = false;

	/**
	 * The default cipher suite for TLS connections.
	 *
	 * @var    array
	 */
	protected static $defaultCipherSuite = [
		'AES128-GCM-SHA256',
		'AES256-GCM-SHA384',
		'AES128-CBC-SHA256',
		'AES256-CBC-SHA384',
		'DES-CBC3-SHA',
	];

	/**
	 * Test to see if the MySQL connector is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 */
	public static function isSupported()
	{
		if (!defined('PDO::ATTR_DRIVER_NAME'))
		{
			return false;
		}

		return in_array('mysql', PDO::getAvailableDrivers());
	}

	/**
	 * Destructor.
	 *
	 */
	public function __destruct()
	{
		if (is_object($this->connection))
		{
			$this->disconnect();
		}
	}

	/**
	 * PDO does not support serialize
	 *
	 * @return  array
	 */
	public function __sleep()
	{
		$serializedProperties = [];

		$reflect = new ReflectionClass($this);

		// Get properties of the current class
		$properties = $reflect->getProperties();

		foreach ($properties as $property)
		{
			// Do not serialize properties that are PDO
			if ($property->isStatic() == false && !($this->{$property->name} instanceof PDO))
			{
				array_push($serializedProperties, $property->name);
			}
		}

		return $serializedProperties;
	}

	/**
	 * Wake up after serialization
	 *
	 * @return  array
	 */
	public function __wakeup()
	{
		// Get connection back
		$this->__construct($this->options);
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
		if ($this->connected())
		{
			return;
		}
		else
		{
			$this->disconnect();
		}

		// Make sure the server is compatible
		if (!$this->isSupported())
		{
			throw new RuntimeException('PDO MySQL is not supported on this server.');
		}

		if (!isset($this->charset))
		{
			$this->charset = 'UTF8';
		}

		$this->driverOptions = isset($this->options['driverOptions']) ? $this->options['driverOptions'] : [];

		$this->options['port'] = $this->options['port'] ?: 3306;

		$format = 'mysql:host=#HOST#;port=#PORT#;dbname=#DBNAME#;charset=#CHARSET#';

		if ($this->options['socket'])
		{
			$format = 'mysql:socket=#SOCKET#;dbname=#DBNAME#;charset=#CHARSET#';
		}

		$this->charset = isset($this->options['charset']) ? $this->options['charset'] : $this->charset;

		$replace = ['#HOST#', '#PORT#', '#SOCKET#', '#DBNAME#', '#CHARSET#'];
		$with    = [
			$this->options['host'], $this->options['port'], $this->options['socket'], $this->options['database'],
			$this->charset,
		];

		// Create the connection string:
		$connectionString = str_replace($replace, $with, $format);
		$connectionString = str_replace(';dbname=#DBNAME#', '', $connectionString);

		// For SSL/TLS connection encryption.
		if ($this->options['ssl'] !== [] && $this->options['ssl']['enable'] === true)
		{
			$sslContextIsNull = true;

			// If customised, add cipher suite, ca file path, ca path, private key file path and certificate file path to PDO driver options.
			foreach (['cipher', 'ca', 'capath', 'key', 'cert'] as $key => $value)
			{
				if ($this->options['ssl'][$value] !== null)
				{
					$this->driverOptions[constant('\PDO::MYSQL_ATTR_SSL_' . strtoupper($value))] = $this->options['ssl'][$value];

					$sslContextIsNull = false;
				}
			}

			// PDO, if no cipher, ca, capath, cert and key are set, can't start TLS one-way connection, set a common ciphers suite to force it.
			if ($sslContextIsNull === true)
			{
				$this->driverOptions[\PDO::MYSQL_ATTR_SSL_CIPHER] = implode(':', static::$defaultCipherSuite);
			}

			// If customised, for capable systems (PHP 7.0.14+ and 7.1.4+) verify certificate chain and Common Name to driver options.
			if ($this->options['ssl']['verify_server_cert'] !== null && defined('\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT'))
			{
				$this->driverOptions[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = $this->options['ssl']['verify_server_cert'];
			}
		}

		// connect to the server
		try
		{
			$this->connection = new PDO(
				$connectionString,
				$this->options['user'],
				$this->options['password'],
				$this->driverOptions
			);
		}
		catch (PDOException $e)
		{
			throw new RuntimeException('Could not connect to MySQL via PDO: ' . $e->getMessage(), 2);
		}

		// Reset the SQL mode of the connection
		try
		{
			$this->connection->exec("SET @@SESSION.sql_mode = '';");
		}
			// Ignore any exceptions (incompatible MySQL versions)
		catch (Exception $e)
		{
		}

		$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

		if ($this->options['select'] && !empty($this->options['database']))
		{
			$this->select($this->options['database']);
		}

		if ($this->hasUTFSupport() && $this->charset !== 'utf8mb4')
		{
			$this->charset = 'utf8mb4';
			$this->disconnect();
			$this->connect();
		}

		$this->freeResult();
	}

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @return  boolean  True if connected to the database engine.
	 *
	 */
	public function connected()
	{
		if (!is_object($this->connection))
		{
			return false;
		}

		// Do not try doing a SELECT 1 query here. It seems that the query fails when we are connected to a database
		// which has no tables?!

		return true;
	}

	/**
	 * Disconnects the database.
	 *
	 * @return  void
	 *
	 */
	public function disconnect()
	{
		$return = false;

		if (is_object($this->cursor))
		{
			$this->cursor->closeCursor();
		}

		$this->connection = null;
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

		if (is_int($text) || is_float($text))
		{
			return $text;
		}

		if (is_null($text))
		{
			return 'NULL';
		}

		$result = substr($this->connection->quote($text), 1, -1);

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

		$this->freeResult();

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
		try
		{
			$this->cursor = $this->connection->query($sql);
		}
		catch (Exception $e)
		{
		}

		// If an error occurred handle it.
		if (!$this->cursor)
		{
			$errorInfo      = $this->connection->errorInfo();
			$this->errorNum = $errorInfo[1];
			$this->errorMsg = $errorInfo[2] . ' SQL=' . $sql;

			unset($sql);

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
					// Throw the normal query exception.
					throw new RuntimeException($this->errorMsg, $this->errorNum);
				}

				// Since we were able to reconnect, run the query again.
				$result               = $this->execute();
				$this->isReconnecting = false;

				return $result;
			}
			// The server was not disconnected.
			else
			{
				// Throw the normal query exception.
				throw new RuntimeException($this->errorMsg, $this->errorNum);
			}
		}

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
		if ($this->cursor instanceof PDOStatement)
		{
			return $this->cursor->rowCount();
		}

		return 0;
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
		if ($cursor instanceof PDOStatement)
		{
			return $cursor->rowCount();
		}

		if ($this->cursor instanceof PDOStatement)
		{
			return $this->cursor->rowCount();
		}

		return 0;
	}

	/**
	 * Get the version of the database connector.
	 *
	 * @return  string  The database connector version.
	 *
	 */
	public function getVersion()
	{
		if (!is_object($this->connection))
		{
			$this->connect();
		}

		$version = $this->connection->getAttribute(\PDO::ATTR_SERVER_VERSION);

		if (stripos($version, 'mariadb') !== false)
		{
			// MariaDB: Strip off any leading '5.5.5-', if present
			return preg_replace('/^5\.5\.5-/', '', $version);
		}

		return $version;
	}

	/**
	 * Determine whether or not the database engine supports UTF-8 character encoding.
	 *
	 * @return  boolean  True if the database engine supports UTF-8 character encoding.
	 *
	 */
	public function hasUTFSupport()
	{
		if (empty($this->connection))
		{
			return true;
		}

		$serverVersion = $this->getVersion();
		$mariadb = stripos($serverVersion, 'mariadb') !== false;

		// At this point we know the client supports utf8mb4.  Now we must check if the server supports utf8mb4 as well.
		$utf8mb4 = version_compare($serverVersion, '5.5.3', '>=');

		if ($mariadb && version_compare($serverVersion, '10.0.0', '<'))
		{
			$utf8mb4 = false;
		}

		return $utf8mb4;
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

		// Error suppress this to prevent PDO warning us that the driver doesn't support this operation.
		return @$this->connection->lastInsertId();
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

		try
		{
			$this->connection->exec('USE ' . $this->quoteName($database));
		}
		catch (Exception $e)
		{
			$errorInfo      = $this->connection->errorInfo();
			$this->errorNum = $errorInfo[1];
			$this->errorMsg = $errorInfo[2];

			throw new RuntimeException('Could not connect to database: ' . $this->errorMsg, $this->errorNum);
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
		return true;
	}

	/**
	 * Method to commit a transaction.
	 *
	 * @return  void
	 */
	public function transactionCommit()
	{
		$this->connection->commit();
	}

	/**
	 * Method to roll back a transaction.
	 *
	 * @return  void
	 */
	public function transactionRollback()
	{
		$this->connection->rollBack();
	}

	/**
	 * Method to initialize a transaction.
	 *
	 * @return  void
	 */
	public function transactionStart()
	{
		$this->connection->beginTransaction();
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
		$ret = null;

		if (!empty($cursor) && $cursor instanceof PDOStatement)
		{
			$ret = $cursor->fetch(PDO::FETCH_NUM);
		}
		elseif ($this->cursor instanceof PDOStatement)
		{
			$ret = $this->cursor->fetch(PDO::FETCH_NUM);
		}

		return $ret;
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
		$ret = null;

		if (!empty($cursor) && $cursor instanceof PDOStatement)
		{
			$ret = $cursor->fetch(PDO::FETCH_ASSOC);
		}
		elseif ($this->cursor instanceof PDOStatement)
		{
			$ret = $this->cursor->fetch(PDO::FETCH_ASSOC);
		}

		return $ret;
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
		$ret = null;

		if (!empty($cursor) && $cursor instanceof PDOStatement)
		{
			$ret = $cursor->fetchObject($class);
		}
		elseif ($this->cursor instanceof PDOStatement)
		{
			$ret = $this->cursor->fetchObject($class);
		}

		return $ret;
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
		if ($cursor instanceof PDOStatement)
		{
			$cursor->closeCursor();
			$cursor = null;
		}

		if ($this->cursor instanceof PDOStatement)
		{
			$this->cursor->closeCursor();
			$this->cursor = null;
		}
	}
}
