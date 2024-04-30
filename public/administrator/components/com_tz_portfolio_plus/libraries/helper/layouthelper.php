<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

# Copyright: Copyright (C) 2011-2019 TZ Portfolio (http://www.tzportfolio.com). All Rights Reserved.

-------------------------------------------------------------------------*/

namespace TZ_Portfolio_Plus\Helper;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Application\ApplicationHelper;

class LayoutHelper{

    protected static $cache         = array();
    protected static $layoutHtml    = '';
    protected static $core_types    = array();
    protected static $mVariables    = array();

    /* Add variable to use in layout file of module
     * @param string    $key    Variable name will be convert (extract) to the variable
     * @param value     $value  The value of variable after the variable converted
     */
    public static function addModuleVariable($key, $value){
        $cKeys  = count(self::$mVariables)?array_keys(self::$mVariables):array();
        self::$mVariables[$key] = $value;
    }

    public static function getModuleVariables(){
        return self::$mVariables;
    }

    /* Generate single layout builder to html
     * @param   array   $args   An array of arguments (optional).
     */
    public static function generateLayout($args = array()){
        $core_types         = \TZ_Portfolio_PlusPluginHelper::getCoreContentTypes();
        self::$core_types   =  ArrayHelper::getColumn($core_types, 'value');

        call_user_func_array(array(__CLASS__, '_generateLayout'), $args);

        // Reset module variables
        self::$mVariables   = array();

        return self::$layoutHtml;
    }

    /* Check is view of component
     * @param   object   $object.
     */
    protected static function isView($object){
        if(!$object){
            return false;
        }

        if($object instanceof \JViewLegacy){
            return true;
        }
        return false;
    }

    /* Load html from view or module's layout
     * @param   array   $margs      An array of arguments (optional).
     * @param   string  $_layout    The layout to generate.
     * @param   object   $object.
     */
    protected static function loadLayoutHtml($_layout, $_object, $margs = array()){
        $html   = null;
        if(self::isView($_object)){
            if(method_exists($_object, 'loadTemplate')) {
                $html   = $_object -> loadTemplate($_layout);
            }
        }else{
            if($_layoutPath = \TZ_Portfolio_PlusModuleHelper::getTZLayoutPath($_object, '_'.$_layout)) {
//                if(count($args)){
                extract(array_merge($margs, self::$mVariables));
//                }
                if(!isset($module)){
                    $module = $_object;
                }
                if(strrpos($_layoutPath,'_'.$_layout.'.php') !== false) {
                    ob_start();
                    require $_layoutPath;
                    $html = ob_get_contents();
                    ob_end_clean();
                }
            }
        }
        return $html;
    }

    protected static function _generateLayout(&$article,&$params, $dispatcher = null, $object = null, $offset = 0, $context = null){
        if(!self::isView($object) && $params -> get('template_id')){
            $template   = \TZ_Portfolio_PlusTemplate::getTemplateById($params -> get('template_id'), true);
        }else{
            $template   = \TZ_Portfolio_PlusTemplate::getTemplate(true);
        }
        if($template){
            $theme  = $template;
            $html   = null;

            $doc        = (self::isView($object) && isset($object -> document))?$object -> document:Factory::getApplication() -> getDocument();
            $dispatcher = $dispatcher?$dispatcher:\TZ_Portfolio_PlusPluginHelper::getDispatcher();

            if(!$context && self::isView($object) && method_exists($object, 'getName')){
                $context    = 'com_tz_portfolio_plus.'.$object -> getName();
            }

            $offsetPrefixlg   = ($params -> get('bootstrapversion', 4) == 4)?' offset-lg-':' col-lg-offset-';
            $offsetPrefixmd   = ($params -> get('bootstrapversion', 4) == 4)?' offset-md-':' col-md-offset-';
            $offsetPrefixsm   = ($params -> get('bootstrapversion', 4) == 4)?' offset-sm-':' col-sm-offset-';
            $offsetPrefixxs   = ($params -> get('bootstrapversion', 4) == 4)?' offset-xs-':' col-xs-offset-';

            if($theme){
                if($tplParams  = $theme -> layout){
                    foreach($tplParams as $tplItems){
                        $rows   = null;

                        $background = null;
                        $color      = null;
                        $margin     = null;
                        $padding    = null;
                        $childRows  = array();
                        $rowName    = null;
                        if(isset($tplItems -> name) && $tplItems -> name){
                            $rowName    = ApplicationHelper::stringURLSafe($tplItems -> name);
                        }

                        if($tplItems && isset($tplItems -> children)){
                            foreach($tplItems -> children as $children){
                                $html   = null;

                                if($children -> type && $children -> type !='none'){
                                    if(in_array($children -> type, self::$core_types)) {
                                        $html = self::loadLayoutHtml($children->type, $object, array('item' => $article, 'params' => $params));
                                    }else{
                                        $plugin = $children -> type;
                                        $layout = null;
                                        if(strpos($children -> type, ':') != false){
                                            list($plugin, $layout)  = explode(':', $children -> type);
                                        }

                                        if($plugin_obj = \TZ_Portfolio_PlusPluginHelper::getPlugin('content', $plugin)) {
                                            $className      = 'PlgTZ_Portfolio_PlusContent'.ucfirst($plugin);

                                            if(!class_exists($className)){
                                                \TZ_Portfolio_PlusPluginHelper::importPlugin('content', $plugin);
                                            }
                                            if(class_exists($className)) {
                                                $registry   = new Registry($plugin_obj -> params);

                                                $plgClass   = new $className($dispatcher,array('type' => ($plugin_obj -> type)
                                                , 'name' => ($plugin_obj -> name), 'params' => $registry));

                                                if(method_exists($plgClass, 'onContentDisplayArticleView')) {
                                                    $html = $plgClass->onContentDisplayArticleView($context, $article, $article->params
                                                        , $offset, $layout);
                                                }
                                            }
                                            if(is_array($html)) {
                                                $html = implode("\n", $html);
                                            }
                                        }
                                    }
                                    $html   = trim($html);
                                }

                                if(!empty($html) || (!empty($children -> children) and is_array($children -> children))){
                                    if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                                        || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                                        || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                                        || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                                        || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                                        $childRows[] = '<div class="'
                                            .(!empty($children -> {"col-lg"})?'col-lg-'.$children -> {"col-lg"}:'')
                                            .(!empty($children -> {"col-md"})?' col-md-'.$children -> {"col-md"}:'')
                                            .(!empty($children -> {"col-sm"})?' col-sm-'.$children -> {"col-sm"}:'')
                                            .(!empty($children -> {"col-xs"})?' col-xs-'.$children -> {"col-xs"}:'')
                                            .(!empty($children -> {"col-lg-offset"})?$offsetPrefixlg.$children -> {"col-lg-offset"}:'')
                                            .(!empty($children -> {"col-md-offset"})?$offsetPrefixmd.$children -> {"col-md-offset"}:'')
                                            .(!empty($children -> {"col-sm-offset"})?$offsetPrefixsm.$children -> {"col-sm-offset"}:'')
                                            .(!empty($children -> {"col-xs-offset"})?$offsetPrefixxs.$children -> {"col-xs-offset"}:'')
                                            .(!empty($children -> {"customclass"})?' '.$children -> {"customclass"}:'')
                                            .($children -> responsiveclass?' '.$children -> responsiveclass:'').'">';
                                    }

                                    $childRows[] = $html;

                                    if( !empty($children -> children) and is_array($children -> children) ){
                                        self::_childrenLayout($childRows,$children,$article,$params,$dispatcher, $object, $offset, $context);
                                    }

                                    if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                                        || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                                        || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                                        || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                                        || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                                        $childRows[] = '</div>'; // Close col tag
                                    }
                                }
                            }
                        }


                        if(count($childRows)) {
                            if (isset($tplItems->backgroundcolor) && $tplItems->backgroundcolor
                                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',
                                    trim($tplItems->backgroundcolor))
                            ) {
                                $background = 'background: ' . $tplItems->backgroundcolor . ';';
                            }
                            if (isset($tplItems->textcolor) && $tplItems->textcolor
                                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',
                                    trim($tplItems->textcolor))
                            ) {
                                $color = 'color: ' . $tplItems->textcolor . ';';
                            }
                            if (isset($tplItems->margin) && !empty($tplItems->margin)) {
                                $margin = 'margin: ' . $tplItems->margin . ';';
                            }
                            if (isset($tplItems->padding) && !empty($tplItems->padding)) {
                                $padding = 'padding: ' . $tplItems->padding . ';';
                            }
                            if ($background || $color || $margin || $padding) {
                                $doc->addStyleDeclaration('
                                    #tz-portfolio-template-' . ($rowName?$rowName:'') . '{
                                        ' . $background . $color . $margin . $padding . '
                                    }
                                ');
                            }
                            if (isset($tplItems->linkcolor) && $tplItems->linkcolor
                                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i', trim($tplItems->linkcolor))
                            ) {
                                $doc->addStyleDeclaration('
                                #tz-portfolio-template-' . ($rowName?$rowName:'') . ' a{
                                    color: ' . $tplItems->linkcolor . ';
                                }
                            ');
                            }
                            if (isset($tplItems->linkhovercolor) && $tplItems->linkhovercolor
                                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i', trim($tplItems->linkhovercolor))
                            ) {
                                $doc->addStyleDeclaration('
                                #tz-portfolio-template-' . ($rowName?$rowName:'') . ' a:hover{
                                    color: ' . $tplItems->linkhovercolor . ';
                                }
                            ');
                            }
                            $rows[] = '<div id="tz-portfolio-template-' . ($rowName?$rowName:'') . '"'
                                . ' class="' . ($tplItems->{"class"} ? ' ' . $tplItems->{"class"} : '')
                                . ((isset($tplItems->responsive) && $tplItems->responsive) ? ' ' . $tplItems->responsive : '') . '">';
                            if (isset($tplItems->containertype) && $tplItems->containertype) {
                                $rows[] = '<div class="' . $tplItems->containertype . '">';
                            }

                            $rows[] = '<div class="row">';

                            $rows = array_merge($rows, $childRows);

                            if (isset($tplItems->containertype) && $tplItems->containertype) {
                                $rows[] = '</div>';
                            }
                            $rows[] = '</div>';
                            $rows[] = '</div>';
                        }
                        if($rows) {
                            self::$layoutHtml   .= implode("\n", $rows);
                        }
                    }
                }
            }
        }
        return false;
    }
    protected static function _childrenLayout(&$rows,$children,&$article,&$params,$dispatcher = null, $object = null, $offset = 0, $context = null){
        $doc    = (self::isView($object) && isset($object -> document))?$object -> document:Factory::getApplication() -> getDocument();

        if(!$context && self::isView($object) && method_exists($object, 'getName')){
            $context    = 'com_tz_portfolio_plus.'.$object -> getName();
        }

        $offsetPrefixlg   = ($params -> get('bootstrapversion', 4) == 4)?' offset-lg-':' col-lg-offset-';
        $offsetPrefixmd   = ($params -> get('bootstrapversion', 4) == 4)?' offset-md-':' col-md-offset-';
        $offsetPrefixsm   = ($params -> get('bootstrapversion', 4) == 4)?' offset-sm-':' col-sm-offset-';
        $offsetPrefixxs   = ($params -> get('bootstrapversion', 4) == 4)?' offset-xs-':' col-xs-offset-';

        foreach($children -> children as $children){
            $background = null;
            $color      = null;
            $margin     = null;
            $padding    = null;
            $childRows  = array();
            $class      = null;
            $rowName    = null;
            $responsive = null;

            if(isset($children -> name) && $children -> name){
                $rowName    = ApplicationHelper::stringURLSafe($children -> name);
            }
            if(isset($children->{"class"}) && $children->{"class"}){
                $class  = $children->{"class"};
            }
            if(isset($children->responsive) && $children->responsive){
                $class  = $children->responsive;
            }

            if (isset($children->backgroundcolor) && $children->backgroundcolor
                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',
                    trim($children->backgroundcolor))) {
                $background = 'background: ' . $children->backgroundcolor . ';';
            }
            if (isset($children->textcolor) && $children->textcolor
                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i', trim($children->textcolor))) {
                $color = 'color: ' . $children->textcolor . ';';
            }
            if (isset($children->margin) && !empty($children->margin)) {
                $margin = 'margin: ' . $children->margin . ';';
            }
            if (isset($children->padding) && !empty($children->padding)) {
                $padding = 'padding: ' . $children->padding . ';';
            }
            if ($background || $color || $margin || $padding) {
                $doc->addStyleDeclaration('
                    #tz-portfolio-template-' . ($rowName?$rowName:'') . '-inner{
                        ' . $background . $color . $margin . $padding . '
                    }
                ');
            }
            if (isset($children->linkcolor) && $children->linkcolor
                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i', trim($children->linkcolor))) {
                $doc->addStyleDeclaration('
                        #tz-portfolio-template-' . ($rowName?$rowName:'') . '-inner a{
                            color: ' . $children->linkcolor . ';
                        }
                    ');
            }
            if (isset($children->linkhovercolor) && $children->linkhovercolor
                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i', trim($children->linkhovercolor))) {
                $doc->addStyleDeclaration('
                        #tz-portfolio-template-' . ($rowName?$rowName:'') . '-inner a:hover{
                            color: ' . $children->linkhovercolor . ';
                        }
                    ');
            }

            if(isset($children -> children) && $children -> children){
                foreach($children -> children as $children){
                    $html   = null;

                    if($children -> type && $children -> type !='none'){
                        if(in_array($children -> type, self::$core_types)) {
                            $html = self::loadLayoutHtml($children -> type, $object, array('item' => $article,
                                'params' => $params));
                        }else{
                            $plugin = $children -> type;
                            $layout = null;
                            if(strpos($children -> type, ':') != false){
                                list($plugin, $layout)  = explode(':', $children -> type);
                            }

                            if($plugin_obj = \TZ_Portfolio_PlusPluginHelper::getPlugin('content', $plugin)) {
                                $className      = 'PlgTZ_Portfolio_PlusContent'.ucfirst($plugin);

                                if(!class_exists($className)){
                                    \TZ_Portfolio_PlusPluginHelper::importPlugin('content', $plugin);
                                }
                                if(class_exists($className)) {
                                    $registry   = new Registry($plugin_obj -> params);

                                    $plgClass   = new $className($dispatcher,array('type' => ($plugin_obj -> type)
                                    , 'name' => ($plugin_obj -> name), 'params' => $registry));

                                    if(method_exists($plgClass, 'onContentDisplayArticleView')) {
                                        $html = $plgClass->onContentDisplayArticleView($context,
                                            $article, $article->params, $offset, $layout);
                                    }
                                }
                                if(is_array($html)) {
                                    $html = implode("\n", $html);
                                }
                            }
                        }
                        $html   = trim($html);
                    }

                    if( !empty($html) || (!empty($children -> children) and is_array($children -> children))){
                        if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                            || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                            || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                            || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                            || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                            $childRows[] = '<div class="'
                                .(!empty($children -> {"col-lg"})?'col-lg-'.$children -> {"col-lg"}:'')
                                .(!empty($children -> {"col-md"})?' col-md-'.$children -> {"col-md"}:'')
                                .(!empty($children -> {"col-sm"})?' col-sm-'.$children -> {"col-sm"}:'')
                                .(!empty($children -> {"col-xs"})?' col-xs-'.$children -> {"col-xs"}:'')
                                .(!empty($children -> {"col-lg-offset"})?$offsetPrefixlg.$children -> {"col-lg-offset"}:'')
                                .(!empty($children -> {"col-md-offset"})?$offsetPrefixmd.$children -> {"col-md-offset"}:'')
                                .(!empty($children -> {"col-sm-offset"})?$offsetPrefixsm.$children -> {"col-sm-offset"}:'')
                                .(!empty($children -> {"col-xs-offset"})?$offsetPrefixxs.$children -> {"col-xs-offset"}:'')
                                .(!empty($children -> {"customclass"})?' '.$children -> {"customclass"}:'')
                                .($children -> responsiveclass?' '.$children -> responsiveclass:'').'">';
                        }
                        $childRows[] = $html;

                        if( !empty($children -> children) and is_array($children -> children) ){
                            self::_childrenLayout($childRows,$children,$article,$params,$dispatcher, $object, $offset, $context);
                        }

                        if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                            || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                            || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                            || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                            || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                            $childRows[] = '</div>'; // Close col tag
                        }
                    }

                }
            }

            if(count($childRows)) {
                $rows[] = '<div id="tz-portfolio-template-' .($rowName?$rowName:'')
                    . '-inner" class="'. ($class?$class:'').
                    ($responsive ? ' ' . $responsive : '') . '">';
                $rows[] = '<div class="row">';
                $rows   = array_merge($rows, $childRows);

                $rows[] = '</div>';
                $rows[] = '</div>';
            }
        }
        return;
    }
}
