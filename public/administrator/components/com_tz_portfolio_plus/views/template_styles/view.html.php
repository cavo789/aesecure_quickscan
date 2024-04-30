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
JHtml::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH.DIRECTORY_SEPARATOR.'html');
JLoader::import('templates', COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH);

class TZ_Portfolio_PlusViewTemplate_Styles extends JViewLegacy
{
    protected $state;
    protected $items;
    protected $sidebar;
    protected $pagination;

    public function display($tpl=null){

        $this->items		    = $this->get('Items');
        $this->state		    = $this->get('State');
        $this->pagination	    = $this->get('pagination');
        $this -> filterForm     = $this -> get('FilterForm');
        $this -> activeFilters  = $this -> get('ActiveFilters');

        Factory::getApplication() -> getLanguage() -> load('com_templates');

        TZ_Portfolio_PlusHelper::addSubmenu('template_styles');
        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
            $this -> addToolbar();
        }

        if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
            $this->sidebar = JHtmlSidebar::render();
        }

        parent::display($tpl);
    }

    protected function addToolbar(){

        $canDo	= TZ_Portfolio_PlusHelper::getActions('com_tz_portfolio_plus', 'style');
        $user   = TZ_Portfolio_PlusUser::getUser();

        JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATE_STYLES_MANAGER'), 'palette');

        if ($canDo->get('core.edit.state'))
        {
            JToolbarHelper::makeDefault($this -> getName().'.setDefault', 'COM_TEMPLATES_TOOLBAR_SET_HOME');
        }

        if($canDo -> get('core.edit')) {
            JToolBarHelper::editList('template_style.edit');
        }

        if ($canDo->get('core.create'))
        {
            JToolbarHelper::custom($this -> getName().'.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
        }

        if ($canDo->get('core.delete')){
            JToolBarHelper::deleteList(JText::_('COM_TZ_PORTFOLIO_PLUS_QUESTION_DELETE'),'template_styles.delete');
        }

        if($user->authorise('core.admin', 'com_tz_portfolio_plus')
            || $user->authorise('core.options', 'com_tz_portfolio_plus')){
            JToolBarHelper::preferences('com_tz_portfolio_plus');
        }

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document/administration/32-how-to-use-template-styles-in-tz-portfolio-plus.html?tmpl=component');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');
    }
}