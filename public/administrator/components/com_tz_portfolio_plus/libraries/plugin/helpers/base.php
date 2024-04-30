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

abstract class TZ_Portfolio_PlusPluginHelperBase{

    protected static $cache         = array();

    public static function import($plugin, $dispatcher)
    {
        static $paths = array();

        $app    = Factory::getApplication();

        // Since v2.3.5
        $app -> triggerEvent('onTPAddOnPrepareImport', array(&$plugin));

        // Get the dispatcher's hash to allow paths to be tracked against unique dispatchers
        $dispatcherHash = is_object($dispatcher)?spl_object_hash($dispatcher):__METHOD__;

        if (!isset($paths[$dispatcherHash]))
        {
            $paths[$dispatcherHash] = array();
        }

        $plugin->type = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->type);
        $plugin->name = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->name);

        $path = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $plugin->type . '/' . $plugin->name . '/' . $plugin->name . '.php';

        if (!isset($paths[$dispatcherHash][$path]))
        {
            if (file_exists($path))
            {
                if (!isset($paths[$dispatcherHash][$path]))
                {
                    require_once $path;
                }

                $paths[$dispatcherHash][$path] = true;
            }
            else
            {
                $paths[$dispatcherHash][$path] = false;
            }
        }

        // Since v2.3.5
        $app -> triggerEvent('onTPAddOnBeforeImport', array($plugin, &$paths[$dispatcherHash][$path]));

        return $paths[$dispatcherHash][$path];
    }

    public static function getDispatcher(){
        $storeId    = md5(__METHOD__);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){
            $dispatcher = Factory::getApplication()->getDispatcher();
        }else{
            $dispatcher = \JEventDispatcher::getInstance();
        }
        if(isset($dispatcher) && $dispatcher){
            self::$cache[$storeId]  = $dispatcher;
            return $dispatcher;
        }
        return false;
    }
}