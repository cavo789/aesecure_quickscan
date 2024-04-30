<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2018 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Event\DispatcherInterface;

tzportfolioplusimport('plugin.helpers.base');

if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){
    // Declare class with Joomla 4
    class TZ_Portfolio_PlusPluginHelperLegacy extends JPluginHelper{

        protected static $loaded    = array();

        protected static function import($plugin, $autocreate = true, DispatcherInterface $dispatcher = null)
        {
            if(TZ_Portfolio_PlusPluginHelperBase::import($plugin, $dispatcher)){
                if ($autocreate)
                {
                    $className = 'PlgTZ_Portfolio_Plus' . $plugin->type . $plugin->name;

                    if (class_exists($className))
                    {
                        // Load the plugin from the database.
                        if (!isset($plugin->params))
                        {
                            // Seems like this could just go bye bye completely
                            $plugin = static::getPlugin($plugin->type, $plugin->name);
                        }

                        // Instantiate and register the plugin.
                        $plg = new $className($dispatcher, (array) ($plugin));

                        if(method_exists($plg, 'registerListeners')) {
                            $plg -> registerListeners();
                        }
                    }
                }
            }
        }


        public static function importPlugin($type, $plugin = null, $autocreate = true, DispatcherInterface $dispatcher = null)
        {
            static $loaded = [];

            // Check for the default args, if so we can optimise cheaply
            $defaults = false;

            if ($plugin === null && $autocreate === true && $dispatcher === null)
            {
                $defaults = true;
            }

            // Ensure we have a dispatcher now so we can correctly track the loaded plugins
            $dispatcher = $dispatcher ?: Factory::getApplication()->getDispatcher();

            // Get the dispatcher's hash to allow plugins to be registered to unique dispatchers
            $dispatcherHash = spl_object_hash($dispatcher);

            if (!isset($loaded[$dispatcherHash]))
            {
                $loaded[$dispatcherHash] = [];
            }

            if (!$defaults || !isset($loaded[$dispatcherHash][$type]))
            {
                $results = null;

                // Load the plugins from the database.
                $plugins = static::load();

                // Get the specified plugin(s).
                for ($i = 0, $t = \count($plugins); $i < $t; $i++)
                {
                    if ($plugins[$i]->type === $type && ($plugin === null || $plugins[$i]->name === $plugin))
                    {
                        static::import($plugins[$i], $autocreate, $dispatcher);
                        $results = true;
                    }
                }

                // Bail out early if we're not using default args
                if (!$defaults)
                {
                    return $results;
                }

                $loaded[$dispatcherHash][$type] = $results;
            }

            return $loaded[$dispatcherHash][$type];
        }
    }
}else{
    class TZ_Portfolio_PlusPluginHelperLegacy extends JPluginHelper{

        // Declare class with not Joomla 4
        protected static function import($plugin, $autocreate = true, \JEventDispatcher $dispatcher = null)
        {
            if(TZ_Portfolio_PlusPluginHelperBase::import($plugin, $dispatcher)){
                if ($autocreate)
                {
                    $className = 'PlgTZ_Portfolio_Plus' . $plugin->type . $plugin->name;

                    if (class_exists($className))
                    {
                        // Load the plugin from the database.
                        if (!isset($plugin->params))
                        {
                            // Seems like this could just go bye bye completely
                            $plugin = static::getPlugin($plugin->type, $plugin->name);
                        }

                        // Instantiate and register the plugin.
                        new $className($dispatcher, (array) ($plugin));
                    }
                }
            }
        }

        public static function importPlugin($type, $plugin = null, $autocreate = true, \JEventDispatcher $dispatcher = null)
        {
            // Ensure we have a dispatcher now so we can correctly track the loaded paths
            $dispatcher = $dispatcher ?: TZ_Portfolio_PlusPluginHelperBase::getDispatcher();

            return parent::importPlugin($type, $plugin, $autocreate, $dispatcher);
        }
    }
}