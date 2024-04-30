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

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;

jimport('joomla.filesytem.file');
jimport('joomla.filesytem.folder');
jimport('joomla.application.component.controller');

class TZ_Portfolio_PlusControllerLegacy  extends JControllerLegacy{

    public function display($cachable = false, $urlparams = array())
    {

        $document = Factory::getApplication() -> getDocument();
        $viewType = $document->getType();
        $viewName = $this->input->get('view', $this->default_view);
        $viewLayout = $this->input->get('layout', 'default', 'string');

        $view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout));

        parent::display($cachable, $urlparams);

        $this -> parseDocument($view);

        return $this;
    }

    public function getView($name = '', $type = '', $prefix = 'TZ_Portfolio_PlusView', $config = array())
    {
        $view   = parent::getView($name,$type,$prefix,$config);
        if($view) {
            $view -> document   = Factory::getApplication() -> getDocument();
            if($template   = TZ_Portfolio_PlusTemplate::getTemplate(true)){
                if($template -> id){
//                    $tplparams  = $template -> params;
                    $path       = $view -> get('_path');

                    $bool_tpl   = false;
                    if(Folder::exists(COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.DIRECTORY_SEPARATOR.$template -> template)) {
                        $bool_tpl   = true;
                    }
                    if($bool_tpl) {
                        $name   = strtolower($name);
                        // Load template language
                        TZ_Portfolio_PlusTemplate::loadLanguage($template -> template);

                        $componentPath  = array_pop($path['template']);
                        if(isset($template -> base_path) && $template -> base_path) {
                            $path['template'][] = $template->base_path . DIRECTORY_SEPARATOR . $name;
                        }
                        $path['template'][] = $componentPath;
                        $view -> set('_path',$path);
                    }
                }
            }

        }
        return $view;
    }

    public function getModel($name = '', $prefix = 'TZ_Portfolio_PlusModel', $config = array())
    {
        return parent::getModel($name,$prefix,$config);
    }

    public function parseDocument(&$view = null){
        if($view){
            if(isset($view -> document)){
                if($template = TZ_Portfolio_PlusTemplate::getTemplate(true)) {
                    if(Folder::exists(COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.DIRECTORY_SEPARATOR.$template -> template)) {

                        $app		= Factory::getApplication('site');
                        $params     = $app -> getParams();

                        $docOptions['template']     = $template->template;
                        $docOptions['file']         = 'template.php';
                        $docOptions['params']       = $template->params;
                        $docOptions['directory']    = COM_TZ_PORTFOLIO_PLUS_PATH_SITE . DIRECTORY_SEPARATOR . 'templates';

                        // Add template.css file if it has have in template
                        if(!$params -> get('enable_bootstrap',1) || ($params -> get('enable_bootstrap',1)
                                && $params -> get('bootstrapversion', 4) == 3)){
                            $view->document -> addStyleSheet(TZ_Portfolio_PlusUri::base(true).'/css/tzportfolioplus.min.css',
                                array('version' => 'auto'));
                        }
                        $legacyPath = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . DIRECTORY_SEPARATOR . $template -> template
                            . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'template.css';
                        if((TZ_Portfolio_PlusTemplate::getSassDirByStyle($template -> template)
                                || (!TZ_Portfolio_PlusTemplate::getSassDirByStyle($template -> template) && TZ_Portfolio_PlusTemplate::getSassDirCore()))
                            && !File::exists($legacyPath)
                            && $cssRelativePath = TZ_Portfolio_PlusTemplate::getCssStyleName($template -> template,
                                $params, $docOptions['params'] -> get('colors', array()), $view -> document)){
                            $view->document->addStyleSheet(TZ_Portfolio_PlusUri::base(true)
                                . '/css/'.$cssRelativePath, array('version' => 'auto'));
                        }elseif (File::exists($legacyPath)) {
                            $view->document->addStyleSheet(TZ_Portfolio_PlusUri::base(true) . '/templates/'
                                . $template -> template . '/css/template.css', array('version' => 'auto'));
                        }

                        // Parse document of view to require template.php(in tz portfolio template) file.
                        $view->document->parse($docOptions);
                    }

                    return true;
                }
            }
            return false;
        }
        return false;
    }

}