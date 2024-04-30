<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

namespace TZ_Portfolio_Plus\Installer;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\String\StringHelper;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\Installer;

\JLoader::import('com_tz_portfolio_plus.includes.framework',JPATH_ADMINISTRATOR.'/components');

class TZ_Portfolio_PlusInstaller extends Installer
{
    protected static $instances;

    public function __construct($basepath = __DIR__, $classprefix = 'TZ_Portfolio_Plus\\Installer\\Adapter', $adapterfolder = 'adapter')
    {
        parent::__construct($basepath, $classprefix, $adapterfolder);

        // Get a generic TZ_Portfolio_PlusTableExtension instance for use if not already loaded
        if (!($this->extension instanceof TZ_Portfolio_PlusTableExtensions)) {
            \JTable::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH . DIRECTORY_SEPARATOR . 'tables');
            $this->extension = \JTable::getInstance('Extensions', 'TZ_Portfolio_PlusTable');
        }

        if(is_object($this -> extension) && isset($this -> extension -> id)) {
            $this->extension->extension_id = $this->extension->id;
        }
    }

    public static function getInstance($basepath = __DIR__, $classprefix = 'TZ_Portfolio_Plus\\Installer\\Adapter', $adapterfolder = 'adapter')
    {
        if (!isset(self::$instances[$basepath]))
        {
            self::$instances[$basepath] = new static($basepath, $classprefix, $adapterfolder);

            // For B/C, we load the first instance into the static $instance container, remove at 4.0
            if(!version_compare(JVERSION, '4.0', 'ge')){

                if (!isset(self::$instance))
                {
                    self::$instance = self::$instances[$basepath];
                }
            }
        }

        return self::$instances[$basepath];
    }

    public function install($path = null)
    {
        if ($path && Folder::exists($path))
        {
            $this->setPath('source', $path);
        }
        else
        {
            $this->abort(\JText::_('JLIB_INSTALLER_ABORT_NOINSTALLPATH'));

            return false;
        }

        if (!$adapter = $this->setupInstall('install', true))
        {
            $this->abort(\JText::_('JLIB_INSTALLER_ABORT_DETECTMANIFEST'));

            return false;
        }

        if (!is_object($adapter))
        {
            return false;
        }

        // Add the languages from the package itself
        if (method_exists($adapter, 'loadLanguage'))
        {
            $adapter->loadLanguage($path);
        }

//        // Fire the onExtensionBeforeInstall event.
//        JPluginHelper::importPlugin('extension');
//        $dispatcher = JEventDispatcher::getInstance();
//        $dispatcher->trigger(
//            'onExtensionBeforeInstall',
//            array(
//                'method' => 'install',
//                'type' => $this->manifest->attributes()->type,
//                'manifest' => $this->manifest,
//                'extension' => 0
//            )
//        );

        // Run the install
        $result = $adapter->install();

//        // Fire the onExtensionAfterInstall
//        $dispatcher->trigger(
//            'onExtensionAfterInstall',
//            array('installer' => clone $this, 'eid' => $result)
//        );

        if ($result !== false)
        {
            // Refresh versionable assets cache
            Factory::getApplication()->flushAssets();

            return true;
        }

        return false;
    }

    public function getAdapter($name, $options = array())
    {
        $this->getAdapters($options);

        if (!$this->setAdapter($name, $this->_adapters[$name]))
        {
            return false;
        }

        return $this->_adapters[$name];
    }

    public function setupInstall($route = 'install', $returnAdapter = false)
    {
        // We need to find the installation manifest file
        if (!$this->findManifest())
        {
            return false;
        }

        // Load the adapter(s) for the install manifest
        $type   = (string) $this->manifest->attributes()->type;
        $type   = StringHelper::str_ireplace('tz_portfolio_plus-','',$type);
        $params = array('route' => $route, 'manifest' => $this->getManifest());

        // Include adapter folder
        $path = $this->_basepath . '/' . $this->_adapterfolder . '/' . $type . '.php';

        $adapterPrefix  = 'TZ_Portfolio_PlusInstaller'.ucfirst($type);

        $class = rtrim($this->_classprefix, '\\') . '\\' . $adapterPrefix . 'Adapter';

        // Try once more to find the class
        \JLoader::register($class, $path);

        $adapter = $this->loadAdapter($adapterPrefix, $params);

        if ($returnAdapter)
        {
            return $adapter;
        }

        return true;
    }
}