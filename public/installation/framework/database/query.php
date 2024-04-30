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
 * Query Element Class.
 *
 * @property-read    string  $name      The name of the element.
 * @property-read    array   $elements  An array of elements.
 * @property-read    string  $glue      Glue piece.
 */
class ADatabaseQueryElement
{
	/**
	 * @var    string  The name of the element.
	 */
	protected $name = null;

	/**
	 * @var    array  An array of elements.
	 */
	protected $elements = null;

	/**
	 * @var    string  Glue piece.
	 */
	protected $glue = null;

	/**
	 * Constructor.
	 *
	 * @param   string  $name      The name of the element.
	 * @param   mixed   $elements  String or array.
	 * @param   string  $glue      The glue for elements.
	 */
	public function __construct($name, $elements, $glue = ',')
	{
		$this->elements = array();
		$this->name = $name;
		$this->glue = $glue;

		$this->append($elements);
	}

	/**
	 * Magic function to convert the query element to a string.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		if (substr($this->name, -2) == '()')
		{
			return PHP_EOL . substr($this->name, 0, -2) . '(' . implode($this->glue, $this->elements) . ')';
		}
		else
		{
			return PHP_EOL . $this->name . ' ' . implode($this->glue, $this->elements);
		}
	}

	/**
	 * Appends element parts to the internal list.
	 *
	 * @param   mixed  $elements  String or array.
	 *
	 * @return  void
	 */
	public function append($elements)
	{
		if (is_array($elements))
		{
			$this->elements = array_merge($this->elements, $elements);
		}
		else
		{
			$this->elements = array_merge($this->elements, array($elements));
		}
	}

	/**
	 * Gets the elements of this element.
	 *
	 * @return  array
	 */
	public function getElements()
	{
		return $this->elements;
	}

	/**
	 * Method to provide deep copy support to nested objects and arrays
	 * when cloning.
	 *
	 * @return  void
	 */
	public function __clone()
	{
		foreach ($this as $k => $v)
		{
			if (is_object($v) || is_array($v))
			{
				$this->{$k} = unserialize(serialize($v));
			}
		}
	}
}

/**
 * Query Building Class.
 *
 * @method      string  q()   q($text, $escape = true)  Alias for quote method
 * @method      string  qn()  qs($name, $as = null)     Alias for quoteName method
 * @method      string  e()   e($text, $extra = false)   Alias for escape method
 */
abstract class ADatabaseQuery
{
	/**
	 * @var    ADatabaseDriver  The database driver.
	 */
	protected $db = null;

	/**
	 * @var    string  The SQL query (if a direct query string was provided).
	 */
	protected $sql = null;

	/**
	 * @var    string  The query type.
	 */
	protected $type = '';

	/**
	 * @var    ADatabaseQueryElement  The query element for a generic query (type = null).
	 */
	protected $element = null;

	/**
	 * @var    ADatabaseQueryElement  The select element.
	 */
	protected $select = null;

	/**
	 * @var    ADatabaseQueryElement  The delete element.
	 */
	protected $delete = null;

	/**
	 * @var    ADatabaseQueryElement  The update element.
	 */
	protected $update = null;

	/**
	 * @var    ADatabaseQueryElement  The insert element.
	 */
	protected $insert = null;

	/**
	 * @var    ADatabaseQueryElement  The from element.
	 */
	protected $from = null;

	/**
	 * @var    ADatabaseQueryElement  The join element.
	 */
	protected $join = null;

	/**
	 * @var    ADatabaseQueryElement  The set element.
	 */
	protected $set = null;

	/**
	 * @var    ADatabaseQueryElement  The where element.
	 */
	protected $where = null;

	/**
	 * @var    ADatabaseQueryElement  The group by element.
	 */
	protected $group = null;

	/**
	 * @var    ADatabaseQueryElement  The having element.
	 */
	protected $having = null;

	/**
	 * @var    ADatabaseQueryElement  The column list for an INSERT statement.
	 */
	protected $columns = null;

	/**
	 * @var    ADatabaseQueryElement  The values list for an INSERT statement.
	 */
	protected $values = null;

	/**
	 * @var    ADatabaseQueryElement  The order element.
	 */
	protected $order = null;

	/**
	 * @var   object  The auto increment insert field element.
	 */
	protected $autoIncrementField = null;

	/**
	 * @var    ADatabaseQueryElement  The call element.
	 */
	protected $call = null;

	/**
	 * @var    ADatabaseQueryElement  The exec element.
	 */
	protected $exec = null;

	/**
	 * @var    ADatabaseQueryElement  The union element.
	 */
	protected $union = null;

	/**
	 * Magic method to provide method alias support for quote() and quoteName().
	 *
	 * @param   string  $method  The called method.
	 * @param   array   $args    The array of arguments passed to the method.
	 *
	 * @return  string  The aliased method's return value or null.
	 */
	public function __call($method, $args)
	{
		if (empty($args))
		{
			return;
		}

		switch ($method)
		{
			case 'q':
				return $this->quote($args[0], isset($args[1]) ? $args[1] : true);
				break;

			case 'qn':
				return $this->quoteName($args[0], isset($args[1]) ? $args[1] : null);
				break;

			case 'e':
				return $this->escape($args[0], isset($args[1]) ? $args[1] : false);
				break;
		}
	}

	/**
	 * Class constructor.
	 *
	 * @param   ADatabaseDriver  $db  The database driver.
	 */
	public function __construct(ADatabaseDriver $db = null)
	{
		$this->db = $db;
	}

	/**
	 * Magic function to convert the query to a string.
	 *
	 * @return  string	The completed query.
	 */
	public function __toString()
	{
		$query = '';

		if ($this->sql)
		{
			return $this->sql;
		}

		switch ($this->type)
		{
			case 'element':
				$query .= (string) $this->element;
				break;

			case 'select':
				$query .= (string) $this->select;
				$query .= (string) $this->from;
				if ($this->join)
				{
					// Special case for joins
					foreach ($this->join as $join)
					{
						$query .= (string) $join;
					}
				}

				if ($this->where)
				{
					$query .= (string) $this->where;
				}

				if ($this->group)
				{
					$query .= (string) $this->group;
				}

				if ($this->having)
				{
					$query .= (string) $this->having;
				}

				if ($this->order)
				{
					$query .= (string) $this->order;
				}

				break;

			case 'union':
				$query .= (string) $this->union;
				break;

			case 'delete':
				$query .= (string) $this->delete;
				$query .= (string) $this->from;

				if ($this->join)
				{
					// Special case for joins
					foreach ($this->join as $join)
					{
						$query .= (string) $join;
					}
				}

				if ($this->where)
				{
					$query .= (string) $this->where;
				}

				break;

			case 'update':
				$query .= (string) $this->update;

				if ($this->join)
				{
					// Special case for joins
					foreach ($this->join as $join)
					{
						$query .= (string) $join;
					}
				}

				$query .= (string) $this->set;

				if ($this->where)
				{
					$query .= (string) $this->where;
				}

				break;

			case 'insert':
				$query .= (string) $this->insert;

				// Set method
				if ($this->set)
				{
					$query .= (string) $this->set;
				}
				// Columns-Values method
				elseif ($this->values)
				{
					if ($this->columns)
					{
						$query .= (string) $this->columns;
					}

					$elements = $this->values->getElements();
					if (!($elements[0] instanceof $this))
					{
						$query .= ' VALUES ';
					}

					$query .= (string) $this->values;
				}

				break;

			case 'call':
				$query .= (string) $this->call;
				break;

			case 'exec':
				$query .= (string) $this->exec;
				break;
		}

		if ($this instanceof ADatabaseQueryLimitable)
		{
			$query = $this->processLimit($query, $this->limit, $this->offset);
		}

		return $query;
	}

	/**
	 * Magic function to get protected variable value
	 *
	 * @param   string  $name  The name of the variable.
	 *
	 * @return  mixed
	 */
	public function __get($name)
	{
		return isset($this->$name) ? $this->$name : null;
	}

	/**
	 * Add a single column, or array of columns to the CALL clause of the query.
	 *
	 * Note that you must not mix insert, update, delete and select method calls when building a query.
	 * The call method can, however, be called multiple times in the same query.
	 *
	 * Usage:
	 * $query->call('a.*')->call('b.id');
	 * $query->call(array('a.*', 'b.id'));
	 *
	 * @param   mixed  $columns  A string or an array of field names.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function call($columns)
	{
		$this->type = 'call';

		if (is_null($this->call))
		{
			$this->call = new ADatabaseQueryElement('CALL', $columns);
		}
		else
		{
			$this->call->append($columns);
		}

		return $this;
	}

	/**
	 * Casts a value to a char.
	 *
	 * Ensure that the value is properly quoted before passing to the method.
	 *
	 * Usage:
	 * $query->select($query->castAsChar('a'));
	 *
	 * @param   string  $value  The value to cast as a char.
	 *
	 * @return  string  Returns the cast value.
	 */
	public function castAsChar($value)
	{
		return $value;
	}

	/**
	 * Gets the number of characters in a string.
	 *
	 * Note, use 'length' to find the number of bytes in a string.
	 *
	 * Usage:
	 * $query->select($query->charLength('a'));
	 *
	 * @param   string  $field      A value.
	 * @param   string  $operator   Comparison operator between charLength integer value and $condition
	 * @param   string  $condition  Integer value to compare charLength with.
	 *
	 * @return  string  The required char length call.
	 */
	public function charLength($field, $operator = null, $condition = null)
	{
		return 'CHAR_LENGTH(' . $field . ')' . (isset($operator) && isset($condition) ? ' ' . $operator . ' ' . $condition : '');
	}

	/**
	 * Clear data from the query or a specific clause of the query.
	 *
	 * @param   string  $clause  Optionally, the name of the clause to clear, or nothing to clear the whole query.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function clear($clause = null)
	{
		$this->sql = null;

		switch ($clause)
		{
			case 'select':
				$this->select = null;
				$this->type = null;
				break;

			case 'delete':
				$this->delete = null;
				$this->type = null;
				break;

			case 'update':
				$this->update = null;
				$this->type = null;
				break;

			case 'insert':
				$this->insert = null;
				$this->type = null;
				$this->autoIncrementField = null;
				break;

			case 'from':
				$this->from = null;
				break;

			case 'join':
				$this->join = null;
				break;

			case 'set':
				$this->set = null;
				break;

			case 'where':
				$this->where = null;
				break;

			case 'group':
				$this->group = null;
				break;

			case 'having':
				$this->having = null;
				break;

			case 'order':
				$this->order = null;
				break;

			case 'columns':
				$this->columns = null;
				break;

			case 'values':
				$this->values = null;
				break;

			case 'exec':
				$this->exec = null;
				$this->type = null;
				break;

			case 'call':
				$this->call = null;
				$this->type = null;
				break;

			case 'limit':
				$this->offset = 0;
				$this->limit = 0;
				break;

			case 'union':
				$this->union = null;
				break;

			default:
				$this->type = null;
				$this->select = null;
				$this->delete = null;
				$this->update = null;
				$this->insert = null;
				$this->from = null;
				$this->join = null;
				$this->set = null;
				$this->where = null;
				$this->group = null;
				$this->having = null;
				$this->order = null;
				$this->columns = null;
				$this->values = null;
				$this->autoIncrementField = null;
				$this->exec = null;
				$this->call = null;
				$this->union = null;
				$this->offset = 0;
				$this->limit = 0;
				break;
		}

		return $this;
	}

	/**
	 * Adds a column, or array of column names that would be used for an INSERT INTO statement.
	 *
	 * @param   mixed  $columns  A column name, or array of column names.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function columns($columns)
	{
		if (is_null($this->columns))
		{
			$this->columns = new ADatabaseQueryElement('()', $columns);
		}
		else
		{
			$this->columns->append($columns);
		}

		return $this;
	}

	/**
	 * Concatenates an array of column names or values.
	 *
	 * Usage:
	 * $query->select($query->concatenate(array('a', 'b')));
	 *
	 * @param   array   $values     An array of values to concatenate.
	 * @param   string  $separator  As separator to place between each value.
	 *
	 * @return  string  The concatenated values.
	 */
	public function concatenate($values, $separator = null)
	{
		if ($separator)
		{
			return 'CONCATENATE(' . implode(' || ' . $this->quote($separator) . ' || ', $values) . ')';
		}
		else
		{
			return 'CONCATENATE(' . implode(' || ', $values) . ')';
		}
	}

	/**
	 * Gets the current date and time.
	 *
	 * Usage:
	 * $query->where('published_up < '.$query->currentTimestamp());
	 *
	 * @return  string
	 */
	public function currentTimestamp()
	{
		return 'CURRENT_TIMESTAMP()';
	}

	/**
	 * Returns a PHP date() function compliant date format for the database driver.
	 *
	 * This method is provided for use where the query object is passed to a function for modification.
	 * If you have direct access to the database object, it is recommended you use the getDateFormat method directly.
	 *
	 * @return  string  The format string.
	 */
	public function dateFormat()
	{
		if (!($this->db instanceof ADatabaseDriver))
		{
			throw new RuntimeException('ANGIE_DATABASE_ERROR_INVALID_DB_OBJECT');
		}

		return $this->db->getDateFormat();
	}

	/**
	 * Creates a formatted dump of the query for debugging purposes.
	 *
	 * Usage:
	 * echo $query->dump();
	 *
	 * @return  string
	 */
	public function dump()
	{
		return '<pre class="ADatabasequery">' . str_replace('#__', $this->db->getPrefix(), $this) . '</pre>';
	}

	/**
	 * Add a table name to the DELETE clause of the query.
	 *
	 * Note that you must not mix insert, update, delete and select method calls when building a query.
	 *
	 * Usage:
	 * $query->delete('#__a')->where('id = 1');
	 *
	 * @param   string  $table  The name of the table to delete from.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function delete($table = null)
	{
		$this->type = 'delete';
		$this->delete = new ADatabaseQueryElement('DELETE', null);

		if (!empty($table))
		{
			$this->from($table);
		}

		return $this;
	}

	/**
	 * Method to escape a string for usage in an SQL statement.
	 *
	 * This method is provided for use where the query object is passed to a function for modification.
	 * If you have direct access to the database object, it is recommended you use the escape method directly.
	 *
	 * Note that 'e' is an alias for this method as it is in ADatabaseDriver.
	 *
	 * @param   string   $text   The string to be escaped.
	 * @param   boolean  $extra  Optional parameter to provide extra escaping.
	 *
	 * @return  string  The escaped string.
	 *
	 * @throws  RuntimeException if the internal db property is not a valid object.
	 */
	public function escape($text, $extra = false)
	{
		if (!($this->db instanceof ADatabaseDriver))
		{
			throw new RuntimeException('ANGIE_DATABASE_ERROR_INVALID_DB_OBJECT');
		}

		return $this->db->escape($text, $extra);
	}

	/**
	 * Add a single column, or array of columns to the EXEC clause of the query.
	 *
	 * Note that you must not mix insert, update, delete and select method calls when building a query.
	 * The exec method can, however, be called multiple times in the same query.
	 *
	 * Usage:
	 * $query->exec('a.*')->exec('b.id');
	 * $query->exec(array('a.*', 'b.id'));
	 *
	 * @param   mixed  $columns  A string or an array of field names.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function exec($columns)
	{
		$this->type = 'exec';

		if (is_null($this->exec))
		{
			$this->exec = new ADatabaseQueryElement('EXEC', $columns);
		}
		else
		{
			$this->exec->append($columns);
		}

		return $this;
	}

	/**
	 * Add a table to the FROM clause of the query.
	 *
	 * Note that while an array of tables can be provided, it is recommended you use explicit joins.
	 *
	 * Usage:
	 * $query->select('*')->from('#__a');
	 *
	 * @param   mixed   $tables         A string or array of table names.
	 *                                  This can be a ADatabaseQuery object (or a child of it) when used
	 *                                  as a subquery in FROM clause along with a value for $subQueryAlias.
	 * @param   string  $subQueryAlias  Alias used when $tables is a ADatabaseQuery.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function from($tables, $subQueryAlias = null)
	{
		if (is_null($this->from))
		{
			if ($tables instanceof $this)
			{
				if (is_null($subQueryAlias))
				{
					throw new RuntimeException('ANGIE_DATABASE_ERROR_NULL_SUBQUERY_ALIAS');
				}

				$tables = '( ' . (string) $tables . ' ) AS ' . $this->quoteName($subQueryAlias);
			}

			$this->from = new ADatabaseQueryElement('FROM', $tables);
		}
		else
		{
			$this->from->append($tables);
		}

		return $this;
	}

	/**
	 * Used to get a string to extract year from date column.
	 *
	 * Usage:
	 * $query->select($query->year($query->quoteName('dateColumn')));
	 *
	 * @param   string  $date  Date column containing year to be extracted.
	 *
	 * @return  string  Returns string to extract year from a date.
	 */
	public function year($date)
	{
		return 'YEAR(' . $date . ')';
	}

	/**
	 * Used to get a string to extract month from date column.
	 *
	 * Usage:
	 * $query->select($query->month($query->quoteName('dateColumn')));
	 *
	 * @param   string  $date  Date column containing month to be extracted.
	 *
	 * @return  string  Returns string to extract month from a date.
	 */
	public function month($date)
	{
		return 'MONTH(' . $date . ')';
	}

	/**
	 * Used to get a string to extract day from date column.
	 *
	 * Usage:
	 * $query->select($query->day($query->quoteName('dateColumn')));
	 *
	 * @param   string  $date  Date column containing day to be extracted.
	 *
	 * @return  string  Returns string to extract day from a date.
	 */
	public function day($date)
	{
		return 'DAY(' . $date . ')';
	}

	/**
	 * Used to get a string to extract hour from date column.
	 *
	 * Usage:
	 * $query->select($query->hour($query->quoteName('dateColumn')));
	 *
	 * @param   string  $date  Date column containing hour to be extracted.
	 *
	 * @return  string  Returns string to extract hour from a date.
	 */
	public function hour($date)
	{
		return 'HOUR(' . $date . ')';
	}

	/**
	 * Used to get a string to extract minute from date column.
	 *
	 * Usage:
	 * $query->select($query->minute($query->quoteName('dateColumn')));
	 *
	 * @param   string  $date  Date column containing minute to be extracted.
	 *
	 * @return  string  Returns string to extract minute from a date.
	 */
	public function minute($date)
	{
		return 'MINUTE(' . $date . ')';
	}

	/**
	 * Used to get a string to extract seconds from date column.
	 *
	 * Usage:
	 * $query->select($query->second($query->quoteName('dateColumn')));
	 *
	 * @param   string  $date  Date column containing second to be extracted.
	 *
	 * @return  string  Returns string to extract second from a date.
	 */
	public function second($date)
	{
		return 'SECOND(' . $date . ')';
	}

	/**
	 * Add a grouping column to the GROUP clause of the query.
	 *
	 * Usage:
	 * $query->group('id');
	 *
	 * @param   mixed  $columns  A string or array of ordering columns.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function group($columns)
	{
		if (is_null($this->group))
		{
			$this->group = new ADatabaseQueryElement('GROUP BY', $columns);
		}
		else
		{
			$this->group->append($columns);
		}

		return $this;
	}

	/**
	 * A conditions to the HAVING clause of the query.
	 *
	 * Usage:
	 * $query->group('id')->having('COUNT(id) > 5');
	 *
	 * @param   mixed   $conditions  A string or array of columns.
	 * @param   string  $glue        The glue by which to join the conditions. Defaults to AND.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function having($conditions, $glue = 'AND')
	{
		if (is_null($this->having))
		{
			$glue = strtoupper($glue);
			$this->having = new ADatabaseQueryElement('HAVING', $conditions, " $glue ");
		}
		else
		{
			$this->having->append($conditions);
		}

		return $this;
	}

	/**
	 * Add an INNER JOIN clause to the query.
	 *
	 * Usage:
	 * $query->innerJoin('b ON b.id = a.id')->innerJoin('c ON c.id = b.id');
	 *
	 * @param   string  $condition  The join condition.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function innerJoin($condition)
	{
		$this->join('INNER', $condition);

		return $this;
	}

	/**
	 * Add a table name to the INSERT clause of the query.
	 *
	 * Note that you must not mix insert, update, delete and select method calls when building a query.
	 *
	 * Usage:
	 * $query->insert('#__a')->set('id = 1');
	 * $query->insert('#__a)->columns('id, title')->values('1,2')->values->('3,4');
	 * $query->insert('#__a)->columns('id, title')->values(array('1,2', '3,4'));
	 *
	 * @param   mixed    $table           The name of the table to insert data into.
	 * @param   boolean  $incrementField  The name of the field to auto increment.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function insert($table, $incrementField=false)
	{
		$this->type = 'insert';
		$this->insert = new ADatabaseQueryElement('INSERT INTO', $table);
		$this->autoIncrementField = $incrementField;

		return $this;
	}

	/**
	 * Add a table name to the REPLACE INTO clause of the query.
	 *
	 * Note that you must not mix insert, update, delete and select method calls when building a query.
	 *
	 * Usage:
	 * $query->insert('#__a')->set('id = 1');
	 * $query->insert('#__a)->columns('id, title')->values('1,2')->values->('3,4');
	 * $query->insert('#__a)->columns('id, title')->values(array('1,2', '3,4'));
	 *
	 * @param   mixed    $table           The name of the table to insert data into.
	 * @param   boolean  $incrementField  The name of the field to auto increment.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function replace($table, $incrementField=false)
	{
		$this->type = 'insert';
		$this->insert = new ADatabaseQueryElement('REPLACE INTO', $table);
		$this->autoIncrementField = $incrementField;

		return $this;
	}

	/**
	 * Add a JOIN clause to the query.
	 *
	 * Usage:
	 * $query->join('INNER', 'b ON b.id = a.id);
	 *
	 * @param   string  $type        The type of join. This string is prepended to the JOIN keyword.
	 * @param   string  $conditions  A string or array of conditions.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function join($type, $conditions)
	{
		if (is_null($this->join))
		{
			$this->join = array();
		}
		$this->join[] = new ADatabaseQueryElement(strtoupper($type) . ' JOIN', $conditions);

		return $this;
	}

	/**
	 * Add a LEFT JOIN clause to the query.
	 *
	 * Usage:
	 * $query->leftJoin('b ON b.id = a.id')->leftJoin('c ON c.id = b.id');
	 *
	 * @param   string  $condition  The join condition.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function leftJoin($condition)
	{
		$this->join('LEFT', $condition);

		return $this;
	}

	/**
	 * Get the length of a string in bytes.
	 *
	 * Note, use 'charLength' to find the number of characters in a string.
	 *
	 * Usage:
	 * query->where($query->length('a').' > 3');
	 *
	 * @param   string  $value  The string to measure.
	 *
	 * @return  int
	 */
	public function length($value)
	{
		return 'LENGTH(' . $value . ')';
	}

	/**
	 * Get the null or zero representation of a timestamp for the database driver.
	 *
	 * This method is provided for use where the query object is passed to a function for modification.
	 * If you have direct access to the database object, it is recommended you use the nullDate method directly.
	 *
	 * Usage:
	 * $query->where('modified_date <> '.$query->nullDate());
	 *
	 * @param   boolean  $quoted  Optionally wraps the null date in database quotes (true by default).
	 *
	 * @return  string  Null or zero representation of a timestamp.
	 */
	public function nullDate($quoted = true)
	{
		if (!($this->db instanceof ADatabaseDriver))
		{
			throw new RuntimeException('ANGIE_DATABASE_ERROR_INVALID_DB_OBJECT');
		}

		$result = $this->db->getNullDate($quoted);

		if ($quoted)
		{
			return $this->db->quote($result);
		}

		return $result;
	}

	/**
	 * Add a ordering column to the ORDER clause of the query.
	 *
	 * Usage:
	 * $query->order('foo')->order('bar');
	 * $query->order(array('foo','bar'));
	 *
	 * @param   mixed  $columns  A string or array of ordering columns.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function order($columns)
	{
		if (is_null($this->order))
		{
			$this->order = new ADatabaseQueryElement('ORDER BY', $columns);
		}
		else
		{
			$this->order->append($columns);
		}

		return $this;
	}

	/**
	 * Add an OUTER JOIN clause to the query.
	 *
	 * Usage:
	 * $query->outerJoin('b ON b.id = a.id')->outerJoin('c ON c.id = b.id');
	 *
	 * @param   string  $condition  The join condition.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function outerJoin($condition)
	{
		$this->join('OUTER', $condition);

		return $this;
	}

	/**
	 * Method to quote and optionally escape a string to database requirements for insertion into the database.
	 *
	 * This method is provided for use where the query object is passed to a function for modification.
	 * If you have direct access to the database object, it is recommended you use the quote method directly.
	 *
	 * Note that 'q' is an alias for this method as it is in ADatabaseDriver.
	 *
	 * Usage:
	 * $query->quote('fulltext');
	 * $query->q('fulltext');
	 *
	 * @param   string   $text    The string to quote.
	 * @param   boolean  $escape  True to escape the string, false to leave it unchanged.
	 *
	 * @return  string  The quoted input string.
	 *
	 * @throws  RuntimeException if the internal db property is not a valid object.
	 */
	public function quote($text, $escape = true)
	{
		if (!($this->db instanceof ADatabaseDriver))
		{
			throw new RuntimeException('ANGIE_DATABASE_ERROR_INVALID_DB_OBJECT');
		}

		return $this->db->quote(($escape ? $this->db->escape($text) : $text));
	}

	/**
	 * Wrap an SQL statement identifier name such as column, table or database names in quotes to prevent injection
	 * risks and reserved word conflicts.
	 *
	 * This method is provided for use where the query object is passed to a function for modification.
	 * If you have direct access to the database object, it is recommended you use the quoteName method directly.
	 *
	 * Note that 'qn' is an alias for this method as it is in ADatabaseDriver.
	 *
	 * Usage:
	 * $query->quoteName('#__a');
	 * $query->qn('#__a');
	 *
	 * @param   mixed  $name  The identifier name to wrap in quotes, or an array of identifier names to wrap in quotes.
	 *                        Each type supports dot-notation name.
	 * @param   mixed  $as    The AS query part associated to $name. It can be string or array, in latter case it has to be
	 *                        same length of $name; if is null there will not be any AS part for string or array element.
	 *
	 * @return  mixed  The quote wrapped name, same type of $name.
	 *
	 * @throws  RuntimeException if the internal db property is not a valid object.
	 */
	public function quoteName($name, $as = null)
	{
		if (!($this->db instanceof ADatabaseDriver))
		{
			throw new RuntimeException('ANGIE_DATABASE_ERROR_INVALID_DB_OBJECT');
		}

		return $this->db->quoteName($name, $as);
	}

	/**
	 * Add a RIGHT JOIN clause to the query.
	 *
	 * Usage:
	 * $query->rightJoin('b ON b.id = a.id')->rightJoin('c ON c.id = b.id');
	 *
	 * @param   string  $condition  The join condition.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function rightJoin($condition)
	{
		$this->join('RIGHT', $condition);

		return $this;
	}

	/**
	 * Add a single column, or array of columns to the SELECT clause of the query.
	 *
	 * Note that you must not mix insert, update, delete and select method calls when building a query.
	 * The select method can, however, be called multiple times in the same query.
	 *
	 * Usage:
	 * $query->select('a.*')->select('b.id');
	 * $query->select(array('a.*', 'b.id'));
	 *
	 * @param   mixed  $columns  A string or an array of field names.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function select($columns)
	{
		$this->type = 'select';

		if (is_null($this->select))
		{
			$this->select = new ADatabaseQueryElement('SELECT', $columns);
		}
		else
		{
			$this->select->append($columns);
		}

		return $this;
	}

	/**
	 * Add a single condition string, or an array of strings to the SET clause of the query.
	 *
	 * Usage:
	 * $query->set('a = 1')->set('b = 2');
	 * $query->set(array('a = 1', 'b = 2');
	 *
	 * @param   mixed   $conditions  A string or array of string conditions.
	 * @param   string  $glue        The glue by which to join the condition strings. Defaults to ,.
	 *                               Note that the glue is set on first use and cannot be changed.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function set($conditions, $glue = ',')
	{
		if (is_null($this->set))
		{
			$glue = strtoupper($glue);
			$this->set = new ADatabaseQueryElement('SET', $conditions, "\n\t$glue ");
		}
		else
		{
			$this->set->append($conditions);
		}

		return $this;
	}

	/**
	 * Allows a direct query to be provided to the database
	 * driver's setQuery() method, but still allow queries
	 * to have bounded variables.
	 *
	 * Usage:
	 * $query->setQuery('select * from #__users');
	 *
	 * @param   mixed  $sql  An SQL Query
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function setQuery($sql)
	{
		$this->sql = $sql;

		return $this;
	}

	/**
	 * Add a table name to the UPDATE clause of the query.
	 *
	 * Note that you must not mix insert, update, delete and select method calls when building a query.
	 *
	 * Usage:
	 * $query->update('#__foo')->set(...);
	 *
	 * @param   string  $table  A table to update.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function update($table)
	{
		$this->type = 'update';
		$this->update = new ADatabaseQueryElement('UPDATE', $table);

		return $this;
	}

	/**
	 * Adds a tuple, or array of tuples that would be used as values for an INSERT INTO statement.
	 *
	 * Usage:
	 * $query->values('1,2,3')->values('4,5,6');
	 * $query->values(array('1,2,3', '4,5,6'));
	 *
	 * @param   string  $values  A single tuple, or array of tuples.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function values($values)
	{
		if (is_null($this->values))
		{
			$this->values = new ADatabaseQueryElement('()', $values, '),(');
		}
		else
		{
			$this->values->append($values);
		}

		return $this;
	}

	/**
	 * Add a single condition, or an array of conditions to the WHERE clause of the query.
	 *
	 * Usage:
	 * $query->where('a = 1')->where('b = 2');
	 * $query->where(array('a = 1', 'b = 2'));
	 *
	 * @param   mixed   $conditions  A string or array of where conditions.
	 * @param   string  $glue        The glue by which to join the conditions. Defaults to AND.
	 *                               Note that the glue is set on first use and cannot be changed.
	 *
	 * @return  ADatabaseQuery  Returns this object to allow chaining.
	 */
	public function where($conditions, $glue = 'AND')
	{
		if (is_null($this->where))
		{
			$glue = strtoupper($glue);
			$this->where = new ADatabaseQueryElement('WHERE', $conditions, " $glue ");
		}
		else
		{
			$this->where->append($conditions);
		}

		return $this;
	}

	/**
	 * Method to provide deep copy support to nested objects and
	 * arrays when cloning.
	 *
	 * @return  void
	 */
	public function __clone()
	{
		foreach ($this as $k => $v)
		{
			if (is_object($v) || is_array($v))
			{
				$this->{$k} = unserialize(serialize($v));
			}
		}
	}

	/**
	 * Add a query to UNION with the current query.
	 * Multiple unions each require separate statements and create an array of unions.
	 *
	 * Usage:
	 * $query->union('SELECT name FROM  #__foo')
	 * $query->union('SELECT name FROM  #__foo','distinct')
	 * $query->union(array('SELECT name FROM  #__foo','SELECT name FROM  #__bar'))
	 *
	 * @param   mixed    $query     The ADatabaseQuery object or string to union.
	 * @param   boolean  $distinct  True to only return distinct rows from the union.
	 * @param   string   $glue      The glue by which to join the conditions.
	 *
	 * @return  mixed    The ADatabaseQuery object on success or boolean false on failure.
	 */
	public function union($query, $distinct = false, $glue = '')
	{

		// Clear any ORDER BY clause in UNION query
		// See http://dev.mysql.com/doc/refman/5.0/en/union.html
		if (!is_null($this->order))
		{
			$this->clear('order');
		}

		// Set up the DISTINCT flag, the name with parentheses, and the glue.
		if ($distinct)
		{
			$name = 'UNION DISTINCT ()';
			$glue = ')' . PHP_EOL . 'UNION DISTINCT (';
		}
		else
		{
			$glue = ')' . PHP_EOL . 'UNION (';
			$name = 'UNION ()';

		}
		// Get the ADatabaseQueryElement if it does not exist
		if (is_null($this->union))
		{
				$this->union = new ADatabaseQueryElement($name, $query, "$glue");
		}
		// Otherwise append the second UNION.
		else
		{
			$glue = '';
			$this->union->append($query);
		}

		return $this;
	}

	/**
	 * Add a query to UNION DISTINCT with the current query. Simply a proxy to Union with the Distinct clause.
	 *
	 * Usage:
	 * $query->unionDistinct('SELECT name FROM  #__foo')
	 *
	 * @param   mixed   $query  The ADatabaseQuery object or string to union.
	 * @param   string  $glue   The glue by which to join the conditions.
	 *
	 * @return  mixed   The ADatabaseQuery object on success or boolean false on failure.
	 */
	public function unionDistinct($query, $glue = '')
	{
		$distinct = true;

		// Apply the distinct flag to the union.
		return $this->union($query, $distinct, $glue);
	}
}
