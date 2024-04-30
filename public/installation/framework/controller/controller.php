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
class AController
{
	/**
	 * The base path of the controller
	 *
	 * @var    string
	 */
	protected $basePath;

	/**
	 * The mapped task that was performed.
	 *
	 * @var    string
	 */
	protected $doTask;

	/**
	 * Redirect message.
	 *
	 * @var    string
	 */
	protected $message;

	/**
	 * Redirect message type.
	 *
	 * @var    string
	 */
	protected $messageType;

	/**
	 * Array of class methods
	 *
	 * @var    array
	 */
	protected $methods;

	/**
	 * The name of the controller
	 *
	 * @var    array
	 */
	protected $name;

	/**
	 * The prefix of the models
	 *
	 * @var    string
	 */
	protected $model_prefix;

	/**
	 * The set of search directories for resources (views).
	 *
	 * @var    array
	 */
	protected $paths;

	/**
	 * URL for redirection.
	 *
	 * @var    string
	 */
	protected $redirect;

	/**
	 * Current or most recently performed task.
	 *
	 * @var    string
	 */
	protected $task;

	/**
	 * Array of class methods to call for a given task.
	 *
	 * @var    array
	 */
	protected $taskMap;

	/**
	 * Hold an AInput object for easier access to the input variables.
	 *
	 * @var    AInput
	 */
	protected $input;

	/**
	 * Instance container.
	 *
	 * @var    AController
	 */
	protected static $instance;

	/**
	 * The current view name; you can override it in the configuration
	 *
	 * @var string
	 */
	protected $view = '';

	/**
	 * The current component's name; you can override it in the configuration
	 *
	 * @var string
	 */
	protected $component = '';

	/**
	 * The current layout; you can override it in the configuration
	 *
	 * @var string
	 */
	protected $layout = null;

	/**
	 * A cached copy of the class configuration parameter passed during initialisation
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * Overrides the name of the view's default model
	 *
	 * @var string
	 */
	protected $modelName = null;

	/**
	 * Overrides the name of the view's default view
	 *
	 * @var string
	 */
	protected $viewName = null;

    /** @var \AContainer Application container */
    protected $container;

	/**
	 * A copy of the AView object used in this triad
	 *
	 * @var AView
	 */
	private $viewObject = null;

	/**
	 * A copy of the AModel object used in this triad
	 *
	 * @var AModel
	 */
	private $modelObject = null;

	/**
	 * Gets a static (Singleton) instance of a controller class. It loads the
	 * relevant controller file from the component's directory or, if it doesn't
	 * exist, creates a new controller object out of thin air.
	 *
	 * @param   string  $option  Component name, e.g. com_foobar
	 * @param   string  $view    The view name, also used for the controller name
	 * @param   array   $config  Configuration parameters
     * @param   AContainer $container   Application container
	 *
	 * @return  AController
	 */
	public static function &getAnInstance($option = null, $view = null, $config = array(), AContainer $container = null)
	{
		static $instances = array();

		$hash = $option . $view;
		if (!array_key_exists($hash, $instances))
		{
            if(is_null($container))
            {
                $container = AApplication::getInstance()->getContainer();
            }

			$instances[$hash] = self::getTmpInstance($option, $view, $config, $container);
		}

		return $instances[$hash];
	}

	/**
	 * Gets a temporary instance of a controller object. A temporary instance is
	 * not a Singleton and can be disposed of after use.
	 *
	 * @param   string  $option  The component name, e.g. com_foobar
	 * @param   string  $view    The view name, e.g. cpanel
	 * @param   array   $config  Configuration parameters
     * @param   AContainer $container   Application container
	 *
	 * @return  AController  A disposable class instance
	 */
	public static function &getTmpInstance($option = null, $view = null, $config = array(), AContainer $container = null)
	{
        if(is_null($container))
        {
            $container = AApplication::getInstance()->getContainer();
        }

		// Get the Input
        $input = $container->input;

		// Determine the option (component name) and view
		$defaultApp = $container->application->getName();

		if (!is_null($option))
		{
			$config['option'] = $option;
		}
		else
		{
			$config['option'] = $input->getCmd('option', $defaultApp);
		}
		if (!is_null($view))
		{
			$config['view'] = $view;
		}
		else
		{
			$config['view'] = $input->getCmd('view','');
		}

		// Get the class base name, e.g. FoobarController
		$classBaseName = ucfirst($config['option']) . 'Controller';
		// Get the class name suffixes, in the order to be searched for
		$classSuffixes = array(
            ucfirst(ANGIE_INSTALLER_NAME).ucfirst($config['view']),
			$config['view'],
			'default'
		);

		// Initialise the base path for performance reasons
		$basePath = APATH_INSTALLATION;
		// Look for the best classname match
		foreach ($classSuffixes as $suffix)
		{
			$className = $classBaseName . ucfirst($suffix);

			if (class_exists($className))
			{
				break;
			}
			elseif (class_exists($className . 'Default'))
			{
				// The default class was loaded successfully. We have a match!
				$className = $className . 'Default';

				break;
			}

			// The class is not already loaded. Try to find and load it.
			$searchPaths = array(
				$basePath . '/' . $config['option'] . '/platform/controllers',
				$basePath . '/platform/controllers',
				$basePath . '/' . $config['option'] . '/controllers',
			);

			// If we have a searchpath in the configuration please search it first
			if (array_key_exists('searchpath', $config))
			{
				array_unshift($searchPaths, $config['searchpath']);
			}

			// Try to find the path to this file
			$path = AUtilsPath::find(
				$searchPaths,
				strtolower($suffix) . '.php'
			);

			// The path is found. Load the file and make sure the expected class name exists.
			if ($path)
			{
				require_once $path;

				if (class_exists($className))
				{
					break;
				}
				elseif (class_exists($className . 'Default'))
				{
					// The default class was loaded successfully. We have a match!
					$className = $className . 'Default';

					break;
				}
			}
		}

		if (!class_exists($className))
		{
			// If no specialised class is found, instantiate the generic FOFController
			$className = 'AController';
		}

		$instance = new $className($config, $container);

		return $instance;
	}

	/**
	 * Public constructor of the Controller class
	 *
	 * @param   array  $config  Optional configuration parameters
     * @param   AContainer  $container  Application container
	 */
	public function __construct($config = array(), AContainer $container = null)
	{
        if(is_null($container))
        {
            $container = AApplication::getInstance()->getContainer();
        }

        $this->container = $container;

		// Initialise
		$this->methods = array();
		$this->message = null;
		$this->messageType = 'message';
		$this->paths = array();
		$this->redirect = null;
		$this->taskMap = array();

		// Get the input
		$this->input = $this->container->input;

		// Determine the methods to exclude from the base class.
		$xMethods = get_class_methods('AController');

		// Get the public methods in this class using reflection.
		$r = new ReflectionClass($this);
		$rMethods = $r->getMethods(ReflectionMethod::IS_PUBLIC);

		foreach ($rMethods as $rMethod)
		{
			$mName = $rMethod->getName();

			// Add default display method if not explicitly declared.
			if (!in_array($mName, $xMethods) || $mName == 'display' || $mName == 'main')
			{
				$this->methods[] = strtolower($mName);

				// Auto register the methods as tasks.
				$this->taskMap[strtolower($mName)] = $mName;
			}
		}

		// Get the default values for the component and view names
		$defaultApp = $this->container->application->getName();
		$this->component = $this->input->get('option',	$defaultApp,	'cmd');
		$this->view      = $this->input->get('view',	'cpanel',		'cmd');
		$this->layout    = $this->input->get('layout',	null,			'cmd');

		// Overrides from the config
		if (array_key_exists('option', $config))
		{
			$this->component = $config['option'];
		}

		if (array_key_exists('view', $config))
		{
			$this->view      = $config['view'];
		}

		if (array_key_exists('layout', $config))
		{
			$this->layout    = $config['layout'];
		}

		$this->input->set('option', $this->component);

		// Set the $name variable
		$this->name = $this->component;

		// Set the basePath variable
		$basePath = APATH_INSTALLATION . '/'.$this->component;
		if (array_key_exists('base_path', $config))
		{
			$basePath = $config['base_path'];
		}
		$this->basePath = $basePath;

		// If the default task is set, register it as such
		if (array_key_exists('default_task', $config))
		{
			$this->registerDefaultTask($config['default_task']);
		}
		else
		{
			$this->registerDefaultTask('main');
		}

		// Set the models prefix
		if (empty($this->model_prefix))
		{
			if (array_key_exists('model_prefix', $config))
			{
				// User-defined prefix
				$this->model_prefix = $config['model_prefix'];
			}
			else
			{
				$this->model_prefix = ucfirst($this->name) . 'Model';
			}
		}

		// Set the default view search path
		if (array_key_exists('view_path', $config))
		{
			// User-defined dirs
			$this->setPath('view', $config['view_path']);
		}
		else
		{
			$this->setPath('view', $this->basePath . '/views');
		}

		// Set the default view.
		if (array_key_exists('default_view', $config))
		{
			$this->default_view = $config['default_view'];
		}
		elseif (empty($this->default_view))
		{
			$this->default_view = $this->getName();
		}

		// Cache the config
		$this->config = $config;

		// Set any model/view name overrides
		if (array_key_exists('viewName', $config))
		{
			$this->setThisViewName($config['viewName']);
		}

		if (array_key_exists('modelName', $config))
		{
			$this->setThisModelName($config['modelName']);
		}
	}

    /**
     * Executes a given controller task. The onBefore<task> and onAfter<task>
     * methods are called automatically if they exist.
     *
     * @param   string $task The task to execute, e.g. "browse"
     *
     * @return  bool|null False on execution failure
     *
     * @throws \Exception
     */
	public function execute($task)
	{
		$this->task = $task;

		$task = strtolower($task);
		if (isset($this->taskMap[$task]))
		{
			$doTask = $this->taskMap[$task];
		}
		elseif (isset($this->taskMap['__default']))
		{
			$doTask = $this->taskMap['__default'];
		}
		else
		{
			throw new Exception(AText::sprintf('ANGI_APPLICATION_ERROR_TASK_NOT_FOUND', $task), 404);
		}

		$method_name = 'onBefore' . ucfirst($task);
		if (method_exists($this, $method_name))
		{
			$result = $this->$method_name();
			if (!$result)
			{
				return false;
			}
		}

		// Do not allow the display task to be directly called
		$task = strtolower($task);
		if (isset($this->taskMap[$task]))
		{
			$doTask = $this->taskMap[$task];
		}
		elseif (isset($this->taskMap['__default']))
		{
			$doTask = $this->taskMap['__default'];
		}
		else
		{
			$doTask = null;
		}

		// Record the actual task being fired
		$this->doTask = $doTask;

		$ret = $this->$doTask();

		$method_name = 'onAfter' . ucfirst($task);
		if (method_exists($this, $method_name))
		{
			$result = $this->$method_name();
			if (!$result)
			{
				return false;
			}
		}

		return $ret;
	}

	/**
	 * Default task. Assigns a model to the view and asks the view to render
	 * itself.
	 */
	public function display()
	{
		$viewType	= $this->input->getCmd('format', 'html');

		$view = $this->getThisView();
		$view->task = $this->task;
		$view->doTask = $this->doTask;

		// Get/Create the model
		if ($model = $this->getThisModel())
		{
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		// Set the layout
		$view->setLayout(is_null($this->layout) ? 'default' : $this->layout);

		// Display the view
		$view->display();
	}

	public function main()
	{
		$this->display();
	}

    /**
     * Returns the default model associated with the current view
     *
     * @param array $config
     *
     * @return AModel The global instance of the model (singleton)
     */
	public final function getThisModel($config = array())
	{
		if (!is_object($this->modelObject))
		{
			$prefix = ucfirst($this->component).'Model';

			if (!empty($this->modelName))
			{
				$modelName = ucfirst($this->modelName);
			}
			else
			{
				$modelName = ucfirst($this->view);
			}

			$this->modelObject = $this->getModel($modelName, $prefix, $config);
		}

		return $this->modelObject;
	}

    /**
     * Returns current view object
     * @param array $config
     *
     * @return \FOFView The global instance of the view object (singleton)
     *
     * @throws \Exception
     */
	public final function getThisView($config = array())
	{
		if (!is_object($this->viewObject))
		{
			$prefix   = null;
			$viewName = null;
			$viewType = null;

			$prefix = ucfirst($this->component).'View';

			if(!empty($this->viewName))
            {
				$viewName = ucfirst($this->viewName);
			}
            else
            {
				$viewName = ucfirst($this->view);
			}

			$viewType = $this->container->input->getCmd('format', 'html');

			$this->container->input->set('base_path', $this->basePath);

			$this->viewObject = $this->getView( $viewName, $viewType, $prefix, $config);
		}

		return $this->viewObject;
	}

	protected function createModel($name, $prefix = '', $config = array())
	{
		$result = null;

		// Clean the model name
		$modelName	 = preg_replace( '/[^A-Z0-9_]/i', '', $name );
		$classPrefix = preg_replace( '/[^A-Z0-9_]/i', '', $prefix );

		$result = AModel::getAnInstance($modelName, $classPrefix, $config, $this->container );

		return $result;
	}

	/**
	 * Creates a View object instance and returns it
	 *
	 * @param   string  $name    The name of the view, e.g. Items
	 * @param   string  $prefix  The prefix of the view, e.g. FoobarView
	 * @param   string  $type    The type of the view, usually one of Html, Raw, Json or Csv
	 * @param   array   $config  The configuration variables to use for creating the view
	 *
	 * @return  FOFView
	 */
	protected function createView($name, $prefix = '', $type = '', $config = array())
	{
		$result = null;

		// Clean the view name
		$viewName	 = preg_replace( '/[^A-Z0-9_]/i', '', $name );
		$classPrefix = preg_replace( '/[^A-Z0-9_]/i', '', $prefix );
		$viewType	 = preg_replace( '/[^A-Z0-9_]/i', '', $type );

		// Guess the component name and view
		if (!empty($prefix))
		{
			preg_match('/(.*)View$/', $prefix, $m);
			$component = strtolower($m[1]);
		}
		else
		{
			$component = '';
		}

		if (empty($component))
		{
			$component = $this->container->input->get('option', $component, 'cmd');
		}

		if (array_key_exists('option', $config))
		{
			if($config['option'])
			{
				$component = $config['option'];
			}
		}
		$config['option'] = $component;

		$view = strtolower($viewName);

		if (empty($view))
		{
			$view = $this->container->input->get('view', $view, 'cmd');
		}

		if (array_key_exists('view', $config))
		{
			if ($config['view'])
			{
				$view = $config['view'];
			}
		}

		$config['view'] = $view;

        $this->container->input->set('option', $config['option']);
        $this->container->input->set('view', $config['view']);

		// Get the base paths where the view class files are expected to live
		$basePaths = array(
			APATH_INSTALLATION . '/' . $config['option'] . '/platform/views',
			APATH_INSTALLATION . '/platform/views',
			APATH_INSTALLATION . '/' . $config['option'] . '/views',
		);
		$basePaths = array_merge($basePaths, $this->paths['view']);

		$suffixes = array(
			$viewName,
			'default'
		);

		foreach ($suffixes as $suffix)
		{
			// Build the view class name
			$viewClass = $classPrefix . ucfirst($suffix);

			if (class_exists($viewClass))
			{
				// The class is already loaded
				break;
			}
			elseif (class_exists($viewClass . 'Default'))
			{
				// The default class was loaded successfully. We have a match!
				$viewClass = $viewClass . 'Default';

				break;
			}

			// The class is not loaded. Let's load it!
			$viewPath = $this->createFileName( 'view', array( 'name' => $suffix, 'type' => $viewType) );
			$path = AUtilsPath::find($basePaths, $viewPath);
			if ($path)
			{
				require_once $path;
			}

			if (class_exists($viewClass))
			{
				// The class is already loaded
				break;
			}
			elseif (class_exists($viewClass . 'Default'))
			{
				// The default class was loaded successfully. We have a match!
				$viewClass = $viewClass . 'Default';

				break;
			}
		}

		if(!class_exists($viewClass))
        {
			//$viewClass = 'AView'.ucfirst($type);
			$viewClass = 'AView';
		}

		// Setup View configuration options
		$basePath = APATH_INSTALLATION;

		if(!array_key_exists('template_path', $config))
        {
			$config['template_path'] = array(
				$basePath . '/' . $config['option'] . '/platform/views/' . $config['view'] . '/tmpl',
				$basePath . '/platform/views/' . $config['view'] . '/tmpl',
				$basePath . '/' . $config['option'] . '/views/' . $config['view'] . '/tmpl',
			);
		}

		if(!array_key_exists('helper_path', $config))
        {
			$config['helper_path'] = array(
				$basePath . '/' . $config['option'] . '/platform/helpers',
				$basePath . '/platform/helpers',
				$basePath . '/' . $config['option'] . '/helpers',
			);
		}

		$result = new $viewClass($config, $this->container);

		return $result;
	}

	/**
	 * Set the name of the view to be used by this Controller
	 *
	 * @param   string  $viewName  The name of the view
	 */
	public function setThisViewName($viewName)
	{
		$this->viewName = $viewName;
	}

	/**
	 * Set the name of the model to be used by this Controller
	 *
	 * @param   string  $modelName  The name of the model
	 */
	public function setThisModelName($modelName)
	{
		$this->modelName = $modelName;
	}

	/**
	 * Create the filename for a resource.
	 *
	 * @param   string  $type   The resource type to create the filename for.
	 * @param   array   $parts  An associative array of filename information. Optional.
	 *
	 * @return  string  The filename.
	 */
	protected static function createFileName($type, $parts = array())
	{
		$filename = '';

		switch ($type)
		{
			case 'controller':
				if (!empty($parts['format']))
				{
					if ($parts['format'] == 'html')
					{
						$parts['format'] = '';
					}
					else
					{
						$parts['format'] = '.' . $parts['format'];
					}
				}
				else
				{
					$parts['format'] = '';
				}

				$filename = strtolower($parts['name'] . $parts['format'] . '.php');
				break;

			case 'view':
				if (!empty($parts['type']))
				{
					$parts['type'] = '.' . $parts['type'];
				}
				else
				{
					$parts['type'] = '';
				}

				$filename = strtolower($parts['name'] . '/view' . $parts['type'] . '.php');
				break;
		}

		return $filename;
	}

	/**
	 * Adds to the search path for templates and resources.
	 *
	 * @param   string  $type  The path type (e.g. 'model', 'view').
	 * @param   mixed   $path  The directory string  or stream array to search.
	 *
	 * @return  AController  A JControllerLegacy object to support chaining.
	 */
	protected function addPath($type, $path)
	{
		// Just force path to array
		settype($path, 'array');

		if (!isset($this->paths[$type]))
		{
			$this->paths[$type] = array();
		}

		// Loop through the path directories
		foreach ($path as $dir)
		{
			// No surrounding spaces allowed!
			$dir = rtrim(AUtilsPath::check($dir, '/'), '/') . '/';

			// Add to the top of the search dirs
			array_unshift($this->paths[$type], $dir);
		}

		return $this;
	}

	/**
	 * Add one or more view paths to the controller's stack, in LIFO order.
	 *
	 * @param   mixed  $path  The directory (string) or list of directories (array) to add.
	 *
	 * @return  AController  This object to support chaining.
	 */
	public function addViewPath($path)
	{
		$this->addPath('view', $path);

		return $this;
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  AModel  The model.
	 */
	public function getModel($name = '', $prefix = '', $config = array())
	{
		if (empty($name))
		{
			$name = $this->getName();
		}

		if (empty($prefix))
		{
			$prefix = $this->model_prefix;
		}

		if ($model = $this->createModel($name, $prefix, $config))
		{
			// Task is a reserved state
			$model->setState('task', $this->task);
		}
		return $model;
	}

	/**
	 * Method to get the controller name
	 *
	 * The controller name is set by default parsed using the classname, or it can be set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @return  string  The name of the controller
	 */
	public function getName()
	{
		if (empty($this->name))
		{
			$r = null;

			if (!preg_match('/(.*)Controller/i', get_class($this), $r))
			{
				throw new Exception(AText::_('ANGI_APPLICATION_ERROR_CONTROLLER_GET_NAME'), 500);
			}

			$this->name = strtolower($r[1]);
		}

		return $this->name;
	}

	/**
	 * Get the last task that is being performed or was most recently performed.
	 *
	 * @return  string  The task that is being performed or was most recently performed.
	 */
	public function getTask()
	{
		return $this->task;
	}

	/**
	 * Gets the available tasks in the controller.
	 *
	 * @return  array  Array[i] of task names.
	 */
	public function getTasks()
	{
		return $this->methods;
	}

    /**
     * Method to get a reference to the current view and load it if necessary.
     *
     * @param   string  $name       The view name. Optional, defaults to the controller name.
     * @param   string  $type       The view type. Optional.
     * @param   string  $prefix     The class prefix. Optional.
     * @param   array   $config     Configuration array for view. Optional.
     *
     * @return \AView Reference to the view or an error.
     *
     * @throws \Exception
     */
	public function getView($name = '', $type = '', $prefix = '', $config = array())
	{
		static $views;

		if (!isset($views))
		{
			$views = array();
		}

		if (empty($name))
		{
			$name = $this->getName();
		}

		if (empty($prefix))
		{
			$prefix = $this->getName() . 'View';
		}

		if (empty($views[$name]))
		{
			if ($view = $this->createView($name, $prefix, $type, $config))
			{
				$views[$name] = & $view;
			}
			else
			{
				throw new Exception(AText::sprintf('ANGI_APPLICATION_ERROR_VIEW_NOT_FOUND', $name, $type, $prefix), 500);
			}
		}

		return $views[$name];
	}

	/**
	 * Redirects the browser or returns false if no redirect is set.
	 *
	 * @return  boolean  False if no redirect exists.
	 *
	 * @since   12.2
	 */
	public function redirect()
	{
		if ($this->redirect)
		{
			$app = $this->container->application;
			$app->redirect($this->redirect, $this->message, $this->messageType);
		}

		return false;
	}

	/**
	 * Register the default task to perform if a mapping is not found.
	 *
	 * @param   string  $method  The name of the method in the derived class to perform if a named task is not found.
	 *
	 * @return  AController  A JControllerLegacy object to support chaining.
	 */
	public function registerDefaultTask($method)
	{
		$this->registerTask('__default', $method);

		return $this;
	}

	/**
	 * Register (map) a task to a method in the class.
	 *
	 * @param   string  $task    The task.
	 * @param   string  $method  The name of the method in the derived class to perform for this task.
	 *
	 * @return  AController  A JControllerLegacy object to support chaining.
	 */
	public function registerTask($task, $method)
	{
		if (in_array(strtolower($method), $this->methods))
		{
			$this->taskMap[strtolower($task)] = $method;
		}

		return $this;
	}

	/**
	 * Unregister (unmap) a task in the class.
	 *
	 * @param   string  $task  The task.
	 *
	 * @return  AController  This object to support chaining.
	 */
	public function unregisterTask($task)
	{
		unset($this->taskMap[strtolower($task)]);

		return $this;
	}

	/**
	 * Sets the internal message that is passed with a redirect
	 *
	 * @param   string  $text  Message to display on redirect.
	 * @param   string  $type  Message type. Optional, defaults to 'message'.
	 *
	 * @return  string  Previous message
	 */
	public function setMessage($text, $type = 'message')
	{
		$previous = $this->message;
		$this->message = $text;
		$this->messageType = $type;

		return $previous;
	}

	/**
	 * Sets an entire array of search paths for resources.
	 *
	 * @param   string  $type  The type of path to set, typically 'view' or 'model'.
	 * @param   string  $path  The new set of search paths. If null or false, resets to the current directory only.
	 *
	 * @return  void
	 */
	protected function setPath($type, $path)
	{
		// Clear out the prior search dirs
		$this->paths[$type] = array();

		// Actually add the user-specified directories
		$this->addPath($type, $path);
	}

	/**
	 * Set a URL for browser redirection.
	 *
	 * @param   string  $url   URL to redirect to.
	 * @param   string  $msg   Message to display on redirect. Optional, defaults to value set internally by controller, if any.
	 * @param   string  $type  Message type. Optional, defaults to 'message' or the type set by a previous call to setMessage.
	 *
	 * @return  AController   This object to support chaining.
	 */
	public function setRedirect($url, $msg = null, $type = null)
	{
		$this->redirect = $url;

		if ($msg !== null)
		{
			// Controller may have set this directly
			$this->message = $msg;
		}

		// Ensure the type is not overwritten by a previous call to setMessage.
		if (empty($type))
		{
			if (empty($this->messageType))
			{
				$this->messageType = 'info';
			}
		}
		// If the type is explicitly set, set it.
		else
		{
			$this->messageType = $type;
		}

		return $this;
	}
}
