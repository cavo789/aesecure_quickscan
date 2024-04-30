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

jimport('joomla.application.component.view');
JHtml::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/helpers');

class TZ_Portfolio_PlusViewTemplates extends JViewLegacy
{
    protected $state;
    protected $items;
    protected $templates;
    protected $form;
    protected $sidebar;
    protected $pagination;

    public function display($tpl=null){
        if($this -> getLayout() == 'upload') {
            $this->form = $this->get('Form');
        }
        $this->state            = $this->get('State');
        $this->items            = $this->get('Items');
        $this -> templates      = $this -> get('Templates');
        $this->pagination       = $this->get('pagination');
        $this -> filterForm     = $this -> get('FilterForm');
        $this -> activeFilters  = $this -> get('ActiveFilters');

        Factory::getApplication() -> getLanguage() -> load('com_templates');

        TZ_Portfolio_PlusHelper::addSubmenu('templates');

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal' && $this->getLayout() !== 'upload') {
            $this -> addToolbar();
        }

        if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
            $this->sidebar = JHtmlSidebar::render();
        }

        parent::display($tpl);
    }

    protected function addToolbar(){

        // Get the results for each action.
        $canDo  = TZ_Portfolio_PlusHelper::getActions('com_tz_portfolio_plus', 'template');
        $user   = TZ_Portfolio_PlusUser::getUser();

        JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATES_MANAGER'),'eye');

        if($canDo -> get('core.create')) {
            JToolbarHelper::addNew('template.upload', 'COM_TZ_PORTFOLIO_PLUS_INSTALL_UPDATE');
        }

        if ($canDo->get('core.delete')){
            JToolBarHelper::deleteList(JText::_('COM_TZ_PORTFOLIO_PLUS_QUESTION_DELETE'),'template.uninstall','JTOOLBAR_UNINSTALL');
        }

        if ($canDo->get('core.edit.state')) {
            JToolBarHelper::publish($this -> getName().'.publish','JENABLED', true);
            JToolBarHelper::unpublish($this -> getName().'.unpublish','JDISABLED', true);
        }

        if($user->authorise('core.admin', 'com_tz_portfolio_plus')
            || $user->authorise('core.options', 'com_tz_portfolio_plus')){
            JToolBarHelper::preferences('com_tz_portfolio_plus');
        }

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document/administration/35-how-to-use-templates-in-tz-portfolio-plus.html?tmpl=component');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');

        JToolbarHelper::link('javascript:', JText::_('COM_TZ_PORTFOLIO_PLUS_INTRO_GUIDE'), 'support');
    }
}