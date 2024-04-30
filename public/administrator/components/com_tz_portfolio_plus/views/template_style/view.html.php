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

//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

class TZ_Portfolio_PlusViewTemplate_Style extends JViewLegacy
{
    protected $state        = null;
    protected $item         = null;
    protected $form         = null;
    protected $presets      = null;
    protected $rowItem      = null;
    protected $rowOuter     = null;
    protected $tzlayout     = null;
    protected $childrens    = null;
    protected $columnItem   = null;
    protected $includeTypes = null;

    public function display($tpl=null)
    {
        Factory::getApplication() -> getLanguage() -> load('com_templates');
        $document   = Factory::getApplication() -> getDocument();
        $this -> document -> addCustomTag('<link rel="stylesheet" href="'.JUri::base(true).'/components/com_tz_portfolio_plus/css/admin-layout.min.css" type="text/css"/>');
        $this -> document -> addCustomTag('<link rel="stylesheet" href="'.JUri::base(true).'/components/com_tz_portfolio_plus/css/spectrum.min.css" type="text/css"/>');

        $this -> state      = $this -> get('State');
        $this -> item       = $this -> get('Item');
        $this -> tzlayout   = $this -> get('TZLayout');
        $this -> form       = $this -> get('Form');
        $this -> presets    = $this -> get('Presets');

        if($includeTypes = TZ_Portfolio_PlusPluginHelper::getContentTypes()) {
            $this->includeTypes = $includeTypes;
        }

        $this -> addToolbar();

        parent::display($tpl);

        $this -> document -> addScript(JUri::base(true).'/components/com_tz_portfolio_plus/js/libs.min.js', array('version' => 'auto'));
        $this -> document -> addScript(JUri::base(true).'/components/com_tz_portfolio_plus/js/jquery-ui.min.js', array('version' => 'v=1.11.4'));
        $this -> document -> addScript(JUri::base(true).'/components/com_tz_portfolio_plus/js/layout-admin.min.js', array('version' => 'auto'));
        $this -> document -> addScript(JUri::base(true).'/components/com_tz_portfolio_plus/js/spectrum.min.js', array('version' => 'auto'));
        $this -> document -> addScriptDeclaration('
        jQuery(document).ready(function(){
            jQuery.tzLayoutAdmin({
                basePath    : "'.JUri::base().'",
                pluginPath  : "'.JURI::root(true).'/administrator/components/com_tz_portfolio_plus/views/template_style/tmpl",
                fieldName   : "jform[attrib]",
                j4Compare   : '.(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE?'true':'false').',
                token       : "'.JSession::getFormToken().'"
                
            });
        })
        Joomla.submitbutton = function(task) {
            if (task == \'template.cancel\' || document.formvalidator.isValid(document.getElementById(\'template-form\'))) {
                jQuery.tzLayoutAdmin.tzTemplateSubmit();
                Joomla.submitform(task, document.getElementById(\'template-form\'));
            }else {
                alert("'.$this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')).'");
            }
        };');
    }

    protected function addToolbar(){

        Factory::getApplication()->input->set('hidemainmenu', true);

        $user   = Factory::getUser();
        $isNew  = ($this -> item -> id == 0);
        $canDo  = JHelperContent::getActions('com_tz_portfolio_plus');

        JToolBarHelper::title(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_TEMPLATES_MANAGER_TASK',
            JText::_(($isNew)?'COM_TZ_PORTFOLIO_PLUS_PAGE_ADD_TEMPLATE':'COM_TZ_PORTFOLIO_PLUS_PAGE_EDIT_TEMPLATE')), 'palette');

        if ($canDo->get('core.edit')) {
            JToolBarHelper::apply('template_style.apply');
            JToolBarHelper::save('template_style.save');
        }

        // If checked out, we can still save
        if (!$isNew && $user->authorise('core.edit.state', 'com_tz_portfolio_plus')) {
            JToolBarHelper::save2copy('template_style.save2copy');
        }

        JToolBarHelper::cancel('template_style.cancel',JText::_('JTOOLBAR_CLOSE'));

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document/administration/32-how-to-use-template-styles-in-tz-portfolio-plus.html?tmpl=component');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');
    }

    protected function get_value($item, $method){
        if (!isset($item -> $method)) {
            if (preg_match('/offset/', $method)) {
                return isset($item -> offset) ? $item -> offset : '';
            }
            if (preg_match('/col/', $method)) {
                return isset($item -> span) ? $item -> span : '12';
            }
        }
        return isset($item -> $method) ? $item -> $method : '';
    }

    protected function get_color($item, $method){
        return isset($item -> $method) ? $item -> $method : 'rgba(255, 255, 255, 0)';
    }
}