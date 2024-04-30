<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author    DuongTVTemPlaza

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

# Copyright Copyright (C) 2011-2019 TZ Portfolio (http://tzport.com). All Rights Reserved.

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Filesystem\Folder;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

JLoader::register('TZ_Portfolio_PlusHelperCategories', JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/helpers/categories.php');

class TZ_Portfolio_PlusTemplate {

    protected static $cache             = array();
    protected static $loaded            = array();
    protected static $imported          = array();
    protected static $languageLoaded    = array();

    public static function getTemplate($params = false)
    {
        $storeId    = __METHOD__;
        $storeId    .= ':'.$params;
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $templateId = self::getTemplateId();
        $template   = new stdClass;

        JTable::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.DIRECTORY_SEPARATOR.'tables');

        $table  = JTable::getInstance('Templates','TZ_Portfolio_PlusTable');

        $template -> template   = 'system';
        $template -> params     = new JRegistry();
        $template -> layout     = null;

        $db                     = TZ_Portfolio_PlusDatabase::getDbo();
        $query                  = $db -> getQuery(true);

        $query -> select('COUNT(t.id)');
        $query -> from('#__tz_portfolio_plus_templates AS t');
        $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e On e.element = t.template');
        $query -> where('e.type = '.$db -> quote('tz_portfolio_plus-template'));
        $query -> where('e.published = 1');
        $query -> where('t.id =' . $templateId);
        $query -> group('t.id');
        $db -> setQuery($query);

        if(!$db -> loadResult()){
            $templateId = null;
        }

        if($home = $table -> getHome()){
            $default_params = new JRegistry;
            $default_params -> loadString($home -> params);
            $home -> params = clone($default_params);
        }

        if($templateId){
            $table -> load($templateId);
            $template -> id         = $templateId;
            $template -> template   = $table -> template;
            if($table -> params && !empty($table -> params)) {
                $_params    = $table -> params;
                if(is_string($_params)) {
                    $_params = new JRegistry($_params);
                }
                $template->params = $_params;
            }
            if($table -> layout){
                $template -> layout = json_decode($table -> layout);
            }
        }else{
            if($home){
                $template -> id         = $home -> id;
                $template -> template   = $home -> template;
                if($home -> params && !empty($home -> params)) {
                    $_params    = $home -> params;
                    if(is_string($_params)) {
                        $_params = new JRegistry($_params);
                    }
                    $template->params = $_params;
                }
                if($home -> layout){
                    $template -> layout = json_decode($home -> layout);
                }
            }
        }

        $tplparams      = $template -> params;

        $template -> base_path  = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.DIRECTORY_SEPARATOR
            . $template->template. DIRECTORY_SEPARATOR . 'html'. DIRECTORY_SEPARATOR
            . $template->params -> get('layout','default');

        if($home){
            if($home -> template != $template -> template) {
                $template->home_path = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . DIRECTORY_SEPARATOR
                    . $home->template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR
                    . $tplparams->get('layout', 'default');
            }else{
                $template->home_path = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . DIRECTORY_SEPARATOR
                    . $home->template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR
                    . $home -> params->get('layout', 'default');
            }
        }

        if ($params)
        {
            self::$cache[$storeId]  = $template;
            return $template;
        }

        self::$cache[$storeId]  = $template -> template;
        return $template->template;
    }

    public static function getTemplateDefault(){

        $storeId    = __METHOD__;
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $template   = new stdClass;

        $template -> template   = 'system';
        $template -> params     = new JRegistry();
        $template -> layout     = null;
        $table  = JTable::getInstance('Templates','TZ_Portfolio_PlusTable');

        if($home = $table -> getHome()){
            $template -> id         = $home -> id;
            $template -> template   = $home -> template;
            if($home -> params && !empty($home -> params)) {
                $_params    = new JRegistry($home -> params);
                $template->params = $_params;
            }
            if($home -> layout){
                $template -> layout = json_decode($home -> layout);
            }
        }

        self::$cache[$storeId]  = $template;
        return $template;
    }

    public static function getTemplateById($id, $params = true){

        if(!$id){
            return self::getTemplate($params);
        }

        $storeId    = __METHOD__;
        $storeId    .= ':'.$id;
        $storeId    .= ':'.$params;
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        JTable::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.DIRECTORY_SEPARATOR.'tables');

        $table  = JTable::getInstance('Templates','TZ_Portfolio_PlusTable');

        $table -> reset();

        if(!$table -> load($id)){
            return false;
        }
        if($db = $table -> getDbo()){
            $query  = $db -> getQuery();
            $db -> setQuery($query);
            $template   = $db -> loadObject();
            if(is_string($template -> params)){
                $_params = new Registry();
                $_params -> loadString($template -> params);
                $template -> params = $_params;
            }

            $tplparams      = $template -> params;

            $template -> base_path  = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.DIRECTORY_SEPARATOR
                . $template->template. DIRECTORY_SEPARATOR . 'html'. DIRECTORY_SEPARATOR
                . $template->params -> get('layout','default');

            if($home = $table -> getHome()){
                $default_params = new JRegistry;
                $default_params -> loadString($home -> params);
                $home -> params = clone($default_params);
            }

            if($home){
                if($home -> template != $template -> template) {
                    $template->home_path = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . DIRECTORY_SEPARATOR
                        . $home->template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR
                        . $tplparams->get('layout', 'default');
                }else{
                    $template->home_path = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . DIRECTORY_SEPARATOR
                        . $home->template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR
                        . $home -> params->get('layout', 'default');
                }
            }

            if(isset($template -> layout) && is_string($template -> layout)){
                $template -> layout = json_decode($template -> layout);
            }

            if ($params)
            {
                self::$cache[$storeId]  = $template;
                return $template;
            }

            self::$cache[$storeId]  = $template -> template;
            return $template -> template;
        }
        return false;
    }

    public static function getTemplateByOption($option){
        if(!$option){
            return false;
        }

        $storeId    = __METHOD__;
        $storeId    .= ':'.serialize($option);
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }


        $option = array_merge(array('type' => 'tz_portfolio_plus-template'), $option );

        $table  = JTable::getInstance('Extensions', 'TZ_Portfolio_PlusTable');

        if(!$table -> load($option)){
            return false;
        }

        $data   = $table -> getDbo() -> loadObject();
        if(isset($data -> manifest_cache) && $data -> manifest_cache && is_string($data -> manifest_cache)){
            $data -> manifest_cache    = json_decode($data -> manifest_cache);
        }

        self::$cache[$storeId]  = $data;
        return $data;
    }


    protected static function getTemplateId($artId = null,$catId = null){

        $db         = TZ_Portfolio_PlusDatabase::getDbo();
        $app        = Factory::getApplication('site');
        $templateId = null;
        $_catId     = null;
        $_artId     = null;

        if($app -> isClient('site')) {
            $params = $app->getParams();
            $templateId = $params->get('tz_template_style_id');
        }

        $input  = $app -> input;
        if($input -> get('option') == 'com_tz_portfolio_plus'){
            switch($input -> get('view')){
                case 'article':
                case 'p_article':
                    $_artId = $input -> get('id',null,'int');
                    if($_catId = TZ_Portfolio_PlusHelperCategories::getCategoriesByArticleId($_artId, true)){
                        $_catId = $_catId -> id;
                    }
                    break;
            }
        }

        if(!empty($catId)){
            $_catId = $catId;
        }
        if(!empty($artId)){
            $_artId = $artId;
        }

        if($_catId){
            $query  = $db -> getQuery(true);
            $query -> select($db -> quoteName('template_id'));
            $query -> from($db -> quoteName('#__tz_portfolio_plus_categories'));
            $query -> where($db -> quoteName('id').'='.$_catId);
            $db -> setQuery($query);
            if($crow = $db -> loadObject()){
                if($crow -> template_id){
                    $templateId = $crow -> template_id;
                }
            }
        }
        if($_artId){
            $query  = $db -> getQuery(true);
            $query -> select($db -> quoteName('template_id'));
            $query -> from($db -> quoteName('#__tz_portfolio_plus_content'));
            $query -> where($db -> quoteName('id').'='.$_artId);
            $db -> setQuery($query);
            if($row = $db -> loadObject()){
                if($row -> template_id){
                    $templateId = $row -> template_id;
                }
            }
        }
        return (int) $templateId;
    }

    public static function loadLanguage($template){

        $lang           = Factory::getApplication() -> getLanguage();
        $tag            = $lang -> getTag();
        $basePath       = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . '/' .$template;

        $prefix      = 'tp_style_';

        $__files    = array();
        if(is_dir($basePath.'/language/'.$tag)) {
            $__files = Folder::files($basePath . '/language/'.$tag, 'tpl_.*.ini$', false);
        }elseif(is_dir($basePath.'/language/en-GB')) {
            $__files = Folder::files($basePath . '/language/en-GB', 'tpl_.*.ini$', false);
        }

        if($__files && count($__files)){
            $prefix = 'tpl_';
        }

        $extension = $prefix . $template;

        if(isset(self::$languageLoaded[$extension])){
            return self::$languageLoaded[$extension];
        }

        $load   = $lang->load(strtolower($extension), $basePath, null, false, true);

        if($load) {
            self::$languageLoaded[$extension] = $load;
        }

        return $load;
    }

    public static function getCssStyleName($styleName, $params = null, $variables = array(), &$document = null){

        if(!$styleName){
            return false;
        }

        if(!$params){
            $params = new Registry();
        }

        $variables  = (array) $variables;

        $storeId    = __METHOD__;
//        $storeId   .= '::'.$styleName;
//        $storeId   .= '::'.$params -> get('enable_bootstrap', 1);
//        $storeId   .= '::'.$params -> get('bootstrapversion', 4);
//        $storeId   .= '::'.serialize($variables);
        $storeId    = md5($storeId);

//        if(!isset(self::$cache[$storeId])) {
//            self::$cache[$storeId] = array();
//        }

        $cssname    = '';
        $scss_files = array();

        if($scss_sfiles = self::getSassDirByStyle($styleName)){
            $scss_files = $scss_sfiles;
        }elseif($scss_cfiles = self::getSassDirCore()){
            $scss_files = $scss_cfiles;
            $styleName  = '';
        }
        if(count($scss_files)){
            $name           = '';

            foreach ($scss_files as $scss_file) {
                $fname  = basename($scss_file);

                $scss_file  = \JPath::clean($scss_file);

                if($params -> get('enable_bootstrap', 1)){
                    // Ignore all scss files of bootstrap3 folder if the bootstrap is version 4
                    if($params -> get('bootstrapversion', 4) == 4
                        && preg_match('#vendor(/|\\\)bootstrap3(/|\\\)'.$fname.'$#', $scss_file)){
                        continue;
                    }elseif($params -> get('bootstrapversion', 4) == 3
                        && preg_match('#vendor(/|\\\)bootstrap(/|\\\)'.$fname.'$#', $scss_file)){

                        // Ignore all scss files of bootstrap folder if the bootstrap is version 3
                        continue;
                    }
                }else{
                    if(preg_match('#vendor(/|\\\)bootstrap3(/|\\\)'.$fname.'$#', $scss_file)||
                        preg_match('#vendor(/|\\\)bootstrap(/|\\\)'.$fname.'$#', $scss_file)){
                        continue;
                    }
                }
                $name .= md5_file($scss_file);
            }

            $bootverPrefix  = $params -> get('enable_bootstrap', 1);
            $bootverPrefix .= $bootverPrefix?'-'.$params -> get('bootstrapversion', 4):'';
            $bootverPrefix .= ':'.serialize($variables);

            if($params -> get('enable_bootstrap', 1) && $params -> get('bootstrapversion', 4)
                && !isset(self::$loaded[$styleName]['import_bootstrap'])) {
                unset(self::$imported['import_fontawesome']);
            }elseif(isset(self::$loaded[$styleName]['loaded'])){
                unset(self::$imported['import_fontawesome']);
            }
            if(!isset(self::$imported['import_fontawesome'])){
                $bootverPrefix  .= ':fontawesome';
            }

            $bootverPrefix  = md5($bootverPrefix);
            $bootverPrefix  = substr($bootverPrefix, 0, 4);
            $cssname        = 'style-'.$bootverPrefix.'-'. md5($name);
        }

        if($cssname){
            $css_path   = COM_TZ_PORTFOLIO_PLUS_PATH_SITE.'/css/style'.($styleName?'/'.$styleName:'');

            if (!file_exists($css_path.'/'. $cssname . '.css')) {
                self::clearCache($styleName, $bootverPrefix);

                require_once COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/libraries/phpclass/scssphp/scss.inc.php';

                $scss       = new ScssPhp\ScssPhp\Compiler();
                $coreSassPath  = COM_TZ_PORTFOLIO_PLUS_PATH_SITE.'/scss';
                $sass_path  = $styleName?COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH:COM_TZ_PORTFOLIO_PLUS_PATH_SITE;

                $sass_prefix_path   = $styleName?$styleName.'/scss':'scss';

                $importPaths    = array( $coreSassPath, $sass_path);

                if(file_exists(COM_TZ_PORTFOLIO_PLUS_PATH_SITE.'/scss/vendor/bootstrap/bootstrap-functions.scss')) {
                    $compileCode = '@import "vendor/bootstrap/bootstrap-functions.scss";';
                }

                if($params -> get('enable_bootstrap',1)) {
                    if($styleName && file_exists($sass_path.'/'.$styleName.'/scss/variables_override.scss')) {
                        $compileCode .= '@import "variables_override.scss";';
                    }
                    if($params -> get('bootstrapversion', 4) == 4
                        && !isset(self::$imported['import_bootstrap'])){
                        if(file_exists(COM_TZ_PORTFOLIO_PLUS_PATH_SITE.'/scss/vendor/bootstrap/bootstrap.scss')) {
                            $compileCode .= '@import "vendor/bootstrap/bootstrap.scss";';
                            self::$imported['import_bootstrap'] = true;
                        }
                        if(file_exists(COM_TZ_PORTFOLIO_PLUS_PATH_SITE.'/scss/_basic.scss')) {
                            $compileCode .= '@import "basic.scss";';
                        }
                    }
//                    else{
//                        $compileCode    .= '@import "vendor/bootstrap3/bootstrap.scss";';
//                        $compileCode    .= '@import "'.$coreSassPath.'/legacy.scss";';
//                    }
                }

                // Import basic
                if(file_exists(COM_TZ_PORTFOLIO_PLUS_PATH_SITE.'/scss/_basic.scss')
                    && !isset(self::$imported['import_basic'])) {
                    $compileCode .= '@import "basic.scss";';
                    self::$imported['import_basic'] = true;
                }

                $compileCode .= '@import "' . $sass_prefix_path . '/style.scss";';

                // Import Font Awesome
                if(file_exists(COM_TZ_PORTFOLIO_PLUS_PATH_SITE.'/scss/vendor/fontawesome/all.scss')
                    && !isset(self::$imported['import_fontawesome'])) {
                    $compileCode .= '@import "vendor/fontawesome/all.scss";';
                    self::$imported['import_fontawesome'] = true;
                }

                if(count($variables)) {
                    $scss->setVariables($variables);
                }
                $scss -> setImportPaths($importPaths);
                $scss -> setFormatter('ScssPhp\ScssPhp\Formatter\Compressed');

                $content    = $scss -> compile($compileCode);

                if(!Folder::exists($css_path)){
                    Folder::create($css_path);
                }

                if($content) {
                    file_put_contents($css_path . '/' . $cssname . '.css', $content);
                }
            }

            if(file_exists($css_path . '/' . $cssname . '.css')) {
                $result = 'style/'.($styleName ? $styleName . '/' . $cssname . '.css' : $cssname . '.css');

                if($params -> get('enable_bootstrap', 1) && $params -> get('bootstrapversion', 4)
                    && !isset(self::$loaded[$styleName]['import_bootstrap'])) {
                    $styleSheets    = $document -> _styleSheets;
                    $styleKeys      = array_keys($styleSheets);

                    $grepKeys   = preg_grep('#components/com_tz_portfolio_plus/css/style/'.$styleName.'/style-#', $styleKeys);
                    if(count($grepKeys)) {
                        $grepKey    = array_shift($grepKeys);
                        if(isset($styleSheets[$grepKey])) {
                            unset($document -> _styleSheets[$grepKey]);
                        }
                    }
                    self::$loaded[$styleName]['import_bootstrap']   = true;
                }elseif(isset(self::$loaded[$styleName]['loaded'])){
                    return false;
                }

                self::$loaded[$styleName]['loaded']     = true;
                self::$imported['import_fontawesome']   = true;

                return $result;
            }
        }

        return false;
    }

    protected static function clearCache($styleName = '', $bootverPrefix = '', $prefix = 'style') {
        $template_dir   = COM_TZ_PORTFOLIO_PLUS_PATH_SITE.'/css/style';
        $template_dir  .= $styleName?'/'.$styleName:'';
        $version = new \JVersion;
        $version->refreshMediaVersion();
        if (!file_exists($template_dir)) {
            return false;
//            throw new \Exception("Template not found.", 404);
        }

        if (is_array($prefix)) {
            foreach ($prefix as $pre) {
                $styles = preg_grep('~^' . $pre . '-.*\.(css)$~', scandir($template_dir));
                foreach ($styles as $style) {
                    unlink($template_dir . '/' . $style);
                }
            }
        } else {
//            $styles = preg_grep('~^' . $prefix . '-.*\.(css)$~', scandir($template_dir));
            $styles = preg_grep('~^' . $prefix . '-'.$bootverPrefix.'-.*\.(css)$~', scandir($template_dir));

            foreach ($styles as $style) {
                unlink($template_dir . '/' . $style);
            }
        }
        return true;
    }

    public static function getSassDirByStyle($styleName){

        if(!$styleName){
            return false;
        }

        $storeId    = __METHOD__;
        $storeId   .= ':'.$styleName;
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $files      = array();
        $sassPath   = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.'/'.$styleName.'/scss';

        if(Folder::exists($sassPath) && $sFiles = Folder::files($sassPath, '.scss', true, true)){
            if($cfiles = self::getSassDirCore()){
                $files  = array_merge($files, $cfiles);
            }
            $files  = array_merge($files, $sFiles);
        }

        if(!count($files)){
            return false;
        }

        self::$cache[$storeId]  = $files;

        return $files;
    }

    public static function getSassDirCore(){

        $storeId    = __METHOD__;
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $sassPath  = COM_TZ_PORTFOLIO_PLUS_PATH_SITE.'/scss';

        if(Folder::exists($sassPath) && $files = Folder::files($sassPath, '.scss', true, true)){
            self::$cache[$storeId]  = $files;
            return $files;
        }

        return false;
    }
}