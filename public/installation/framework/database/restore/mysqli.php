<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class ADatabaseRestoreMysqli extends ADatabaseRestore
{
	/**
	 * Overloaded constructor, allows us to set up error codes and connect to
	 * the database.
	 *
	 * @param   string $dbkey        @see {ADatabaseRestore}
	 * @param   array  $dbjsonValues @see {ADatabaseRestore}
	 */
	public function __construct($dbkey, $dbjsonValues)
	{
		parent::__construct($dbkey, $dbjsonValues);

		// Set up allowed error codes
		$this->allowedErrorCodes = array(
			1262,   // Truncated row when importing CSV (should ever occur)
			1263,   // Data truncated, NULL for NOT NULL...
			1264,   // Out of range value for column
			1265,   // "Data truncated" warning
			1266,   // Table created with MyISAM instead of InnoDB
			1287,   // Deprecated syntax
			1299,   // Invalid TIMESTAMP column value
			// , 1406	// "Data too long" error
		);

		// Set up allowed comment delimiters
		$this->comment = array(
			'#',
			'\'-- ',
			'---',
			'/*!',
		);

		// Connect to the database
		$this->getDatabase();

		// Suppress foreign key checks
		if ($this->dbjsonValues['foreignkey'])
		{
			$this->executeQueryWithoutFailing('SET FOREIGN_KEY_CHECKS = 0');
		}

		// Suppress auto value on zero
		if ($this->dbjsonValues['noautovalue'])
		{
			$this->executeQueryWithoutFailing('SET @@SESSION.sql_mode = \'NO_AUTO_VALUE_ON_ZERO\'');
		}
	}

	/**
	 * Overloaded method which will create the database (if it doesn't exist).
	 *
	 * @return  ADatabaseDriver
	 */
	protected function getDatabase($selectDatabase = true)
	{
		if (is_object($this->db))
		{
			return $this->db;
		}

		try
		{
			$db = parent::getDatabase(false);

			if (!$db->connected())
			{
				$db->connect();
			}
		}
		catch (Exception $e)
		{
			throw new ADatabaseRestoreExceptionDbuser('', 500, $e);
		}

		$db->setUtf8Mb4AutoDetection($this->dbjsonValues['utf8mb4']);

		$previousException = null;

		try
		{
			$db->select($this->dbjsonValues['dbname']);
		}
		catch (Exception $exc)
		{
			$previousException = $exc;
		}

		if ($previousException)
		{
			try
			{
				// We couldn't connect to the database. Maybe we have to create it first. Let's see...
				$options = (object) [
					'db_name' => $this->dbjsonValues['dbname'],
					'db_user' => $this->dbjsonValues['dbuser'],
				];
				$db->createDatabase($options, true);
				$db->select($this->dbjsonValues['dbname']);
			}
			catch (Exception $e)
			{
				throw new ADatabaseRestoreExceptionDbname('', 500, $previousException);
			}
		}

		// Try to change the database collation, if requested
		if ($this->dbjsonValues['utf8db'])
		{
			try
			{
				$db->alterDbCharacterSet($this->dbjsonValues['dbname']);
			}
			catch (Exception $exc)
			{
				// Ignore any errors
			}
		}

		return $this->db;
	}

	/**
	 * Processes and runs the query
	 *
	 * @param   string  $query The query to process
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 */
	protected function processQueryLine($query)
	{
		static $MySQL55 = null;
		static $MySQL8 = null;

		// Check for old MySQL version. Required to downgrade utf8mb4_unicode_520_ci (MySQL 5.6 only) to utf8mb4_unicode_ci
		if (is_null($MySQL55))
		{
			$MySQL55 = version_compare($this->db->getVersion(), '5.6', 'lt');
		}

		if (is_null($MySQL8))
		{
			$MySQL8 = version_compare($this->db->getVersion(), '7.999.999', 'ge');
		}

		$forceutf8     = $this->dbjsonValues['utf8tables'];
		$downgradeUtf8 = $forceutf8 && (
				!$this->dbjsonValues['utf8mb4']
				|| ($this->dbjsonValues['utf8mb4'] && !$this->db->supportsUtf8mb4())
			);

		$changeEncoding = false;
		$query          = trim($query);

		/**
		 * If there is a multiline comment at the beginning of the query remove it now.
		 */
		if (substr($query, 0, 2) == '/*')
		{
			$endCommentPos = strpos($query, '*/');

			if ($endCommentPos !== false)
			{
				$query = substr($query, $endCommentPos + 2);
			}
		}

		// CREATE TABLE query pre-processing
		if (substr($query, 0, 12) == 'CREATE TABLE')
		{
			// If the table has a prefix, back it up (if requested). In any case, drop the table. before attempting to create it.
			$tableName = $this->getCreateTableName($query);

			// Should we really restore it or ignore it?
			if (!$this->shouldRestoreEntity($tableName))
			{
				return true;
			}

			$this->dropOrRenameTable($tableName);
			$query          = $this->replaceEngineType($query);
			$query          = $this->removeTableOptions($query);
			$changeEncoding = $forceutf8;
		}
		// CREATE VIEW query pre-processing
		elseif ((substr($query, 0, 7) == 'CREATE ') && (strpos($query, ' VIEW ') !== false))
		{
			// In any case, drop the view before attempting to create it. (Views can't be renamed)
			$tableName = $this->getViewName($query);

			// Should we really restore it or ignore it?
			if (!$this->shouldRestoreEntity($tableName))
			{
				return true;
			}

			$this->dropView($tableName);
		}
		// CREATE PROCEDURE pre-processing
		elseif ((substr($query, 0, 7) == 'CREATE ') && (strpos($query, 'PROCEDURE ') !== false))
		{
			$entity_keyword = ' PROCEDURE ';

			// Drop the entity (it cannot be renamed)
			$entity_name = $this->getEntityName($query, $entity_keyword);

			// Should we really restore it or ignore it?
			if (!$this->shouldRestoreEntity($entity_name))
			{
				return true;
			}

			$this->dropEntity($entity_keyword, $entity_name);
		}
		// CREATE FUNCTION pre-processing
		elseif ((substr($query, 0, 7) == 'CREATE ') && (strpos($query, 'FUNCTION ') !== false))
		{
			$entity_keyword = ' FUNCTION ';

			// Drop the entity (it cannot be renamed)
			$entity_name = $this->getEntityName($query, $entity_keyword);

			// Should we really restore it or ignore it?
			if (!$this->shouldRestoreEntity($entity_name))
			{
				return true;
			}

			$this->dropEntity($entity_keyword, $entity_name);
		}
		// CREATE TRIGGER pre-processing
		elseif ((substr($query, 0, 7) == 'CREATE ') && (strpos($query, 'TRIGGER ') !== false))
		{
			$entity_keyword = ' TRIGGER ';

			// Drop the entity (it cannot be renamed)
			$entity_name = $this->getEntityName($query, $entity_keyword);

			// Should we really restore it or ignore it?
			if (!$this->shouldRestoreEntity($entity_name))
			{
				return true;
			}

			$this->dropEntity($entity_keyword, $entity_name);
		}
		elseif (substr($query, 0, 6) == 'INSERT')
		{
			$tableName = $this->getInsertTableName($query);

			// Should we really restore it or ignore it?
			if (!$this->shouldRestoreEntity($tableName))
			{
				return true;
			}

			$query = $this->applyReplaceInsteadofInsert($query);
		}
		/** @noinspection PhpStatementHasEmptyBodyInspection */
		else
		{
			// Maybe a DROP statement from the extensions filter?
		}

		if (empty($query))
		{
			return true;
		}

		/**
		 * Automatic downgrade of Unicode multibyte encoding as necessary.
		 *
		 * Each version family of MySQL supports different possible Unicode multibyte encodings.
		 *
		 * MySQL 8 supports the utf8mb4_0900_*_ci (Unicode 9.0.0), utf8mb4_unicode_520_ci (Unicode 5.2.0), the
		 * utf8mb4_unicode_ci (Unicode 4.0.0) and the other language-specific utf8mb4_*_ci encodings. It needs no
		 * conversion.
		 *
		 * MySQL 5.6 does not support the utf8mb4_0900_*_ci (Unicode 9.0.0) encodings. They need to be squashed to
		 * utf8mb4_unicode_520_ci.
		 *
		 * MySQL 5.5 does not support the utf8mb4_unicode_520_ci encoding. Both utf8mb4_0900_*_ci and
		 * utf8mb4_unicode_520_ci encodings need to be squashed into the legacy utf8mb4_unicode_ci encoding.
		 *
		 * Finally, we may be asked to downgrade the query to plain old UTF-8.
		 *
		 * The following code deals with the necessary downgrades.
		 *
		 * @see https://mysqlserverteam.com/new-collations-in-mysql-8-0-0/
		 *
		 * If the MySQL version is lower than 5.6.0 switch utf8mb4_unicode_520_ci to utf8mb4_unicode_ci. We need to do
		 * this regardless of UTF8MB4 support and/or downgrade. The idea is that utf8mb4_unicode_520_ci would be
		 * downgraded to utf8_unicode_520_ci which is an invalid collation. By converting it to utf8mb4_unicode_ci first
		 * we let MySQL 5.5 with utf8mb4 support work correctly AND ALSO the downgrade to plain UTF8 to work fine (by
		 * having the downgradeQueryToUtf8 convert the collation to the valid utf8_unicode_ci value).
		 */
		if ($downgradeUtf8)
		{
			/**
			 * Downgrade to UTF-8 requested.
			 *
			 * Convert all UTF8MB4 encoding references to UTF8. Convert four-byte characters to "Unicode replacement
			 * character" (U+FFFD). Data loss will result.
			 */
			$query = $this->downgradeQueryToUtf8($query);
		}
		elseif ($MySQL55)
		{
			/**
			 * MySQL 5.5
			 *
			 * Convert all utf8mb4 character encodings to utf8mb4_unicode_ci.
			 *
			 * No data loss is expected but ordering issues will most definitely occur.
			 */
			$query = preg_replace('/utf8mb4_([a-z0-9]{1,}_){1,}ci/i', 'utf8mb4_unicode_ci', $query);
		}
		elseif (!$MySQL8)
		{
			/**
			 * MySQL 5.6 and 5.7.
			 *
			 * Convert utf8mb4_0900_* character encodings to the more generic utf8mb4_unicode_520_ci encoding.
			 *
			 * No data loss is expected but ordering issues are likely to occur.
			 */
			$query = preg_replace('/utf8mb4_0900_(([a-z0-9]{1,})_){1,}ci/i', 'utf8mb4_unicode_520_ci', $query);
		}

		/**
		 * A note about MySQL, PHP, stored procedures and the delimiter keyword. First read this:
		 * @see https://stackoverflow.com/questions/5311141/how-to-execute-mysql-command-delimiter
		 *
		 * In a traditional MySQL client defining a stored procedure, function, trigger etc usually requires doing:
		 * DELIMITER //
		 * CREATE TRIGGER foo.bar BEFORE INSERT ON bat_baz FOR EACH ROW
		 * BEGIN
		 * <body>
		 * EOT //
		 * DELIMITER ;
		 *
		 * If you tried executing this query one line at a time the DELIMITER lines would give you an error. So how do
		 * you define a stored procedure in PHP? Simply by executing the CREATE PROCEDURE / FUNCTION / TRIGGER without
		 * using the DELIMITER lines. PHP's mysql, mysqli and mysqlnd simply send a raw query to MySQL server for
		 * execution. They do not parse the text trying to figure out what is a "query" (unless using mysqli_multi_query
		 * which we don't actually use in our code). As a result MySQL is pretty darned happy executing your mulit-line
		 * definition query without batting an eyelid. Simple, huh?
		 */
		$this->execute($query);

		// Do we have to forcibly apply UTF8 encoding?
		if (isset($tableName) && $changeEncoding)
		{
			$this->forciblyApplyTableEncoding($tableName);
		}

		return true;
	}

	/**
	 * Extract the table name from a CREATE TABLE command
	 *
	 * @param   string  $query  The SQL query for the CREATE TABLE
	 *
	 * @return  string
	 */
	protected function getCreateTableName($query)
	{
		// Rest of query, after CREATE TABLE
		$restOfQuery = trim(substr($query, 12, strlen($query) - 12));

		// Is there a backtick?
		if (substr($restOfQuery, 0, 1) == '`')
		{
			// There is... Good, we'll just find the matching backtick
			$pos       = strpos($restOfQuery, '`', 1);
			$tableName = substr($restOfQuery, 1, $pos - 1);
		}
		else
		{
			// If there are no backticks the table name ends in the next blank character
			$pos       = strpos($restOfQuery, ' ', 1);
			$tableName = substr($restOfQuery, 1, $pos - 1);
		}

		unset($restOfQuery);

		return $tableName;
	}

	/**
	 * Extract the table name from a INSERT INTO command
	 *
	 * @param   string  $query  The SQL query for the INSERT INTO
	 *
	 * @return  string
	 */
	protected function getInsertTableName($query)
	{
		// Rest of query, after INSERT INTO
		$restOfQuery = trim(substr($query, 11, strlen($query) - 11));

		// Is there a backtick?
		if (substr($restOfQuery, 0, 1) == '`')
		{
			// There is... Good, we'll just find the matching backtick
			$pos       = strpos($restOfQuery, '`', 1);
			$tableName = substr($restOfQuery, 1, $pos - 1);
		}
		else
		{
			// If there are no backticks the table name ends in the next blank character
			$pos       = strpos($restOfQuery, ' ', 1);
			$tableName = substr($restOfQuery, 1, $pos - 1);
		}

		unset($restOfQuery);

		return $tableName;
	}

	/**
	 * Drop or rename a table (with a bak_ prefix), depending on the user options
	 *
	 * @param   string  $tableName  The table name to drop or rename
	 *
	 * @return  void
	 */
	protected function dropOrRenameTable($tableName)
	{
		$db = $this->getDatabase();

		$prefix   = $this->dbjsonValues['prefix'];
		$existing = $this->dbjsonValues['existing'];

		// Should I back the table up?
		if (($prefix != '') && ($existing == 'backup') && (strpos($tableName, '#__') == 0))
		{
			// It's a table with a prefix, a prefix IS specified and we are asked to back it up.
			// Start by dropping any existing backup tables
			$backupTable = str_replace('#__', 'bak_', $tableName);
			try
			{
				$db->dropTable($backupTable);

				$db->renameTable($tableName, $backupTable);
			}
			catch (Exception $exc)
			{
				// We can't rename the table. Fall-through to the final line to delete it.
			}
		}

		// Try to drop the table anyway
		$db->dropTable($tableName);
	}

	/**
	 * Extract the View name from a CREATE VIEW query
	 *
	 * @param   string  $query The SQL query
	 *
	 * @return  string
	 */
	protected function getViewName($query)
	{
		$view_pos    = strpos($query, ' VIEW ');
		$restOfQuery = trim(substr($query, $view_pos + 6)); // Rest of query, after VIEW string

		// Is there a backtick?
		if (substr($restOfQuery, 0, 1) == '`')
		{
			// There is... Good, we'll just find the matching backtick
			$pos       = strpos($restOfQuery, '`', 1);
			$tableName = substr($restOfQuery, 1, $pos - 1);
		}
		else
		{
			// Nope, let's assume the table name ends in the next blank character
			$pos       = strpos($restOfQuery, ' ', 1);
			$tableName = substr($restOfQuery, 1, $pos - 1);
		}

		unset($restOfQuery);

		return $tableName;
	}

	/**
	 * Drops a View (VIEWs cannot be renamed)
	 *
	 * @param   string  $tableName
	 *
	 * @return  void
	 */
	protected function dropView($tableName)
	{
		$db        = $this->getDatabase();
		$dropQuery = 'DROP VIEW IF EXISTS `' . $tableName . '`;';
		$db->setQuery(trim($dropQuery));
		try
		{
			$db->execute();
		}
		catch (Exception $e)
		{
		}
	}

	/**
	 * Extracts the name of an entity (procedure, trigger, function) from a CREATE query
	 *
	 * @param   string  $query           The SQL query
	 * @param   string  $entity_keyword  The entity type, uppercase (e.g. "PROCEDURE")
	 *
	 * @return  string
	 */
	protected function getEntityName($query, $entity_keyword)
	{
		$entity_pos  = strpos($query, $entity_keyword);
		$restOfQuery =
			trim(substr($query, $entity_pos + strlen($entity_keyword))); // Rest of query, after entity key string

		// Is there a backtick?
		if (substr($restOfQuery, 0, 1) == '`')
		{
			// There is... Good, we'll just find the matching backtick
			$pos         = strpos($restOfQuery, '`', 1);
			$entity_name = substr($restOfQuery, 1, $pos - 1);
		}
		else
		{
			// Nope, let's assume the entity name ends in the next blank character
			$pos         = strpos($restOfQuery, ' ', 1);
			$entity_name = substr($restOfQuery, 1, $pos - 1);
		}

		unset($restOfQuery);

		return $entity_name;
	}

	/**
	 * Drops an entity (procedure, trigger, function)
	 *
	 * @param   string  $entity_keyword  Entity type, e.g. "PROCEDURE"
	 * @param   string  $entity_name     Entity name
	 *
	 * @return  void
	 */
	protected function dropEntity($entity_keyword, $entity_name)
	{
		$db        = $this->getDatabase();
		$dropQuery = 'DROP' . $entity_keyword . 'IF EXISTS `' . $entity_name . '`;';
		$db->setQuery(trim($dropQuery));
		$db->execute();
	}

	/**
	 * Switches an INSERT INTO query into a REPLACE INTO query if the user has so specified
	 *
	 * @param   string  $query  The query to switch
	 *
	 * @return  string  The switched query
	 */
	protected function applyReplaceInsteadofInsert($query)
	{
		$replacesql = $this->dbjsonValues['replace'];

		if ($replacesql)
		{
			// Use REPLACE instead of INSERT selected
			$query = 'REPLACE ' . substr($query, 7);

			return $query;
		}

		return $query;
	}

	/**
	 * Downgrade a query from UTF8MB4 to plain old UTF8
	 *
	 * @param   string  $query  The query to downgrade
	 *
	 * @return  string  The downgraded query
	 */
	protected function downgradeQueryToUtf8($query)
	{
		// Squash all possible utf8mb4_*_ci collations into the legacy utf8_unicode_ci
		$query = preg_replace('/utf8mb4_([a-z0-9]{1,}_){1,}ci/i', 'utf8_unicode_ci', $query);
		// Failsafe for any utf8mb4_ encodings we didn't catch above
		$query = str_ireplace('utf8mb4_', 'utf8_', $query);
		// In case the encoding and character set are defined separately
		$query = str_ireplace('utf8mb4', 'utf8', $query);

		// Squash UTF8MB4 characters to "Unicode replacement character" (U+FFFD). Slow and reliable.
		$query = preg_replace('%(?:\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})%xs', '�', $query);

		return $query;
	}

	/**
	 * Forcibly apply a new table encoding (UTF8 or UTF8MB4 depending on user selections and execution environment)
	 *
	 * @param   string  $tableName  The table name to apply the encoding to
	 *
	 * @return  void
	 */
	protected function forciblyApplyTableEncoding($tableName)
	{
		$db = $this->getDatabase();

		// Get a list of columns
		$columns = $db->getTableColumns($tableName);
		$mods    = array(); // array to hold individual MODIFY COLUMN commands

		if (is_array($columns))
		{
			foreach ($columns as $field => $column)
			{
				// Make sure we are redefining only columns which do support a collation
				$col = (object) $column;

				if (empty($col->Collation))
				{
					continue;
				}

				$null    = $col->Null == 'YES' ? 'NULL' : 'NOT NULL';
				$default = is_null($col->Default) ? '' : "DEFAULT '" . $db->escape($col->Default) . "'";

				$collation = $this->db->supportsUtf8mb4() ? 'utf8mb4_unicode_ci' : 'utf8_general_ci';

				$mods[] = "MODIFY COLUMN `$field` {$col->Type} $null $default COLLATE $collation";
			}
		}

		// Begin the modification statement
		$sql = "ALTER TABLE `$tableName` ";

		// Add commands to modify columns
		if (!empty($mods))
		{
			$sql .= implode(', ', $mods) . ', ';
		}

		// Add commands to modify the table collation
		$charset   = $this->db->supportsUtf8mb4() ? 'utf8mb4' : 'utf8';
		$collation = $this->db->supportsUtf8mb4() ? 'utf8mb4_unicode_ci' : 'utf8_general_ci';
		$sql .= 'DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collation . ';';
		$db->setQuery($sql);

		try
		{
			$db->execute();
		}
		catch (Exception $exc)
		{
			// Don't fail if the collation could not be changed
		}
	}

	/**
	 * Execute a database query, ignoring any failures
	 *
	 * @param   string  $sql  The SQL query to execute
	 *
	 * @return  void
	 */
	protected function executeQueryWithoutFailing($sql)
	{
		$this->db->setQuery($sql);

		try
		{
			$this->db->execute();
		}
		catch (Exception $exc)
		{
			// Do nothing if that fails. Maybe we can continue with the restoration.
		}
	}

	/**
	 * Replaces the engine type in a CREATE TABLE query when restoring from Percona or MariaDB to MySQL. Basically, it
	 * assumes that any kind of database storage engine it cannot recognize has to be replaced with MyISAM.
	 *
	 * @param   string  $query  The CREATE TABLE SQL query that you want modified
	 *
	 * @return  string  The modified CREATE TABLE query
	 */
	protected function replaceEngineType($query)
	{
		static $supportedEngines = null;
		static $defaultEngine = 'MyISAM';

		if (is_null($supportedEngines))
		{
			// Get the supported database engines and convert them to all uppercase
			$supportedEngines = $this->getSupportedDatabaseEngines();
			$supportedEngines = array_map('strtoupper', $supportedEngines);

			// The server's default engine is the first one listed (see getSupportedDatabaseEngines)
			$defaultEngine = reset($supportedEngines);

			// However, InnoDB + UTF8MB4 = lots of pain if the developer hadn't expected it. So we shall always try
			// to use MyISAM whenever possible. In fact, in most of the cases we're trying to convert Aria tables back
			// to MyISAM when you transfer between MariaDB and MySQL.
			if (in_array('MYISAM', $supportedEngines))
			{
				$defaultEngine = 'MyISAM';
			}
		}

		// Get the engine in the CREATE TABLE command
		$engine = $this->getCreateTableEngine($query);
		$engineUppercase = strtoupper($engine);

		// Check if the engine is supported. Otherwise use the default engine instead.
		if (!in_array($engineUppercase, $supportedEngines))
		{
			$replacements = array(
				'ENGINE=' . $engine,
				'ENGINE =' . $engine,
				'ENGINE= ' . $engine,
				'ENGINE = ' . $engine,
				'TYPE=' . $engine,
				'TYPE =' . $engine,
				'TYPE= ' . $engine,
				'TYPE = ' . $engine,
			);

			foreach ($replacements as $find)
			{
				$replaceWith = (substr($find, 0, 4) == 'TYPE') ? 'TYPE=' : 'ENGINE=';
				$query = str_ireplace($find, $replaceWith . $defaultEngine, $query);
			}
		}

		return $query;
	}

	/**
	 * Postprocess a CREATE TABLE query in the same way the backup engine does.
	 *
	 * This is here to deal with backups taken with a version of the backup engine older than this version of ANGIE. It
	 * allows us to retroactively address restoration issues caused at backup time by the database server returning too
	 * much information about the table which makes the CREATE TABLE incompatible with the new host.
	 *
	 * @param   string  $query  The CREATE TABLE query to post-process
	 *
	 * @return  string The post-processed query
	 */
	protected function removeTableOptions($query)
	{
		// Translate TYPE= to ENGINE=
		$query = str_replace('TYPE=', 'ENGINE=', $query);

		/**
		 * Remove the TABLESPACE option.
		 *
		 * The format of the TABLESPACE table option is:
		 * TABLESPACE tablespace_name [STORAGE {DISK|MEMORY}]
		 * where tablespace_name can be a quoted or unquoted identifier.
		 */
		list($validCharRegEx, $unicodeFlag) = $this->getMySQLIdentifierCharacterRegEx();
		$tablespaceName = "((($validCharRegEx){1,})|(`.*`))";
		$suffix         = 'STORAGE\s{1,}(DISK|MEMORY)';
		$regex          = "#TABLESPACE\s{1,}$tablespaceName\s{0,}($suffix){0,1}#i" . $unicodeFlag;
		$query      = preg_replace($regex, '', $query);

		// Remove table options {DATA|INDEX} DIRECTORY
		$regex     = "#(DATA|INDEX)\s{1,}DIRECTORY\s*=?\s*'.*'#i";
		$query = preg_replace($regex, '', $query);

		// Remove table options ROW_FORMAT=whatever (this can be realyl problematic with InnoDB)
		$regex     = "#ROW_FORMAT\s*=\s*[A-Z]{1,}#i";
		$query = preg_replace($regex, '', $query);

		// Abstract the names of table constraints and indices
		$prefix = $this->db->getPrefix();
		$regex = "#(CONSTRAINT|KEY|INDEX)\s{1,}`{$prefix}#i";
		$query = preg_replace($regex, '$1 `#__', $query);

		return $query;
	}

	/**
	 * Get a regular expression and its options for valid characters of an unquoted MySQL identifier.
	 *
	 * This is used wherever we need to detect an arbitrary, unquoted MySQL identifier per
	 * https://dev.mysql.com/doc/refman/5.7/en/identifiers.html
	 *
	 * Normally, we can use a pretty simple regular expression that makes use of the \X property (extended grapheme
	 * cluster) to describe the supported characters outside the 0-9, a-Z, A-Z, dollar and underscore ASCII ranges.
	 *
	 * HOWEVER! We discovered that Ubuntu 18.04 ships with a version of PCRE which does not support the \X property
	 * in character classes (the stuff between brackets). In this case we have to fall back to a long-winded regex
	 * that explicitly adds the \u0080 - \uFFFF range as an alternative.
	 *
	 * Also what if Unicode support is not compiled in PCRE? In this case we will fall back to a much simpler regex
	 * which only supports the ASCII subset of the allowed characters. In this case your database dump will be wrong
	 * if you use table names with non-ASCII characters.
	 *
	 * Since the detection is horribly slow we cache its results in an internal static variable.
	 *
	 * @return  array  In the format [$regex, $flags]
	 * @since   7.0.0
	 */
	protected function getMySQLIdentifierCharacterRegEx()
	{
		static $validCharRegEx = null;
		static $unicodeFlag = null;

		if (is_null($validCharRegEx) || is_null($unicodeFlag))
		{
			$brokenPCRE     = @preg_match('/[0-9a-zA-Z$_\X]/u', 's') === false;
			$noUnicode      = @preg_match('/\p{L}/u', 'σ') !== 1;
			$unicodeFlag    = $noUnicode ? '' : 'u';
			$validCharRegEx = $noUnicode ? '[0-9a-zA-Z$_]' : ($brokenPCRE ? '[0-9a-zA-Z$_]|[\x{0080}-\x{FFFF}]' : '[0-9a-zA-Z$_\X]');
		}

		return [$validCharRegEx, $unicodeFlag];
	}

	/**
	 * Ask the database to return a list of the supported database storage engines.
	 *
	 * @return  array
	 */
	protected function getSupportedDatabaseEngines()
	{
		// Default database engines
		$defaultEngines = array('MyISAM', 'BLACKHOLE', 'MEMORY', 'ARCHIVE', 'InnoDB');

		$db = $this->getDatabase();
		$sql = 'SHOW ENGINES';

		try
		{
			$engineMatrix = $db->setQuery($sql)->loadAssocList();
		}
		catch (\Exception $e)
		{
			return $defaultEngines;
		}

		$engines = array();

		foreach ($engineMatrix as $engineItem)
		{
			if (!isset($engineItem['Engine']))
			{
				continue;
			}

			if (!isset($engineItem['Support']))
			{
				continue;
			}

			$support = strtoupper($engineItem['Support']);

			if (!in_array($support, array('YES', 'DEFAULT', 'TRUE', '1')))
			{
				continue;
			}

			// The default engine goes on top
			if ($support == 'DEFAULT')
			{
				array_unshift($engines, $engineItem['Engine']);

				continue;
			}

			// Other engines go to the bottom of the list
			$engines[] = $engineItem['Engine'];
		}

		if (empty($engines))
		{
			return $defaultEngines;
		}

		return $engines;
	}

	protected function getCreateTableEngine($query)
	{
		// Fallback...
		$engine = 'MyISAM';

		// This is what MySQL should be using.
		$engine_keys = array('ENGINE=', 'TYPE=', 'ENGINE =', 'TYPE =');

		foreach ($engine_keys as $engine_key)
		{
			$start_pos = strrpos($query, $engine_key);

			if ($start_pos !== false)
			{
				// Advance the start position just after the position of the ENGINE keyword
				$start_pos += strlen($engine_key);
				// Try to locate the space after the engine type
				$end_pos = stripos($query, ' ', $start_pos);

				if ($end_pos === false)
				{
					// Uh... maybe it ends with ENGINE=EngineType;
					$end_pos = stripos($query, ';', $start_pos);
				}

				if ($end_pos !== false)
				{
					// Grab the string
					$engine = substr($query, $start_pos, $end_pos - $start_pos);

					break;
				}
			}
		}

		return $engine;
	}

	/**
	 * Drops tables before restoration if the 'existing' option is set to 'dropprefix' or 'dropall'.
	 *
	 * @return  void
	 */
	protected function conditionallyDropTables()
	{
		$db       = $this->getDatabase();
		$prefix   = $this->dbjsonValues['prefix'];
		$existing = $this->dbjsonValues['existing'];

		if (!in_array($existing, array('dropall', 'dropprefix')))
		{
			return;
		}

		$query = $db->getQuery(true)
			->select($db->qn('table_name'))
			->from($db->qn('information_schema') . '.' . $db->qn('tables'))
			->where($db->qn('table_schema') . ' = ' . $db->q($this->dbjsonValues['dbname']));

		if ($existing == 'dropprefix')
		{
			$query->where($db->qn('table_name') . ' LIKE ' . $db->q($prefix . '%'));
		}

		$tables = $db->setQuery($query)->loadColumn();

		$db->setQuery('SET FOREIGN_KEY_CHECKS = 0');
		$db->execute();

		foreach ($tables as $table)
		{
			try
			{
				$db->dropTable($table, true);
			}
			catch (Exception $e)
			{
				// We can safely ignore errors at this stage
			}
		}

		$db->setQuery('SET FOREIGN_KEY_CHECKS = 1');
		$db->execute();
	}

	/**
	 * Drops views before restoration if the 'existing' option is set to 'dropprefix' or 'dropall'.
	 *
	 * @return  void
	 */
	protected function conditionallyDropViews()
	{
		$db       = $this->getDatabase();
		$prefix   = $this->dbjsonValues['prefix'];
		$existing = $this->dbjsonValues['existing'];

		if (!in_array($existing, array('dropall', 'dropprefix')))
		{
			return;
		}

		$query = $db->getQuery(true)
			->select($db->qn('table_name'))
			->from($db->qn('information_schema') . '.' . $db->qn('views'))
			->where($db->qn('table_schema') . ' = ' . $db->q($this->dbjsonValues['dbname']));

		if ($existing == 'dropprefix')
		{
			$query->where($db->qn('table_name') . ' LIKE ' . $db->q($prefix . '%'));
		}

		$views = $db->setQuery($query)->loadColumn();

		$db->setQuery('SET FOREIGN_KEY_CHECKS = 0');
		$db->execute();

		foreach ($views as $view)
		{
			try
			{
				$sql = 'DROP VIEW IF EXISTS ' . $db->q($view);

				$db->setQuery($sql)->execute();
			}
			catch (Exception $e)
			{
				// We can safely ignore errors at this stage
			}
		}

		$db->setQuery('SET FOREIGN_KEY_CHECKS = 1');
		$db->execute();
	}

	/**
	 * Drops triggers before restoration if the 'existing' option is set to 'dropprefix' or 'dropall'.
	 *
	 * Normally triggers are dropped when dropping the table. However, if we end up with orphan triggers this code will
	 * collect and purge them.
	 *
	 * @return  void
	 */
	protected function conditionallyDropTriggers()
	{
		$db       = $this->getDatabase();
		$prefix   = $this->dbjsonValues['prefix'];
		$existing = $this->dbjsonValues['existing'];

		if (!in_array($existing, array('dropall', 'dropprefix')))
		{
			return;
		}

		$query = $db->getQuery(true)
			->select($db->qn('TRIGGER_NAME'))
			->from($db->qn('information_schema') . '.' . $db->qn('triggers'))
			->where($db->qn('TRIGGER_SCHEMA') . ' = ' . $db->q($this->dbjsonValues['dbname']));

		if ($existing == 'dropprefix')
		{
			$query->where($db->qn('TRIGGER_NAME') . ' LIKE ' . $db->q($prefix . '%'));
		}

		$triggers = $db->setQuery($query)->loadColumn();

		$db->setQuery('SET FOREIGN_KEY_CHECKS = 0');
		$db->execute();

		foreach ($triggers as $trigger)
		{
			try
			{
				$sql = 'DROP TRIGGER IF EXISTS ' . $db->q($trigger);

				$db->setQuery($sql)->execute();
			}
			catch (Exception $e)
			{
				// We can safely ignore errors at this stage
			}
		}

		$db->setQuery('SET FOREIGN_KEY_CHECKS = 1');
		$db->execute();
	}

	/**
	 * Drops functions before restoration if the 'existing' option is set to 'dropprefix' or 'dropall'.
	 *
	 * @return  void
	 */
	protected function conditionallyDropFunctions()
	{
		$db       = $this->getDatabase();
		$prefix   = $this->dbjsonValues['prefix'];
		$existing = $this->dbjsonValues['existing'];

		if (!in_array($existing, array('dropall', 'dropprefix')))
		{
			return;
		}

		$query = $db->getQuery(true)
			->select($db->qn('ROUTINE_NAME'))
			->from($db->qn('information_schema') . '.' . $db->qn('ROUTINES'))
			->where($db->qn('ROUTINE_SCHEMA') . ' = ' . $db->q($this->dbjsonValues['dbname']))
			->where($db->qn('ROUTINE_TYPE') . ' = ' . $db->q('FUNCTION'));

		if ($existing == 'dropprefix')
		{
			$query->where($db->qn('ROUTINE_NAME') . ' LIKE ' . $db->q($prefix . '%'));
		}

		$functions = $db->setQuery($query)->loadColumn();

		$db->setQuery('SET FOREIGN_KEY_CHECKS = 0');
		$db->execute();

		foreach ($functions as $function)
		{
			try
			{
				$sql = 'DROP FUNCTION IF EXISTS ' . $db->q($function);

				$db->setQuery($sql)->execute();
			}
			catch (Exception $e)
			{
				// We can safely ignore errors at this stage
			}
		}

		$db->setQuery('SET FOREIGN_KEY_CHECKS = 1');
		$db->execute();
	}

	/**
	 * Drops procedures before restoration if the 'existing' option is set to 'dropprefix' or 'dropall'.
	 *
	 * @return  void
	 */
	protected function conditionallyDropProcedures()
	{
		$db       = $this->getDatabase();
		$prefix   = $this->dbjsonValues['prefix'];
		$existing = $this->dbjsonValues['existing'];

		if (!in_array($existing, array('dropall', 'dropprefix')))
		{
			return;
		}

		$query = $db->getQuery(true)
			->select($db->qn('ROUTINE_NAME'))
			->from($db->qn('information_schema') . '.' . $db->qn('ROUTINES'))
			->where($db->qn('ROUTINE_SCHEMA') . ' = ' . $db->q($this->dbjsonValues['dbname']))
			->where($db->qn('ROUTINE_TYPE') . ' = ' . $db->q('PROCEDURE'));

		if ($existing == 'dropprefix')
		{
			$query->where($db->qn('ROUTINE_NAME') . ' LIKE ' . $db->q($prefix . '%'));
		}

		$procedures = $db->setQuery($query)->loadColumn();

		$db->setQuery('SET FOREIGN_KEY_CHECKS = 0');
		$db->execute();

		foreach ($procedures as $procedure)
		{
			try
			{
				$sql = 'DROP FUNCTION IF EXISTS ' . $db->q($procedure);

				$db->setQuery($sql)->execute();
			}
			catch (Exception $e)
			{
				// We can safely ignore errors at this stage
			}
		}

		$db->setQuery('SET FOREIGN_KEY_CHECKS = 1');
		$db->execute();
	}
}
