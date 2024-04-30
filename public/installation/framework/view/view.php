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
class AView
{
	/**
	 * The name of the view
	 *
	 * @var    array
	 */
	protected $_name = null;

	/**
	 * Registered models
	 *
	 * @var    array
	 */
	protected $_models = array();

	/**
	 * The base path of the view
	 *
	 * @var    string
	 */
	protected $_basePath = null;

	/**
	 * The default model
	 *
	 * @var	string
	 */
	protected $_defaultModel = null;

	/**
	 * Layout name
	 *
	 * @var    string
	 */
	protected $_layout = 'default';

	/**
	 * Layout extension
	 *
	 * @var    string
	 */
	protected $_layoutExt = 'php';

	/**
	 * Layout template
	 *
	 * @var    string
	 */
	protected $_layoutTemplate = '_';

	/**
	 * The set of search directories for resources (templates)
	 *
	 * @var array
	 */
	protected $_path = array('template' => array(), 'helper' => array());

	/**
	 * The name of the default template source file.
	 *
	 * @var string
	 */
	protected $_template = null;

	/**
	 * The output of the template script.
	 *
	 * @var string
	 */
	protected $_output = null;

	/**
	 * A cached copy of the configuration
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * The input object
	 *
	 * @var AInput
	 */
	protected $input = null;

    /** @var  AContainer Application container */
    public $container;

    /**
     * Constructor
     *
     * @param   array $config A named configuration array for object construction.<br/>
     *                          name: the name (optional) of the view (defaults to the view class name suffix).<br/>
     *                          escape: the name (optional) of the function to use for escaping strings<br/>
     *                          base_path: the parent path (optional) of the views directory (defaults to the component folder)<br/>
     *                          template_plath: the path (optional) of the layout directory (defaults to base_path + /views/ + view name<br/>
     *                          helper_path: the path (optional) of the helper files (defaults to base_path + /helpers/)<br/>
     *                          layout: the layout (optional) to use to display the view<br/>
     *
     * @param   \AContainer $container
     *
     * @throws \AExceptionApp
     * @throws \Exception
     */
	public function __construct($config = array(), AContainer $container = null)
	{
        if(is_null($container))
        {
            $container = AApplication::getInstance()->getContainer();
        }

        $this->container = $container;
        $this->input     = $this->container->input;

		// Get the component name
		if (array_key_exists('option', $config))
		{
			if ($config['option'])
			{
				$component = $config['option'];
			}
		}

		$config['option'] = $component;

		// Get the view name
		$view = $this->input->getCmd('view', '');

		if (array_key_exists('view', $config))
		{
			if ($config['view'])
			{
				$view = $config['view'];
			}
		}
		$config['view'] = $view;

		// Set the component and the view to the input array
		$this->input->set('option', $config['option']);
		$this->input->set('view', $config['view']);

		// Set the view name
		if (array_key_exists('name', $config))
		{
			$this->_name = $config['name'];
		}
		else
		{
			$this->_name = $config['view'];
		}
		$this->input->set('view', $this->_name);
		$config['input'] = $this->input;
		$config['name'] = $this->_name;
		$config['view'] = $this->_name;

		// Set a base path for use by the view
		if (array_key_exists('base_path', $config))
		{
			$this->_basePath	= $config['base_path'];
		}
		else
		{
			$this->_basePath	= APATH_INSTALLATION . '/' . $config['option'];
		}

		// Set the default template search path
		if (array_key_exists('template_path', $config))
		{
			// User-defined dirs
			$this->_setPath('template', $config['template_path']);
		}
		else
		{
			$this->_setPath('template', $this->_basePath . '/views/' . $this->getName() . '/tmpl');
		}

		// Set the default helper search path
		if (array_key_exists('helper_path', $config))
		{
			// User-defined dirs
			$this->_setPath('helper', $config['helper_path']);
		}
		else
		{
			$this->_setPath('helper', $this->_basePath . '/helpers');
		}

		// Set the layout
		if (array_key_exists('layout', $config))
		{
			$this->setLayout($config['layout']);
		}
		else
		{
			$this->setLayout('default');
		}

		$this->config = $config;

		$app = $this->container->application;
		$component = preg_replace('/[^A-Z0-9_\.-]/i', '', $component);
		$fallback = APATH_THEMES . '/' . $app->getTemplate() . '/html/' . $component . '/' . $this->getName();
		$this->_addPath('template', $fallback);

		$this->baseurl = AUri::base(true);
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * Method to get data from a registered model or a property of the view
	 *
	 * @param   string  $property  The name of the method to call on the model or the property to get
	 * @param   string  $default   The name of the model to reference or the default value [optional]
	 *
	 * @return  mixed  The return value of the method
	 */
	public function get($property, $default = null)
	{
		// If $model is null we use the default model
		if (is_null($default))
		{
			$model = $this->_defaultModel;
		}
		else
		{
			$model = strtolower($default);
		}

		// First check to make sure the model requested exists
		if (isset($this->_models[$model]))
		{
			// Model exists, let's build the method name
			$method = 'get' . ucfirst($property);

			// Does the method exist?
			if (method_exists($this->_models[$model], $method))
			{
				// The method exists, let's call it and return what we get
				$result = $this->_models[$model]->$method();

				return $result;
			}

		}

		return $default;
	}

	/**
	 * Method to get the model object
	 *
	 * @param   string  $name  The name of the model (optional)
	 *
	 * @return  mixed  AModel object
	 */
	public function getModel($name = null)
	{
		if ($name === null)
		{
			$name = $this->_defaultModel;
		}
		return $this->_models[strtolower($name)];
	}

	/**
	 * Get the layout.
	 *
	 * @return  string  The layout name
	 */
	public function getLayout()
	{
		return $this->_layout;
	}

	/**
	 * Get the layout template.
	 *
	 * @return  string  The layout template name
	 */
	public function getLayoutTemplate()
	{
		return $this->_layoutTemplate;
	}

	/**
	 * Method to get the view name
	 *
	 * The model name by default parsed using the classname, or it can be set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @return  string  The name of the model
	 *
	 * @throws  Exception
	 */
	public function getName()
	{
		if (empty($this->_name))
		{
			$classname = get_class($this);
			$viewpos = strpos($classname, 'View');

			if ($viewpos === false)
			{
				throw new Exception(AText::_('ANGI_APPLICATION_ERROR_VIEW_GET_NAME'), 500);
			}

			$this->_name = strtolower(substr($classname, $viewpos + 4));
		}

		return $this->_name;
	}

	/**
	 * Method to add a model to the view.  We support a multiple model single
	 * view system by which models are referenced by classname.
	 *
	 * @param   AModel  $model    The model to add to the view.
	 * @param   boolean       $default  Is this the default model?
	 *
	 * @return  object   The added model.
	 */
	public function setModel($model, $default = false)
	{
		$name = strtolower($model->getName());
		$this->_models[$name] = $model;

		if ($default)
		{
			$this->_defaultModel = $name;
		}
		return $model;
	}

	/**
	 * Sets the layout name to use
	 *
	 * @param   string  $layout  The layout name or a string in format <template>:<layout file>
	 *
	 * @return  string  Previous value.
	 *
	 * @since   12.2
	 */
	public function setLayout($layout)
	{
		$previous = $this->_layout;
		if (strpos($layout, ':') === false)
		{
			$this->_layout = $layout;
		}
		else
		{
			// Convert parameter to array based on :
			$temp = explode(':', $layout);
			$this->_layout = $temp[1];

			// Set layout template
			$this->_layoutTemplate = $temp[0];
		}

		return $previous;
	}

	/**
	 * Allows a different extension for the layout files to be used
	 *
	 * @param   string  $value  The extension.
	 *
	 * @return  string   Previous value
	 *
	 * @since   12.2
	 */
	public function setLayoutExt($value)
	{
		$previous = $this->_layoutExt;
		if ($value = preg_replace('#[^A-Za-z0-9]#', '', trim($value)))
		{
			$this->_layoutExt = $value;
		}

		return $previous;
	}

	/**
	 * Adds to the stack of view script paths in LIFO order.
	 *
	 * @param   mixed  $path  A directory path or an array of paths.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function addTemplatePath($path)
	{
		$this->_addPath('template', $path);
	}

	/**
	 * Adds to the stack of helper script paths in LIFO order.
	 *
	 * @param   mixed  $path  A directory path or an array of paths.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function addHelperPath($path)
	{
		$this->_addPath('helper', $path);
	}

    /**
     * Loads a template given any path. The path is in the format:
     * viewname/templatename
     *
     * @param   string  $path
     * @param   array   $forceParams A hash array of variables to be extracted in the local scope of the template file
     *
     * @return \Exception|string
     */
	public function loadAnyTemplate($path = '', $forceParams = array())
	{
		$template = $this->container->application->getTemplate();
		$layoutTemplate = $this->getLayoutTemplate();

		// Parse the path
		$templateParts = $this->_parseTemplatePath($path);

		// Get the default paths
		$paths = array();
		$paths[] =  APATH_THEMES . '/' . $template . '/html/' . $this->input->getCmd('option', 'angie') . '/' . $templateParts['view'];
		$paths[] =  APATH_INSTALLATION . '/' . $this->input->getCmd('option', 'angie') . '/platform/views/' . $templateParts['view'] . '/tmpl';
		$paths[] =  APATH_INSTALLATION . '/platform/views/' . $templateParts['view'] . '/tmpl';
		$paths[] =  APATH_INSTALLATION . '/' . $this->input->getCmd('option', 'angie') . '/views/' . $templateParts['view'] . '/tmpl';

		if (isset($this->_path) || property_exists($this, '_path'))
		{
			$paths = array_merge($paths, $this->_path['template']);
		}
		elseif (isset($this->path) || property_exists($this, 'path'))
		{
			$paths = array_merge($paths, $this->path['template']);
		}

		// Look for a template override
		if (isset($layoutTemplate) && $layoutTemplate != '_' && $layoutTemplate != $template)
		{
			$apath = array_shift($paths);
			array_unshift($paths, str_replace($template, $layoutTemplate, $apath));
		}

		$filetofind = $templateParts['template'].'.php';
		$this->_tempFilePath = AUtilsPath::find($paths, $filetofind);

		if($this->_tempFilePath)
        {
			// Unset from local scope
			unset($template); unset($layoutTemplate); unset($paths); unset($path);
			unset($filetofind);

			// Never allow a 'this' property
			if (isset($this->this))
			{
				unset($this->this);
			}

			// Force parameters into scope
			if(!empty($forceParams))
			{
				extract($forceParams);
			}

			// Start capturing output into a buffer
			ob_start();
			// Include the requested template filename in the local scope
			// (this will execute the view logic).
			include $this->_tempFilePath;

			// Done with the requested template; get the buffer and
			// clear it.
			$this->_output = ob_get_contents();
			ob_end_clean();

			return $this->_output;
		}
		else
		{
			return new Exception(AText::sprintf('ANGI_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $path), 500);
		}
	}

    /**
     * Overrides the default method to execute and display a template script.
     * Instead of loadTemplate is uses loadAnyTemplate.
     *
     * @param   string $tpl The name of the template file to parse
     *
     * @return  mixed A string if successful, otherwise an exception.
     *
     * @throws
     */
	public function display($tpl = null)
	{
		$method = 'onBefore' . ucfirst($this->doTask);

		if(method_exists($this, $method))
		{
			$result = $this->$method();

			if (!$result)
			{
				return false;
			}
		}

		$result = $this->loadTemplate($tpl);
		$method = 'onAfter' . ucfirst($this->doTask);

		if(method_exists($this, $method))
		{
			$result = $this->$method();
			if (!$result)
			{
				return false;
			}
		}

		if(is_object($result) && ($result instanceof Exception))
		{
			throw $result;
		}
		else
		{
			echo $result;
		}
	}

	/**
	 * Our function uses loadAnyTemplate to provide smarter view template loading.
	 *
	 * @param   string   $tpl     The name of the template file to parse
	 * @param   boolean  $strict  Should we use strict naming, i.e. force a non-empty $tpl?
	 *
	 * @return  mixed  A string if successful, otherwise an Exception
	 */
	public function loadTemplate($tpl = null, $strict = false)
    {
		$basePath = $this->config['view'].'/';

		if ($strict)
		{
			$paths = array(
				$basePath.$this->getLayout().($tpl ? "_$tpl" : ''),
				$basePath.'default'.($tpl ? "_$tpl" : ''),
			);
		}
		else
		{
			$paths = array(
				$basePath.$this->getLayout().($tpl ? "_$tpl" : ''),
				$basePath.$this->getLayout(),
				$basePath.'default'.($tpl ? "_$tpl" : ''),
				$basePath.'default',
			);
		}

		foreach($paths as $path)
        {
			$result = $this->loadAnyTemplate($path);

			if (!($result instanceof Exception))
            {
				break;
			}
		}

		return $result;
	}

	private function _parseTemplatePath($path = '')
	{
		$parts = array(
			'view'		=> $this->config['view'],
			'template'	=> 'default'
		);

		if(empty($path)) return null;

		$pathparts = explode('/', $path, 2);

		switch(count($pathparts))
		{
			case 2:
				$parts['view'] = array_shift($pathparts);
				// DO NOT BREAK!

			case 1:
				$parts['template'] = array_shift($pathparts);
				break;
		}

		return $parts;
	}

	/**
	 * Load a helper file
	 *
	 * @param   string  $hlp  The name of the helper source file automatically searches the helper paths and compiles as needed.
	 *
	 * @return  void
	 */
	public function loadHelper($hlp = null)
	{
		// Clean the file name
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $hlp);

		// Load the helper script
		$helper = AUtilsPath::find($this->_path['helper'], $this->_createFileName('helper', array('name' => $file)));

		if ($helper != false)
		{
			// Include the requested template filename in the local scope
			include_once $helper;
		}
	}

	/**
	 * Returns the view's option (component name) and view name in an
	 * associative array.
	 *
	 * @return  array
	 *
	 * @since   2.0
	 */
	public function getViewOptionAndName()
	{
		return array(
			'option'	=> $this->config['option'],
			'view'		=> $this->config['view'],
		);
	}

	/**
	 * Sets an entire array of search paths for templates or resources.
	 *
	 * @param   string  $type  The type of path to set, typically 'template'.
	 * @param   mixed   $path  The new search path, or an array of search paths.  If null or false, resets to the current directory only.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function _setPath($type, $path)
	{
		// Clear out the prior search dirs
		$this->_path[$type] = array();

		// Actually add the user-specified directories
		$this->_addPath($type, $path);

		// Always add the fallback directories as last resort
		switch (strtolower($type))
		{
			case 'template':
				// Set the alternative template search dir
				$app = $this->container->application;
				$component = preg_replace('/[^A-Z0-9_\.-]/i', '', $this->input->getCmd('option', ''));
				$fallback = APATH_THEMES . '/' . $app->getTemplate() . '/html/' . $component . '/' . $this->getName();
				$this->_addPath('template', $fallback);
				break;
		}
	}

	/**
	 * Adds to the search path for templates and resources.
	 *
	 * @param   string  $type  The type of path to add.
	 * @param   mixed   $path  The directory or stream, or an array of either, to search.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function _addPath($type, $path)
	{
		// Just force to array
		settype($path, 'array');

		// Loop through the path directories
		foreach ($path as $dir)
		{
			// No surrounding spaces allowed!
			$dir = trim($dir);

			// Add trailing separators as needed
			if (substr($dir, -1) != DIRECTORY_SEPARATOR)
			{
				// Directory
				$dir .= DIRECTORY_SEPARATOR;
			}

			// Add to the top of the search dirs
			array_unshift($this->_path[$type], $dir);
		}
	}

	/**
	 * Create the filename for a resource
	 *
	 * @param   string  $type   The resource type to create the filename for
	 * @param   array   $parts  An associative array of filename information
	 *
	 * @return  string  The filename
	 *
	 * @since   12.2
	 */
	protected function _createFileName($type, $parts = array())
	{
		switch ($type)
		{
			case 'template':
				$filename = strtolower($parts['name']) . '.' . $this->_layoutExt;
				break;

			default:
				$filename = strtolower($parts['name']) . '.php';
				break;
		}
		return $filename;
	}
}
