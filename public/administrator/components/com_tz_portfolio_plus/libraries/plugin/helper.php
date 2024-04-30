<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2013 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Filesystem\Folder;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

tzportfolioplusimport('template');
tzportfolioplusimport('plugin.helpers.legacy');

class TZ_Portfolio_PlusPluginHelper extends TZ_Portfolio_PlusPluginHelperLegacy {

    protected static $layout        = 'default';
    protected static $plugins       = null;
    protected static $instances     = array();
    protected static $plugin_types  = null;
    protected static $languageLoaded    = array();

    public static function getInstance($type, $plugin = null, $enabled=true, $dispatcher = null){
        if (!isset(self::$instances[$type.$plugin])) {
            if ($plugin_obj = self::getPlugin($type, $plugin, $enabled)) {
                if($type == 'extrafields'){
                    tzportfolioplusimport('fields.extrafield');
                }
                if(!$dispatcher){
                    $dispatcher = TZ_Portfolio_PlusPluginHelperBase::getDispatcher();
                }
                $className = 'PlgTZ_Portfolio_Plus' . ucfirst($type) . ucfirst($plugin);
                if (!class_exists($className)) {
                    self::importPlugin($type, $plugin);
                }
                if (class_exists($className)) {
                    $registry = new JRegistry($plugin_obj->params);

                    self::$instances[$type.$plugin] = new $className($dispatcher, array('type' => ($plugin_obj->type)
                    , 'name' => ($plugin_obj->name), 'params' => $registry));
                    return self::$instances[$type.$plugin];
                }
            }
        }
        return false;
    }

    public static function getDispatcher(){
        return TZ_Portfolio_PlusPluginHelperBase::getDispatcher();
    }

    public static function getLayoutPath($type, $name, $client = 'site', $layout = 'default',$viewName = null)
    {
        $defaultLayout  = $layout;
        if($client == 'site' && $viewName && !empty($viewName)) {
            $_template  = TZ_Portfolio_PlusTemplate::getTemplate(true);
            $template   = $_template->template;
            $params     = $_template->params;

            if (strpos($layout, ':') !== false)
            {
                // Get the template and file name from the string
                $temp           = explode(':', $layout);
                $template       = ($temp[0] == '_') ? $_template -> template : $temp[0];
                $layout         = $temp[1];
                $defaultLayout  = ($temp[1]) ? $temp[1] : 'default';
            }

            self::$layout = $defaultLayout;

            // Build the template and base path for the layout
            $tPath = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . '/' . $template . '/html/'.$params -> get('layout','default')
                .'/'.$viewName.'/plg_' . $type . '_' . $name . '/' . $layout . '.php';
            $bPath = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $type . '/' . $name
                .'/views'.'/'.$viewName.'/tmpl'
                .'/' . $defaultLayout . '.php';
            $dPath = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $type . '/' . $name
                .'/views'.'/'.$viewName.'/tmpl'
                .'/default.php';
        }elseif($client == 'admin'){
            $template = Factory::getApplication()->getTemplate();

            if (strpos($layout, ':') !== false)
            {
                // Get the template and file name from the string
                $temp = explode(':', $layout);
                $template = ($temp[0] == '_') ? $template : $temp[0];
                $layout = $temp[1];
                $defaultLayout = ($temp[1]) ? $temp[1] : 'default';
            }

            // Build the template and base path for the layout
            $tPath = JPATH_THEMES . '/' . $template . '/html/plg_' . $type . '_' . $name . '/' . $layout . '.php';
            $bPath = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $type . '/' . $name . '/tmpl/' . $defaultLayout . '.php';
            $dPath = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $type . '/' . $name . '/tmpl/default.php';
        }

        // If the template has a layout override use it
        if (file_exists($tPath))
        {
            return $tPath;
        }
        elseif (file_exists($bPath))
        {
            return $bPath;
        }
        else
        {
            return $dPath;
        }
    }

    public static function getLayout(){
        return self::$layout;
    }

    public static function getPlugin($type, $plugin = null, $enabled=true)
    {
        $result = array();
        $plugins = static::load($enabled);

        // Find the correct plugin(s) to return.
        if (!$plugin)
        {
            foreach ($plugins as $p)
            {
                // Is this the right plugin?
                if ($p->type == $type)
                {
                    $result[] = $p;
                }
            }
        }
        else
        {
            if($plugins){
                foreach ($plugins as &$p)
                {
                    Factory::getApplication() -> triggerEvent('onTPAddOnProcess', array(&$p));
                    // Is this plugin in the right group?
                    if ($p && $p->type == $type && $p->name == $plugin)
                    {
                        $result = $p;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    public static function getPluginById($id, $enabled=true)
    {
        $result = array();
        $plugins = static::load($enabled);

        // Find the correct plugin(s) to return.
        if ($id)
        {
            foreach ($plugins as $p)
            {
                // Is this plugin in the right group?
                if ($p->id == $id)
                {
                    $result = $p;
                    break;
                }
            }
        }

        return $result;
    }

    public static function getCoreContentTypes(){
        $content_types	= array();
        $array			= array(
            'none' => JText::_('JNONE'),
            'hits' => JText::_('JGLOBAL_HITS'),
            'title' => JText::_('JGLOBAL_TITLE'),
            'author' => JText::_('JAUTHOR'),
            'author_about' => JText::_('COM_TZ_PORTFOLIO_PLUS_ABOUT_AUTHOR'),
            'tags' => JText::_('COM_TZ_PORTFOLIO_PLUS_TAGS'),
            'icons' => JText::_('COM_TZ_PORTFOLIO_PLUS_ICONS'),
            'media' => JText::_('COM_TZ_PORTFOLIO_PLUS_TAB_MEDIA'),
            'extrafields' => JText::_('COM_TZ_PORTFOLIO_PLUS_TAB_FIELDS'),
            'introtext' => JText::_('COM_TZ_PORTFOLIO_PLUS_FIELD_INTROTEXT'),
            'fulltext' => JText::_('COM_TZ_PORTFOLIO_PLUS_FIELD_FULLTEXT'),
            'category' => JText::_('JCATEGORY'),
            'created_date' => JText::_('JGLOBAL_FIELD_CREATED_LABEL'),
            'modified_date' => JText::_('COM_TZ_PORTFOLIO_PLUS_MODIFIED_DATE'),
            'related' => JText::_('COM_TZ_PORTFOLIO_PLUS_FIELD_RELATED_ARTICLE'),
            'published_date' => JText::_('COM_TZ_PORTFOLIO_PLUS_PUBLISHED_DATE'),
            'parent_category' => JText::_('COM_TZ_PORTFOLIO_PLUS_PARENT_CATEGORY'),
            'project_link' => JText::_('COM_TZ_PORTFOLIO_PLUS_PROJECT_LINK_LABEL')
        );

        $std				= new stdClass();
        foreach($array as $key => $text){
            $std -> value		= $key;
            $std -> text		= $text;
            $content_types[]	= clone($std);
        }

        return $content_types;
    }

    public static function getContentTypes(){
        if($core_types = self::getCoreContentTypes()) {

            $includeTypes   = $core_types;
            $types          = ArrayHelper::getColumn($core_types, 'value');

            if ($contentPlugins = self::importPlugin('content')) {
                if ($pluginTypes = Factory::getApplication()->triggerEvent('onAddContentType')) {
                    if(count($pluginTypes)){
                        $pluginTypes    = array_filter($pluginTypes);
                    }
                    foreach ($pluginTypes as $i => $plgType) {
                        if (is_array($plgType) && count($plgType)) {
                            foreach ($plgType as $j => $type) {
                                if (in_array($type->value, $types)) {
                                    unset ($pluginTypes[$i][$j]);
                                }
                            }
                        } else {
                            if (in_array($plgType->value, $types)) {
                                unset($pluginTypes[$i]);
                            }
                        }
                    }
                    $includeTypes = array_merge($includeTypes, $pluginTypes);
                    return $includeTypes;
                }
            }
            return $core_types;
        }
        return false;
    }

    protected static function load($enabled=true)
    {
        if (static::$plugins !== null)
        {
            return static::$plugins;
        }

        $user = Factory::getUser();
        $cache = Factory::getCache('com_tz_portfolio_plus', '');

        $levels = implode(',', $user->getAuthorisedViewLevels());

        if (!(static::$plugins = $cache->get($levels)))
        {
            $db     = TZ_Portfolio_PlusDatabase::getDbo();
            $query  = $db->getQuery(true)
                ->select('id, folder AS type, element AS name, params, manifest_cache, asset_id')
                ->from('#__tz_portfolio_plus_extensions')
                ->where('type =' . $db->quote('tz_portfolio_plus-plugin'))
                ->where('access IN(' . $levels.')')
                ->order('ordering');

            if($enabled){
                $query -> where('published = 1');
            }
            $db -> setQuery($query);

            if($plugins = $db->loadObjectList()){
                foreach($plugins as &$item){
                    $item -> manifest_cache = json_decode($item -> manifest_cache);
                }

                Factory::getApplication() -> triggerEvent('onTPAddOnIsLoaded', array(&$plugins));

                static::$plugins = $plugins;
            }else{
                static::$plugins    = false;
            }

            $cache->store(static::$plugins, $levels);
        }

        return static::$plugins;
    }

    public static function getAddonController($addon_id, $config = array()){
        if($addon_id){

            if($addon  = self::getPluginById($addon_id)){

                tzportfolioplusimport('controller.legacy');

                $app    = Factory::getApplication();
                $input  = $app -> input;
                $result = true;

                // Check task with format: addon_name.addon_view.addon_task (example image.default.display);
                $adtask     = $input -> get('addon_task');
                if($adtask && strpos($adtask,'.') > 0 && substr_count($adtask,'.') > 1){
                    list($plgname,$adtask) = explode('.',$adtask,2);
                    if($plgname == $addon -> name){
                        $input -> set('addon_task',$adtask);
                    }
                }
                $basePath   = JPath::clean(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.'/'.$addon -> type
                    .'/'.$addon -> name);


                $_config['base_path']    = $basePath;
                $_config['addon']    = $addon;

                $config = array_merge($_config, $config);

                if($controller = TZ_Portfolio_Plus_AddOnControllerLegacy::getInstance('PlgTZ_Portfolio_Plus'
                    .ucfirst($addon -> type).ucfirst($addon -> name)
                    , $config)) {
                    tzportfolioplusimport('plugin.modelitem');

                    return $controller;
                }
            }
        }
        return false;
    }

    public static function loadLanguage($element, $type){

        $lang           = Factory::getApplication() -> getLanguage();
        $tag            = $lang -> getTag();
        $prefix         = 'tp_addon_';
        $basePath       = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $type . '/' . $element;
        $_filename      = $type . '_' . $element;

        $__files    = array();
        if(is_dir($basePath.'/language/'.$tag)) {
            $__files = Folder::files($basePath . '/language/'.$tag, 'plg_.*.ini$', false);
        }elseif(is_dir($basePath.'/language/en-GB')) {
            $__files = Folder::files($basePath . '/language/en-GB', 'plg_.*.ini$', false);
        }

        if($__files && count($__files)){
            $prefix = 'plg_';
        }
        $extension = $prefix . $_filename;

        if(isset(self::$languageLoaded[$extension])){
            return self::$languageLoaded[$extension];
        }

        $load   = $lang->load(strtolower($extension), $basePath, null, false, true);

        if($load) {
            self::$languageLoaded[$extension] = $load;
        }

        return $load;
    }

    /* Import all add-ons
    *  Since v2.4.3
    */
    public static function importAllAddOns(){
        $imported   = false;
        // Get
        $folders = Folder::folders(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH);
        if(count($folders)){
            foreach ($folders as $group){
                $imported   = self::importPlugin($group);
            }
        }
        return $imported;
    }

}