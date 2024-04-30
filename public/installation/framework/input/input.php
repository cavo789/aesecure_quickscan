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
 * Input handling class
 *
 * @method 	integer	getInt($name, $default)
 * @method 	integer	getInteger($name, $default)
 * @method 	integer	getUint($name, $default)
 * @method 	float 	getFloat($name, $default)
 * @method 	float 	getDouble($name, $default)
 * @method 	boolean getBool($name, $default)
 * @method 	boolean	getBoolean($name, $default)
 * @method 	string 	getWord($name, $default)
 * @method 	string 	getAlnum($name, $default)
 * @method 	string 	getCmd($name, $default)
 * @method 	string 	getBase64($name, $default)
 * @method 	string 	getString($name, $default)
 * @method 	string 	getHtml($name, $default)
 * @method 	string 	getPath($name, $default)
 * @method 	string 	getUsername($name, $default)
 */
class AInput implements Serializable, Countable
{
	/** @var AFilterInput Filter object to use. */
	protected $filter = null;

	/** @var array Input data */
	protected $data = array();

	/** @var array Input objects */
	protected $inputs = array();

	/**
	 * Constructor
	 *
	 * @param   array  $source   Source data (Optional, default is $_REQUEST)
	 */
	public function __construct($source = null)
	{
		$this->filter = AFilterInput::getInstance();

		if (is_null($source))
		{
			$this->data = &$_REQUEST;
		}
		else
		{
			$this->data = $source;
		}
	}

	/**
	 * Magic method to get an input object
	 *
	 * @param   mixed  $name  Name of the input object to retrieve.
	 *
	 * @return  AInput  The request input object
	 */
	public function __get($name)
	{
		if (isset($this->inputs[$name]))
		{
			return $this->inputs[$name];
		}

		$className = 'AInput' . ucfirst($name);
		if (class_exists($className))
		{
			$this->inputs[$name] = new $className(null);
			return $this->inputs[$name];
		}

		$superGlobal = '_' . strtoupper($name);
		if (isset($GLOBALS[$superGlobal]))
		{
			$this->inputs[$name] = new AInput($GLOBALS[$superGlobal]);
			return $this->inputs[$name];
		}
	}

	/**
	 * Get the number of variables.
	 *
	 * @return  integer  The number of variables in the input.
	 *
	 * @see     Countable::count()
	 */
	#[\ReturnTypeWillChange]
	public function count()
	{
		return count($this->data);
	}

	/**
	 * Gets a value from the input data.
	 *
	 * @param   string  $name     Name of the value to get.
	 * @param   mixed   $default  Default value to return if variable does not exist.
	 * @param   string  $filter   Filter to apply to the value.
	 *
	 * @return  mixed  The filtered input value.
	 *
	 * @since   11.1
	 */
	public function get($name, $default = null, $filter = 'cmd')
	{
		if (isset($this->data[$name]))
		{
			return $this->filter->clean($this->data[$name], $filter);
		}

		return $default;
	}

	/**
	 * Gets an array of values from the request.
	 *
	 * @param   array  $vars        Associative array of keys and filter types to apply.
	 * @param   mixed  $datasource  Array to retrieve data from, or null
	 *
	 * @return  mixed  The filtered input data.
	 */
	public function getArray(array $vars, $datasource = null)
	{
		$results = array();

		foreach ($vars as $k => $v)
		{
			if (is_array($v))
			{
				if (is_null($datasource))
				{
					$results[$k] = $this->getArray($v, $this->get($k, null, 'array'));
				}
				else
				{
					$results[$k] = $this->getArray($v, $datasource[$k]);
				}
			}
			else
			{
				if (is_null($datasource))
				{
					$results[$k] = $this->get($k, null, $v);
				}
				elseif (isset($datasource[$k]))
				{
					$results[$k] = $this->filter->clean($datasource[$k], $v);
				}
				else
				{
					$results[$k] = $this->filter->clean(null, $v);
				}
			}
		}

		return $results;
	}

	/**
	 * Sets a value
	 *
	 * @param   string  $name   Name of the value to set.
	 * @param   mixed   $value  Value to assign to the input.
	 *
	 * @return  void
	 */
	public function set($name, $value)
	{
		$this->data[$name] = $value;
	}

	/**
	 * Define a value. The value will only be set if there's no value for the name or if it is null.
	 *
	 * @param   string  $name   Name of the value to define.
	 * @param   mixed   $value  Value to assign to the input.
	 *
	 * @return  void
	 */
	public function def($name, $value)
	{
		if (isset($this->data[$name]))
		{
			return;
		}

		$this->data[$name] = $value;
	}

	/**
	 * Magic method to get filtered input data.
	 *
	 * @param   string  $name       Name of the filter type prefixed with 'get'.
	 * @param   array   $arguments  [0] The name of the variable [1] The default value.
	 *
	 * @return  mixed   The filtered input value.
	 */
	public function __call($name, $arguments)
	{
		if (substr($name, 0, 3) == 'get')
		{

			$filter = substr($name, 3);

			$default = null;
			if (isset($arguments[1]))
			{
				$default = $arguments[1];
			}

			return $this->get($arguments[0], $default, $filter);
		}
	}

	/**
	 * Gets the request method.
	 *
	 * @return  string   The request method.
	 */
	public function getMethod()
	{
		$method = strtoupper($_SERVER['REQUEST_METHOD']);
		return $method;
	}

	/**
	 * Method to serialize the input.
	 *
	 * @return  string  The serialized input.
	 * @deprecated
	 */
	public function serialize()
	{
		// Load all of the inputs.
		$this->loadAllInputs();

		// Remove $_ENV and $_SERVER from the inputs.
		$inputs = $this->inputs;
		unset($inputs['env']);
		unset($inputs['server']);

		// Serialize the data and inputs.
		return serialize(array($this->data, $inputs));
	}

	/**
	 * Method to unserialize the input.
	 *
	 * @param   string  $input  The serialized input.
	 *
	 * @return  JInput  The input object.
	 * @deprecated
	 *
	 * @since   12.1
	 */
	public function unserialize($input)
	{
		// Unserialize the data, and inputs.
		list($this->data, $this->inputs) = unserialize($input);

		// Load the filter.
		$this->filter = AFilterInput::getInstance();
	}

	public function __serialize()
	{
		// Load all of the inputs.
		$this->loadAllInputs();

		// Remove $_ENV and $_SERVER from the inputs.
		$inputs = $this->inputs;
		unset($inputs['env']);
		unset($inputs['server']);

		// Serialize the data and inputs.
		return [
			'data'    => $this->data,
			'inputs'  => $inputs,
		];
	}

	public function __unserialize(array $data)
	{
		$this->data    = $data['data'];
		$this->inputs  = $data['inputs'];
	}

	/**
	 * Method to load all of the global inputs.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function loadAllInputs()
	{
		static $loaded = false;

		if (!$loaded)
		{
			// Load up all the globals.
			foreach ($GLOBALS as $global => $data)
			{
				// Check if the global starts with an underscore.
				if (strpos($global, '_') === 0)
				{
					// Convert global name to input name.
					$global = strtolower($global);
					$global = substr($global, 1);

					// Get the input.
					$this->$global;
				}
			}

			$loaded = true;
		}
	}

	public function getData()
	{
		return (array) $this->data;
	}

	public function setData($data)
	{
		$this->data = (array) $data;
	}
}
