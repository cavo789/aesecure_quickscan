<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2015 templaza.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class TZ_Portfolio_Plus_AddOnControllerLegacy extends JControllerLegacy{

    protected static $instance;

    protected $addon;

    protected $article;

    protected $trigger_params;

    protected $core_view_list;

    public function __construct($config = array())
    {

        parent::__construct($config);

        if(isset($config['addon'])){
            $this -> addon          = $config['addon'];
        }
        if(isset($config['article'])){
            $this -> article        = $config['article'];
        }
        if(isset($config['trigger_params'])){
            $this -> trigger_params = $config['trigger_params'];
        }

        // Guess the list view as the suffix, eg: OptionControllerSuffix.
        if (empty($this->core_view_list))
        {
            $view   = $this -> input -> getCmd('view');
            $this->core_view_list = strtolower($view);
        }
    }

    public function display($cachable = false, $urlparams = array())
    {
        $app        = Factory::getApplication();
        $document   = Factory::getApplication() -> getDocument();
        $viewType   = $document->getType();
        $viewName   = $this->input->get('addon_view', $this->default_view);
        $viewLayout = $this->input->get('addon_layout', 'default', 'string');

        if($view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath,
            'layout' => $viewLayout))){

            // Check manage permission if the addon have manage datas (only use of back-end)
            if($app -> isClient('administrator') && $addon_id = $this -> input -> get('addon_id', 0, 'int')){
                if($plugin = TZ_Portfolio_PlusPluginHelper::getPluginById($addon_id)){
                    $user   = TZ_Portfolio_PlusUser::getUser();

                    if(isset($plugin -> asset_id) &&$plugin -> asset_id && !$user -> authorise('core.manage',
                            'com_tz_portfolio_plus.addon.'.$plugin -> id)){

                        // Somehow the person just went to the form - we don't allow that.
                        $this->setError(JText::_('JERROR_ALERTNOAUTHOR'));
                        $this->setMessage($this->getError(), 'error');

                        $this->setRedirect(JRoute::_('index.php?option=com_tz_portfolio_plus&view=addons', false));

                        return false;
                    }
                }
            }
        }
        $view -> setLayout($viewLayout);

        // Get/Create the model
        if ($model = $this->getModel($viewName))
        {
            // Push the model into the view (as default)
            $view->setModel($model, true);
        }

        $view->document = $document;

        $conf = Factory::getConfig();

        // Display the view
        if ($cachable && $viewType != 'feed' && $conf->get('caching') >= 1)
        {
            $option = $this->input->get('option');
            $cache = Factory::getCache($option, 'addon_view');

            if (is_array($urlparams))
            {
                $app = Factory::getApplication();

                if (!empty($app->registeredurlparams))
                {
                    $registeredurlparams = $app->registeredurlparams;
                }
                else
                {
                    $registeredurlparams = new stdClass;
                }

                foreach ($urlparams as $key => $value)
                {
                    // Add your safe url parameters with variable type as value {@see JFilterInput::clean()}.
                    $registeredurlparams->$key = $value;
                }

                $app->registeredurlparams = $registeredurlparams;
            }

            $cache->get($view, 'display');
        }
        else
        {
            $view->display();
        }

        return $this;
    }

    public function getView($name = '', $type = '', $prefix = '', $config = array())
    {
        $view = parent::getView($name, $type, $prefix, $config);

        if($view){

            if($addon = $this -> addon){
                $plugin_path = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . DIRECTORY_SEPARATOR
                    . $addon -> type . DIRECTORY_SEPARATOR
                    . $addon -> name;

                // Create template path of tz_portfolio_plus
                $template = TZ_Portfolio_PlusTemplate::getTemplate(true);
                $tplparams = $template->params;

                // Create default template of tz_portfolio_plus
                $defaultPath = null;
                $tpath = null;

//                if(isset($template -> home_path) && $template -> home_path){
//                    $defaultPath    = $template -> home_path. DIRECTORY_SEPARATOR
//                        .($name?$name . DIRECTORY_SEPARATOR:''). 'plg_'
//                        . $addon -> type . '_' . $addon -> name;
//                }
                if(isset($template -> base_path) && $template -> base_path){
                    $tpath    = $template -> base_path. DIRECTORY_SEPARATOR
                        .($name?$name . DIRECTORY_SEPARATOR:'') . 'plg_'
                        . $addon -> type . '_' . $addon -> name;
                }

                $vpaths = $view->get('_path');
                $vpaths = $vpaths['template'];
                $view->set('_path', array('template' => array()));

                $plgVPath = $plugin_path . DIRECTORY_SEPARATOR . 'views'
                    . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'tmpl';

                if (!in_array($plgVPath, $vpaths)) {
                    $view->addTemplatePath($plgVPath);
                }

                // Create template path from template site
                $_template = Factory::getApplication()->getTemplate();
                $jPathSite = JPATH_SITE . '/templates/' . $_template . '/html/com_tz_portfolio_plus/'
                    . $name . '/plg_' . $addon -> type . '_' . $addon -> name;

                // Add default template path
                if ($defaultPath && !in_array($defaultPath, $vpaths)) {
                    $view->addTemplatePath($defaultPath);
                }
                // Add template path which chosen in menu
                if ($tpath && !in_array($tpath, $vpaths)) {
                    $view->addTemplatePath($tpath);
                }
                // Add template path from template site
                if (!in_array($jPathSite, $vpaths)) {
                    $view->addTemplatePath($jPathSite);
                }
            }
        }

        return $view;
    }

    public function execute($task)
    {
        $this->task = $task;

        $task = $task ? strtolower($task) : '';

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
            throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_TASK_NOT_FOUND', $task), 404);
        }

        // Record the actual task being fired
        $this->doTask = $doTask;

        return $this->$doTask();
    }

    public function getModel($name = '', $prefix = '', $config = array())
    {
        $model = parent::getModel($name, $prefix, $config);

        if($model){
            if($this -> addon){
                $model -> set('addon',$this -> addon);
            }
            if($this -> article){
                $model -> set('article',$this -> article);
            }
            if($this -> trigger_params){
                $model -> set('trigger_params',$this -> trigger_params);
            }
        }

        return $model;
    }


    public static function getInstance($prefix, $config = array())
    {
        if(self::$instance && isset(self::$instance[$prefix])){
            if (is_object(self::$instance[$prefix]))
            {
                $_class = self::$instance[$prefix];
                $reflection = new ReflectionClass($_class);

                return $reflection ->newInstance($config);
            }
        }else{
            self::$instance[$prefix]    = false;
        }

        $input = Factory::getApplication()->input;

        // Get the environment configuration.
        $basePath = array_key_exists('base_path', $config) ? $config['base_path'] : COM_TZ_PORTFOLIO_PLUS_ADDON_PATH;
        $format   = $input->getWord('format');
        $command  = $input->get('addon_task', 'display');

        // Check for array format.
        $filter = JFilterInput::getInstance();

        if (is_array($command))
        {
            $command = $filter->clean(array_pop(array_keys($command)), 'cmd');
        }
        else
        {
            $command = $filter->clean($command, 'cmd');
        }

        // Check for a controller.task command.
        if (strpos($command, '.') !== false)
        {
            // Explode the controller.task command.
            list ($type, $task) = explode('.', $command);

            // Define the controller filename and path.
            $file = self::createFileName('controller', array('name' => $type, 'format' => $format));
            $path = $basePath . '/controllers/' . $file;
            $backuppath = $basePath . '/controller/' . $file;

            // Reset the task without the controller context.
            $input->set('addon_view', $type);
            $input->set('addon_task', $task);
        }
        else
        {
            // Base controller.
            $type = null;

            // Define the controller filename and path.
            $file       = self::createFileName('controller', array('name' => 'controller', 'format' => $format));
            $path       = $basePath . '/' . $file;
            $backupfile = self::createFileName('controller', array('name' => 'controller'));
            $backuppath = $basePath . '/' . $backupfile;
        }
        // Get the controller class name.
        $class = ($prefix ? ucfirst($prefix) : '') . 'Controller' . ($type ? ucfirst($type) : '');

        // Include the class if not present.
        if (!class_exists($class))
        {
            // If the controller file path exists, include it.
            if (file_exists($path))
            {
                require_once $path;
            }
            elseif (isset($backuppath) && file_exists($backuppath))
            {
                require_once $backuppath;
            }
        }

        // Instantiate the class.
        if (class_exists($class))
        {
            self::$instance[$prefix] = new $class($config);
        }

        return self::$instance[$prefix];
    }

    protected function getCoreRedirect(){
        $link   = '';
        if($coreView = $this -> core_view_list){
            $link   = 'index.php?option='.$this -> option.'&view='.$this -> core_view_list;
        }
        return $link;
    }

    protected function getAddonRedirect($addon_view = null){
        $link   = $this -> getCoreRedirect();

        if($addon_id = $this->input -> getInt('addon_id')){
            $link   .= '&addon_id='.$addon_id;
        };

        if($addon_view){
            $link   .= '&addon_view='.$addon_view;
        }

        return $link;
    }
}