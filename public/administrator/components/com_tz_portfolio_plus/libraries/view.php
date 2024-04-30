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

// No direct access
defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Application\ApplicationHelper;

jimport('joomla.application.component.view');

class TZ_Portfolio_PlusViewLegacy extends JViewLegacy{
    protected $generateLayout   = null;
    protected $core_types       = array();

    public function generateLayout(&$article,&$params,$dispatcher){
        if($template   = TZ_Portfolio_PlusTemplate::getTemplate(true)){
            $tplparams  = $template -> params;
            if($tplparams -> get('use_single_layout_builder',1)){

                $core_types         = TZ_Portfolio_PlusPluginHelper::getCoreContentTypes();
                $this -> core_types = ArrayHelper::getColumn($core_types, 'value');

                $this->_generateLayout($article, $params, $dispatcher);
                return $this -> generateLayout;
            }
        }
        return false;
    }

    protected function _generateLayout(&$article,&$params, $dispatcher){
        if($template   = TZ_Portfolio_PlusTemplate::getTemplate(true)){
            $theme  = $template;
            $html   = null;

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
                                    if(in_array($children -> type, $this -> core_types)) {
                                        $html = $this->loadTemplate($children->type);
                                    }else{
                                        $plugin = $children -> type;
                                        $layout = null;
                                        if(strpos($children -> type, ':') != false){
                                            list($plugin, $layout)  = explode(':', $children -> type);
                                        }

                                        if($plugin_obj = TZ_Portfolio_PlusPluginHelper::getPlugin('content', $plugin)) {
                                            $className      = 'PlgTZ_Portfolio_PlusContent'.ucfirst($plugin);

                                            if(!class_exists($className)){
                                                TZ_Portfolio_PlusPluginHelper::importPlugin('content', $plugin);
                                            }
                                            if(class_exists($className)) {
                                                $registry   = new JRegistry($plugin_obj -> params);

                                                $plgClass   = new $className($dispatcher,array('type' => ($plugin_obj -> type)
                                                , 'name' => ($plugin_obj -> name), 'params' => $registry));

                                                if(method_exists($plgClass, 'onContentDisplayArticleView')) {
                                                    $html = $plgClass->onContentDisplayArticleView('com_tz_portfolio_plus.'
                                                        .$this -> getName(), $this->item, $this->item->params
                                                        , $this->state->get('list.offset'), $layout);
                                                }
                                            }
                                            if(is_array($html)) {
                                                $html = implode("\n", $html);
                                            }
                                        }
                                    }
                                    $html   = $html ? trim($html) : '';
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
                                        $this -> _childrenLayout($childRows,$children,$article,$params,$dispatcher);
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
                                $this->document->addStyleDeclaration('
                                    #tz-portfolio-template-' . ($rowName?$rowName:'') . '{
                                        ' . $background . $color . $margin . $padding . '
                                    }
                                ');
                            }
                            if (isset($tplItems->linkcolor) && $tplItems->linkcolor
                                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i', trim($tplItems->linkcolor))
                            ) {
                                $this->document->addStyleDeclaration('
                                #tz-portfolio-template-' . ($rowName?$rowName:'') . ' a{
                                    color: ' . $tplItems->linkcolor . ';
                                }
                            ');
                            }
                            if (isset($tplItems->linkhovercolor) && $tplItems->linkhovercolor
                                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i', trim($tplItems->linkhovercolor))
                            ) {
                                $this->document->addStyleDeclaration('
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
                            $this->generateLayout .= implode("\n", $rows);
                        }
                    }
                }
            }
        }
    }

    protected function _childrenLayout(&$rows,$children,&$article,&$params,$dispatcher){

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
                $this->document->addStyleDeclaration('
                        #tz-portfolio-template-' . ($rowName?$rowName:'') . '-inner{
                            ' . $background . $color . $margin . $padding . '
                        }
                    ');
            }
            if (isset($children->linkcolor) && $children->linkcolor
                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i', trim($children->linkcolor))) {
                $this->document->addStyleDeclaration('
                            #tz-portfolio-template-' . ($rowName?$rowName:'') . '-inner a{
                                color: ' . $children->linkcolor . ';
                            }
                        ');
            }
            if (isset($children->linkhovercolor) && $children->linkhovercolor
                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i', trim($children->linkhovercolor))) {
                $this->document->addStyleDeclaration('
                            #tz-portfolio-template-' . ($rowName?$rowName:'') . '-inner a:hover{
                                color: ' . $children->linkhovercolor . ';
                            }
                        ');
            }

            if(isset($children -> children) && $children -> children){
                foreach($children -> children as $children){
                    $html   = null;

                    if($children -> type && $children -> type !='none'){
                        if(in_array($children -> type, $this -> core_types)) {
                            $html = $this -> loadTemplate($children -> type);
                        }else{
                            $plugin = $children -> type;
                            $layout = null;
                            if(strpos($children -> type, ':') != false){
                                list($plugin, $layout)  = explode(':', $children -> type);
                            }

                            if($plugin_obj = TZ_Portfolio_PlusPluginHelper::getPlugin('content', $plugin)) {
                                $className      = 'PlgTZ_Portfolio_PlusContent'.ucfirst($plugin);

                                if(!class_exists($className)){
                                    TZ_Portfolio_PlusPluginHelper::importPlugin('content', $plugin);
                                }
                                if(class_exists($className)) {
                                    $registry   = new JRegistry($plugin_obj -> params);

                                    $plgClass   = new $className($dispatcher,array('type' => ($plugin_obj -> type)
                                    , 'name' => ($plugin_obj -> name), 'params' => $registry));

                                    if(method_exists($plgClass, 'onContentDisplayArticleView')) {
                                        $html = $plgClass->onContentDisplayArticleView('com_tz_portfolio_plus.'.$this -> getName(),
                                            $this->item, $this->item->params, $this->state->get('list.offset'), $layout);
                                    }
                                }
                                if(is_array($html)) {
                                    $html = implode("\n", $html);
                                }
                            }
                        }
                        $html   = $html ? trim($html) : '';
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
                            $this -> _childrenLayout($childRows,$children,$article,$params,$dispatcher);
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