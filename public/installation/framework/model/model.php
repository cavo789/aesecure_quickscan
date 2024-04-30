<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

#[\AllowDynamicProperties]
class AModel
{
	/**
	 * Should I save the model's state in the session?
	 * @var bool
	 */
	protected $_savestate = true;

	/**
	 * Are the state variables already set?
	 *
	 * @var bool
	 */
	protected $_state_set = false;

	/** @var \AContainer Application container */
	protected $container;

	/**
	 * Input variables, passed on from the controller, in an associative array
	 * @var array
	 */
	protected $input = [];

	/**
	 * The model (base) name
	 *
	 * @var    string
	 */
	protected $name;

	/**
	 * The URL option for the component.
	 *
	 * @var    string
	 */
	protected $option = null;

	/**
	 * A state object
	 *
	 * @var    string
	 */
	protected $state;

	private $hash = null;

	/**
	 * Public class constructor
	 *
	 * @param   array       $config
	 * @param   AContainer  $container  Application container
	 */
	public function __construct($config = [], AContainer $container = null)
	{
		if (is_null($container))
		{
			$container = AApplication::getInstance()->getContainer();
		}

		$this->container = $container;

		// Get the input
		$this->input = $this->container->input;

		// Set the $name variable
		$component = $this->input->getCmd('option', 'com_foobar');

		if (array_key_exists('option', $config))
		{
			$component = $config['option'];
		}

		$name = strtolower($component);

		if (array_key_exists('name', $config))
		{
			$name = $config['name'];
		}
		$this->input->set('option', $component);
		$this->name   = $name;
		$this->option = $component;

		// Get the view name
		$className = get_class($this);

		if ($className == 'AModel')
		{
			if (array_key_exists('view', $config))
			{
				$view = $config['view'];
			}

			if (empty($view))
			{
				$view = $this->input->getCmd('view', 'cpanel');
			}
		}
		else
		{
			$eliminatePart = ucfirst($name) . 'Model';
			$view          = strtolower(str_replace($eliminatePart, '', $className));
		}

		// Set the model state
		if (array_key_exists('state', $config))
		{
			$this->state = $config['state'];
		}
		else
		{
			$this->state = new AObject;
		}

		// Set the internal state marker - used to ignore setting state from the request
		if (!empty($config['ignore_request']))
		{
			$this->_state_set = true;
		}
	}

	/**
	 * Returns a new model object. Unless overriden by the $config array, it will
	 * try to automatically populate its state from the request variables.
	 *
	 * @param   string      $type
	 * @param   string      $prefix
	 * @param   array       $config
	 * @param   AContainer  $container
	 *
	 * @return AModel
	 */
	public static function &getAnInstance($type, $prefix = '', $config = [], AContainer $container = null)
	{
		if (is_null($container))
		{
			$container = AApplication::getInstance()->getContainer();
		}

		$type          = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
		$types         = [ANGIE_INSTALLER_NAME . $type, $type];
		$modelClass    = '';
		$modelClassAlt = $prefix . ucfirst($type) . 'Default';

		// Guess the component name and include path
		if (!empty($prefix))
		{
			preg_match('/(.*)Model$/', $prefix, $m);
			$component = strtolower($m[1]);
		}
		else
		{
			$component = '';
		}

		if (empty($component))
		{
			$defaultApp = $container->application->getName();
			$component  = $container->input->get('option', $defaultApp);
		}

		$config['option'] = $component;

		$needsAView = true;

		if (array_key_exists('view', $config))
		{
			if (!empty($config['view']))
			{
				$needsAView = false;
			}
		}

		if ($needsAView)
		{
			$config['view'] = strtolower($type);
		}

		$container->input->set('option', $config['option']);
		$container->input->set('view', $config['view']);

		foreach ($types as $currentType)
		{
			$modelClass = $prefix . ucfirst($currentType);

			// Try to load the requested model class
			if (!class_exists($modelClass))
			{
				$include_paths = [
					APATH_INSTALLATION . '/' . $component . '/platform/models',
					APATH_INSTALLATION . '/platform/models',
					APATH_INSTALLATION . '/' . $component . '/models',
				];

				// Try to load the model file
				$path = AUtilsPath::find(
					$include_paths,
					self::createFileName('model', ['name' => $currentType])
				);

				if ($path)
				{
					require_once $path;
				}
			}

			if (class_exists($modelClass))
			{
				break;
			}
		}

		if (!class_exists($modelClass) && class_exists($modelClassAlt))
		{
			$modelClass = $modelClassAlt;
		}
		elseif (!class_exists($modelClass))
		{
			$modelClass = 'AModel';
		}

		$result = new $modelClass($config, $container);

		return $result;
	}

	/**
	 * Returns a new instance of a model, with the state reset to defaults
	 *
	 * @param   string      $type
	 * @param   string      $prefix
	 * @param   array       $config
	 * @param   AContainer  $container
	 *
	 * @return  AModel
	 */
	public static function &getTmpInstance($type, $prefix = '', $config = [], AContainer $container = null)
	{
		$ret = self::getAnInstance($type, $prefix, $config, $container)
			->getClone()
			->clearState()
			->clearInput()
			->savestate(0);

		return $ret;
	}

	/**
	 * Create the filename for a resource
	 *
	 * @param   string  $type   The resource type to create the filename for.
	 * @param   array   $parts  An associative array of filename information.
	 *
	 * @return  string  The filename
	 */
	protected static function createFileName($type, $parts = [])
	{
		$filename = '';

		switch ($type)
		{
			case 'model':
				$filename = strtolower($parts['name']) . '.php';
				break;

		}

		return $filename;
	}

	/**
	 * Magic caller; allows to use the name of model state keys as methods to
	 * set their values.
	 *
	 * @param   string  $name
	 * @param   mixed   $arguments
	 *
	 * @return $this
	 */
	public function __call($name, $arguments)
	{
		$arg1 = array_shift($arguments);
		$this->setState($name, $arg1);

		return $this;
	}

	/**
	 * Magic getter; allows to use the name of model state keys as properties
	 *
	 * @param   string  $name
	 *
	 * @return mixed
	 */
	public function __get($name)
	{
		return $this->getState($name);
	}

	/**
	 * Magic setter; allows to use the name of model state keys as properties
	 *
	 * @param   string  $name
	 *
	 * @return mixed
	 */
	public function __set($name, $value)
	{
		return $this->setState($name, $value);
	}

	/**
	 * Clears the input array.
	 *
	 * @return AModel
	 */
	public function clearInput()
	{
		$this->input = new AInput([]);

		return $this;
	}

	/**
	 * Clears the model state, but doesn't touch the internal lists of records,
	 * record tables or record id variables. To clear these values, please use
	 * reset().
	 *
	 * @return AModel
	 */
	public function clearState()
	{
		$this->state = new AObject();

		return $this;
	}

	/**
	 * Clones the model object and returns the clone
	 * @return AModel
	 */
	public function &getClone()
	{
		$clone = clone($this);

		return $clone;
	}

	public function getHash()
	{
		if (is_null($this->hash))
		{
			$defaultApp = $this->container->application->getName();
			$option     = $this->input->getCmd('option', $defaultApp);
			$view       = $this->input->getCmd('view', 'cpanel');
			$this->hash = "$option.$view.";
		}

		return $this->hash;
	}

	/**
	 * Method to get the model name
	 *
	 * The model name. By default parsed using the classname or it can be set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @return  string  The name of the model
	 */
	public function getName()
	{
		if (empty($this->name))
		{
			$r = null;
			if (!preg_match('/Model(.*)/i', get_class($this), $r))
			{
				JError::raiseError(500, AText::_('ANGI_APPLICATION_ERROR_MODEL_GET_NAME'));
			}
			$this->name = strtolower($r[1]);
		}

		return $this->name;
	}

	/**
	 * Get a filtered state variable
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @param   string  $filter_type
	 *
	 * @return mixed
	 */
	public function getState($key = null, $default = null, $filter_type = 'raw')
	{
		if (empty($key))
		{
			return $this->internal_getState();
		}

		// Get the savestate status
		$value = $this->internal_getState($key);

		if (is_null($value))
		{
			$value = $this->getUserStateFromRequest($key, $key, $value, 'none', $this->_savestate);

			if (is_null($value))
			{
				return $default;
			}
		}

		if (strtoupper($filter_type) == 'RAW')
		{
			return $value;
		}
		else
		{
			$filter = new AFilterInput();

			return $filter->clean($value, $filter_type);
		}
	}

	/**
	 * Method to set model state variables
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $value     The value of the property to set or null.
	 *
	 * @return  mixed  The previous value of the property or null if not set.
	 */
	public function setState($property, $value = null)
	{
		return $this->state->set($property, $value);
	}

	public function populateSavesate()
	{
		if (is_null($this->_savestate))
		{
			$savestate = $this->input->getInt('savestate', -999);

			if ($savestate == -999)
			{
				$savestate = true;
			}

			$this->savestate($savestate);
		}
	}

	/**
	 * Sets the model state auto-save status. By default the model is set up to
	 * save its state to the session.
	 *
	 * @param   bool  $newState  True to save the state, false to not save it.
	 *
	 * @return $this
	 */
	public function &savestate($newState)
	{
		$this->_savestate = $newState ? true : false;

		return $this;
	}

	/**
	 * Gets the value of a user state variable.
	 *
	 * @access    public
	 *
	 * @param   string  $key           The key of the user state variable.
	 * @param   string  $request       The name of the variable passed in a request.
	 * @param   string  $default       The default value for the variable if not found. Optional.
	 * @param   string  $type          Filter for the variable, for valid values see {@link JFilterInput::clean()}.
	 *                                 Optional.
	 * @param   bool    $setUserState  Should I save the variable in the user state? Default: true. Optional.
	 *
	 * @return    mixed   The request user state.
	 */
	protected function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $setUserState = true)
	{
		$session = $this->container->session;
		$hash    = $this->getHash();

		$old_state = $session->get($hash . $key, null);
		$cur_state = (!is_null($old_state)) ? $old_state : $default;
		$new_state = $this->input->get($request, null, $type);

		// Save the new value only if it was set in this request
		if ($setUserState)
		{
			if ($new_state !== null)
			{
				$session->set($hash . $key, $new_state);
			}
			else
			{
				$new_state = $cur_state;
			}
		}
		elseif (is_null($new_state))
		{
			$new_state = $cur_state;
		}

		return $new_state;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 */
	protected function populateState()
	{
	}

	/**
	 * Method to get model state variables
	 *
	 * @param   string  $property  Optional parameter name
	 * @param   mixed   $default   Optional default value
	 *
	 * @return  object  The property where specified, the state object where omitted
	 */
	private function internal_getState($property = null, $default = null)
	{
		if (!$this->_state_set)
		{
			// Protected method to auto-populate the model state.
			$this->populateState();

			// Set the model state set flag to true.
			$this->_state_set = true;
		}

		return $property === null ? $this->state : $this->state->get($property, $default);
	}
}
